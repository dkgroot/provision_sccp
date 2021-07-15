<?php

//Usage: php createTftpbootXmlStructure.php;

function createCats($currDirPos){

   $xml = '';

   if(is_dir($currDirPos)){
       // Remove unix ./..
       foreach (array_diff(scandir($currDirPos),array('.','..')) as $subDir) {
          if (is_dir("{$currDirPos}/{$subDir}")) {
             $thisPath = "{$currDirPos}/{$subDir}";
             if (is_link("{$currDirPos}/{$subDir}")) {
                 $thisPath= $currDirPos . "/" . readlink("{$currDirPos}/{$subDir}");
             }
             $xml .= "<Directory name = '{$subDir}'><DirectoryPath>" . ltrim($thisPath,'./') . "/</DirectoryPath>";
             $xml .= createCats("{$currDirPos}/{$subDir}");
               $xml .= "</Directory>";
           } else {
               $xml .= "<FileName>{$subDir}</FileName>";
           }
       }
   }
   return $xml;
}

function saveXml($xml, $filename) {
   $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.'<tftpboot>' . $xml . '</tftpboot>';
   $dom = new \DOMDocument("1.0");
   $dom->preserveWhiteSpace = false;
   $dom->formatOutput = true;
   $dom->loadXML($xml);
   $dom->save($filename);
}

saveXml(createCats('../tftpboot'),'tftpbootFiles.xml');
?>
