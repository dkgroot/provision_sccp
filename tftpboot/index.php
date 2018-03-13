<?php
//
// Written by Alex / github.com/PhantomVI
//

// Setup should be moved to ini/json file

// --  tftpboot  path 
$path['tftp'] = '/tftpboot';

$request = $_REQUEST;
$req_file = !empty($request['id']) ? $request['id'] : '';

// --  TFTPD - structure 
$path['firmware'] = $path['tftp']. '/firmware';
$path['settings'] = $path['tftp']. '/settings';
$path['wallpapers'] = $path['tftp']. '/wallpapers';
$path['ringtones'] = $path['tftp']. '/ringtones';
$path['countries'] = $path['tftp']. '/locales/countries';
$path['languages'] = $path['tftp']. '/locales/languages';

$fsufix = ".bin;.bin;.loads;.LOADS;.sbn;.SBN;.sb2;.sbin;.zz;.zup;.sgn;.SGN";
$rings_list = array('distinctive.xml', 'distinctive.sgn', 'ringlist.xml', 'ringlist.sgn');
$locale_list = array('-dictionary.', 'dictionary-ext.', '-dictionary.utf-8.', '-kate.xml', '-font.xml', '-tones.xml',
                     'be-sccp.jar', 'tc-sccp.jar', 'td-sccp.jar', 'ipc-sccp.jar', 'mk-sccp.jar', '_locale.loads', 'i-button-help.xml');

function file_force_download($file) {
  if (file_exists($file)) {

    if (ob_get_level()) {
      ob_end_clean();
    }

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));

    if ($fd = fopen($file, 'rb')) {
      while (!feof($fd)) {
        print fread($fd, 1024);
      }
      fclose($fd);
    }
    exit;
  }
}

function strpos_array($haystack, $needles, $mode='any') {
    if (is_array($needles)) {    // Handle multiple needles via recursive call
        foreach ($needles as $str) {
            $pos = strpos_array($haystack, $str, $mode);
            if ($pos !== FALSE) {
                return $pos;
            }
        }
    } else {
        if (is_array($haystack) && ($mode == 'any')) {
            foreach ($haystack as $key => $subtr) {
                $pos = strpos(strtolower($subtr), strtolower($needles));
                if ($pos !== FALSE) {
                    return $key;
                }
            }
            return FALSE;
        } else {
            return strpos($haystack, $needles);
        }
    }
    return FALSE;
}

function find_all_files($dir, $file_mask=null, $mode='full'){

    $result = NULL;
    if (empty($dir) || (!file_exists($dir))) {
        return $result;
    }

    $root = scandir($dir);
    foreach($root as $value) {
        if($value === '.' || $value === '..') {continue;}
        if(is_file("$dir/$value")) {
            $filter = false;
            if (!empty($file_mask)) {
                if (is_array($file_mask)) {
                    foreach ($file_mask as $k){
                        if (strpos(strtolower($value), strtolower($k)) !== false) {$filter = true;}
                    }
                } else {
                    if (strpos(strtolower($value), strtolower($file_mask)) !== false) {$filter = true;}
                }
              } else {$filter = true;}
            if ($filter) {
                if ($mode=='fileonly'){
                    $result[]="$value";
                } else {
                    $result[]="$dir/$value";
                }
            } else {$result[]=null;}
            continue;
        }
        $sub_fiend = find_all_files("$dir/$value", $file_mask, $mode);
        if (!empty($sub_fiend)) {
            foreach($sub_fiend as $sub_value) {
                if (!empty($sub_value)) {
                    $result[]=$sub_value;
                }
            }
        }
    }
    return $result;
} 

$req_file_full_path = '' ;
$firmware_list = find_all_files($path['firmware']);

if (!empty($req_file)) {
    $req_data_ar = explode('/', $req_file);
    $req_file_name = end($req_data_ar);
    $req_data_len = count($req_data_ar) - 1;
    if (file_exists($path['tftp'].$req_file_name)) { // file exist $req_file_name need remove "/../...//" -back door
        $req_file_full_path = $path['tftp'].$req_file_name; 
    } else { 
        $tmp_file = explode('.', $req_file_name);
        $tmp = end($tmp_file);
        $pos = strpos($fsufix, '.'.$tmp.';');
        if ($pos !== false) { // Request Firmware 
            $pos2 = strpos_array($firmware_list, $req_file_name, 'any'); // case unsensitive
            if ($pos2 !== false) { // Request Firmware 
                $req_file_full_path = $firmware_list[$pos2];
            }
            print_r('<br>Firmware : '. $req_file_full_path. 'END Firmware<br>');
        } 
        if (empty($req_file_full_path)) {
            if (strpos(implode(';', $rings_list), strtolower($req_file_name)) !== FALSE) { // Request ring list
                $req_file_full_path = $path['ringtones'].'/ringlist.xml';  // hard link
            }
            $tmp_file = '';

            if (strpos(strtolower($req_file_name), '.cnf.xml') !==  FALSE) { // Request Settings
                $tmp_file =$path['settings'].'/'.$req_file_name;
            }
            
/*
            if (strpos(strtolower($req_file), '-tones.xml') !== FALSE) { // Request countries
                $tmp_file = $path['countries'].'/'. $req_data_ar[$req_data_len-1].'/'. $req_data_ar[$req_data_len];
            }
 
            if (strpos(strtolower($req_file), '-dictionary.') !== FALSE) { // Request countries
                $tmp_file = $path['languages'].'/'. $req_data_ar[$req_data_len-1].'/'. $req_data_ar[$req_data_len];
            }

            if (strpos_array($req_file, $locale_list, 'any') !== FALSE) { 
                $tmp_file = $path['languages'].'/'. $req_data_ar[$req_data_len-1].'/'. $req_data_ar[$req_data_len];
            }

            if (strpos(strtolower($req_file), '-dictionary.jar') !== FALSE) { // Request countries
                $tmp_file = $path['languages'].'/'. $req_data_ar[$req_data_len-1].'/'. $req_data_ar[$req_data_len];
            }
 * 
 */
            if (strpos_array($req_file, $locale_list, 'any') !== FALSE) { 
                $tmp_file = $path['languages'].'/'. $req_data_ar[$req_data_len-1].'/'. $req_data_ar[$req_data_len];
            }
            if (strpos(strtolower($req_file), '/desktops/') !== FALSE) { // Request wallpapers
                $tmp_file = $path['wallpapers'].'/'. $req_data_ar[$req_data_len-1].'/'. $req_data_ar[$req_data_len];
            }
            
            if (!empty($tmp_file)) { 
                if (file_exists($tmp_file)) { 
                    $req_file_full_path = $tmp_file;
                }
            }
            
        }
    }
    if (!empty($req_file_full_path)) { 
        print_r('<br>Send: '. $req_file_full_path. ' file.<br>');
        file_force_download($req_file_full_path);
    }
}
