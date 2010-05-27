<?php

/*

dc=max-server
    cn=ebox => ADMIN
    
    ou=Computers => Equipos (hay que añadir más atributos)
            Samba 3 Machine
                attr:nombre (acabado en $)
                attr:GIDNumber (domain computers)
                NUEVOS
                    attr:MAC
                    attr:GIDAula
    
    ou=Users => Usuarios
        uid=nombre.unico


UNIR PC A DOMINIO

  Editar smb.conf

  workgroup=EBOX
  security=domain


   Añadir máquina al domino
     net ads join -D EBOX -U test%test

   Respuesta:

    Using short domain name -- EBOX
    Joined 'MARIO-DESKTOP' to domain 'EBOX'

PSEXEC:
http://gabrielstein.org/blog/?p=538
@Echo Off
for /f %%a in (file_with_clients_names.txt) do (psexec \\%%a -u DOMAIN\USER-p -e cmd.exe /c “reg import \\UNC_PATH.reg”) 2> errorlog.txt

*/

global $gui;

class BASE {
    var $ldapdata=array();
    
    function BASE(array $parameter = array()) {
        $this->set($parameter);
        $this->init();
    }
    
    function set(array $parameter = array()) {
        foreach($parameter as $k => $v) {
            $this->ldapdata[$k]=$v;
            if ( isset($this->$k) ) {
                if ( isset($v['count']) && $v['count'] == 1) {
                    $this->$k=$v[0];
                }
                else {
                    //unset($v['count']);
                    $this->$k=$v;
                }
            }
            
            /*else {
                echo "var '\$$k' not in USER<br>\n";
            }*/
        }
    }
    
    function show() {
        /*
        *  Return array of vars of $this object
        */
        $allvars= get_class_vars(get_class($this));
        $new=array();
        foreach($allvars as $k => $v) {
            if ($k != 'ldapdata' && $k != 'role')
                $new[$k]=$this->$k;
        }
        return $new;
    }
    
    function attr($attrname) {
        /*
        * Return value of internal attribute
        */
        if ( ! isset($this->$attrname) )
            return NULL;
        return $this->$attrname;
    }

    function init(){
        global $gui;
        $gui->debug("empty init()");
        return;
    }
    
    function pre_save() {
        global $gui;
        $gui->debug("empty pre_save()");
        return;
    }
    
    function get_save_dn(){
        global $gui;
        $gui->debug("WARNING get_save_dn() from BASE");
        return LDAP_BASEDN;
    }
    
    function load_class_required($varname) {
        return null;
    }
    
    function is_restricted($varname) {
        return false;
    }
    
    function save($attrs=array()) {
        global $gui;
        $saveok=true;
        $this->pre_save();
        
        $gui->debug("ldap::BASE::save() dn=".$this->get_save_dn());
        
        //$gui->debug( $this->ldapdata );
        
        //$gui->debug("======================================================");

        // connect with privileges
        $ldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        
        
        
        $new_objects=array_diff($this->objectClass, $this->ldapdata['objectClass']);
        //add new objectclass
        if ( $new_objects ) {
            $gui->debug("BASE:save() NEW OBJECTS <pre>".print_r($new_objects, true)."</pre>");
            foreach( $new_objects as $k => $v) {
                if ($k == "count")
                    continue;
                
                // get original data
                $obj=array('objectClass'=> $this->ldapdata['objectClass']);
                
                // append $v
                $obj['objectClass'][]=$v;
                
                // remove count
                unset($obj['objectClass']['count']);
                
                // load value if required
                $new=$this->load_class_required($v);
                //$gui->debug("load_class_required($v)<pre>".print_r($new, true)."</pre>");
                if ($new) {
                    $obj=array_merge($obj, $new);
                }
                
                //$gui->debug("ldap::BASE::save() objectClass loop add $v\n<br/><pre>".print_r($obj, true)."</pre>");
                
                //save
                $r = ldap_modify($ldap->cid, $this->get_save_dn(), $obj );
                if ($r)
                    $this->ldapdata['objectClass'][]=$v;
                else
                    $saveok=false;
            }
        }
        
        // save new values
        foreach($attrs as $k) {
            //$gui->debug("BASE:save() try to save $k");
            if ( $this->$k == '' || $k == 'uid' )
                continue;
            if ( $this->is_restricted($k) && ! isset($this->$k) ) {
                $gui->debug("BASE:save() $k is restricted");
                continue;
            }
            $tmp=array();
            $tmp[$k]=$this->ldapdata[$k];
            //$gui->debug("BASE:save() dn=".$this->get_save_dn()." data=<pre>".print_r($tmp, true)."\nERROR=".ldap_error($ldap->cid)."</pre>");
            $r = ldap_modify($ldap->cid, $this->get_save_dn(), $tmp );
            $gui->debug("BASE:save() dn=".$this->get_save_dn()." data=<pre>".print_r($tmp, true)."\nERROR=".ldap_error($ldap->cid)."</pre>");
            if ( !$r) {
                $gui->debug( ldap_error($ldap->cid) );
                $saveok=false;
            }
        }
        $ldap->disconnect();
        return $saveok;
    }
}


