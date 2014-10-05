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



// $user = get_class_methods('COMPUTER');
// sort($user);
// $gui->debuga($user);


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

SAMBA 4
 
class BASE 
    function BASE(array $parameter = array()) 
    function init()
    function set(array $parameter = array()) 
    function split_dn($dn) 
    function attr($attrname) 


class USER extends BASE 
--    public static function attrs() 
**    function set_role() 
--    function save($data) 
**    function update_password($new, $cn) 
--    function init()
**    function is_role($role) 
**    function get_role() 
NN    function is_romaing() 
**    function getNumericQuota() 
**    function getquota() 
**    function newUser($data=array()) 
**    function delUser($delprofile='') 
**    function resetProfile() 


class GROUP extends BASE 
--    public static function attrs() 
--    function init()
**  function get_num_users() 
**    function get_users() 
    
**    function newMember($username) 
**    function delMember($username) 

**    function newGroup($createshared, $readonly, $grouptype=2) 
**    function delGroup($delprofile='') 

WW    function renameGroup($newname)


class AULA extends BASE 
--    public static function attrs() 
--    function init()
--    function safecn() 
**    function get_num_users() 
**    function get_users() 
**    function get_num_computers() 

**    function teacher_in_aula() 
    function get_computers() 

NN    function add_computer($computer) 
NN    function del_computer($computer) 

**    function newMember($username) 
**    function delMember($username) 

**    function newAula()
**    function delAula() 

**    function getBoot() 
**    function genPXELinux() 


class COMPUTER extends BASE 
    public static function attrs() 
    function init()
    function save($data=array()) 
    function saveIPMAC() 
    function hostname() 
    function rnd() 
    function get_aula() 
    function teacher_in_computer() 
    function show() 
    function boot($conffile) 
    function getBoot() 
    function pxeMAC() 
    function getMACIP() 
    function action($actionname, $mac)


class LDAP 
    function LDAP($binddn = "", $bindpw = "", $hostname = LDAP_HOST) 
    function get_error()
    function is_connected() 
    function connect()
    function search($filter, $basedn='', $attrs=array('*'))  
    function disconnect($txt='') 
    function get_users($filter='', $group=LDAP_OU_USERS, $filterrole='') 
    function get_user($cn='') 
    function user_exists($cn) 
    function get_user_uids($group=LDAP_OU_USERS) 
    function get_groups($filter='', $include_system=false) 
    function get_group($cn) 
    function get_members_in_and_not_group($groupfilter) 
    function get_tics_uids() 
    function is_tic($uid='') 
    function get_teachers_uids($filter='*') 
    function is_teacher($uid='') 
    function is_admin($uid='') 
    function get_aulas($aula='') 
    function get_aulas_cn($aula='') 
    function get_computers($com='') 
    function get_aula($aulafilter) 
    function get_teacher_from_aula($aulafilter) 
    function get_macs_from_aula($aula) 
    function get_computers_from_aula($aula) 
    function get_computers_in_and_not_aula($aula) 
    function readMenu($fmenu) 
    function getBootMenus($aula=False) 
    function getISOS($filter='')

===============================================================
OpenLDAP

class BASE 
    function BASE(array $parameter = array()) 
    function set(array $parameter = array()) 
    function show() 
    function attr($attrname) 
    function init()
    function pre_save() 
    function get_save_dn()
    function load_class_required($varname) 
    function is_restricted($varname) 
    function save($attrs=array()) 
    function empty_attr( $attr ) 


class USER extends BASE 
--    function init()
--    function get_save_dn()
**    function get_role() 
**    function is_role($role) 
OB    function reQuota()
**    function set_role($role) 
**    function update_password($new) 
**    function newUser() 
**    function delUser($delprofile='') 
**    function getNumericQuota() 
**    function getquota() 
**    function resetProfile() 


class GROUP extends BASE 
--    function init()
**    function get_num_users() 
**    function get_users() 
--    function get_save_dn()
    
**    function newMember($username) 
**    function delMember($username) 

OB    function addGroup() 
**    function newGroup($createshared, $readonly, $grouptype=2) 
**    function delGroup($delprofile='') 

    function renameGroup($newname) 


class AULA extends BASE 
--    function init()
--    function safecn() 
**    function get_num_users() 
**    function get_users() 
**    function get_num_computers() 

**    function teacher_in_aula() 
--    function get_save_dn()

