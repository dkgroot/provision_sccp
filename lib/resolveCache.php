<?php
namespace SCCP\ResolveCache;
use SCCP\Utils as Utils;

abstract class resolveCache {
	abstract protected function addFile($filename, $realpath);
	abstract protected function removeFile($filename);
	abstract protected function check($filename);
	abstract protected function getPath($filename);
}

class fileCache extends resolveCache {
	private $_isDirty = false;
	private $_cache = array();
	private $_cache_file = NULL;

	function __construct($filename) {
		$this->_cache_file = $filename;
		if (file_exists($this->_cache_file)) {
			$this->_cache = unserialize(file_get_contents($this->_cache_file));
			$this->_isDirty = false;
		} else {
			$this->_isDirty = true;
		}
	}

	function __destruct() {
		if ($this->_isDirty) {
			/*if (!is_writable($this->_cache_file)) {
				log_error_and_throw("Could not write to file '".$this->_cache_file."' at Resolver::destruct");
			}*/
			if (!file_put_contents($this->_cache_file, serialize($this->_cache))) {
				Utils\log_error_and_throw("Could not write to file '".$this->_cache_file."' at Resolver::destruct");
			}
		}
	}

	public function isDirty() {
		return $this->_isDirty;
	}

	public function check($filename) {
		return array_key_exists($filename, $this->_cache);
	}

	public function addFile($filename, $realpath) {
		if ($this->check($filename))
			Utils\log_error("Duplicate file:$filename");	/* should we prevent this ? */
		$this->_cache[$filename] = $realpath;
		$this->_isDirty  =true;
	}

	public function removeFile($filename) {
		if ($this->check($filename)) {
			unset($this->_cache[$filename]);
			$this->_isDirty = true;
		}
	}

	public function getPath($filename) {
		if ($this->check($filename)) {
			return $this->_cache[$filename];
		}
		return false;
	}

	protected function printCache() {
		print_r($this->_cache);
	}
}

/*
class sqliteCache extends resolveCache {
	function __construct() {
	}
	function __destruct() {
	}
	public function addFile($filename, $realpath);
	public function removeFile($filename);
	public function check($filename);
	public function getPath($filename);
}
*/
?>