/***********************************************************************/
class USER extends BASE {
    var $cn='';
    var $uid='';
    var $sn='';
    var $givenName='';
    var $description='';
    var $uidNumber='';
    var $gidNumber='';
    var $loginShell='/bin/false';    /* /bin/bash or /bin/false */
    var $sambaPrimaryGroupSID='';
    var $homeDirectory='';
    var $sambaHomePath='';
    var $sambaProfilePath='';
    var $objectClass='';
    var $sambaSID='';
    
    /* passwords */
    var $userPassword='';
    var $eboxSha1Password='';
    var $eboxMd5Password='';
    var $eboxLmPassword='';
    var $eboxNtPassword='';
    var $eboxDigestPassword='';
    var $eboxRealmPassword='';
    var $sambaNTPassword='';
    var $sambaLMPassword='';
    
    /* passwords expire */
    var $sambaPwdCanChange=0;
    var $sambaLogonTime=0;
    var $sambaLogoffTime=2147483647;
    
    var $sambaPwdMustChange=2147483647;
    var $sambaPwdLastSet='';  /**/
    var $sambaPasswordHistory='00000000000000000000000000000000000000000000000000000000';
    
    var $sambaAcctFlags='[U]';
    var $sambaKickoffTime=2147483647;
    
    var $role='unset';
    
    function get_save_dn(){
        return 'uid='.$this->uid.','.LDAP_OU_USERS;
    }
    
    function get_role() {
        //global $gui;
        //$gui->debug("USER:get_role() ='".$this->role."'");
        if ($this->role != 'unset')
            return $this->role;
        
        $ldap=new LDAP();
        if ( $ldap->is_admin($this->uid)) {
            $this->role="admin";
        }
        elseif ( $ldap->is_teacher($this->uid) ) {
            $this->role="teacher";
        }
        else {
            $this->role="";
        }
        return $this->role;
    }
    
    function set_role($role) {
        global $gui;
        $gui->debug("USER:set_role('$role')");
        $ldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        $gui->debug("USER:set_role() LDAP_BINDN='".LDAP_BINDDN."' LDAP_BINDPW='".LDAP_BINDPW."'");
        if ($role == '') {
            // usuario sin permisos
            // debe estar en el grupo Domain Users y __USERS__
            
            //quitar de profesores
            $teachers=array();
            $newteachers=array();
            $teachers=$ldap->get_teachers_uids();

            if ( in_array($this->uid, $teachers) ) {
                foreach($teachers as $t) {
                    if ($t != $this->uid)
                        $newteachers['memberUid'][]=$t;
                }
                $gui->debuga($newteachers);
                $r = ldap_modify($ldap->cid, LDAP_OU_TEACHERS, $newteachers );
            }
            
            
            //quitar de las aulas
            $aulas=$ldap->get_aulas();
            foreach ($aulas as $aula){
                $aula->delMember($this->uid);
            }
            // quitar de administradores
            return;
        }
        elseif ($role == 'teacher') {
            // debe estar en el grupo LDAP_OU_TEACHERS Domain Users y __USERS__
            $teachers=array();
            $teachers['memberUid']=$ldap->get_teachers_uids();
            if ( in_array($this->uid, $teachers['memberUid']) ) {
                $ldap->disconnect();
                $gui->debug("USUARIO:set_role($role) ".$this->uid . " ya es miembro de profesores");
                return true;
            }
            $teachers['memberUid'][]=$this->uid;
            $gui->debuga($teachers);
            $r = ldap_modify($ldap->cid, LDAP_OU_TEACHERS, $teachers );
            $ldap->disconnect();
            if ($r)
                return true;
            else
                return false;
        }
        elseif ($role == 'admin') {
            // debe estar en el grupo Domain Administrator y Administrator
            
            
            //quitar de profesores
            $aulas=$ldap->get_aulas();
            foreach ($aulas as $aula){
                $aula->delMember($this->uid);
            }
            return;
        }
    }
    
    function update_password($new) {
        global $gui;
        $ldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        $newpassword=$ldap->additionalPasswords($new, $this->uid);
        $gui->debuga($newpassword);
        $r = ldap_modify($ldap->cid, $this->get_save_dn() , $newpassword );
        $ldap->disconnect();
        if ($r)
            return true;
        else
            return false;
    }
    
