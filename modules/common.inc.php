<?php


/*

 Common functions

*/





function __unserialize($string) {
    $tmp=array();
    if($string == "") return $tmp;
    $unserialized = stripslashes($string);
    $unserialized = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $unserialized );
    $tmp=@unserialize($unserialized);
    return $tmp;
}



function leer_datos($nombre="", $type='plain'){
    if (isset($_POST[$nombre]) ){
        $txt=$_POST[$nombre];
    }
    elseif ( isset($_GET[$nombre]) ){
        $txt=$_GET[$nombre];
    }
    else{
        $txt="";
    }
    return sanitizeOne( $txt , $type );
}



function truncate($substring, $max = 50, $rep = '...') {
    if(strlen($substring) < 1){
       $string = $rep;
    }else{
       $string = $substring;
    }

    $leave = $max - strlen ($rep);

    if(strlen($string) > $max){
       return substr_replace($string, $rep, $leave);
    }else{
       return $string;
    }
}

function human_size($size,$dec=1){
    $size_names= array('Byte','KByte','MByte','GByte', 'TByte','PB','EB','ZB','YB','NB','DB');
    $name_id=0;
    while($size>=1024 && ($name_id<count($size_names)-1)){
        $size/=1024;
        $name_id++;
    }
    return round($size,$dec).' '.$size_names[$name_id];
}


function time_start() {
    global $starttime;
    $mtime = microtime();
    $mtime = explode(" ",$mtime);
    $mtime = $mtime[1] + $mtime[0];
    $starttime = $mtime;
}
 
function time_end() {
    global $starttime;
    $mtime = microtime();
    $mtime = explode(" ",$mtime);
    $mtime = $mtime[1] + $mtime[0];
    return ($mtime - $starttime);
}

function createPassword() {
    $chars = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-";
    $i = 0;
    $password = "";
    while ($i <= 10) {
        $password .= $chars{mt_rand(0,strlen($chars))};
        $i++;
    }
    return $password;
}

function readLDAPFile($fname, $varname) {
    $value='';
    $file_handle = fopen($fname, 'r');
    while (!feof($file_handle) ) {
        $line_of_text = fgets($file_handle);
        $parts = preg_split ("/\s+/", $line_of_text);
        if ( $parts[0] == $varname ) {
            $value=$parts[1];
        }
    }
    fclose($file_handle);
    return $value;
}


/*

    Sanitize class
    Copyright (C) 2007 CodeAssembly.com  

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see http://www.gnu.org/licenses/
*/
/**

 * Sanitize only one variable .
 * Returns the variable sanitized according to the desired type or true/false 
 * for certain data types if the variable does not correspond to the given data type.
 * 
 * NOTE: True/False is returned only for telephone, pin, id_card data types
 *
 * @param mixed The variable itself
 * @param string A string containing the desired variable type
 * @return The sanitized variable or true/false
 */



function sanitizeOne($var, $type) {
    switch ( $type ) {
        case 'int': // integer
        $var = (int) $var;
        break;

        case 'str': // trim string
        $var = trim ( $var );
        break;

        case 'nohtml': // trim string, no HTML allowed
        $var = htmlentities ( trim ( $var ), ENT_QUOTES );
        break;

        case 'plain': // trim string, no HTML allowed, plain text
        $var =  htmlentities ( trim ( $var ) , ENT_NOQUOTES )  ;
        break;

        case 'upper_word': // trim string, upper case words
        $var = ucwords ( strtolower ( trim ( $var ) ) );
        break;

        case 'ucfirst': // trim string, upper case first word
        $var = ucfirst ( strtolower ( trim ( $var ) ) );
        break;

        case 'lower': // trim string, lower case words
        $var = strtolower ( trim ( $var ) );
        break;

        case 'urle': // trim string, url encoded
        $var = urlencode ( trim ( $var ) );
        break;

        case 'trim_urle': // trim string, url decoded
        $var = urldecode ( trim ( $var ) );
        break;
        
        case 'role': // admin, tic, teacher or empty
            switch ( $var ) {
                case "admin":   $var='admin'; break;
                case "tic":     $var='tic'; break;
                case "teacher": $var='teacher'; break;
                default:        $var=''; break;
            }
        break;
        
        case 'charnum': // only chars, numbers and some special
        $var = preg_replace("/[^A-Za-z0-9.-_ áéíóúÁÉÍÓÚñÑüÜ]/","", $var); 
        break;
        
        case 'shell': // /bin/bash /bin/false
            switch ( $var ) {
                case "/bin/bash":   $var='/bin/bash'; break;
                default:            $var='/bin/false'; break;
            }
        break;
        
        case 'net': // xx.xx.xx.xx
        $var = preg_replace("/[^0-9.]/","", $var); 
        break;
        
        case 'mac': // xx:xx:xx:xx:xx:xx
        $var = preg_replace("/[^A-Za-z0-9:]/","", $var); 
        break;
    }
    return $var;
}





/**
 * Sanitize an array.
 * 
 * sanitize($_POST, array('id'=>'int', 'name' => 'str'));
 * sanitize($customArray, array('id'=>'int', 'name' => 'str'));
 *
 * @param array $data
 * @param array $whatToKeep
 */



function sanitize( &$data, $whatToKeep ) {
        $data = array_intersect_key( $data, $whatToKeep ); 
        
        foreach ($data as $key => $value) {
                $data[$key] = sanitizeOne( $data[$key] , $whatToKeep[$key] );
        }
}

/* see http://www.phpbuilder.com/columns/sanitize_inc_php.txt */


