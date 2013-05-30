<?

#ini_set("track_errors", 1);
#//error_reporting(E_ALL);
#ini_set("display_errors",1);
#error_reporting(E_ERROR | E_WARNING | E_PARSE);
#error_reporting(1);

class GUI {
    function debug($txt) {
        if($txt == '') return;
        echo "D: ".print_r($txt, true)." <br>\n";
    }
}

$gui = new GUI();

include("../conf.inc.php");
include "../classes/ldap.class.php";



$ldap=new LDAP($binddn='cn=ebox,dc=max-server',$bindpw='GzxovzAANdxoPux9');
$gui->debug($ldap->error);



$ldap->search("(uid=mario)");
while($attrs = $ldap->fetch())
{
	echo "<pre>" .print_r($attrs, true) . "</pre><br>";
}