    function newUser() {
        global $gui;
        
        $ldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        
        if ( $ldap->get_user($this->uid) ) {
            $gui->session_error("El usuario '".$this->uid."' ya existe.");
            return false;
        }
        
        $this->cn=$this->uid. " ".$this->sn;
        
        $this->uidNumber=$ldap->lastUID() +1;
        $this->gidNumber=$ldap->getGID('__USERS__');
        
        
        $rid = (2 * $this->uidNumber) + 1000;
        
        $this->sambaSID=$ldap->getSID()."-$rid";
        $this->sambaPrimaryGroupSID=$ldap->getSID()."-". $ldap->getGID('Domain Users');
        
        $this->homeDirectory=HOMES . $this->uid;
        $this->sambaHomePath=SAMBA_HOMES . $this->uid;
        $this->sambaProfilePath=SAMBA_PROFILES . $this->uid;
        
        $this->objectClass = array('inetOrgPerson', 'posixAccount', 'passwordHolder', 'sambaSamAccount');
        $additionalPasswords=$ldap->additionalPasswords(leer_datos('password') , $this->uid, $samba=true);
        $this->set( $additionalPasswords );
        
        $this->sambaPwdLastSet=time();
        
        $data=$this->show();
        
        $init=array(
            "uid" => $this->uid,
            "cn" => $this->cn,
            "sn" => $this->sn,
            "uidNumber" => $this->uidNumber,
            "gidNumber" => $this->gidNumber,
            "homeDirectory" => $this->homeDirectory,
            "userPassword" => "{SHA}".base64_encode(sha1(leer_datos('password'), TRUE)),
            "objectClass" => array('inetOrgPerson', 'posixAccount'),
                    );
        $gui->debuga($init);
        $r=ldap_add($ldap->cid, "uid=".$this->uid.",".LDAP_OU_USERS, $init);
        
        $gui->debug(ldap_error($ldap->cid));
        
        
        if ( ! $r ) {
            $gui->session_error("No se ha podido añadir el usuario, compruebe todos los campos.");
            return false;
        }
        
        $gui->debug("INIT DONE save rest");
        $this->ldapdata=$data;
        
        // save passwords
        $init=array(
            "objectClass" => array('inetOrgPerson', 'posixAccount', 'passwordHolder'),
                    );
        $init=array_merge($init, $ldap->additionalPasswords(leer_datos('password') , $this->uid, $samba=false));
        $gui->debuga($init);
        $r=ldap_modify($ldap->cid, "uid=".$this->uid.",".LDAP_OU_USERS, $init);
        
        $gui->debug(ldap_error($ldap->cid));
        if ( ! $r )
            return false;
        $gui->debug("PASSWORDS DONE save rest");
        
        
        // save SAMBA attributes
        $init=array(
            "objectClass" => array('inetOrgPerson', 'posixAccount', 'passwordHolder', 'sambaSamAccount'),
            
            "sambaNTPassword" => $this->sambaNTPassword,
            "sambaLMPassword" => $this->sambaLMPassword,
            
            "sambaPwdCanChange" => $this->sambaPwdCanChange,
            "sambaLogonTime" => $this->sambaLogonTime,
            "sambaLogoffTime" => $this->sambaLogoffTime,
            
            "sambaPwdMustChange" => $this->sambaPwdMustChange,
            "sambaPwdLastSet" => $this->sambaPwdLastSet,
            "sambaPasswordHistory" => $this->sambaPasswordHistory,
            
            "sambaAcctFlags" => $this->sambaAcctFlags,
            "sambaKickoffTime" => $this->sambaKickoffTime,
            
            "sambaNTPassword" => $this->sambaNTPassword,
            "sambaLMPassword" => $this->sambaLMPassword,
            
            "sambaPrimaryGroupSID" => $this->sambaPrimaryGroupSID,
            "sambaHomePath" => $this->sambaHomePath,
            "sambaProfilePath" => $this->sambaProfilePath,
            "sambaSID" => $this->sambaSID,
                    );
        $gui->debuga($init);
        $r=ldap_modify($ldap->cid, "uid=".$this->uid.",".LDAP_OU_USERS, $init);
        
        $gui->debug(ldap_error($ldap->cid));
        if ( ! $r )
            return false;
        
        $other=array('loginShell', 'description');
        $this->save( $other );
        
        //$gui->debuga($this);
        
        // añadir a domain users
        
        $ldap->addUserToGroup($this->uid, LDAP_OU_DUSERS);
        
        // si el rol es profesor añadir a profesores
        if ($this->role == 'teacher') {
            $ldap->addUserToGroup($this->uid, LDAP_OU_TEACHERS);
        }
        // si el rol es administrador añadir a administradores
        if ($this->role == 'admin') {
            $ldap->addUserToGroup($this->uid, LDAP_OU_DADMINS);
            $ldap->addUserToGroup($this->uid, LDAP_OU_ADMINS);
        }
        
        
        // crear home, profiles y aplicar quota
        exec('sudo '.MAXCONTROL.' createhome '.$this->uid.' '.$ldap->getDefaultQuota().' 2>&1', &$output);
        $gui->debuga($output);
        
        $gui->session_info("Usuario añadido correctamente.");
    }

}



class COMPUTER extends BASE {

    var $count='';
    var $cn='';
    var $uid='';
    var $description='';
    var $uidNumber='';
    var $gidNumber='';
    var $loginShell='';    # /bin/false
    var $displayName='';
    var $homeDirectory=''; # /dev/null
    var $objectClass=''; # añadir ieee802Device (macAddress)
                         #        bootableDevice (bootFile, bootParameter)
                         #        ipHost        (ipHostNumber)
    var $sambaSID='';
    
    /* passwords */
    var $gecos='';
    
    
    var $sambaAcctFlags='[W          ]';
    var $sambaNTPassword='';
    var $sambaPwdLastSet='';
    
    // save here the group (AULA) to boot
    var $sambaProfilePath='';  # <=====================
    
