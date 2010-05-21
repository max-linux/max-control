<?php

#ini_set("track_errors", 1);
#ini_set("display_errors",1);
#error_reporting(E_ERROR | E_WARNING | E_PARSE);
#error_reporting(1);

include("../conf.inc.php");

class GUI {
    function debug($txt) {
        if($txt == '') return;
        echo "D: ".print_r($txt, true)." <br>\n";
    }
}

$gui = new GUI();

#$a=array("uno");
#$b=array("uno");
#$gui->debug(array_diff($a, $b));
#die();

include("../classes/ldap.class.php");



//echo conectar('ebox','GzxovzAANdxoPux9');
//echo conectar('cn=ebox,dc=max-server','GzxovzAANdxoPux9');
//echo conectar('uid=test,ou=Users,dc=max-server','test');
//echo conectar('uid=mario,ou=Users,dc=max-server','12345');

echo "<h2>LDAP</h2><br/>\n";

//$ldap=new LDAP($binddn='cn=ebox,dc=max-server',$bindpw='GzxovzAANdxoPux9');
//$ldap=new LDAP();

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

/*

$ldap->disconnect();
$gui->debug($ldap->error);

$gui->debug("\n\n\n");

*/


include("../classes/winexe.class.php");

$exe=new WINEXE('192.168.0.244');
echo $exe->isLinux();


?>
