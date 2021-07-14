<?php

//Usage: php createTftpbootXmlStructure.php;

function createCats($currDirPos){

    $xml = '';

    if(is_dir($currDirPos)){
        // Remove unix ./..
        foreach (array_diff(scandir($currDirPos),array('.','..')) as $subDir) {
            //remove link information
            $subDir = (explode(' ', $subDir));

            if(is_dir("{$currDirPos}/{$subDir[0]}")) {
                $xml .= "<Directory name = '{$subDir[0]}'><DirectoryPath>" . ltrim("{$currDirPos}/{$subDir[0]}",'./') . "/</DirectoryPath>";
                $xml .= createCats("{$currDirPos}/{$subDir[0]}");
                $xml .= "</Directory>";
            } else {
                $xml .= "<FileName>{$subDir[0]}</FileName>";
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