    // objectClass=ipHost        (ipHostNumber)
    var $ipHostNumber='';     # IP address
    
    //objectClass=ipNetwork     (ipNetworkNumber)
    var $ipNetworkNumber='';  # red
    var $ipNetmaskNumber='';  # mascara
    
    // objectClass=ieee802Device (macAddress)
    var $macAddress='';
    
    // objectClass=bootableDevice (bootFile, bootParameter)
    var $bootFile='';
    #var $bootParameter=''; # disable, complex syntax, see: http://tools.ietf.org/html/rfc2307
    
    
    function init(){
        $this->exe=new WINEXE($this->hostname());
        return;
    }
    
    function hostname() {
        return str_replace('$', '', $this->uid);
    }
    
    function get_save_dn(){
        return 'uid='.$this->uid.','.LDAP_OU_COMPUTERS;
    }
    
    function load_class_required($varname) {
        if ($varname == 'ipHost')
            return array('ipHostNumber' => $this->ldapdata['ipHostNumber']);
        return null;
    }
    
    function is_restricted($varname) {
        if ($varname == 'ipHostNumber')
            return true;
        return false;
    }
    
    function pre_save() {
        // add objectClass if vars are not empty
        if ( ($this->ipHostNumber != '') && ( ! in_array("ipHost", $this->objectClass) ) ) {
            $this->objectClass[]="ipHost";
        }
        
        if ( ( ($this->ipNetworkNumber != '') || ($this->ipNetmaskNumber != '') ) && 
             ( ! in_array("ipNetwork", $this->objectClass) ) ) {
            $this->objectClass[]="ipNetwork";
        }
        
        if ( ($this->macAddress != '') && ( ! in_array("ieee802Device", $this->objectClass) ) ) {
            $this->objectClass[]="ieee802Device";
        }
        
        if ( ( ($this->bootFile != '') || ($this->bootParameter != '') ) && 
             ( ! in_array("bootableDevice", $this->objectClass) ) ) {
            $this->objectClass[]="bootableDevice";
        }
    }
    
    function action($actionname, $mac){
        global $gui;
        $gui->debug("COMPUTER:action($actionname) ".$this->uid);
        if ( method_exists($this->exe, $actionname) ) {
            $this->exe->hostname=$this->hostname();
            $gui->debug("   COMPUTER:action($actionname) method exists");
            $this->exe->$actionname($mac);
        }
        else {
            $gui->debug("method '$actionname' don't exists");
        }
    }
    
    function getMACIP() {
        global $gui;
        $ip=$this->exe->getIpAddress($this->hostname());
        $gui->debug("ip=$ip");
        $mac=$this->exe->getMacAddress($this->hostname());
        $gui->debug("mac=$mac");
        
        if ($ip != '' && $mac != '') {
            $this->ipHostNumber=$ip;
            $this->macAddress=$mac;
            $this->ldapdata['ipHostNumber']=$ip;
            $this->ldapdata['macAddress']=$mac;
            
            $res=$this->save( array('ipHostNumber', 'macAddress') );
            
            if ($res) {
                $gui->session_info("Equipo '".$this->hostname()."' guardado correctamente.");
                return true;
            }
            else {
                $gui->session_error("Error guardando datos del equipo '".$this->hostname()."', por favor inténtelo de nuevo.");
                return false;
            }
        }
        $gui->session_error("El equipo '".$this->hostname()."' no está encendido o no se pudo resolver su IP.");
    }
}


class AULA extends BASE {
    var $cn='';
    var $gidNumber='';
    var $objectClass='';
    var $description='';
    var $sambaSID='';
    var $displayName='';
    var $memberUid='';
    var $sambaGroupType=''; 
    /*
        sambaGroupType
        > #ifndef USE_UINT_ENUMS
              {
             SID_NAME_USE_NONE=0,
             SID_NAME_USER=1,
             SID_NAME_DOM_GRP=2,   <=== grupos del dominio
             SID_NAME_DOMAIN=3,
             SID_NAME_ALIAS=4,
             SID_NAME_WKN_GRP=5,   <=== privilegios admin (replicator, backup operator...)
             SID_NAME_DELETED=6,
             SID_NAME_INVALID=7,
             SID_NAME_UNKNOWN=8,
             SID_NAME_COMPUTER=9  <= usaremos este para ser aula
             }
        */
    
    function init(){
        return;
    }
    
    function get_num_users() {
        //global $gui;
        //$gui->debug("get_num_users() aula=".$this->cn . "<pre>".print_r($this->ldapdata['memberUid'], true)."</pre>");
        if ( isset($this->ldapdata['memberUid']) )
            return $this->ldapdata['memberUid']['count'];
        return 0;
    }
    
    function get_num_computers() {
        if ( isset($this->cachednumcomputers) )
            return $this->cachednumcomputers;
        $ldap = new LDAP();
        $this->cachednumcomputers=count($ldap->get_macs_from_aula($this->cn));
        return $this->cachednumcomputers;
    }
    
    function get_save_dn(){
        //cn=aula primaria 1,ou=Groups,dc=max-server
        return 'cn='.$this->cn.','.LDAP_OU_GROUPS;
    }
    
