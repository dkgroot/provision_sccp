<?php declare(strict_types=1);

namespace PROVISION;
use PROVISION\ResolveResult as ResolveResult;
use PROVISION\ResolveCache as ResolveCache;
use PROVISION\Utils as Utils; 

/* Todo:
 ✔️ setup logging
 ✔️ read config.file
 
 ✔?️ improve error handling
 ✔️? secure urlencoding/urldecoding
 ✔️? don't allow browsing
 
 - check source ip-range
 - check HTTPHeader for known BrowserTypes
 
 - Could use some more test-cases, especially error ones
*/
class Resolve {
	private $isDirty = FALSE;
	private $cache;
	private $config;
	function __construct($config) {
		$this->config = $config;
 		$this->cache = new ResolveCache\FileCache($this->config['main']['cache_filename']);
		if ($this->cache->isDirty()) {
			$this->rebuildCache();
		}
	}
	
	public function searchForFile($filename) {
		$path = "";
		foreach($this->config['subdirs'] as $key => $value) {
			if ($key === "firmware" || $key === "data" || $key === "etc") {
				continue;
			}
			$path = realpath($this->config['main']['base_path'] . DIRECTORY_SEPARATOR . $value['path'] . DIRECTORY_SEPARATOR . $filename);
			if (!$path) {
				print("path: '" . $this->config['main']['base_path'] . DIRECTORY_SEPARATOR . $value['path'] . DIRECTORY_SEPARATOR . $filename . "' not found\n");
				return ResolveResult::FileNotFound;
			}
			$this->cache->addFile($filename, $path);
			return $path;
		}
		Utils::log_error("File '$filename' does not exist");
		return ResolveResult::FileNotFound;
	}
	
	public function rebuildCache() {
		Utils::log_debug("Rebuilding Cache, standby...");
		foreach($this->config['subdirs'] as $key =>$value) {
			if ($key === "data" || $key === "etc") {
				continue;
			}
			$path = realpath($this->config['main']['base_path'] . DIRECTORY_SEPARATOR . $value['path'] . DIRECTORY_SEPARATOR);
			if (!$path) {
				print("path: '" . $this->config['main']['base_path'] . DIRECTORY_SEPARATOR . $value['path'] . "' not found\n");
				break;
			}
			$dir_iterator = new \RecursiveDirectoryIterator($path);
			$filter = new \RecursiveCallbackFilterIterator($dir_iterator, function ($current, $key, $iterator) {
				// Skip hidden files and directories.
				if ($current->getFilename()[0] === '.' || $current->getFilename() == "bak") {
					return FALSE;
				}
				return TRUE;
			});
			$iterator = new \RecursiveIteratorIterator($filter, \RecursiveIteratorIterator::SELF_FIRST);
			foreach ($iterator as $file) {
				if ($file->isFile()) {
					if ($value['strip']) {
						$this->cache->addFile($file->getFileName(), $file->getPathname());
					} else {
						$subdir = basename(dirname($file->getPathname()));
						$this->cache->addFile($subdir. DIRECTORY_SEPARATOR . $file->getFileName(), $file->getPathname());
					}
				}
			}
		}
		$this->isDirty  = TRUE;
	}
	
	public function validateRequest($request) {
		/* make sure request does not startwith or contain: "/", "../" or "/./" */
		/* make sure request only starts with filename or one of $config[$subdir]['locale'] or $config[$subdir]['wallpaper'] */
		/* check uri/url decode */
		if (!$request || empty($request)) {
			Utils::log_error("Request is empty");
			return ResolveResult::EmptyRequest;
		}
		if (!is_string($request)) {
			Utils::log_error("Request is not a string");
			return ResolveResult::RequestNotAString;
		}
		Utils::log_debug($request . ":" . escapeshellarg($request) . ":" . Utils::utf8_urldecode($request) . "\n");
		$escaped_request = escapeshellarg(Utils::utf8_urldecode($request));
		if ($escaped_request !== "'" . $request . "'") {
			Utils::log_error("Request '$request' contains invalid characters");
			return ResolveResult::RequestContainsInvalidChar;
		}
		if (strstr($escaped_request, "..")) {
			Utils::log_error("Request '$request' contains '..'");
			return ResolveResult::RequestContainsPathWalk;
		}
		return ResolveResult::Ok;
	}

	public function resolve($request) /* canthrow */ {
		$path = '';
		$result = $this->validateRequest($request);
		if ($result !== ResolveResult::Ok) {
			return $result;
		}
		if (($path = $this->cache->getPath($request))) {
			if (!file_exists($path)) {
				 $this->cache->removeFile($request);
				 Utils::log_error("File '$request' does not exist on FS");
				 return ResolveResult::FileNotFound;
			}
			return $path;
		}
		if ($this->searchForFile($request)) {
			$path = $this->cache->getPath($request);
		}
		return $path;
	}
	
	/* temporary */
	function printCache() {
		print_r($this->cache);
	}
}
?>
