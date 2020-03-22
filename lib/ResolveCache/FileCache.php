<?php
namespace PROVISION\ResolveCache;

use PROVISION\Utils as Utils;

class FileCache extends ResolveCache {
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
				Utils::log_error_and_throw("Could not write to file '".$this->_cache_file."' at Resolver::destruct");
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
			Utils::log_error("Duplicate file:$filename");	/* should we prevent this ? */
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

?>