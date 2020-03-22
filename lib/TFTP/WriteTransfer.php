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
use PROVISION\TFTP as TFTP;

class TFTPWriteTransfer extends TFTP\Transfer {
  private $_last_sent_ack;
  private $_last_recv_data;
  private $_buffer;
  private $_buffer_size;
  private $_next_block;
  private $_filename;
  private $_mode;

  function __construct($server, $peer, $extensions)
  {
    parent::__construct($server, $peer, $extensions);
    $this->_last_sent_ack = time();
    $this->_last_recv_data = $this->_last_sent_ack;
    $this->_buffer = array();
    $this->_buffer_size = 0;
    $this->_last_recv_block = 0;
    $this->_filename = false;
    $this->_mode = false;

    if(isset($extensions["tsize"]))
      $this->tsize = (int)$extensions["tsize"];

    $this->log_debug("new write transfer");
  }

  private function packet_ack_current()
  {
    $this->_last_sent_ack = time();

    if($this->_last_recv_block == 0 && $this->use_extensions())
      return $this->packet_oack();
    else
      return TFTPServer::packet_ack($this->_last_recv_block);
  }

  public function wrq($filename, $mode)
  {
    $this->log_debug("WRQ: filename $filename in $mode mode");

    if($this->state != TFTPTransferState::READY)
      return $this->illegal_operation("WRQ", "Not in ready state");

    if(!$this->_server->writable($this->peer, $filename))
      return $this->terminal_info(TFTPError::ACCESS_VIOLATION,
				  "File $filename is not writable");

    if($this->tsize != 0 && $this->tsize > $this->_server->max_put_size)
      return $this->terminal_info(TFTPError::DISK_FULL,
				  "File too big, " .
				  $this->tsize . "(tsize) > " .
				  $this->_server->max_put_size);

    $this->state = TFTPTransferState::RECEIVING;
    $this->_filename = $filename;
    $this->_mode = $mode;
    $this->_last_sent_ack = time();

    $this->log_debug("WRQ: ack request");
    if($this->use_extensions())
      return $this->packet_oack();
    else
      return TFTPServer::packet_ack(0);
  }

  public function data($block, $data)
  {
    if($this->state != TFTPTransferState::RECEIVING)
      return $this->illegal_operation("DATA", "Not in receiving state");

    $this->log_debug("DATA: block $block");
    $this->last_recv_data = time();

    if($block <= $this->_last_recv_block) {
      $this->log_debug("DATA: duplicate block $block");
      // just ignore it
      return false;
    }

    if($block != $this->_last_recv_block + 1)
      return $this->illegal_operation("DATA",
				      "Expected block " . 
				      ($this->_last_recv_block + 1) .
				      " got $block");

    $this->_last_recv_block = $block;
    $this->_last_recv_data = time();
    array_push($this->_buffer, $data);
    $this->_buffer_size += strlen($data);

    if($this->_buffer_size > $this->_server->max_put_size)
      return $this->terminal_info(TFTPError::DISK_FULL,
				  "File too big, " .
				  $this->_buffer_size . " > " .
				  $this->_server->max_put_size);

    if(strlen($data) < $this->block_size) {
      $this->log_debug("DATA: last, done");
      $this->state = TFTPTransferState::TERMINATING;
      $this->log_info("Writing {$this->_filename} " .
		      "({$this->_buffer_size} bytes)");
      $this->_server->put($this->peer, $this->_filename, $this->_mode,
			  implode("", $this->_buffer));
      return $this->packet_ack_current();
    }

    $this->log_debug("DATA: ack block $block");
    return $this->packet_ack_current();
  }

  public function retransmit($now)
  {
    if($now - $this->_last_recv_data > $this->_server->timeout) {
      $this->log_debug("retransmit: timeout");
      $this->state = TFTPTransferState::TERMINATING;
      return false;
    }

    if($now - $this->_last_sent_ack > $this->retransmit_timeout) {
      $this->log_debug("retransmit: reack block {$this->_last_recv_block}");
      return $this->packet_ack_current();
    }

    return false;
  }
}
?>
