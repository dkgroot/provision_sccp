<?php
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
 */
declare(strict_types=1);
namespace PROVISION\TFTP;

use PROVISION\Logger as Logger;

abstract class Transfer {
  public $state;
  public $peer;
  public $retransmit_timeout;
  public $block_size;
  public $tsize;
  protected $_server; // TFTPServer reference

  function __construct($server, $peer, $extensions)
  {
    $this->state = TFTPTransferState::READY;
    $this->peer = $peer;
    $this->retransmit_timeout = $server->retransmit_timeout;
    $this->block_size = $server->block_size;
    $this->tsize = 0;
    $this->_server = $server;

    if(isset($extensions["timeout"])) {
      $timeout = (int)$extensions["timeout"];
      if($timeout > 0 && $timeout < 256)
	$this->retransmit_timeout = $timeout;
    }

    if(isset($extensions["blksize"])) {
      $blksize = (int)$extensions["blksize"];
      if($blksize > 0 && $blksize <= $server->max_block_size)
	$this->block_size = $blksize;
    }

    // tsize is only checked for in write transfers
  }

  protected function log_debug($message)
  {
    $this->_server->log_debug($this->peer, $message);
  }

  protected function log_info($message)
  {
    $this->_server->log_info($this->peer, $message);
  }

  protected function log_warning($message)
  {
    $this->_server->log_warning($this->peer, $message);
  }

  protected function log_error($message)
  {
    $this->_server->log_error($this->peer, $message);
  }

  protected function terminal_info($error, $message)
  {
    $this->log_info($message);
    $this->state = TFTPTransferState::TERMINATING;
    return TFTPServer::packet_error($error, $message);
  }

  protected function terminal_error($op, $error, $message)
  {
    $this->log_debug("$op: $message");
    $this->state = TFTPTransferState::TERMINATING;
    return TFTPServer::packet_error($error, $message);
  }

  protected function illegal_operation($op, $message = "Illegal operation")
  {
    return $this->terminal_error($op, TFTPError::ILLEGAL_OPERATION, $message);
  }

  public function rrq($filename, $mode)
  {
    return $this->illegal_operation("RRQ");
  }

  public function wrq($filename, $mode)
  {
    return $this->illegal_operation("WRQ");
  }

  public function data($block, $data)
  {
    return $this->illegal_operation("DATA");
  }

  public function ack($block)
  {
    return $this->illegal_operation("ACK");
  }

  public function error($error, $message)
  {
    $this->log_debug("ERROR: $error: $message");
    $this->state = TFTPTransferState::TERMINATING;
  }

  protected function use_extensions() {
    return
      $this->retransmit_timeout != $this->_server->retransmit_timeout ||
      $this->block_size != $this->_server->block_size ||
      $this->tsize != 0;
  }

  protected function packet_oack() {
    $options = array();

    if($this->retransmit_timeout != $this->_server->retransmit_timeout)
      $options["timeout"] = (string)$this->retransmit_timeout;

    if($this->block_size != $this->_server->block_size)
      $options["blksize"] = (string)$this->block_size;

    if($this->tsize != 0)
      $options["tsize"] = (string)$this->tsize;

    return TFTPServer::packet_oack($options);
  }
}
?>
