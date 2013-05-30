<?php
/* se llama a traves de consola para ejecutar acciones largas en segundo plano */


include("./conf.inc.php");
include('./modules/common.inc.php');

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

$args=ParseCMDArgs();

if (! isset($args['ip'])) {
    die("no ip\n");
}
if (! isset($args['action'])) {
    die("no action\n");
}

include("./classes/ldap.class.php");
include("./classes/winexe.class.php");

$ldap=new LDAP();


if ( checkIP($args['ip']) != $args['ip']) {
    $computers=$ldap->get_computers($args['ip']. '$');
    $computer=$computers[0];
}
else {
    $computer=$ldap->get_computer_by_ip($args['ip']);
}

if ( ! $computer->action($args['action'], $computer->macAddress)) {
    echo ("error");
}
else {
    echo ("ok");
}

