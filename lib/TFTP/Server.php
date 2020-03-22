<?php
// origin: https://github.com/tm1000/tftpserver
/*
 * PHP TFTP Server
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
 *
 * Extend TFTPServer class and then call loop method with UDP URL.
 * Possible methods to override:
 * exists($peer, $filename)
 *   Check if file exist, default always true.
 * readable($peer, $filename)
 *   Check if file is readable, default always true.
 * get($peer, $filename, $mode)
 *   Return content of file, default always false.
 *   Only called if both exists and readable returns true.
 * writable($peer, $filename)
 *   Check if file is writable, default always false.
 * put($peer, $filename, $mode, $content)
 *   Write content to file.
 *   Only falled if both exists and writable returns true.
 *
 * $peer is $ip:$port, source ip and port of client
 * $filename is filename specified by client
 * $mode is probably "octet" or "netascii"
 * $content is file content
 *
 * The server support multiple concurrent read and writes, but the method calls
 * are serialized, so make sure to return quickly.
 *
 * TODO:
 * select must handle EINTR, how?
 * multiple recv per select?
 *
 */
declare(strict_types=1);
namespace PROVISION\TFTP;

use PROVISION\Logger as Logger;

class Server {
  public $block_size = 512;
  public $max_block_size = 65464; // max block size from rfc2348
  public $timeout = 10;
  public $retransmit_timeout = 1;
  public $max_put_size = 10485760; // 10 Mibi
  private $_socket_url;
  private $_socket;
  private $_transfers = array();
  private $_logger = NULL;

  function __construct($socket_url, $logger = NULL)
  {
    $this->_socket_url = $socket_url;
    $this->_logger = $logger;
  }

  public function exists($peer, $filename)
  {
    return true;
  }

  public function readable($peer, $filename)
  {
    return true;
  }

  public function get($peer, $filename, $mode)
  {
    return false;
  }

  public function writable($peer, $filename)
  {
    return false;
  }

  public function put($peer, $filename, $mode, $content)
  {
  }

  public function logger_log($priority, $message) {
    if($this->_logger === NULL)
      return;

    $this->_logger->log($priority, $message);
  }

  public function log_debug($peer, $message)
  {
    $this->logger_log(LOG_DEBUG, "$peer $message");
  }

  public function log_info($peer, $message)
  {
    $this->logger_log(LOG_INFO, "$peer $message");
  }

  public function log_warning($peer, $message)
  {
    $this->logger_log(LOG_WARNING, "$peer $message");
  }

  public function log_error($peer, $message)
  {
    $this->logger_log(LOG_ERR, "$peer $message");
  }

  public static function packet_ack($block)
  {
    return pack("nn", TFTPOpcode::ACK, $block);
  }

  public static function packet_data($block, $data)
  {
    return pack("nn", TFTPOpcode::DATA, $block) . $data;
  }

  public static function packet_error($code, $message = "")
  {
    return pack("nn", TFTPOpcode::ERROR, $code) . $message . "\0";
  }

  public static function packet_oack($options)
  {
    $data = "";
    foreach($options as $key => $value)
      $data .= "$key\0$value\0";
    return pack("n", TFTPOpcode::OACK) . $data;
  }

  public static function escape_string($str)
  {
    $b = "";
    $l = strlen($str);
    for($i = 0; $i < $l; $i++) {
      $c = $str[$i];
      if(ctype_print($c))
	$b .= $c;
      else
	$b .= sprintf("\\x%'02x", ord($c));
    }

    return $b;
  }

  public function loop(&$error = false, $user = null)
  {
    $this->_socket =
      stream_socket_server($this->_socket_url, $errno, $errstr,
			   STREAM_SERVER_BIND);
    if(!$this->_socket) {
      if($error !== false)
	$error = "$errno: $errstr";
      return false;
    }

    if($user != null) {
      posix_seteuid($user["uid"]);
      posix_setegid($user["gid"]);
    }

    stream_set_blocking($this->_socket, false);

    return $this->loop_ex();
  }

  private function loop_ex()
  {
    $now = $last = time();

    while(true) {
      $read = array($this->_socket);
      //$r = stream_select($read, $write = null, $excpt = null, 1);
      $write = null;
      $except = null;
      $r = stream_select($read, $write, $excpt, 1);

      if($r === false) {
	$this->log_error("server", "select returned false");
	continue;
      }

      if(count($read) > 0) {
	$packet = stream_socket_recvfrom($this->_socket,
					 65535, // max udp packet size
					 0, // no flags
					 $peer);
	// ipv6 hack, convert to [host]:port format
	if(strpos($peer, ".") === false) {
	  $portpos = strrpos($peer, ":");
	  $host = substr($peer, 0, $portpos);
	  $port = substr($peer, $portpos + 1);
	  $peer = "[$host]:$port";
	}
	$this->log_debug($peer, "request: " . strlen($packet). " bytes");
	$this->log_debug($peer, "request: " . 
			 TFTPServer::escape_string($packet));
	$reply = $this->request($peer, $packet);
	if($reply !== false) {
	  $this->log_debug($peer, "reply: " .
			   TFTPServer::escape_string($reply));
	  stream_socket_sendto($this->_socket, $reply, 0, $peer);
	}
      }

      $now = time();
      if($now != $last) {
	$last = $now;
	$this->retransmit($now);
      }
    }
  }

