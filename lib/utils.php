<?php
//
// Helper functions
//
declare(strict_types=1);
namespace SCCP\Utils;

function utf8_urldecode($str) {
	$str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($str));
	return html_entity_decode($str,0,'UTF-8');;
}

function log_debug($message) {
	global $logger;
	$logger->log('LOG_DEBUG', $message);
}

function log_error($message) {
	global $logger;
	$logger->log('LOG_ERROR', $message);
}

function log_error_and_throw($message) {
	log_error($message);
	throw new Exception($message);
}
?>
