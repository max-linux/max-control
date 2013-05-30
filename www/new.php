<?php


include("../conf.inc.php");
include('../modules/common.inc.php');

error_reporting(E_ALL);

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

require("../classes/winexe.class.php");
include("../classes/ldap.class.php");

$ldap=new LDAP();
//$ldap->get_users('test');
//$gui->debug($ldap->get_user('test'));
//$gui->debug(print_r($ldap->user_exists('test1'), true));

//$gui->debuga($ldap->get_groups());
//$gui->debuga($ldap->get_members_in_and_not_group('grupo1'));

//$user=$ldap->get_user('test2');
//$gui->debuga($ldap->is_admin('Administrator'));
//$gui->debuga($user->get_role());

$gui->debuga( $ldap->get_user('Administrator') );
//$gui->debuga( $ldap->get_users() );


//$gui->debuga( $ldap->get_aulas() );


//$gui->debuga( $ldap->get_computers() );

// $computers = $ldap->get_computers();
// $c = $computers[0];
// $c->ipHostNumber="192.168.1.2";
// $c->macAddress="08:00:27:72:50:fe";
// $c->saveIPMAC();


// $computers = $ldap->get_computers('WINXPLDAP$');
// $gui->debuga($computers);
// $gui->debuga($computers[0]->exe->is_alive());

//$gui->debuga($user->get_role());


//$gui->debuga( $ldap->get_teacher_from_aula('Aula23') );


//$gui->debuga($ldap->get_computers_from_aula('Aula23'));