    function newMember($username) {
        global $gui;
        $gui->debug("AULA:newMember($username)");
        //$gui->debuga($this);
        if ( ! isset($this->ldapdata['memberUid']) ){
            $this->ldapdata['memberUid']=array();
        }
        $members=$this->ldapdata['memberUid'];
        $members[]=$username;
        unset($members['count']);
        $gui->debuga($members);
        $this->ldapdata['memberUid']=$members;
        $this->memberUid=$members;
        return $this->save(array('memberUid'));
    }
    
    function delMember($username) {
        global $gui;
        $gui->debug("AULA:delMember($username)");
        
        if ( ! isset($this->ldapdata['memberUid']) ){
            $this->ldapdata['memberUid']=array();
        }
        $members=$this->ldapdata['memberUid'];
        unset($members['count']);
        if ( ! in_array($username, $members) ) {
            $gui->debug("delMember($username) not in aula=".$this->cn);
            return true;
        }
        $newmembers=array();
        foreach($members as $member) {
            if ( $member != $username )
                $newmembers[]=$member;
        }
        
        $gui->debuga($newmembers);
        $this->ldapdata['memberUid']=$newmembers;
        return $this->save(array('memberUid'));
    }
}


class LDAP {
        var $hostname = LDAP_HOSTNAME;
        var $basedn = LDAP_BASEDN;
        var $binddn = "";
        var $bindpw = "";
        var $cid = 0; // LDAP Server Connection ID
        var $bid = 0; // LDAP Server Bind ID
        var $error = "";

    function LDAP($binddn = "", $bindpw = "", $hostname = LDAP_HOSTNAME) {
        
        if ($binddn != "")
            $this->binddn = $binddn;
        if ($bindpw != "")
            $this->bindpw = $bindpw;
        if ($hostname != "")
            $this->hostname = $hostname;
        $this->connect();
    }


    function get_error(){
        return $this->error;
    }

    function connect(){
        global $gui;
        
        // activar el debug de libldap
        //ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
        
        if (! @($this->cid=ldap_connect($this->hostname))) {
            $this->error = "Error: No se pudo conectar con el servidor de autenticación.\n".ldap_error($this->cid);
            $gui->debug("==> ".$this->error."<====");
            return false;
        }

        ldap_set_option($this->cid, LDAP_OPT_PROTOCOL_VERSION, 3);
        //$gui->debug("=============> ldap_bind($this->cid, $this->binddn, $this->bindpw)<=================");
        if ( ! @($this->bid=ldap_bind($this->cid, $this->binddn, $this->bindpw)) ) {
            $this->error = "Error: Usuario o contraseña incorrectos.\n".ldap_error($this->cid);
            $gui->debug("===> ".$this->error."<========");
            return false;
        }
        return true;
    }

    function is_connected() {
        if (! $this->cid) 
            return false;
        return true;
    }

    function get_users($filter='*', $group=LDAP_OU_USERS) {
        global $gui;
        if ( $filter == '' )
            $filter='*';
        $users=array();
        $gui->debug("ldap::get_users() (uid='$filter') basedn='$group'");
        $this->search("(uid=$filter)", $basedn=$group);
        while($attrs = $this->fetch())
        {
            $user= new USER($attrs);
            $users[]=$user;
        }
        return $users;
    }

    function get_user($uid='') {
        global $gui;
        
        if ( ! $this->connect() )
            return false;
        
        $filter = "(&(objectClass=posixAccount)(uid=$uid))";
        if (! ($search=ldap_search($this->cid, LDAP_OU_USERS, $filter))) {
            $this->error="Error: búsqueda incorrecta.\n".ldap_error($this->cid);
            return false;
        }
        $number_returned = ldap_count_entries($this->cid, $search);
        if ($number_returned != 1) {
            return false;
        }
        $found=ldap_get_attributes($this->cid, ldap_first_entry($this->cid, $search));
        
        $user = new USER($found);
        return $user;
    }

    function get_user_uids($group=LDAP_OU_USERS) {
        $uids=array();
        $users=$this->get_users($filter='*', $group=$group);
        foreach($users as $user) {
            $uids[]=$user->uid;
        }
        return $uids;
    }

    function get_teachers_uids($filter='*') {
        global $gui;
        if ( $filter == '' )
            $filter='*';
        $teachers=array();
        $gui->debug("ldap::get_teachers_uids() ".LDAP_OU_TEACHERS);
        $this->search("(cn=*)", $basedn=LDAP_OU_TEACHERS);
        
        while($attrs = $this->fetch()) {
            if ( isset($attrs['memberUid']) ) {
                $teachers=$attrs['memberUid'];
                unset($teachers['count']);
            }
        }
        return $teachers;
    }


    function is_teacher($uid='') {
        $teachers=$this->get_teachers_uids();
        foreach ($teachers as $teacher) {
            if ( $uid == $teacher )
                return true;
        }
        return false;
    }

