#!/usr/bin/env php
<?php
require_once("lib/tftp.php");
require_once("lib/config.php");
require_once("lib/resolver.php");

class TFTPProvisioner extends TFTPServer
{
  private $_debug;
  private $_resolver;
  private $_filename;

  function __construct($server_url, $config, $logger = NULL, $debug = false)
  {
    $this->_config = $config;
    if (!$logger) {
      $logger = new Logger_NULL('LOG_ERROR');
    }
    parent::__construct($server_url, $logger);
    $this->_debug = $debug;
    $this->max_put_size = 60000000;
    $this->_resolver = new Resolver($config);
  }

  public function exists($peer, $req_filename)
  {
    if (($this->_filename = $this->_resolver->resolve($req_filename))) {
      return file_exists($this->_filename);
    }
    return false;
  }

  public function readable($peer, $req_filename)
  {
    return is_readable($this->_filename);
  }

  public function get($peer, $req_filename, $mode)
  {
    return file_get_contents($this->_filename);
  }

  public function writable($peer, $req_filename)
  {
    // check $req_filename starts with 'settings/' (SPA phones can write to tftpboot)
    $settings_path = $this->_config['main']['base_path'] . DIRECTORY_SEPARATOR
                . $this->_config['subdirs']['settings']['path'] . DIRECTORY_SEPARATOR;
    $filename = $settings_path . basename($req_filename);
    if (is_writable($filename) || (!file_exists($filename) && is_writable($settings_path))) {
      $this->_filename = $filename;
      return true;
    }
    return false;
  }

  public function put($peer, $filename, $mode, $content)
  {
    return file_put_contents($this->_filename, $content);
  }

  /*
   * STDOUT Log functions
   */
  private function log($peer, $level, $message)
  {
    echo(date("H:i:s") . " $level $peer $message\n");
  }

  public function log_debug($peer, $message)
  {
    if($this->_debug)
      $this->log($peer, "D", $message);
  }

  public function log_info($peer, $message)
  {
    $this->log($peer, "I", $message);
  }

  public function log_warning($peer, $message)
  {
    $this->log($peer, "W", $message);
  }

  public function log_error($peer, $message)
  {
    $this->log($peer, "E", $message);
  }

}

$host = "127.0.0.1";
$port = 10069;
$url = "udp://$host:$port";

echo "\nStarting TFTP Provisioner...\n";
$server = new TFTPProvisioner($url, $config, $logger);
if(!$server->loop($error))
  die("$error\n");
?>

