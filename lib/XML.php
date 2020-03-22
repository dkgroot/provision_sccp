<?php
declare(strict_types=1);
namespace PROVISION;
//use PROVISION\ConfigParser;

class XML {
	private $config;
	private $data;
	private $values;
	private $index;
	private $parser;
	function __construct($config) {
		$this->config = $config;
		$this->parser = xml_parser_create();
		//xml_error_string 
	}
	function __destruct() {
		xml_parser_free($this->parser);
	}
	function readStr($content) {
		xml_parse_into_struct($this->parser , $content , $this->values, $this->index);
	}
	function readFile($filename) {
		$content = file_get_contents($filename);
		$this->read($content);
	}
	function write() {
	}
	function addAttr($elem, $attr) {
	}
	function removeAttr($elem, $attr) {
	}
	function updateAttr($elem, $attr) {
	}
	function add($parent) {
	}
	function remove($parent) {
	}
	function update($parent) {
	}
}

// Testing
if(defined('STDIN') ) {
	$xml = new XML($config);
	$test_cases = Array(
	);
	foreach($test_cases as $test) {
		try {
			$xml->readStr("<device></device>");
		} catch (Exception $e) {
		}
	}
	unset($resolver);
}
?>