    function is_admin($uid='') {
        global $gui;
        
        if ( ! $this->connect() )
            return false;
        
        $filter = "(cn=Administrators)";
        if (! ($search=ldap_search($this->cid, $this->basedn, $filter))) {
            $this->error="Error: busqueda incorrecta.\n".ldap_error($this->cid);
            return false;
        }
        $number_returned = ldap_count_entries($this->cid, $search);
        if ($number_returned != 1) {
            return false;
        }
        //$found=ldap_get_attributes($this->cid, ldap_first_entry($this->cid, $search));
        //$gui->debug("<pre>".print_r($found, true)." </pre><br>\n");
        
        $attrs = ldap_get_attributes($this->cid, ldap_first_entry($this->cid, $search));
        //$gui->debug("<pre>".print_r($attrs, true)." </pre><br>\n");
        
        if ( array_key_exists("memberUid", $attrs) ) {
            $members=$attrs["memberUid"];
            //$gui->debug("<pre>".print_r($members, true)." </pre><br>\n");
            if ( in_array($uid, $members) ) {
                $gui->debug("ldap::is_admin() user $uid is admin");
                return true;
            }
        }
        $gui->debug("ldap::is_admin() user $uid is NOT admin");
        return false;
    }


    function get_computers($uid='*') {
        global $gui;
        if ( ! $this->connect() )
            return false;
        if ( $uid == '' )
            $uid='*';
        $computers=array();
        $gui->debug("ldap::get_computers() (uid='$uid')".LDAP_OU_COMPUTERS);
        $this->search("(uid=$uid)", $basedn=LDAP_OU_COMPUTERS);
        while($attrs = $this->fetch())
        {
            $computer= new COMPUTER($attrs);
            $computers[]=$computer;
        }
        
        return $computers;
    }

    function get_aulas($aula='') {
        /*
        sambaGroupType
        > #ifndef USE_UINT_ENUMS
              {
             SID_NAME_USE_NONE=0,
             SID_NAME_USER=1,
             SID_NAME_DOM_GRP=2,   <=== grupos del dominio
             SID_NAME_DOMAIN=3,
             SID_NAME_ALIAS=4,
             SID_NAME_WKN_GRP=5,   <=== privilegios admin (replicator, backup operator...)
             SID_NAME_DELETED=6,
             SID_NAME_INVALID=7,
             SID_NAME_UNKNOWN=8,
             SID_NAME_COMPUTER=9  <= usaremos este para ser aula
             }
        */
        global $gui;
        if ( ! $this->connect() )
            return false;
        
        $aulas=array();
        $gui->debug("ldap::get_aulas() (cn='*')".LDAP_OU_GROUPS);
        $this->search("(cn=*)", $basedn=LDAP_OU_GROUPS);
        
        while($attrs = $this->fetch()) {
            //$gui->debug("<pre>".print_r($attrs, true)."</pre>");
            if ( isset($attrs['sambaGroupType']) && ($attrs['sambaGroupType'][0] == 9) ) {
                if ($aula == '') {
                    //$aulas[]=$attrs['cn'][0];
                    $aulas[]=new AULA($attrs);
                    $gui->debug("ldap::get_aulas() ADD aula='".$attrs['cn'][0]."'");
                }
                else {
                    if (preg_match("/$aula/i", $attrs['cn'][0])) {
                        //$aulas[]=$attrs['cn'][0];
                        $aulas[]=new AULA($attrs);
                        $gui->debug("ldap::get_aulas() ADD '$aula' match '".$attrs['cn'][0]."'");
                    }
                    else {
                        $gui->debug("ldap::get_aulas() '$aula' don't match '".$attrs['cn'][0]."'");
                    }
                }
            }
        }
        
        return $aulas;
    }

    function get_aula($aulafilter) {
        global $gui;
        if ( ! $this->connect() )
            return false;
        $gui->debug("ldap::get_aula() (cn='$aulafilter')".LDAP_OU_GROUPS);
        $this->search("(cn=$aulafilter)", $basedn=LDAP_OU_GROUPS);
        while($attrs = $this->fetch()) {
            //$gui->debug("<pre>".print_r($attrs, true)."</pre>");
            if ( isset($attrs['sambaGroupType']) && ($attrs['sambaGroupType'][0] == 9) ) {
                return new AULA($attrs);
            }
        }
        return new AULA();
    }


    function get_teacher_from_aula($aulafilter) {
        global $gui;
        $aula=$this->get_aula($aulafilter);
        // return empty array if aula not found
        $uids=$this->get_teachers_uids();
        $ingroup=array();
        $outgroup=array();
        
        if( $aula->cn=='' ) {
            $gui->debug("aula not found");
            return array();
        }
        
        if ( ! isset($aula->ldapdata['memberUid']) ) {
            $gui->debug("aula without members");
            return array(
                    "ingroup" => $ingroup,
                    "outgroup" => $uids
                    );
        }
        
        foreach($uids as $uid) {
            if( in_array($uid, $aula->ldapdata['memberUid']) ){
                $ingroup[]=$uid;
            }
            else {
                $outgroup[]=$uid;
            }
        }
        
        return array(
                    "ingroup" => $ingroup,
                    "outgroup" => $outgroup
                    );
    }

