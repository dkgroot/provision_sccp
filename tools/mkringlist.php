<?php

//Usage: php mkringlist.php;

function createCats($currDirPos){

   $xml = '';

   if(is_dir($currDirPos)){
       // Remove unix ./..
       foreach (array_diff(scandir($currDirPos),array('.','..')) as $subDir) {
           //remove link information
           $subDir = explode(' ', $subDir);
						$displayName = explode('.',$subDir[0]);
           $fileName = str_replace('../tftpboot/','',$currDirPos) .'/'. $subDir[0];
           if (in_array($displayName[1], array('pcm','raw'))) {
   						$xml .= "<Ring><DisplayName>{$displayName[0]}</DisplayName><FileName>{$fileName}</FileName></Ring>";
           }
       }
   }
   return $xml;
}

function saveXml($xml, $filename) {
   $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.'<CiscoIPPhoneRingList>' . $xml . '</CiscoIPPhoneRingList>';
   $dom = new \DOMDocument("1.0");
   $dom->preserveWhiteSpace = false;
   $dom->formatOutput = true;
   $dom->loadXML($xml);
   $dom->save($filename);
}

saveXml(createCats('../tftpboot/ringtones'),'../tftpboot/ringtones/ringlist.xml');
?>
