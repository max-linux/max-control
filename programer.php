<?php
/*  programer run as www-data user */

if ( ! is_readable('conf.inc.php') ) {
    die('conf.inc.php no encontrado');
}
include('conf.inc.php');
include('modules/common.inc.php');


$now=strftime("%d/%m/%Y %H:%M:%S");

class GUI {
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
        $this->info("SESSION INFO: ".$txt);
    }
    function session_error($txt) {
        $this->debug("SESSION ERROR:".$txt);
    }
}

$gui = new GUI();


include("classes/ldap.class.php");
include("classes/programer.class.php");
include('classes/winexe.class.php');


$cronprogramer = new CronProgramer();
$cronprogramer->doJobs();


