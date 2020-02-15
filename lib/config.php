<?php
$base_path = !empty($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT'] . "/../"): realpath(getcwd()."/../");
$base_config = Array(
	'main' => Array(
		'debug' => 1,
		'default_language' => 'English_United_States',
	),
	'subdirs' => Array(
		'tftproot' => 'tftpboot',
		'firmware' => 'firmware',
		'settings' => 'settings',
		'wallpapers' => 'wallpapers',
		'ringtones' => 'ringtones',
		'locales' => 'locales',
		'countries' => 'countries',
		'languages' => 'languages',
	)
);
$tree_base = Array(
	'settings' => array('path' => 'tftproot', "strip" => 1),
	'wallpapers' => array('path' => 'tftproot', "strip" => 0),
	'ringtones' => array('path' => 'tftproot', "strip" => 1),
	'locales' => array('path' => 'tftproot', "strip" => 1),
	'firmware' => array('path' => 'tftproot', "strip" => 1),
	'languages' => array('path' => 'locales', "strip" => 0),
	'countries' => array('path' => 'locales', "strip" => 0),
	'default_language' => array('path' => 'locales', "strip" => 1),
);

# Merge config
$ini_array = parse_ini_file('../config.ini', TRUE, INI_SCANNER_TYPED);
if (!empty($ini_array)) {
	$config = array_merge($base_config, $ini_array);
}

# rewrite config['subdirs'] paths using tree_base data
# Not sure if this is a good way
foreach ($tree_base as $key => $value) {
	if (!empty($config['subdirs'][$key])) {
		if (substr($config['subdirs'][$key], 0, 1) !== "/") {
			$path = $config['subdirs'][$value['path']].'/'.$config['subdirs'][$key];
			$config['subdirs'][$key] = $path;
		}
	}
}
foreach ($tree_base as $key => $value) {
	if (!empty($config['subdirs'][$key])) {
		$config['subdirs'][$key] = array('path' => $config['subdirs'][$key], 'strip' => $value['strip']);
	}
}

$config['main']['base_path'] = $base_path;
print_r($config['main']['base_path']);
$config['main']['tftproot'] = (!empty($config['main']['tftproot'])) ? $base_path . "tftpboot" : '/tftpboot';

# Fixup debug
$print_debug = (!empty($config['main']['debug'])) ? $config['main']['debug'] : 'off';
$print_debug = ($print_debug == 1) ? 'on' : $print_debug;
?>
