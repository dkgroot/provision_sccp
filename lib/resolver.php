<?php
include_once("config.php");
include_once("utils.php");

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
class ResolveResult {
	const Ok = 0;
	const EmptyRequest = 1;
	const RequestNotAString = 2;
	const RequestContainsInvalidChar = 3;
	const RequestContainsPathWalk = 4;
	const FileNotFound = 5;
	const InvalidFilename = 6;
	const InvalidPath = 7;
}

class Resolver {
	private $isDirty = FALSE;
	private $cache = array();
	private $config;
	//private $logger;
	function __construct($config) {
		//global $logger;
		$this->config = $config;
		//$this->logger = $logger;
		if(file_exists($this->config['main']['cache_filename'])) {
	 		$this->cache = unserialize(file_get_contents($config['main']['cache_filename'])); 
		} else {
			$this->buildCleanCache();
		}
	}
	function __destruct() {
		// $this->printCache()
		if ($this->isDirty) {
			if (!is_writable($this->config['main']['cache_filename'])) {
				$this->log_error_and_throw("Could not write to file '".$this->config['cache_filename']."' at Resolver::destruct");
			}
			if (!file_put_contents($this->config['main']['cache_filename'], serialize($this->cache))) {
				$this->log_error_and_throw("Could not write to file '".$this->config['cache_filename']."' at Resolver::destruct");
			}
		}
	}
	
	public function log_debug($message) {
		global $logger;
		$logger->log('LOG_DEBUG', $message);
	}

	public function log_error($message) {
		global $logger;
		$logger->log('LOG_ERROR', $message);
	}

	public function log_error_and_throw($message) {
		$this->log_error($message);
		throw new Exception($message);
	}
	
	public function searchForFile($filename) {
		foreach($this->config['subdirs'] as $key => $value) {
			if ($key === "firmware" || $key === "tftproot" ) {
				continue;
			}
			$path = realpath($this->config['main']['base_path'] . "/" . $value['path'] . "/$filename");
			if (file_exists($path)) {
				 $this-> addFile($filename, $path);
				 return $path;
			}
		}
		$this->log_error("File '$filename' does not exist");
		return ResolveResult::FileNotFound;
	}
	
	public function buildCleanCache() {
		foreach($this->config['subdirs'] as $key =>$value) {
			if ($key === "tftproot") {
				continue;
			}
			$path = $this->config['main']['base_path'] . "/" . $value['path'] . "/";
			$dir_iterator = new RecursiveDirectoryIterator($path);
			$iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
			foreach ($iterator as $file) {
				if ($file->isFile()) {
					if ($value['strip']) {
						$this->addFile($file->getFileName(), $file->getPathname());
					} else {
						$subdir = basename(dirname($file->getPathname()));
						$this->addFile('$subpath/'.$file->getFileName(), $file->getPathname());
					}
				}
			}
		}
		$this->isDirty  = TRUE;
	}
	
	public function addFile($requestpath, $truepath) {
		$this->log_debug("Adding $requestpath");
		$this->cache[$requestpath] = $truepath;
		$this->isDirty  =TRUE;
	}
	
	public function removeFile($requestpath) {
		$this->log_debug("Removing $hash");
		unset($this->cache[$requestpath]);
		$this->isDirty = TRUE;
	}
	
	public function validateRequest($request) {
		/* make sure request does not startwith or contain: "/", "../" or "/./" */
		/* make sure request only starts with filename or one of $config[$subdir]['locale'] or $config[$subdir]['wallpaper'] */
		/* check uri/url decode */
		if (!$request || empty($request)) {
			$this->log_error("Request is empty");
			return ResolveResult::EmptyRequest;
		}
		if (!is_string($request)) {
			$this->log_error("Request is not a string");
			return ResolveResult::RequestNotAString;
		}
		$this->log_debug($request . ":" . escapeshellarg($request) . ":" . utf8_urldecode($request) . "\n");
		$escaped_request = escapeshellarg(utf8_urldecode($request));
		if ($escaped_request !== "'" . $request . "'") {
			$this->log_error("Request '$request' contains invalid characters");
			return ResolveResult::RequestContainsInvalidChar;
		}
		if (strstr($escaped_request, "..")) {
			$this->log_error("Request '$request' contains '..'");
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
		if (array_key_exists($request, $this->cache)) {
			if ($path = $this->cache[$request]) {
				if (!file_exists($path)) {
					 $this->removeFile($request);
					 $this->log_error("File '$request' does not exist on FS");
					 return ResolveResult::FileNotFound;
				}
				return $path;
			}
		}
		if ($this->searchForFile($request)) {
			$path = $this->cache[$request];
		}
		return $path;
	}
	
	/* temporary */
	function printCache() {
		print_r($this->cache);
	}
}

// Testing
if(defined('STDIN') ) {
	$resolver = new Resolver($config);
	$test_cases = Array(
		Array('request' => 'jar70sccp.9-4-2ES26.sbn', 'expected' => '/tftpboot/firmware/7970/jar70sccp.9-4-2ES26.sbn'),
		Array('request' => 'Russian_Russian_Federation/be-sccp.jar', 'expected' => '/tftpboot/locales/languages/Russian_Russian_Federation/be-sccp.jar'),
		Array('request' => 'Spain/g3-tones.xml', 'expected' => '/tftpboot/locales/countries/Spain/g3-tones.xml'),
		Array('request' => '320x196x4/Chan-SCCP-b.png', 'expected' => '/tftpboot/wallpapers/320x196x4/Chan-SCCP-b.png'),
		Array('request' => 'XMLDefault.cnf.xml', 'expected' => '/tftpboot/settings/bak/XMLDefault.cnf.xml'),
		Array('request' => '../XMLDefault.cnf.xml', 'expected' => ResolveResult::RequestContainsPathWalk),
		Array('request' => 'XMLDefault.cnf.xml/../../text.xml', 'expected' => ResolveResult::RequestContainsPathWalk),
	);
	foreach($test_cases as $test) {
		try {
			$result = $resolver->resolve($test['request']);
			if (is_string($result)) {
				if ($result === $base_path . $test['expected']) {
					print("'" . $test['request'] . "' => '" . $result . "'\n");
					continue;
				}
			} else {
				if ($result === $test['expected']) {
					print("'" . $test['request'] . "' => '" . $result . "'\n");
					continue;
				}
			}
			print("Error: expected result does not match what we got\n");
			print("request:'".$test['request']."'\n");
			print("expected:'" . $base_path . $test['expected'] . "'\n");
			print("result:'" . $result . "'\n\n");
		} catch (Exception $e) {
			print("'" . $test['request'] . "' => throws error as expected\n");
			print("Exception: " . $e->getMessage() . "\n");
		}
	}
	unset($resolver);
	#unlink($CACHEFILE_NAME);
}
?>
