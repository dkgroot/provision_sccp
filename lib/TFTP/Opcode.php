<?php
declare(strict_types=1);
namespace PROVISION;
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

class Opcode
{
  public static function name($v)
  {
    static $names = array(TFTPOpcode::RRQ => "RRQ",
			  TFTPOpcode::WRQ => "WRQ",
			  TFTPOpcode::DATA => "DATA",
			  TFTPOpcode::ACK => "ACK",
			  TFTPOpcode::ERROR => "ERROR",
			  TFTPOpcode::OACK => "OACK");
    if(isset($names[$v]))
      return $names[$v];
    else
      return "UNKNOWN";
  }

  const RRQ = 1; // read request
  const WRQ = 2; // write request
  const DATA = 3; // send data
  const ACK = 4; // ack data
  const ERROR = 5;
  const OACK = 6; // option ack, instead of first ACK/DATA
}
?>
