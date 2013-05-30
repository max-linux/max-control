<?php

#ini_set("track_errors", 1);
#ini_set("display_errors",1);
#error_reporting(E_ERROR | E_WARNING | E_PARSE);
#error_reporting(1);

include("conf.inc.php");
include('modules/common.inc.php');

class GUI {
    function debug($txt) {
        if($txt == '') return;
        echo "D: ".print_r($txt, true)." \n";
    }
    function debuga($txt) {
        if($txt == '') return;
        echo "D: ".print_r($txt, true)." \n";
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

$ldap=new LDAP();
$gui->debuga($ldap->is_teacher('festeban'));





