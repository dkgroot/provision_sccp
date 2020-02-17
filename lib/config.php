<?php
include_once("logger.php");
include_once("utils.php");

$base_path = !empty($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT'] . "/../"): realpath(getcwd()."/../");
$base_config = Array(
	'main' => Array(
		'debug' => TRUE,
		'default_language' => 'English_United_States',
		'log_type' => "NULL",
		'log_level' => 'LOG_EMERG'
	),
	'subdirs' => Array(
		'tftproot' => 'tftpboot',
		'firmware' => 'firmware',
		'settings' => 'settings',
		'wallpapers' => 'wallpapers',
		'ringtones' => 'ringtones',
		'locales' => 'locales',
		'countries' => 'countries',
		'languages' => 'languages'
	),
	'security' => Array(
        	'cert_ca' => NULL,
        	'cert_priv' => NULL,
        	'cert_pub' => NULL,
        	'hash' => NULL
        ),
	'settings' => Array(
		'sshUserId' => 'cisco',
		'sshPassword' => 'cisco',
		'ipAddress' => '0',
		'datetime' => Array(
			'template' => 'M/D/YA',
			'timezone' => 'W. Europe Standard/Daylight Time',
			'ipaddress' => '10.x.x.x',
			'mode' => 'Unicast'
		),
		'members' => Array(
			'myhost' => Array(
				'hostname' => 'myhost.domain.com',
				'ipv4' => '10.x.x.x',
				'ipv6' => '2001:470::x:x',
				'port' => '2000'
			)
		),
		'locale' => Array(
			'country' => 'United_States',
			'language' => 'English_United_States',
			'langcode' => 'en_US',
			'charset' => 'utf-8'
		),
		'urls' => Array(
			'security' => FALSE,
			'information' => NULL,
			'authentication' => NULL,
			'services' => NULL,
			'direcory' => NULL,
			'messages' => NULL,
			'proxyserver' => NULL
		)
	)
);
$tree_base = Array(
	'settings' => array('path' => 'tftproot', "strip" => TRUE),
	'wallpapers' => array('path' => 'tftproot', "strip" => FALSE),
	'ringtones' => array('path' => 'tftproot', "strip" => TRUE),
	'locales' => array('path' => 'tftproot', "strip" => TRUE),
	'firmware' => array('path' => 'tftproot', "strip" => TRUE),
	'languages' => array('path' => 'locales', "strip" => FALSE),
	'countries' => array('path' => 'locales', "strip" => FALSE),
	'default_language' => array('path' => 'locales', "strip" => TRUE),
);

# Merge config
//$ini_array = parse_ini_file("$base_path/config.ini", TRUE, INI_SCANNER_TYPED);
$ini_array = parse_ini_file_multi("$base_path/config.ini", TRUE, INI_SCANNER_TYPED);
if (!empty($ini_array)) {
	$config = array_merge($base_config, $ini_array);
}

# build new config['subdirs'] paths substituting bases from tree_base
foreach ($tree_base as $key => $value) {
	$tmp = $config;
	if (!empty($tmp['subdirs'][$key])) {
		if (substr($tmp['subdirs'][$key], 0, 1) !== "/") {
			if (is_array($tmp['subdirs'][$value['path']])) {
				$path = $tmp['subdirs'][$value['path']]['path'].'/'.$tmp['subdirs'][$key];
			} else {
				$path = $tmp['subdirs'][$value['path']].'/'.$tmp['subdirs'][$key];
			}
		}
		$config['subdirs'][$key] = array('path' => $path, 'strip' => $value['strip']);
	}
}

$config['main']['base_path'] = $base_path;
$config['main']['tftproot'] = (!empty($config['main']['tftproot'])) ? $base_path . "tftpboot" : '/tftpboot';

switch($config['main']['log_type']) {
	case 'SYSLOG':
		$logger = new Logger_Syslog($config['main']['log_level']);
		break;
	case 'FILE':
		if (!isempty($config['main']['log_file'])) {
			$logger = new Logger_Filename($config['main']['log_level'], $config['main']['log_file']);
		}
		break;
	case 'STDOUT':
		$logger = new Logger_Stdout($config['main']['log_level']);
		break;
	case 'STDERR':
		$logger = new Logger_Stderr($config['main']['log_level']);
		break;
	default:
		$logger = new Logger_Null($config['main']['log_level']);
}

# Fixup debug
$print_debug = (!empty($config['main']['debug'])) ? $config['main']['debug'] : 'off';
$print_debug = ($print_debug == 1) ? 'on' : $print_debug;

if(defined('STDIN') ) {
	print_r($config);
	//var_dump($config);
}

?>
