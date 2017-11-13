atapost.pl Version 2.0

atapost.pl is a PERL script tool that can be used to change
parameter/value pair(s) that representing ATA configuration
parameter(s) from DOS command or UNIX shell without using a
browser. In order to use this tool, ATA web configuration
should not be disabled (OpFlags parameter bit 7 should not
be set on ATA). The usage of the tool can be found in the
following usage messsage by invoking atapost.pl without any
argument.

NOTE: atapost.pl require PERL version 5 or later, on DOS command
	prompt, the perl command should be invoked ahead of
	atapost.pl. e.g. C> perl atapost.pl


  Usage:
  ------
  Program atapost.pl Version 2.0
  is to post parameter/value to one ATA186 via http protocol, given it's IP.

  Usage:
  ------
  1. atapost.pl [-h]elp
  2. atapost.pl <ipaddress> <-UIPassword=secret> <-field1=one> <-field2=two> <-...>
  3. atapost.pl <ipaddress> -xml <-UIPassword=secret>


  1. Get Help Information
	  -h: 		This help information

  2, Post parameter/value pairs
	  <ipaddress>:	it's in the form of X.Y.Z.W
	  <-field1=..>:	value of "-field1" to change

  Example 
  To change ata with ip=192.168.2.119 to use codec G711:

  unix-prompt%> atapost.pl 192.168.2.119 -txcodec=1 -rxcodec=1
  Establishing connection...
  Connected.
  Changing TxCodec to new value = 1
  Changing RxCodec to new value = 1
  done.

  NOTE: require version 2.0 or later firmware

  3. Get XML page

  Get XML formatted ATA configuration page.

  NOTE: require version 3.0 or later firmware

  NOTE: specify password via -UIPassword option if ATA is password protected.


