<?php
namespace PROVISION\ResolveCache;

abstract class ResolveCache {
	abstract protected function addFile($filename, $realpath);
	abstract protected function removeFile($filename);
	abstract protected function check($filename);
	abstract protected function getPath($filename);
}

/*
class SqliteCache extends ResolveCache {
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