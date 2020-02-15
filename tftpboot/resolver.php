#!/usr/bin/php
<?php
$CACHEFILE_NAME="/tmp/provision_sccp_resolver.cache";

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
	function __construct() {
		if(file_exists($GLOBALS["CACHEFILE_NAME"])) {
	 		$this->cache = unserialize(file_get_contents($GLOBALS["CACHEFILE_NAME"])); 
		} else {
			$this->buildCleanCache();
		}
	}
	function __destruct() {
		//print_r($this->cache);
		if ($this->isDirty) {
			if (!file_put_contents($GLOBALS["CACHEFILE_NAME"], serialize($this->cache))) {
				throw new Exception("Could not write to file ".$GLOBALS["CACHEFILE_NAME"]." at Resolver::destruct");
			}
		}
	}
	function searchForFile($filename) {
		foreach(["settings","wallpapers","ringtones","locales/countries","locales/languages"] as $subdir) {
			$path = "$subdir/$filename";
			if (file_exists($path)) {
				 $this-> addFile($filename, $path);
				 return $path;
			}
		}
		throw new Exception("File '$request' does not exist");
	}
	function buildCleanCache() {
		// Intelligently walk tree
		$currentdir=getcwd();
		//foreach(["firmware","ringtones","settings"] as $subdir) {
		foreach(["firmware","ringtones"] as $subdir) {
			$dir_iterator = new RecursiveDirectoryIterator("$currentdir/$subdir");
			$iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
			foreach ($iterator as $file) {
				if ($file->isFile()) {
					$this->addFile($file->getFileName(), $file->getPathname());
				}
			}
		}
		foreach(["locales/languages", "locales/countries", "wallpapers"] as $subdir) {
			$dir_iterator = new RecursiveDirectoryIterator("$currentdir/$subdir");
			$iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
			foreach ($iterator as $file) {
				if ($file->isFile()) {
					$path = basename(dirname($file->getPathname()));
					$this->addFile("$path/".$file->getFileName(), $file->getPathname());
				}
			}
		}
		$this->isDirty  = TRUE;
	}
	function addFile($hash, $path) {
		//echo "Rdding $hash\n";
		$this->cache[$hash] = $path;
		$this->isDirty  =TRUE;
	}
	function removeFile($hash) {
		//echo "Removing $hash\n";
		unset($this->cache[$hash]);
		$this->isDirty = TRUE;
	}
	function resolve($request) /* canthrow */ {
		$path = "";
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

$resolver = new Resolver();
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
