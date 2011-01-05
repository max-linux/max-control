<?php
/*  programer run as www-data user */

include('conf.inc.php');
include('modules/common.inc.php');

class GUI {
    function debug($txt) {
        if($txt == '') return;
        fwrite(STDERR, "D: ".print_r($txt, true)." \n");
    }
    function debuga($txt) {
        if($txt == '') return;
        fwrite(STDERR, "D: ".print_r($txt, true)." \n");
    }
    function session_info($txt) {
        $this->debug("SESSION INFO: ".$txt);
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


?>
