<?php

#ini_set("track_errors", 1);
#ini_set("display_errors",1);
#error_reporting(E_ERROR | E_WARNING | E_PARSE);
#error_reporting(1);

require("../conf.inc.php");
require('../modules/common.inc.php');

class GUI {
    function debug($txt) {
        if (! pruebas)
            return;
        if($txt == '') return;
        echo "D: ".print_r($txt, true)." <br>\n";
    }
    function debuga($txt) {
        if (! pruebas)
            return;
        if($txt == '') return;
        echo "D: ".print_r($txt, true)." <br>\n";
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

/* check IP address */
//$gui->debug("<pre>".print_r($_SERVER, true) . "</pre>");

if ( ! isset($_SERVER['REMOTE_ADDR']) ) {
    die("error: bad origin");
}

if ( ! pruebas) {
    if ( ($_SERVER['REMOTE_ADDR'] != "127.0.0.1") ) {
        die("error: access denied from your IP");
    }
}


$mac="08:00:27:96:0d:e6";

$ldap=new LDAP($binddn='cn=ebox,dc=max-server',$bindpw='GzxovzAANdxoPux9');

$changed=false;

$computers=$ldap->get_computers();
foreach($computers as $c) {
    if ( isset($c->macAddress ) && $c->macAddress == $mac) {
        $gui->debug("found computer ". $c->hostname());
        if ( $c->resetBoot() )
            $changed=true;
            break;
    }
}
$ldap->disconnect();

if ($changed)
    echo "ok";
else
    echo "error: not found";



?>
