#!/usr/bin/env php
<?php
/*
 * Functional test for TFTPServer
 *
 * Copyright (c) 2011 <mattias.wadman@gmail.com>
 *
 * MIT License:
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */
require_once("lib/tftp.php");

class TestTFTPServer extends TFTPServer
{
  private $_files = array();
  private $_debug;

  function __construct($server_url, $logger = NULL, $debug = false)
  {
    parent::__construct($server_url, $logger);
    $this->_debug = $debug;
    $this->max_put_size = 60000000;
  }

  private function log($peer, $level, $message)
  {
    echo
      date("H:i:s") . " " .
      $level . " " .
      $peer . " " .
      $message . "\n";
  }

  public function log_debug($peer, $message)
  {
    if(!$this->_debug)
      return;

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

  public function exists($peer, $filename)
  {
    if($filename == "not_writable" || $filename == "not_readable")
      return true;

    return isset($this->_files[$filename]);
  }

  public function readable($peer, $filename)
  {
    if($filename == "not_readable")
      return false;

    return isset($this->_files[$filename]);
  }

  public function get($peer, $filename, $mode)
  {
    if(isset($this->_files[$filename]))
      return $this->_files[$filename];
    else
      return false;
  }

  public function writable($peer, $filename)
  {
    if($filename == "not_writable")
      return false;

    return true;
  }

  public function put($peer, $filename, $mode, $content)
  {
    $this->_files[$filename] = $content;
  }
}

$host = "127.0.0.1";
$port = 1196;
$url = "udp://$host:$port";

if(count($_SERVER["argv"]) > 1) {
  $server = new TestTFTPServer($url, true);
  if(!$server->loop($error))
    die("$error\n");
} else {

  $pid = pcntl_fork();
  $logger = new Logger_Stdout('LOG_DEBUG');
  if($pid == 0) {
    $server = new TestTFTPServer($url, $logger);
    if(!$server->loop($error))
      die("$error\n");
    exit(0);
  }
  usleep(100000);
  // kill server
  posix_kill($pid, SIGINT);
}

?>

