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

#$a=array("uno");
#$b=array("uno");
#$gui->debug(array_diff($a, $b));
#die();

if(@$argv[1] == 'old') {
    include("classes/ldap-OLD.class.php");
}
else {
    include("classes/ldap.class.php");
}


include("classes/winexe.class.php");



$user = get_class_methods('COMPUTER');
sort($user);
$gui->debuga($user);


/*

USER


    [2] => delUser                    [2] => attrs    
    [3] => empty_attr                 [3] => delUser       
          
    
    [6] => get_save_dn                [6] => getquota        
    [7] => getquota                   [7] => init     
    [8] => init                       [8] => is_role 
    [9] => is_restricted              [9] => is_romaing          
    [10] => is_role                   [10] => newUser     
    [11] => load_class_required       [11] => resetProfile                 
    [12] => newUser                   [12] => save     
    [13] => pre_save                  [13] => set      
    [14] => reQuota                   [14] => set_role     
    [15] => resetProfile              [15] => split_dn          
    [16] => save                      [16] => update_password  
    [17] => set
    [18] => set_role
    [19] => show
    [20] => update_password

 
 GROUP


    [1] => addGroup                 
                                    [2] => attrs
   
    [4] => delMember                [4] => get_users     
    [5] => empty_attr               [5] => init      
    [6] => get_num_users            [6] => newGroup         
    [7] => get_save_dn              [7] => newMember       
    [8] => get_users                [8] => set     
    [9] => init                     [9] => split_dn
    [10] => is_restricted
    [11] => load_class_required
    [12] => newGroup
    [13] => newMember
    [14] => pre_save
    [15] => renameGroup
    [16] => save
    [17] => set
    [18] => show

AULA


    [0] => BASE                        [0] => BASE
    [1] => attr                        [1] => add_computer
    [2] => boot                        [2] => attr
    [3] => delAula                     [3] => attrs   
    [4] => delMember                   [4] => del_computer     
    [5] => empty_attr                  [5] => getBoot      
    [6] => genPXELinux                 [6] => get_computers       
    [7] => getBoot                     [7] => get_num_computers   
    [8] => get_num_computers           [8] => get_num_users             
    [9] => get_num_users               [9] => get_users         
    [10] => get_save_dn                [10] => init        
    [11] => get_users                  [11] => newAula      
    [12] => init                       [12] => safecn 
    [13] => is_restricted              [13] => set          
    [14] => load_class_required        [14] => split_dn                
    [15] => newAula                    [15] => teacher_in_aula    
    [16] => newMember
    [17] => pre_save
    [18] => safecn
    [19] => save
    [20] => set
    [21] => show
    [22] => teacher_in_aula

COMPUTER

    [0] => BASE                             [0] => BASE 
    [1] => action                           [1] => action   
    [2] => attr                             [2] => attr 
    [3] => boot                             [3] => attrs 
    [4] => cleanPXELinux                    [4] => boot          
    [5] => delComputer                      [5] => getBoot        
    [6] => empty_attr                       [6] => getMACIP       
    [7] => genPXELinux                      [7] => get_aula        
    [8] => getBoot                          [8] => hostname    
    [9] => getMACIP                         [9] => init     
    [10] => get_save_dn                     [10] => pxeMAC         
    [11] => hostname                        [11] => rnd      
    [12] => init                            [12] => save  
    [13] => is_restricted                   [13] => saveIPMAC           
    [14] => load_class_required             [14] => set                 
    [15] => newComputer                     [15] => show         
    [16] => pre_save                        [16] => split_dn      
    [17] => pxeMAC                          [17] => teacher_in_computer    
    [18] => resetBoot
    [19] => rnd
    [20] => save
    [21] => set
    [22] => show
    [23] => teacher_in_computer


 
# Objeto 1: CN=Grupo4,CN=Users,DC=madrid,DC=lan
dn: CN=Grupo4,CN=Users,DC=madrid,DC=lan
cn: Grupo4
description: grupo 4
distinguishedname: CN=Grupo4,CN=Users,DC=madrid,DC=lan
gidnumber: 3131
grouptype: -2147483646
instancetype: 4
name: Grupo4
objectcategory: CN=Group,CN=Schema,CN=Configuration,DC=madrid,DC=lan
objectclass: top
objectclass: posixAccount
objectclass: group
objectguid:: ZDgZVuw5f0GPuxDeFpPBnQ==
objectsid:: AQUAAAAAAAUVAAAAWkPcISoQO251ALVOawQAAA==
samaccountname: Grupo4
samaccounttype: 268435456
usnchanged: 4346
usncreated: 4345
whenchanged: 20140922182552.0Z
whencreated: 20140922182552.0Z



# Objeto 1: CN=Group5,CN=Users,DC=madrid,DC=lan
dn: CN=Group5,CN=Users,DC=madrid,DC=lan
cn: Group5
description: esto es grupo 5
distinguishedname: CN=Group5,CN=Users,DC=madrid,DC=lan
gidnumber: 3134
grouptype: -2147483646
instancetype: 4
name: Group5
objectcategory: CN=Group,CN=Schema,CN=Configuration,DC=madrid,DC=lan
objectclass: top
objectclass: posixAccount
objectclass: group
objectguid:: u+mpheUiwkOkOlWVGHQfdw==
objectsid:: AQUAAAAAAAUVAAAAWkPcISoQO251ALVObgQAAA==
samaccountname: Group5
samaccounttype: 268435456
usnchanged: 4354
usncreated: 4353
whenchanged: 20140922184235.0Z
whencreated: 20140922184235.0Z



 */

