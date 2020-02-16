<?php
include_once("../lib/config.php");
include_once("../lib/resolver.php");
$request = $_REQUEST ?? null;

function send_fallback_html($message) {
	while (ob_get_level()) {ob_end_clean();}
	if (ob_get_length() === false) {
		ob_start();
		header('Content-Description: README');
		header('Content-Type: text/html');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
	}
	$content="
		<html>
		<header>
		</header>
		<body>
			<h1>provision_sccp</h1>
			<p>Request:" . json_encode($request) . "</p>
			<p>Message:" . $message . "</p>
		</body>
		</html>
	";
	print ($content);
	ob_flush();
	flush();
}

function sendfile($file) {
	if (file_exists($file)) {
		while (ob_get_level()) {ob_end_clean();}
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));

		/* want to stream out, so don't use file_get_contents() in this case */
		if ($fd = fopen($file, 'rb')) {
			while (!feof($fd)) {
				print fread($fd, 1024);
			}
			fclose($fd);
		}
	}
}
if (!$request || empty($request) || !array_key_exists('filename',$request) || empty($request['filename'])) {
	send_fallback_html("Empty request sent");
	exit();
}
try {
	$req_filename=$request['filename'];
	$resolver = new Resolver($config);
	if (($filename = $resolver->resolve($req_filename))) {
		sendfile($filename);
	}
	unset($resolver);
} catch(Exception $e) {
	send_fallback_html($e->getMessage());
}
?>