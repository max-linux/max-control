<?php
/*  generate default config  */

include('conf.inc.php.init');
include('modules/common.inc.php');

class GUI {
    function debug($txt) {
        if($txt == '') return;
        //fwrite(STDERR, "D: ".print_r($txt, true)." \n");
    }
    function debuga($txt) {
        if($txt == '') return;
        //fwrite(STDERR, "D: ".print_r($txt, true)." \n");
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

$LDAP_BASEDN=readLDAPFile('/etc/ldap.conf', 'base');
define('LDAP_BASEDN', $LDAP_BASEDN);
$LDAP_BINDDN=readLDAPFile('/etc/ldap.conf', 'rootbinddn');
define('LDAP_BINDDN', $LDAP_BINDDN);
$LDAP_BINDPW=file_get_contents('/etc/ldap.secret');
define('LDAP_BINDPW', $LDAP_BINDPW);



/* usuario creado por max-control */
define('LDAP_ADMIN', '$LDAP_ADMIN');
define('LDAP_PASS', '$LDAP_PASS');



exec("net getdomainsid | grep domain", &$output);
$parts = preg_split ("/\s+/", $output[0]);
$LDAP_DOMAIN=$parts[3];
define('LDAP_DOMAIN', $LDAP_DOMAIN);

$LDAP_ADMIN='max-control';
$LDAP_PASS=createPassword();





function create_user($username) {
    $ldap=new LDAP();
    $user=$ldap->get_user($username);
    if ( ! $user ) {
        // user not exists, create it
        $new=array(
                "uid" => $username,
                "cn" => $username,
                "sn" => "admin-no-borrar",
                "description" => "Usuario ",
                "password" => $username,
                "role" => "alumno",
                "loginShell" => "/bin/false"
                );
        $user = new USER( $new );
        $user->newUser();
        echo " * Usuario '$username' creado.\n";
    }
    else {
        echo " * El usuario '$username' ya existe.\n";
    }
}

$prefix="alumno";
for($i=1000; $i<1013; $i++) {
    create_user("$prefix$i");
}
die("fin\n");


?>
