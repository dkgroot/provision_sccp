#!/usr/bin/php
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
			if (!file_put_contents($this->config['main']['cache_filename'], serialize($this->cache))) {
				$this->log_error_and_throw("Could not write to file '".$this->config['cache_filename']."' at Resolver::destruct");
			}
		}
	}
	function log_error_and_throw($message) {
		global $logger;
		$logger->log('LOG_ERROR', $message);
		throw new Exception($message);
	}
	function log_debug($message) {
		global $logger;
		$logger->log('LOG_DEBUG', $message);
	}
	function searchForFile($filename) {
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
		$this->log_error_and_throw("File '$filename' does not exist");
	}
	function buildCleanCache() {
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
	function addFile($requestpath, $truepath) {
		//$this->logger->log('LOG_DEBUG', "Adding $requestpath");
		$this->log_debug("Adding $requestpath");
		$this->cache[$requestpath] = $truepath;
		$this->isDirty  =TRUE;
	}
	function removeFile($requestpath) {
		$this->log_debug("Removing $hash");
		unset($this->cache[$requestpath]);
		$this->isDirty = TRUE;
	}
	function validateRequest($request) {
		/* make sure request does not startwith or contain: "/", "../" or "/./" */
		/* make sure request only starts with filename or one of $config[$subdir]['locale'] or $config[$subdir]['wallpaper'] */
		/* check uri/url decode */
		$this->log_debug($request . ":" . escapeshellarg($request) . ":" . utf8_urldecode($request) . "\n");
		$escaped_request = escapeshellarg(utf8_urldecode($request));
		if ($escaped_request !== "'" . $request . "'") {
			$this->log_error_and_throw("Request '$request' contains invalid characters");
		}
		if (strstr($escaped_request, "..")) {
			$this->log_error_and_throw("Request '$request' containst '..'");
		}
	}
	function resolve($request) /* canthrow */ {
		$this->validateRequest($request);
		$path = '';
		if (array_key_exists($request, $this->cache)) {
			if ($path = $this->cache[$request]) {
				if (!file_exists($path)) {
					 $this->removeFile($request);
					 $this->log_error_and_throw("File '$request' does not exist on FS");
				}
				return $path;
			}
		}
		if ($this->searchForFile($request)) {
			return $this->cache[$request];
		}
		return $path;
	}
	/* temporairy */
	function printCache() {
		print_r($this->cache);
	}
}

//$resolver = new Resolver($config);
$resolver = new Resolver($config);

// Tests
$test_cases = Array(
	Array('request' => 'jar70sccp.9-4-2ES26.sbn', 'expected' => '/tftpboot/firmware/7970/jar70sccp.9-4-2ES26.sbn', 'throws' => FALSE),
	Array('request' => 'Russian_Russian_Federation/be-sccp.jar', 'expected' => '/tftpboot/locales/languages/Russian_Russian_Federation/be-sccp.jar', 'throws' => FALSE),
	Array('request' => 'Spain/g3-tones.xml', 'expected' => '/tftpboot/locales/countries/Spain/g3-tones.xml', 'throws' => FALSE),
	Array('request' => '320x196x4/Chan-SCCP-b.png', 'expected' => '/tftpboot/wallpapers/320x196x4/Chan-SCCP-b.png', 'throws' => FALSE),
	Array('request' => 'XMLDefault.cnf.xml', 'expected' => '/tftpboot/settings/bak/XMLDefault.cnf.xml', 'throws' => FALSE),
	Array('request' => '../XMLDefault.cnf.xml', 'expected' => '', 'throws' => TRUE),
	Array('request' => 'XMLDefault.cnf.xml/../../text.xml', 'expected' => '', 'throws' => TRUE),
	
);
foreach($test_cases as $test) {
	try {
		$result = $resolver->resolve($test['request']);
		if ($result !== $base_path . $test['expected']) {
			print("Error: expected result does not match what we got\n");
			print("request:'".$test['request']."', result:'" . $base_path . $test['expected'] . "'\n");
		} else {
			print("'" . $test['request'] . "' => '" . $result . "'\n");
		}
	} catch (Exception $e) {
		if (!$test['throws']) {
			print("Error: request was expected to throw: $e\n");
		} else {
			print("'" . $test['request'] . "' => throws error as expected\n");
		}
	}
}
unset($resolver);
#unlink($CACHEFILE_NAME);
?>
