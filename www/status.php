<?php
/*
*
*  status.php: make and action and redirect to another url (img for example)
*
*/

ini_set("track_errors", 1);
ini_set("display_errors",1);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(1);

require("../conf.inc.php");
require('../modules/common.inc.php');

class GUI {
    function debug($txt) {
        if (! DEBUG)
            return;
        if($txt == '') return;
        //echo "D: ".print_r($txt, true)." <br>\n";
    }
    function debuga($txt) {
        if (! DEBUG)
            return;
        if($txt == '') return;
        //echo "D: ".print_r($txt, true)." <br>\n";
    }
    function session_info($txt) {
        $this->debug("SESSION INFO: ".$txt);
    }
    function session_error($txt) {
        $this->debug("SESSION ERROR:".$txt);
    }
}

$gui = new GUI();

require("../classes/winexe.class.php");
require("../classes/ldap.class.php");
$ldap=new LDAP();


function mdie() {
    global $ldap;
    $ldap->disconnect();
    die();
}

$schema = $_SERVER['SERVER_PORT'] == '443' ? 'https' : 'http';
$host = strlen($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME'];
$path=dirname($_SERVER["PHP_SELF"]);
time_start();


$hostname=leer_datos('hostname');



$equipo=$ldap->get_computers($hostname .'$');
if ( ! isset($equipo[0]->cn) ) {
    /* hostname not found */
    header("Location: $schema://$host/$path/img/warning.png");
    mdie();
}


if (! $equipo[0]->exe->is_alive() ) {
    /* ping failed*/
    $gui->debug("APAGADO");
    header("Location: $schema://$host/$path/img/status_poweroff.png");
    mdie();
}
$gui->debug("ENCENDIDO");
header("Location: $schema://$host/$path/img/status_ok.png");



mdie();

