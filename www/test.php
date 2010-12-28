<?php

#ini_set("track_errors", 1);
#ini_set("display_errors",1);
#error_reporting(E_ERROR | E_WARNING | E_PARSE);
#error_reporting(1);

include("../conf.inc.php");
include('../modules/common.inc.php');

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

#$a=array("uno");
#$b=array("uno");
#$gui->debug(array_diff($a, $b));
#die();

include("../classes/ldap.class.php");
include("../classes/winexe.class.php");


//echo conectar('ebox','GzxovzAANdxoPux9');
//echo conectar('cn=ebox,dc=max-server','GzxovzAANdxoPux9');
//echo conectar('uid=test,ou=Users,dc=max-server','test');
//echo conectar('uid=mario,ou=Users,dc=max-server','12345');

//echo "<h2>LDAP</h2><br/>\n";

$ldap=new LDAP($binddn='cn=ebox,dc=max-server',$bindpw='GzxovzAANdxoPux9');
//$ldap=new LDAP();

//$gui->debug( $ldap->lastUID() );
//$gui->debug( $ldap->lastGID() );

//$gui->debug( $ldap->getGID('__USERS__') );

//$gui->debug( $ldap->getSID() );

//$gui->debug( $ldap->addUserToGroup('aaaa', LDAP_OU_DUSERS) );
//$gui->debug( $ldap->delUserFromGroup('aaaa', LDAP_OU_DUSERS) );

#$gui->debug( $ldap->getDefaultQuota() );


//$gui->debug($ldap->error);
//$gui->debug($ldap->is_connected());

//$gui->debug($ldap->get_users());
//$gui->debug($ldap->get_user($uid=$argv[1]));
//$gui->debug($ldap->error);

//$gui->debug($ldap->is_admin($uid=$argv[1]));
//$gui->debug($ldap->error);

//$gui->debug($ldap->get_computers('mario*'));
//$gui->debug($ldap->get_computers('*'));

//$gui->debug($ldap->get_aulas('ma'));
//$gui->debug($ldap->get_macs_from_aula('aula Primaria A'));


/* editar mario-desktop computer */
//$host=$ldap->get_computers('mario-desktop$');

//$gui->debug($host[0]);
//$gui->debug("PURE: ". $host[0]->_pure);

#$host[0]->macAddress='FF:FF:FF:FF:29:86';
#$host[0]->ipHostNumber='192.168.1.2';
#$host[0]->bootFile='/pxelinux.0';
#$host[0]->sambaProfilePath='aula Primaria A';

#$res=$host[0]->save( array('sambaProfilePath', 
#                         'ipHostNumber', 
#                         'ipNetmaskNumber', 
#                         'ipNetmaskNumber', 
#                         'macAddress', 
#                         'bootFile') );
#$gui->debug("save result '$res'");


//$gui->debug($host[0]);



#$ldap->disconnect();
#$gui->debug($ldap->error);

#$gui->debug("\n\n\n");


#$teachers=$ldap->get_groups('Teachers', $include_system=true);
#$gui->debuga($teachers);
#if ( count($teachers) < 1 ) {
#    $group = new GROUP( array('cn' => 'Teachers' ) );
#    $group->newGroup('');
#}
#else {
#    $gui->debug("El grupo Teachers existe");
#}



#$exe=new WINEXE('wxp');
#echo $exe->isLinux();

#$hosts=$ldap->get_computers('win2008$');
#$host=$hosts[0];
#//$gui->debug($host);
#//$host->action('wakeonlan', $host->macAddress);
#$host->boot('max');


#$gui->debug($ldap->getBootMenus());

#$gui->debug($ldap->lastUID());
#$gui->debug($ldap->lastGID());

#$exe=new WINEXE('mario-desktop');
#echo $exe->isLinux();
#echo $exe->mount('test.iso');
//echo $exe->umount();
//echo $exe->reboot($exe->mac);

#$exe=new WINEXE('192.168.1.148');
#$exe->fork('rebootwindows');

$computers=$ldap->get_computers_from_aula('aula primaria 2');
$gui->debuga($computers);
foreach($computers as $computer) {
    $computer->empty_attr( 'sambaProfilePath' );
}


?>
