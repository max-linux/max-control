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




$ldap=new LDAP();
$user=$ldap->get_user($LDAP_ADMIN);
if ( ! $user ) {
    // user not exists, create it
    $new=array(
            "uid" => $LDAP_ADMIN,
            "givenName" => $LDAP_ADMIN,
            "sn" => "admin-no-borrar",
            "description" => "Usuario administrador creado para uso del panel max-control",
            "password" => $LDAP_PASS,
            "role" => "admin",
            "loginShell" => "/bin/bash"
            );
    $user = new USER( $new );
    $user->newUser();
    echo " * Usuario 'max-control' creado.\n";
}
else {
    //change password
    echo " * Usuario 'max-control' actualizado.\n";
    //$gui->debuga( $user->show() );
    $user->update_password($LDAP_PASS, $LDAP_ADMIN);
}

// crear grupo de profesores
$teachers=$ldap->get_groups(TEACHERS, $include_teachers=true);
if ( ! isset($teachers[0]) ) {
    $group = new GROUP( array('cn' => TEACHERS ) );
    $group->newGroup('');
    $group->description="Profesores no-borrar";
    $group->ldapdata['description']="Profesores no-borrar";
    $group->save( array('description') );
    echo " * Creado grupo Teachers (profesores).\n";
}
else {
    $teachers[0]->description="Profesores no-borrar";
    $teachers[0]->ldapdata['description']="Profesores no-borrar";
    $teachers[0]->save( array('description') );
    echo " * El grupo Teachers (profesores) ya existe.\n";
}




$extra=file_get_contents('conf.inc.php.init');
$extra = str_replace ( "<?php" , "" , $extra );
$extra = str_replace ( "<?" , "" , $extra );
$extra = str_replace ( "?>" , "" , $extra );
$out="<?php
/* file autogenerated with pygenconfig */


/* domain (net getdomainsid */
define('LDAP_DOMAIN', '$LDAP_DOMAIN');

/* ldap admin from /etc/ldap.conf and /etc/ldap.secret */
define('LDAP_BINDDN', '$LDAP_BINDDN');
define('LDAP_BINDPW', '$LDAP_BINDPW');

/* usuario creado por max-control */
define('LDAP_ADMIN', '$LDAP_ADMIN');
define('LDAP_PASS', '$LDAP_PASS');

define('LDAP_BASEDN', '$LDAP_BASEDN');

define('CONFIGURED', True);

$extra
?>";




$config = "/etc/max-control/conf.inc.php";
$fh = fopen($config, 'w');
fwrite($fh, $out);
fclose($fh);

/* write domain in netlogon */
$domainfd=fopen("/home/samba/netlogon/domain.txt", 'w');
fwrite($domainfd, $LDAP_DOMAIN);
fclose($domainfd);

?>