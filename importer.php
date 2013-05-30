<?php
/*  importer run as www-data user */

include('conf.inc.php');
include('modules/common.inc.php');

$now=strftime("%d/%m/%Y %H:%M:%S");

class GUI {
    function GUI() {
        $this->info='';
        $this->error='';
    }
    function info($txt) {
        global $now;
        if($txt == '') return;
        fwrite(STDERR, "[$now]: ".print_r($txt, true)." \n");
    }
    function debug($txt) {
        if($txt == '') return;
        if (DEBUG)
            fwrite(STDERR, "D: ".print_r($txt, true)." \n");
    }
    function debuga($txt) {
        $this->debug($txt);
    }
    function session_info($txt) {
        $this->info.="$txt<br/>";
        $this->info("SESSION INFO: ".$txt);
    }
    function session_error($txt) {
        $this->error.="$txt<br/>";
        $this->debug("SESSION ERROR:".$txt);
    }
}

$gui = new GUI();


include("classes/ldap.class.php");
include("classes/importer.class.php");

global $ldap;
$ldap = new LDAP();

$importer = new Importer();
$importer->doImport();