  private function retransmit($now)
  {
    foreach($this->_transfers as $peer => $transfer) {
      $reply = $transfer->retransmit($now);
      if($reply !== false) {
	$this->log_debug($peer, "resend: " .
			 TFTPServer::escape_string($reply));
	stream_socket_sendto($this->_socket, $reply, 0, $peer);
      }

      if($transfer->state == TFTPTransferState::TERMINATING)
	unset($this->_transfers[$peer]);
    }
  }

  private function request($peer, $packet)
  {
    if(strlen($packet) < 4) {
      $this->log_debug($peer, "request: short packet");
      return false;
    }

    $reply = false;
    $transfer = false;
    if(isset($this->_transfers[$peer])) {
      $this->log_debug($peer, "request: existing transfer");
      $transfer = $this->_transfers[$peer];
    }

    $fields = unpack("n", $packet);
    $op = $fields[1];
    $this->log_debug($peer, "request: opcode " .
		     TFTPOpcode::name($op) . " ($op)");
    switch($op) {
      case TFTPOpcode::WRQ:
      case TFTPOpcode::RRQ:
	$a = explode("\0", substr($packet, 2));
	if(count($a) < 3 || $a[count($a) - 1] != "") {
	  $this->log_warning($peer, "request: malformed " .
			     TFTPOpcode::name($op));
	  return false;
	}

	$rawexts = array_slice($a, 2, -1);

	// Cisco IP Phone 7941 (and possibly others) return an extra null
	// at the end; a breach of RFC rfc2347. This is a workaround.
	// If odd count strip last and continue if empty, else warn and ignore
	if(count($rawexts) % 2 != 0) {
	  if(array_pop($rawexts)!="") {
	    $this->log_warning($peer, "request: malformed extension " .
			       "key/value pairs " . TFTPOpcode::name($op));
	    return false;
	  }
	}

	$extensions = array();
	foreach(array_chunk($rawexts, 2) as $pair)
	  $extensions[strtolower($pair[0])] = $pair[1];

	if($transfer === false) {
	  if($op == TFTPOpcode::RRQ)
	    $transfer = new TFTPReadTransfer($this, $peer, $extensions);
	  else
	    $transfer = new TFTPWriteTransfer($this, $peer, $extensions);

	  $this->_transfers[$peer] = $transfer;
	}
	
	if($op == TFTPOpcode::RRQ)
	  $reply = $transfer->rrq($a[0], $a[1]);
	else
	  $reply = $transfer->wrq($a[0], $a[1]);

	break;
      case TFTPOpcode::ACK:
	if(strlen($packet) != 4) {
	  $this->log_warning($peer, "request: malformed ACK");
	  return false;
	}

	$a = unpack("n", substr($packet, 2));
	if($transfer === false) {
	  // do not warn, some clients like BSD tftp sends ack on read error
	  $this->log_debug($peer, "request: ack from unknwon peer");
	} else
	  $reply = $transfer->ack($a[1]);
	break;
      case TFTPOpcode::DATA:
	if(strlen($packet) < 4) {
	  $this->log_warning($peer, "request: malformed DATA");
	  return false;
	}
	
	$a = unpack("n", substr($packet, 2));
	$data = substr($packet, 4, strlen($packet) - 4);
	if($transfer === false) {
	  $this->log_warning($peer, "request: data from unknwon peer");
	  $reply = TFTPServer::packet_error(TFTPError::UNKNOWN_TID,
					    "Unknown TID for DATA");
	} else
	  $reply = $transfer->data($a[1], $data);
	break;
      case TFTPOpcode::ERROR:
	$a = unpack("n", substr($packet, 2, 2));
	$message = substr($packet, 4, strlen($packet) - 5);

	if($transfer === false)
	  $this->log_warning($peer, "request: error from unknwon peer, " .
			     "{$a[1]}:$message");
	else
	  $transfer->error($a[1], $message);
	break;
      default:
	break;
    }

    if($transfer !== false &&
       $transfer->state == TFTPTransferState::TERMINATING) {
      $this->log_debug($peer, "request: terminating");
      unset($this->_transfers[$transfer->peer]);
    }

    return $reply;
  }
}
?>
