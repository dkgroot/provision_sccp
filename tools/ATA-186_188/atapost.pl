#!/usr/bin/env perl
#
# 05/02/01 msadikun - initial revision
# 11/25/03 budi - add -UIPassword to post to protected page.
#		  add -xml to get xml page
#

######################################################
#-How to use: ./atapost.pl -rxcodec=3 -txcodec=1
######################################################
use HTTP::Request::Common; 
use LWP::UserAgent;
&GetArg();
if ($xml) {
  &GetXML();
  }
else {
  &GetConfig();
  &PostConfig();
  }

######################################################
#			GetArg
######################################################
sub GetArg 
{
  my ($elem,$c1,$c2);

  &Usage() if ($#ARGV < 1); #-no argument
  $IPaddress = $ARGV[0];
  $URL = "http://$IPaddress/dev";
  $URLXML = "http://$IPaddress/dev.xml";

  for $elem (@ARGV) {
    ($c1,$c2) = split (/=/,$elem);
    &Usage() if (($c1 =~ "-help") || ($c1 =~ "-h\$")); 
    $c1 =~ s/-//; # remove -
    $c1 =~ tr/A-Z/a-z/; # to lowercase
    if ($c1 =~ "uipassword") {
      $UIPassword=$c2;
      }
    elsif ($c1 =~ "xml") {
      $xml=1;
      }
    else {
      $cmd_arg{$c1}=$c2 if ($c2); # if $c2 is not junk,
     }
   }
}

######################################################
#			Usage
######################################################
sub Usage
{
 print "
  Usage:
  ------
  Program $0 Version 2.0
  is to post parameter/value to one ATA186 via http protocol, given it's IP.

  Usage:
  ------
  1. $0 [-h]elp
  2. $0 <ipaddress> <-UIPassword=secret> <-field1=one> <-field2=two> <-...>
  3. $0 <ipaddress> -xml <-UIPassword=secret>


  1. Get Help Information
	  -h: 		This help information

  2, Post parameter/value pairs
	  <ipaddress>:	it's in the form of X.Y.Z.W
	  <-field1=..>:	value of \"-field1\" to change

  Example 
  To change ata with ip=192.168.2.119 to use codec G711:

  unix-prompt%> $0 192.168.2.119 -txcodec=1 -rxcodec=1
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
";
 exit 1;
}

######################################################
#			GetConfig
######################################################
sub GetConfig
{
  my ($elem,$c1,$junk,$req,$res);

  $ua=LWP::UserAgent->new;

  if ($UIPassword) {
    $req = (POST "$URL");
    $req->content_type ('application/x-www-form-urlencoded');
    #print "ChangeUIPasswd=$UIPassword&ChangeUIPasswd=&ChangeUIPasswd=&apply=apply";
    $req->content("ChangeUIPasswd=$UIPassword&ChangeUIPasswd=&ChangeUIPasswd=&apply=apply");
    }
  else {
    if ($IPaddress) {
      $req= (GET "$URL");
      }
    else {
      print "IP address unspecified"; exit 1;
      }
    }

  print "Establishing connection...\n";
  $res=$ua->request($req);
  if ($res->is_success) { print "Connected to $IPaddress.\n"; }
  else { print "ERROR: Problem retrieving data from $IPaddress\n"; exit 1; }

  @content=split (/name="/,$res->as_string);
  for $elem (@content) {
    if ($elem =~ /Password Protected/) {
      print "ERROR: Please specify valid password with -uipassword=secret\n";
      exit 1;
      }
    ($c1,$junk)=split (/">/,$elem); #-strip junks
    ($name,$value)=split (/" Value="/,$c1); #-only care about name & value field
    $lowercase=$name;
    ($lowercase) =~ tr/A-Z/a-z/; #-this will allow lowercase in cmd argument
    if ($cmd_arg{$lowercase}) {
      print "Changing $name, new value = $cmd_arg{$lowercase}...\n";
      $fields=sprintf ("%s%s",$fields,"$name=$cmd_arg{$lowercase}&");
      }
    else {
      #print "$name=$value\n";
      $fields=sprintf ("%s%s",$fields,"$name=$value&");
      }
    }

  chop ($fields); #-take out the last &
}

######################################################
#			PostConfig
######################################################
sub PostConfig
{
  my ($ua,$req,$res);
  $ua=LWP::UserAgent->new;

  if ($IPaddress) {
    $req= (POST "$URL");
    }
  else {
    print "don't know what ip address"; exit 1;
    }
  $req->content_type ('application/x-www-form-urlencoded');
  $req->content("$fields");	#-fields that I will post

  $res=$ua->request($req);

  if ($res->is_success) {
    #print $res->content;
    #print $res->as_string;	#-print to screen if debugging
    }
  else { 
    #print $res->error_as_HTML;
    print "ERROR: connection problem, perhaps box with $IPaddress is not ready?\n";
    }
  print "Done.\n";
}


######################################################
#			GetXML
######################################################
sub GetXML
{
  my ($elem,$c1,$junk,$req,$res);

  $ua=LWP::UserAgent->new;

  if ($UIPassword) {
    $req = (POST "$URLXML");
    $req->content_type ('application/x-www-form-urlencoded');
    print "post $URLXML\n";
    $req->content("ChangeUIPasswd=$UIPassword&ChangeUIPasswd=&ChangeUIPasswd=&apply=apply");
    }
  else {
    print "GET $URLXML\n";
    if ($IPaddress) {
      $req= (GET "$URLXML");
      }
    else {
      print "IP address unspecified"; exit 1;
      }
    }

  print "Establishing connection...\n";
  $res=$ua->request($req);
  if ($res->is_success) { print "Connected to $IPaddress.\n"; }
  else { print "ERROR: Problem retrieving data from $IPaddress\n"; exit 1; }

  if ($res->as_string =~ /Password Protected/) {
    print "ERROR: Please specify valid password with -uipassword=secret\n";
    exit 1;
    }
  else {
    print $res->as_string;
    }
}