die();


//echo conectar('ebox','GzxovzAANdxoPux9');
//echo conectar('cn=ebox,dc=max-server','GzxovzAANdxoPux9');
//echo conectar('uid=test,ou=Users,dc=max-server','test');
//echo conectar('uid=mario,ou=Users,dc=max-server','12345');

//echo "<h2>LDAP</h2><br/>\n";

// $ldap=new LDAP($binddn='cn=admin,cn=Users,dc=madrid,dc=lan',$bindpw='admin2');
$ldap=new LDAP();

// $gui->debug( $ldap->lastUID() );
// $gui->debug( $ldap->lastGID() );

//$gui->debug( $ldap->getGID('__USERS__') );

//$gui->debug( $ldap->getSID() );

//$gui->debug( $ldap->addUserToGroup('aaaa', LDAP_OU_DUSERS) );
//$gui->debug( $ldap->delUserFromGroup('aaaa', LDAP_OU_DUSERS) );

#$gui->debug( $ldap->getDefaultQuota() );


//$gui->debug($ldap->error);
//$gui->debug($ldap->is_connected());

//$ldap->get_users('prue');
// $gui->debug($ldap->get_users());
// $gui->debug($ldap->get_users('prueba2'));
//$gui->debug($ldap->get_user($uid=$argv[1]));
//$gui->debug($ldap->error);

//$gui->debug($ldap->is_admin($uid=$argv[1]));
//$gui->debug($ldap->error);

// $gui->debug($ldap->get_computers('mario'));
// $gui->debug($ldap->get_computers('*'));

// $gui->debug($ldap->get_aulas());
// $gui->debug($ldap->get_computers_from_aula('Aula1'));
// $gui->debug($ldap->get_macs_from_aula('Aula1'));


/* editar mario-desktop computer */
//$host=$ldap->get_computers('mario-desktop$');

$aulas=$ldap->get_aulas();
$gui->debuga($aulas[0]);
// $gui->debuga( $aulas[0]->get_computers() );

// $gui->debuga( $aulas[0]->add_computer('MAX75RC4') );
// $gui->debuga( $aulas[0]->get_computers() );

$gui->debuga( $aulas[0]->del_computer('MAX75RC4') );
$gui->debuga( $aulas[0]->del_computer('max75rc3') );
$gui->debuga( $aulas[0]->get_computers() );

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
#    $group->newGroup('0', '0');
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

#$computers=$ldap->get_computers_from_aula('aula primaria 2');
#$gui->debuga($computers);
#foreach($computers as $computer) {
#    $computer->empty_attr( 'sambaProfilePath' );
#}

// $exe=new WINEXE('winxp3');
// #echo $exe->reboot($exe->mac);
// $exe->init();
// echo $exe->windowsexe("ipconfig");