    function get_macs_from_aula($aula) {
        global $gui;
        if ( ! $this->connect() )
            return false;
        
        $macs=array();
        $gui->debug("ldap::get_macs_from_aula($aula) (uid='*')".LDAP_OU_COMPUTERS);
        $this->search("(uid=*)", $basedn=LDAP_OU_COMPUTERS);
        while($attrs = $this->fetch())
        {
            if ( isset($attrs['sambaProfilePath']) ) {
                if ($aula == $attrs['sambaProfilePath'][0]) {
                    if ( $attrs['macAddress'][0] != '')
                        $macs[]=$attrs['macAddress'][0];
                    else
                        $gui->debug("ldap::get_macs_from_aula() empty MAC for uid='".$attrs['uid'][0]."'");
                }
            }
        }
        
        return $macs;
    }
    
    function get_computers_from_aula($aula) {
        global $gui;
        if ( ! $this->connect() )
            return false;
        
        $computers=array();
        $gui->debug("ldap::get_computers_from_aula($aula) (uid='*')".LDAP_OU_COMPUTERS);
        $this->search("(uid=*)", $basedn=LDAP_OU_COMPUTERS);
        while($attrs = $this->fetch()) {
            if ( isset($attrs['sambaProfilePath']) && ($aula == $attrs['sambaProfilePath'][0]) ) {
                if ( $attrs['uid'][0] != '')
                    $computers[]=new COMPUTER($attrs);
            }
        }
        
        return $computers;
    }

    function lastUID() {
        global $gui;
        $uidNumbers=array();
        $this->search("(objectclass=posixAccount)", $basedn=LDAP_OU_USERS);
        while($attrs = $this->fetch()) {
            $uidNumbers[]=$attrs['uidNumber'][0];
        }
        //$gui->debuga($uidNumbers);
        $gui->debug("LDAP:lastUID() =".max($uidNumbers));
        
        // UID must be > 2000
        if ( max($uidNumbers) < 2000 )
            return 2000;
        return max($uidNumbers);
    }

    function lastGID() {
        /*
        use constant SYSMINUID      => 1900;
        use constant SYSMINGID      => 1900;
        use constant MINUID         => 2000;
        use constant MINGID         => 2000;
        */
        global $gui;
        $gidNumbers=array();
        $this->search("(objectclass=posixGroup)", $basedn=LDAP_OU_GROUPS);
        while($attrs = $this->fetch()) {
            //$gui->debuga($attrs);
            $gidNumbers[]=$attrs['gidNumber'][0];
        }
        //$gui->debuga($gidNumbers);
        $gui->debug("LDAP:lastGID() =".max($gidNumbers));
        
        // GID must be > 2000
        if ( max($gidNumbers) < 2000 )
            return 2000;
        return max($gidNumbers);
    }

    function getGID($gidname) {
        global $gui;
        $gidNumber=-1;
        $this->search("(objectclass=posixGroup)", $basedn=LDAP_OU_GROUPS);
        while($attrs = $this->fetch()) {
            if ( $attrs['cn'][0] == $gidname ) {
                $gidNumber= $attrs['gidNumber'][0];
            }
        }
        
        return $gidNumber;
    }

    
    function additionalPasswords($txtpasswd, $user, $samba=false) {
        $passwd=array();
        
        $realm=strtolower(LDAP_DOMAIN);
        /*
        eBoxLmPassword
        eBoxMd5Password
        eBoxNtPassword
        eBoxSha1Password
        eboxDigestPassword
        eboxRealmPassword
        */
        $passwd['eboxSha1Password']="{SHA}".base64_encode(sha1($txtpasswd, TRUE));
        $passwd['eboxMd5Password']="{MD5}".base64_encode(md5($txtpasswd, TRUE));
        $passwd['eboxLmPassword']=LMhash($txtpasswd);
        $passwd['eboxNtPassword']=NTLMHash($txtpasswd);
        $passwd['eboxDigestPassword']="{MD5}".base64_encode(md5("$user:$realm:$txtpasswd", TRUE));
        $passwd['eboxRealmPassword']="{MD5}".md5("$user:$realm:$txtpasswd");
        if ( $samba ) {
            $passwd['sambaNTPassword']=NTLMHash($txtpasswd);
            $passwd['sambaLMPassword']=LMhash($txtpasswd);
        }
        return $passwd;
    }
    