**    function newMember($username) 
**    function delMember($username) 

**    function newAula() 
**    function delAula() 

**    function getBoot() 
**    function genPXELinux() 
    function boot($conffile)


class COMPUTER extends BASE 
    function init() 
    function hostname() 
    function get_save_dn()
    function rnd() 
    function load_class_required($varname) 
    function is_restricted($varname) 
    function pre_save() 
    function action($actionname, $mac)
    function getMACIP() 
    function genPXELinux() 
    function cleanPXELinux() 
    function resetBoot() 
    function boot($conffile) 
    function getBoot() 
    function pxeMAC() 
    function teacher_in_computer() 
    function delComputer() 
    function newComputer($data) 


class ISO extends BASE
    function save() 
    function init()


class LDAP 
    function LDAP($binddn = "", $bindpw = "", $hostname = LDAP_HOSTNAME) 
    function get_error()
    function connect()
    function is_connected() 
    function get_users($filter='*', $group=LDAP_OU_USERS, $ignore="max-control", $filterrole='') 
    function get_user($uid='') 
    function user_exists($uid) 
    function get_user_uids($group=LDAP_OU_USERS) 
    function get_tics_uids($filter='*') 
    function is_tic($uid='') 
    function get_teachers_uids($filter='*') 
    function is_teacher($uid='') 
    function is_admin($uid='') 
    function get_computers($uid='') 
    function get_computer_by_ip($ip='') 
    function get_aulas($aula='') 
    function get_aula($aulafilter) 
    function get_teacher_from_aula($aulafilter) 
    function get_macs_from_aula($aula) 
    function get_computers_from_aula($aula) 
    function get_computers_in_and_not_aula($aula) 
    function get_aulas_cn($aula='') 
    function get_group($cn) 
    function get_groups($groupfilter='*', $include_system=false) 
    function get_members_in_and_not_group($groupfilter) 
    function lastUID() 
    function lastGID() 
    function getGID($gidname) 
    function additionalPasswords($txtpasswd, $user, $samba=false) 
    function getSID() 
    function getDefaultQuota() 
    function deleteProfile($uid) 
    function deleteGroupProfile($group) 
    function addGroupProfile($group, $readonly=0) 
    function genSamba() 
    function updateLogonShares() 
    function purgeWINS() 
    function getGroupMembers($group) 
    function addUserToGroup($user, $group) 
    function delUserFromGroup($user, $group) 
    function readMenu($fmenu) 
    function getBootMenus($aula=False) 
    function getISOS($filter='') 
    function search($filter, $basedn='')
    function fetch() 
    function resetResult() 
    function disconnect($txt='') 

 */




//echo conectar('ebox','GzxovzAANdxoPux9');
//echo conectar('cn=ebox,dc=max-server','GzxovzAANdxoPux9');
//echo conectar('uid=test,ou=Users,dc=max-server','test');
//echo conectar('uid=mario,ou=Users,dc=max-server','12345');

//echo "<h2>LDAP</h2><br/>\n";

// $ldap=new LDAP($binddn='cn=admin,cn=Users,dc=madrid,dc=lan',$bindpw='admin2');
$ldap=new LDAP();

if( ! $ldap->connected ) {
    echo "error\n";
}
else {
    echo "ok\n";
}

// $users = $ldap->get_users('admin');
// $gui->debuga($users);
// $users[0]->role="teacher";
// $users[0]->set_role();

// $data=$ldap->search("(&(objectclass=posixAccount)(CN=Teachers))",
//                             $basedn=LDAP_OU_BUILTINS,
//                             $attrs=GROUP::attrs());

// // $gui->debuga($data);
// $gui->debuga(new GROUP($data[0]));
// 

// $a = $ldap->get_builtin_groups(TICS);
// $gui->debuga($a);


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

// $aulas=$ldap->get_aulas();
// $gui->debuga($aulas[0]);
// $gui->debuga( $aulas[0]->get_computers() );

// $gui->debuga( $aulas[0]->add_computer('MAX75RC4') );
// $gui->debuga( $aulas[0]->get_computers() );

// $gui->debuga( $aulas[0]->del_computer('MAX75RC4') );
// $gui->debuga( $aulas[0]->del_computer('max75rc3') );
// $gui->debuga( $aulas[0]->get_computers() );
// 

// $gui->debuga($ldap->get_macs_from_aula('Aula1') );

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

