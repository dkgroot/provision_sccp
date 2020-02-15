#!/usr/bin/php
<?php
include_once("config.php");

/* Todo:
 - setup logging
 - read config.file
 - improve error handling
 - secure urlencoding/urldecoding
 - don't allow browsing
 - check source ip-range
 - check HTTPHeader for known BrowserTypes
*/

class Resolver {
	private $isDirty = FALSE;
	private $cache = array();
	private $config;
	function __construct($config) {
		$this->config = $config;
		if(file_exists($this->config['main']['cache_filename'])) {
	 		$this->cache = unserialize(file_get_contents($config['main']['cache_filename'])); 
		} else {
			$this->buildCleanCache();
		}
	}
	function __destruct() {
		//print_r($this->cache);
		if ($this->isDirty) {
			if (!file_put_contents($this->config['main']['cache_filename'], serialize($this->cache))) {
				throw new Exception("Could not write to file '".$this->config['cache_filename']."' at Resolver::destruct");
			}
		}
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
		throw new Exception("File '$filename' does not exist");
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
					if ($value['strip'] === 1) {
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
	function addFile($hash, $path) {
		//echo 'Adding $hash\n';
		$this->cache[$hash] = $path;
		$this->isDirty  =TRUE;
	}
	function removeFile($hash) {
		//echo 'Removing $hash\n';
		unset($this->cache[$hash]);
		$this->isDirty = TRUE;
	}
	function resolve($request) /* canthrow */ {
		$path = '';
		if (array_key_exists($request, $this->cache)) {
			if ($path = $this->cache[$request]) {
				if (!file_exists($path)) {
					 $this->removeFile($request);
					 throw new Exception("File '$request' does not exist on FS");
				}
				return $path;
			}
		}
		if ($this->searchForFile($request)) {
			return $this->cache[$request];
		}
		return $path;
	}
}

$resolver = new Resolver($config);
try {
	print($resolver->resolve("jar70sccp.9-4-2ES26.sbn")."\n");
	print($resolver->resolve("Russian_Russian_Federation/be-sccp.jar")."\n");
	print($resolver->resolve("Spain/g3-tones.xml")."\n");
	print($resolver->resolve("320x196x4/Chan-SCCP-b.png")."\n");
} catch (Exception $e) {
	print($e . "\n");
}
try {
	print($resolver->resolve("XMLDefault.cnf.xml")."\n");
} catch (Exception $e) {
	print($e . "\n");
}

unset($resolver);
#unlink($CACHEFILE_NAME);
?>
