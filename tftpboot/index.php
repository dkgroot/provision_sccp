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
$fw_suffix = array('bin', 'loads', 'sbn', 'sb2', 'sbin', 'zz', 'zup');

$path['settings'] = $path['tftp']. '/settings';
//$settings_suffix = array('cnf.xml');

$path['wallpapers'] = $path['tftp']. '/wallpapers';

$path['ringtones'] = $path['tftp']. '/ringtones';
$ringtones_list = array('distinctive.xml', 'distinctive.sgn', 'ringlist.xml', 'ringlist.sgn');

$path['locales'] = $path['tftp']. '/locales';
$path['countries'] = $path['tftp']. '/locales/countries';
$path['languages'] = $path['tftp']. '/locales/languages';
$locale_list = array('-dictionary.', 'dictionary-ext.', '-dictionary.utf-8.', '-kate.xml', '-font.xml', '-tones.xml',
                     'be-sccp.jar', 'tc-sccp.jar', 'td-sccp.jar', 'ipc-sccp.jar', 'mk-sccp.jar', '_locale.loads', 'i-button-help.xml');


$req_file_full_path = '' ;

if (!empty($req_file)) {
    $signed = FALSE;
    $req_data_ar = explode('/', $req_file);
    $orig_req_file_name = end($req_data_ar);
    $req_file_name = $orig_req_file_name;
    $req_data_len = count($req_data_ar) - 1;
        
    if (file_exists($path['tftp'].$req_file_name))					// prevent "/../...//" browsing - (eliminate back door)
    {
        $req_file_full_path = $path['tftp'].$req_file_name; 
    } 
    else 
    { 
        $tmp_file = explode('.', $req_file_name);
        $tmp = end($tmp_file);
        if (strpos(".sgn;", '.'.strtolower($tmp).';') !== FALSE) {			// handle signed files
            $signed = TRUE;
            $req_file_name = basename($req_file_name, ".sgn");				// strip signed part
        }

        if (strpos_array($fw_suffix, $req_file_name, 'any') !== FALSE) {		// Firmware file was requested
            $firmware_list = find_all_files($path['firmware']);
            $pos2 = strpos_array($firmware_list, $req_file_name, 'any'); 		// case unsensitive
            if ($pos2 !== FALSE) { 							// Request Firmware 
                $req_file_full_path = $firmware_list[$pos2];
            }
            print_r('<br>Requested Firmware:'. $req_file_full_path. '<br>');
        }
        else 
        {
            $tmp_file = '';

            //if (strpos_array($settings_suffix, $req_file_name, 'any') !==  FALSE) { 	// Request Settings
            if (strpos(strtolower($req_file_name), '.cnf.xml') !==  FALSE) {		// Request Settings
                $tmp_file = $path['settings'].'/'.$req_file_name;
            }
            else if (strpos(strtolower($req_file), '/desktops/') !== FALSE) { 		// Request Wallpapers
                $tmp_file = $path['wallpapers'].'/'. $req_data_ar[$req_data_len-1].'/'. $req_data_ar[$req_data_len];
            }
            else if (strpos_array($ringtones_list, $req_file_name, 'any') !== FALSE) {	// Request RingTones
                $tmp_file = $path['ringtones'].'/ringlist.xml';
            } 
            
/*
            else if (strpos(strtolower($req_file), '-tones.xml') !== FALSE) { 		// Request Countries
                $tmp_file = $path['countries'].'/'. $req_data_ar[$req_data_len-1].'/'. $req_data_ar[$req_data_len];
            }
 
            else if (strpos(strtolower($req_file), '-dictionary.') !== FALSE) { 		// Request Countries
                $tmp_file = $path['languages'].'/'. $req_data_ar[$req_data_len-1].'/'. $req_data_ar[$req_data_len];
            }

            else if (strpos_array($req_file, $locale_list, 'any') !== FALSE) { 		// Request Languages
                $tmp_file = $path['languages'].'/'. $req_data_ar[$req_data_len-1].'/'. $req_data_ar[$req_data_len];
            }

            else if (strpos(strtolower($req_file), '-dictionary.jar') !== FALSE) { 	// Request Countries
                $tmp_file = $path['languages'].'/'. $req_data_ar[$req_data_len-1].'/'. $req_data_ar[$req_data_len];
            }
*/
            else if (strpos_array($req_file, $locale_list, 'any') !== FALSE) { 		// Request Languages
                $tmp_file = $path['languages'].'/'. $req_data_ar[$req_data_len-1].'/'. $req_data_ar[$req_data_len];
            }
            
            if (empty($tmp_file)) { 
                die('ERROR: no match found.');
            }
            $req_file_full_path = $tmp_file;
        }
    }
    if (!empty($req_file_full_path)) { 
        if ($signed) {
            $req_file_full_path = $req_file_full_path . '.sgn';
        }
        if (!file_exists($req_file_full_path)) { 
            die('Could not find:'. $req_file_full_path);
        }
        print_r('<br>Returning:'. $req_file_full_path. '<br>');
        file_force_download($req_file_full_path);
    }
}

/*
 * Helper functiosn 
 */
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
            foreach ($haystack as $key => $substr) {
                $pos = strpos(strtolower($substr), strtolower($needles));
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
            $filter = FALSE;
            if (!empty($file_mask)) {
                if (is_array($file_mask)) {
                    foreach ($file_mask as $k){
                        if (strpos(strtolower($value), strtolower($k)) !== FALSE) {$filter = true;}
                    }
                } else {
                    if (strpos(strtolower($value), strtolower($file_mask)) !== FALSE) {$filter = true;}
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