function clean_array( $data, $varname, $type='str') {
    $newdata=array();
    if ( ! isset($data[$varname]) ) {
        return $newdata;
    }
    if ( count($data[$varname]) < 1 ) {
        return $newdata;
    }
    foreach($data[$varname] as $k => $v) {
        $newdata[$k]=sanitizeOne($v, $type);
    }
    return $newdata;
}


function NTLMHash($Input) {
  // Convert the password from UTF8 to UTF16 (little endian)
  $Input=iconv('UTF-8','UTF-16LE',$Input);

  // Encrypt it with the MD4 hash
  $MD4Hash=bin2hex(mhash(MHASH_MD4,$Input));

  // You could use this instead, but mhash works on PHP 4 and 5 or above
  // The hash function only works on 5 or above
  //$MD4Hash=hash('md4',$Input);

  // Make it uppercase, not necessary, but it's common to do so with NTLM hashes
  $NTLMHash=strtoupper($MD4Hash);

  // Return the result
  return($NTLMHash);
}

function LMhash_DESencrypt($string)
{
    $key = array();
    $tmp = array();
    $len = strlen($string);

    for ($i=0; $i<7; ++$i)
        $tmp[] = $i < $len ? ord($string[$i]) : 0;

    $key[] = $tmp[0] & 254;
    $key[] = ($tmp[0] << 7) | ($tmp[1] >> 1);
    $key[] = ($tmp[1] << 6) | ($tmp[2] >> 2);
    $key[] = ($tmp[2] << 5) | ($tmp[3] >> 3);
    $key[] = ($tmp[3] << 4) | ($tmp[4] >> 4);
    $key[] = ($tmp[4] << 3) | ($tmp[5] >> 5);
    $key[] = ($tmp[5] << 2) | ($tmp[6] >> 6);
    $key[] = $tmp[6] << 1;
    
    $is = mcrypt_get_iv_size(MCRYPT_DES, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($is, MCRYPT_RAND);
    $key0 = "";
    
    foreach ($key as $k)
        $key0 .= chr($k);
    $crypt = mcrypt_encrypt(MCRYPT_DES, $key0, "KGS!@#$%", MCRYPT_MODE_ECB, $iv);

    return bin2hex($crypt);
}

function LMhash($string)
{
    $string = strtoupper(substr($string,0,14));

    $p1 = LMhash_DESencrypt(substr($string, 0, 7));
    $p2 = LMhash_DESencrypt(substr($string, 7, 7));

    return strtoupper($p1.$p2);
}



function ParseCMDArgs() {
    $args=array();
    foreach( $_SERVER['argv'] as $k => $v) {
        if ( $k < 1) {
            continue;
        }
        $item=preg_split("#=#i",$v);
        $args[$item[0]]=$item[1];
    }
    return $args;
}

function checkIP($ip) {
    $cIP = ip2long($ip);
    $fIP = long2ip($cIP);
    return $fIP;
}


function test_string($uid) {
    /* comprobar no espacios, empiece por letra y no caracteres raros */
    $re='/(^[A-Za-z])([A-Za-z0-9-._]+)$/';
    
    /* si cumple la anterior no tiene que tener caracteres no ASCII */
    $noascii='~[^\x00-\x7F]~u';
    if ( preg_match($re, $uid) ) {
        if ( preg_match($noascii, $uid)) {
            //echo "no ascii<br/>";
            return false;
        }
        //echo "ok<br/>";
        return true;
    }
    //echo "mal formato<br/>";
    return false;
}

function backharddi_installed() {
    /* return True/False if this file is found /boot/linux-backharddi-ng */
    return file_exists ( '/boot/linux-backharddi-ng' );
}

function remove_accent($str) {
  $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', '-', ' '); 
  $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', '_', '_'); 
  return str_replace($a, $b, $str); 
}

function parse_valid($txt) {
    $orig=$txt;
    
    $txt=remove_accent($txt);
    $txt=preg_replace('/\W-/', '', $txt);
    $txt=preg_replace('/"/', '', $txt);
    return $txt;
}

function removeFileIfExists($fname) {
    if ( file_exists ( $fname ) ) {
        unlink($fname);
    }
}


function date_diff2($start, $end="NOW")
{
        $sdate = strtotime($start);
        $edate = strtotime($end);

        $time = $edate - $sdate;
        if($time>=0 && $time<=59) {
                // Seconds
                $timeshift = $time.' segundos ';

        } elseif($time>=60 && $time<=3599) {
                // Minutes + Seconds
                $pmin = ($edate - $sdate) / 60;
                $premin = explode('.', $pmin);
                
                $presec = $pmin-$premin[0];
                $sec = $presec*60;
                
                $timeshift = $premin[0].' min '.round($sec,0).' seg ';

        } elseif($time>=3600 && $time<=86399) {
                // Hours + Minutes
                $phour = ($edate - $sdate) / 3600;
                $prehour = explode('.',$phour);
                
                $premin = $phour-$prehour[0];
                $min = explode('.',$premin*60);
                
                $presec = '0.'.$min[1];
                $sec = $presec*60;

                $timeshift = $prehour[0].' horas '.$min[0].' min '.round($sec,0).' seg ';

        } elseif($time>=86400) {
                // Days + Hours + Minutes
                $pday = ($edate - $sdate) / 86400;
                $preday = explode('.',$pday);

                $phour = $pday-$preday[0];
                $prehour = explode('.',$phour*24); 

                $premin = ($phour*24)-$prehour[0];
                $min = explode('.',$premin*60);
                
                $presec = '0.'.$min[1];
                $sec = $presec*60;
                
                $timeshift = $preday[0].' días '.$prehour[0].' horas '.$min[0].' min '.round($sec,0).' seg ';

        }
        return $timeshift;
}

?>
