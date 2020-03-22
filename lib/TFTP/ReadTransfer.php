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

class ReadTransfer extends TFTP\Transfer {
  private $_last_recv_ack;
  private $_last_sent_data;
  private $_buffer;
  private $_block;
  private $_last_block;

  function __construct($server, $peer, $extensions)
  {
    parent::__construct($server, $peer, $extensions);
    $this->_last_recv_ack = time();
    $this->_last_sent_data = $this->_last_recv_ack;
    $this->_buffer = false;
    $this->_block = 1;
    $this->_last_block = 1;

    $this->log_debug("new read transfer");
  }

  private function current_block()
  {
    return substr($this->_buffer,
		  ($this->_block - 1) * $this->block_size,
		  $this->block_size);
  }

  private function packet_data_current()
  {
    $this->_last_sent_data = time();

    if($this->state == TFTPTransferState::SENDING_WAIT_OACK)
      return $this->packet_oack();
    else
      return TFTPServer::packet_data($this->_block, $this->current_block());
  }

  public function rrq($filename, $mode)
  {
    $this->log_debug("RRQ: filename $filename in $mode mode");

    if($this->state != TFTPTransferState::READY)
      return $this->illegal_operation("RRQ", "Not in ready state");

    if(!$this->_server->exists($this->peer, $filename))
      return $this->terminal_info(TFTPError::FILE_NOT_FOUND,
				  "File $filename does not exist");

    if(!$this->_server->readable($this->peer, $filename))
      return $this->terminal_info(TFTPError::ACCESS_VIOLATION,
				  "File $filename is not readable");

    $this->_buffer = $this->_server->get($this->peer, $filename, $mode);
    if($this->_buffer === false)
      return $this->terminal_info(TFTPError::FILE_NOT_FOUND,
				  "Failed to read $filename");

    $this->log_info("Reading $filename (" .
		    strlen($this->_buffer) . " bytes)");

    if($this->use_extensions())
      $this->state = TFTPTransferState::SENDING_WAIT_OACK;
    else
      $this->state = TFTPTransferState::SENDING;
    $this->_last_block = floor(strlen($this->_buffer) /
			       $this->block_size) + 1;

    $this->log_debug("RRQ: send first block or OACK");
    return $this->packet_data_current();
  }

  public function ack($block)
  {
    if($this->state == TFTPTransferState::SENDING_WAIT_OACK) {
      if($block != 0) {
	$this->log_debug("ACK: waiting OACK ACK got block $block");
	return false;
      }

      $this->state = TFTPTransferState::SENDING;
      $this->log_debug("ACK: got OACK ACK, send first block");
      return $this->packet_data_current();
    }

    if($this->state != TFTPTransferState::SENDING)
      return $this->illegal_operation("ACK", "Not in sending state");

    $this->log_debug("ACK: block $block");
    $this->_last_recv_ack = time();

    if($block < $this->_block) {
      $this->log_debug("ACK: duplicate block $block");
      // just ignore it
      return false;
    }

    if($block > $this->_last_block)
      return $this->illegal_operation("ACK",
				      "Block $block outside " .
				      "range 1-{$this->_last_block}");

    if($block == $this->_last_block) {
      $this->log_debug("ACK: last block, done");
      $this->state = TFTPTransferState::TERMINATING;
      return false;
    }

    // move to next block
    $this->_block = $block + 1;

    $this->log_debug("ACK: sending block {$this->_block}");
    return $this->packet_data_current();
  }

  public function retransmit($now)
  {
    if($now - $this->_last_recv_ack > $this->_server->timeout) {
      $this->log_debug("retransmit: timeout");
      $this->state = TFTPTransferState::TERMINATING;
      return false;
    }

    if($now - $this->_last_sent_data > $this->retransmit_timeout) {
      $this->log_debug("retransmit: resending block {$this->_block} or OACK");
      return $this->packet_data_current();
    }

    return false;
  }
}
?>