    function getSID() {
        global $gui;
        $sid='S-1-5-21-3818554400-921237426-3143208535';
        exec('sudo '.MAXCONTROL.' getdomainsid', &$output);
        //$gui->debug("<pre>".print_r($output, true)."</pre>");
        foreach($output as $line) {
            if (preg_match("/SID for domain/i", $line)) {
                //$gui->debug("SID found in line='$line'");
                $words=preg_split('/\s/', $line);
                //$gui->debuga($words);
                //$gui->debug("return element".(count($words)-1));
                $sid=$words[count($words)-1];
            }
        }
        return $sid;
        /*
        /usr/share/perl5/EBox/SambaLdapUser.pm
        # FIXME: Hardcore SID for testing purposes
        #
        return 'S-1-5-21-3818554400-921237426-3143208535';
        
        net getdomainsid
            SID for local machine MAX-SERVER is: S-1-5-21-3818554400-921237426-3143208535
            SID for domain EBOX is: S-1-5-21-3818554400-921237426-3143208535
        */
        
        /*
        # Default values for samba user
        use constant SMBLOGONTIME       => '0';
        use constant SMBLOGOFFTIME      => '2147483647';
        use constant SMBKICKOFFTIME     => '2147483647';
        use constant SMBPWDCANCHANGE    => '0';
        use constant SMBPWDMUSTCHANGE   => '2147483647';
        use constant SMBGROUP           => '513';
        use constant SMBACCTFLAGS       => '[U]';
        use constant SMBACCTFLAGSDISABLED       => '[UD]';
        use constant GECOS              => 'Ebox file sharing user ';
        use constant USERGROUP          => 513;
        use constant DEFAULT_SHELL      => '/bin/false';
        # Home path for users and groups
        use constant BASEPATH           => '/home/samba';
        use constant USERSPATH          => BASEPATH . '/users';
        use constant GROUPSPATH         => BASEPATH . '/groups';
        use constant PROFILESPATH       => BASEPATH . '/profiles';


         _addUserLdapAttrs
         
         my $rid = 2 * $unixuid + 1000;
         my %attrs = (
            changes => [
                    add => [
                        objectClass         => 'sambaSamAccount',
                        %userCommonAttrs,
                        sambaHomePath        => _smbHomes() . $user,
                        sambaPrimaryGroupSID => $sid . '-' . SMBGROUP,
                        sambaLMPassword      => $lm,
                        sambaNTPassword      => $nt,
                        sambaSID             => $sambaSID, # $sid. '-' .  $rid
                        # gecos              => GECOS
                    ],
                    replace => [
                        homeDirectory => BASEPATH . "/users/$user",
                    ]
            ]
        );

        */
        
        /*
        my  $samba = EBox::Global->modInstance('samba');
        $self->_createDir(USERSPATH . "/$user", $unixuid, USERGROUP, '0701');
        $self->_createDir(PROFILESPATH . "/$user", $unixuid, USERGROUP, '0700');
        $self->_createDir(PROFILESPATH . "/$user.V2", $unixuid, USERGROUP, '0700');
        $self->{samba}->setUserQuota($unixuid, $samba->defaultUserQuota());

        */
    }

    function getDefaultQuota() {
        exec('sudo '.MAXCONTROL.' getdefaultquota', &$output);
        //$gui->debug("<pre>".print_r($output, true)."</pre>");
        return $output[0];
    }

    function getGroupMembers($group) {
        $members=array('memberUid' => array());
        if (!$this->connect()) {
            return false;
        }
        
        // search members of group
        $this->search("(cn=*)", $basedn=$group);
        while($attrs = $this->fetch())
        {
            if ( isset($attrs['memberUid'])) {
                $members['memberUid']=$attrs['memberUid'];
                unset($members['memberUid']['count']);
            }
        }
        return $members;
    }

    function addUserToGroup($user, $group) {
        global $gui;
        
        $oldmembers=$this->getGroupMembers($group);
        
        $gui->debuga($oldmembers);
        if ( in_array($user, $oldmembers['memberUid']) ) {
            // user is in group
            return true;
        }
        
        $oldmembers['memberUid'][]=$user;
        $gui->debuga($oldmembers['memberUid']);
        
        // save new members
        $r = ldap_modify($this->cid, $group, $oldmembers );
        $gui->debug("addUserToGroup() result=".ldap_error($this->cid) );
        if ( ! $r)
            return false;
        return true;
    }

    function delUserFromGroup($user, $group) {
        global $gui;
        
        $oldmembers=$this->getGroupMembers($group);
        
        $gui->debuga($oldmembers);
        if ( ! in_array($user, $oldmembers['memberUid']) ) {
            // user is not in group
            return true;
        }
        
        $newmembers=array('memberUid'=>array());
        foreach($oldmembers['memberUid'] as $m) {
            if( $m != $user) {
                $newmembers['memberUid'][]=$m;
            }
        }
        $gui->debuga($newmembers);
        
        // save new members
        $r = ldap_modify($this->cid, $group, $newmembers );
        $gui->debug("delUserFromGroup() result=".ldap_error($this->cid) );
        if ( ! $r)
            return false;
        return true;
    }

    function search($filter, $basedn='')
    {
        $localbasedn=$this->basedn;
        if ($basedn != '')
            $localbasedn=$basedn;
        
        $result = array();
        if (!$this->connect()) {
            return(0);
        }

        $this->sr = ldap_search($this->cid, $localbasedn, $filter);
        $this->error = ldap_error($this->cid);
        $this->resetResult();
        return($this->sr);
    }
    
    function fetch() {
        $att=null;
        if ($this->start == 0) {
            $this->start = 1;
            $this->re = ldap_first_entry($this->cid, $this->sr);
        } 
        else {
            $this->re = ldap_next_entry($this->cid, $this->re);
        }
        if ($this->re) {
            $att = ldap_get_attributes($this->cid, $this->re);
        }
        $this->error = ldap_error($this->cid);
        return($att);
    }

    function resetResult() {
        $this->start = 0;
    }

    function disconnect() {
        ldap_close($this->cid);
    }
}
?>
