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
                echo "var '\$$k' not in BASE<br>\n";
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
            $gui->debug("BASE:save() try to save $k");
            if ($k == 'uid')
                continue;
            
            if ( $this->is_restricted($k) && ! isset($this->$k) ) {
                $gui->debug("BASE:save() $k is restricted");
                continue;
            }
            if ( $this->$k == '' )
                $this->$k=array();
            
            $tmp=array();
            $tmp[$k]=$this->ldapdata[$k];
            //$gui->debug("BASE:save() dn=".$this->get_save_dn()." data=<pre>".print_r($tmp, true)."\nRESULT=".ldap_error($ldap->cid)."</pre>");
            $r = ldap_modify($ldap->cid, $this->get_save_dn(), $tmp );
            $gui->debug("BASE:save() dn=".$this->get_save_dn()." data=<pre>".print_r($tmp, true)."\nRESULT=".ldap_error($ldap->cid)."</pre>");
            if ( !$r) {
                $gui->debug( ldap_error($ldap->cid) );
                $saveok=false;
            }
        }
        $ldap->disconnect();
        return $saveok;
    }
    
    function empty_attr( $attr ) {
        global $gui;
        $saveok=true;
        $ldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        
        // to empty an attribute set empty array()
        $tmp=array( $attr => array() );
        
        $r = ldap_modify($ldap->cid, $this->get_save_dn(), $tmp );
        $gui->debug("BASE:empty_attr() dn=".$this->get_save_dn()." data=<pre>".print_r($tmp, true)."\RESULT=".ldap_error($ldap->cid)."</pre>");
        if (! $r)
            $saveok=false;
        
        
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
        
        /*
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
        */
        
        // add to Domain Users
        $ldap->addUserToGroup($this->uid, LDAP_OU_DUSERS);
        
        
        if ($role == '') {
            // usuario sin permisos
            // debe estar en el grupo Domain Users y __USERS__
            
            //quitar de profesores
            $ldap->delUserFromGroup($this->uid, LDAP_OU_TEACHERS);
            
            
            //quitar de las aulas
            $aulas=$ldap->get_aulas();
            foreach ($aulas as $aula){
                $aula->delMember($this->uid);
            }
            // quitar de administradores
            $ldap->delUserFromGroup($this->uid, LDAP_OU_DADMINS);
            $ldap->delUserFromGroup($this->uid, LDAP_OU_ADMINS);
            return;
        }
        elseif ($role == 'teacher') {
            // debe estar en el grupo LDAP_OU_TEACHERS Domain Users y __USERS__
            $ldap->addUserToGroup($this->uid, LDAP_OU_TEACHERS);
            
            // quitar de administradores
            $ldap->delUserFromGroup($this->uid, LDAP_OU_DADMINS);
            $ldap->delUserFromGroup($this->uid, LDAP_OU_ADMINS);
            return;
        }
        elseif ($role == 'admin') {
            // quitar de profesores
            $ldap->delUserFromGroup($this->uid, LDAP_OU_TEACHERS);
            
            //quitar de profesores
            $aulas=$ldap->get_aulas();
            foreach ($aulas as $aula){
                $aula->delMember($this->uid);
            }
            
            // debe estar en el grupo Domain Administrator y Administrator
            $ldap->addUserToGroup($this->uid, LDAP_OU_DADMINS);
            $ldap->addUserToGroup($this->uid, LDAP_OU_ADMINS);
            return;
        }
    }
    
    function update_password($new) {
        global $gui;
        $ldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        $newpassword=$ldap->additionalPasswords($new, $this->uid, $samba=true);
        $newpassword['userPassword']="{SHA}".base64_encode(sha1($new, TRUE));
        $gui->debuga($newpassword);
        $r = ldap_modify($ldap->cid, $this->get_save_dn() , $newpassword );
        
        if ($r) {
            $gui->session_error("Contraseñas actualizadas: ".ldap_error($ldap->cid));
            $ldap->disconnect();
            return true;
        }
        else {
            $gui->session_error("Error cambiando contraseñas: ".ldap_error($ldap->cid));
            $ldap->disconnect();
            return false;
        }
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
        return true;
    }
    
    function delUser($delprofile='') {
        global $gui;
        //$gui->add( "<pre>". print_r($this->show(), true) . "</pre>" );
        // borrarlo de todos los grupos
        //     Administrator
        //     Domain Admins
        //     Domain Users
        //     Teachers
        
        $ldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        
        if ($delprofile == '1') {
            $ldap->deleteProfile($this->uid);
        }
        
        
        // borrar de las aulas
        $aulas=$ldap->get_aulas();
        foreach($aulas as $aula) {
            $aula->delMember($this->uid);
        }
        
        // delete from Teachers
        $ldap->delUserFromGroup($this->uid, LDAP_OU_TEACHERS);
        
        // delete from admins
        $ldap->delUserFromGroup($this->uid, LDAP_OU_ADMINS);
        $ldap->delUserFromGroup($this->uid, LDAP_OU_DADMINS);
        
        // delete from Domain Users
        $ldap->delUserFromGroup($this->uid, LDAP_OU_DUSERS);
        
        // borrarlo de LDAP_OU_USERS
        $gui->debug("ldap_delete ( $ldap->cid , 'uid=".$this->uid.",".LDAP_OU_USERS ."')");
        $r=ldap_delete ( $ldap->cid , "uid=".$this->uid.",".LDAP_OU_USERS );
        if ( ! $r)
            return false;
        return true;
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
    //var $ipNetworkNumber='';  # red
    //var $ipNetmaskNumber='';  # mascara
    
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
        switch($varname) {
            case "ipHostNumber": return true; break;
            case "ipNetworkNumber": return true; break;
            case "ipNetmaskNumber": return true; break;
            default: return false;
        }
    }
    
    function pre_save() {
        // add objectClass if vars are not empty
        if ( ($this->ipHostNumber != '') && ( ! in_array("ipHost", $this->objectClass) ) ) {
            $this->objectClass[]="ipHost";
        }
        
#        if ( ( ($this->ipNetworkNumber != '') || ($this->ipNetmaskNumber != '') ) && 
#             ( ! in_array("ipNetwork", $this->objectClass) ) ) {
#            $this->objectClass[]="ipNetwork";
#        }
        
        if ( ($this->macAddress != '') && ( ! in_array("ieee802Device", $this->objectClass) ) ) {
            $this->objectClass[]="ieee802Device";
        }
        
        if ( ( ($this->bootFile != '') /*|| ($this->bootParameter != '')*/ ) && 
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
    
    function genPXELinux() {
        global $gui;
        //bin/max-control pxe --genpxelinux
        exec("sudo ".MAXCONTROL." pxe --genpxelinux 2>&1", &$output);
        return;
    }
    
    function resetBoot() {
        global $gui;
        $gui->debug("COMPUTER:resetBoot() bootFile=".$this->bootFile);
        // reset boot file in this computer (empty)
        $this->ldapdata["bootFile"] = array();
        $this->bootFile = array();
        
        $res = $this->save( array('bootFile') );
        
        exec("sudo ".MAXCONTROL." pxe --delete='".$this->macAddress."' 2>&1", &$output);
        $gui->debuga($output);
        
        $this->genPXELinux();
        $this->boot('aula');
        return $res;
    }
    
    function boot($conffile) {
        /*
        $conffile must be windows, max, backhardding or aula name
        */
        
        global $gui;
        
        if ( ! isset($this->macAddress) ) {
            if( ! $this->getMACIP() ) {
                $gui->session_error("El equipo '".$this->hostname()."' no tiene configurada su dirección MAC y está apagado.");
                return false;
            }
        }
        
        if ( $this->macAddress == '' ) {
            $gui->session_error("El equipo '".$this->hostname()."' no tiene configurada su dirección MAC.");
            return false;
        }
        
        if ($conffile == '') {
            $conffile="default";
        }
        
        /****************************/
        if ( $conffile == 'aula') {
            if ( isset($this->sambaProfilePath) && $this->sambaProfilePath != '' ) {
                $conffile = preg_replace('/\s+/', '_', $this->sambaProfilePath);
            }
            else {
                $conffile='default';
            }
        }
        
        //quitar espacios del aula por '_'
        $conffile=preg_replace('/\s+/', '_', $conffile);
        
        $mac=$this->macAddress;
        
        //max-control pxe --boot=max.menu --mac=08:00:27:96:0D:E6
        exec("sudo ".MAXCONTROL." pxe --boot='$conffile' --mac='$mac' ", &$output);
        $gui->debug("LDAP:boot($conffile, $mac)<pre>".print_r($output, true)."</pre>");
        if ( ! isset($result[0]) ) {
            $gui->session_info("Arranque PXE de '".$this->hostname()."' actualizado.");
        }
        return true;
    }
    
    function getBoot() {
        global $gui;
        //bin/max-control pxe --getboot=08:00:27:96:0D:E6
        exec("sudo ".MAXCONTROL." pxe --getboot='".$this->macAddress."' ", &$output);
        //$gui->debug("COMPUTER:getBoot(".$this->macAddress.")<pre>".print_r($output, true)."</pre>");
        if ( ! isset($output[0]) ) {
            $gui->session_error("No se puedo leer el modo de arranque de '".$this->hostname()."'.");
            return 'default';
        }
        return $output[0];
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
    
    function safecn() {
        return preg_replace('/\s+/', '_', $this->cn);
    }
    
    function get_users() {
        $users=array();
        if ( isset($this->ldapdata['memberUid']) ) {
            $users=$this->ldapdata['memberUid'];
            unset ($users['count']);
        }
        return $users;
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
    
    
    function getBoot() {
        global $gui;
        //bin/max-control pxe --getbootaula='aula primaria 1'
        exec("sudo ".MAXCONTROL." pxe --getbootaula='".$this->safecn()."' ", &$output);
        //$gui->debug("LDAP:getBoot(".$this->safecn().")<pre>".print_r($output, true)."</pre>");
        if ( ! isset($output[0]) ) {
            $gui->session_error("No se puedo leer el modo de arranque de '".$this->safecn()."'.");
            return 'default';
        }
        return $output[0];
    }
    
    
    function genPXELinux() {
        global $gui;
        //bin/max-control pxe --genpxelinux
        exec("sudo ".MAXCONTROL." pxe --genpxelinux 2>&1", &$output);
        return;
    }
    
    function boot($conffile) {
        /*
        $conffile must be windows, max, backhardding or aula name
        */
        
        global $gui;
        $aula=$this->cn;
        
        //max-control pxe --aula=aula_primaria_1 --boot=windows
        exec("sudo ".MAXCONTROL." pxe --boot='$conffile' --aula='".$this->safecn()."' ", &$output);
        $gui->debug("AULA:boot($conffile, ".$this->safecn().")<pre>".print_r($output, true)."</pre>");
        if ( ! isset($result[0]) ) {
            $gui->session_info("Arranque de '".$this->cn."' actualizado.");
        }
        return true;
    }
    
    function newAula() {
        global $gui;
        
        $ldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        
        $this->displayName=$this->cn;
        $this->sambaGroupType=9;
        $this->objectClass=array('posixGroup', 'sambaGroupMapping', 'eboxGroup');
        
        
        $this->gidNumber=$ldap->lastGID() + 1 ;
        $rid= 2 * $this->gidNumber + 1001;
        $this->sambaSID=$ldap->getSID(). "-" . $rid;
        
        $this->memberUid=array();
        
        $gui->debuga($this->show());
        
        $init=array(
            "cn" => $this->cn,
            "gidNumber" => $this->gidNumber,
            "objectClass" => array('posixGroup'),
                    );
        $gui->debuga($init);
        $r=ldap_add($ldap->cid, "cn=".$this->cn.",".LDAP_OU_GROUPS, $init);
        
        $gui->debug("AULA:newAula() dn=".$this->get_save_dn()." data=<pre>".print_r($init, true)."\nRESULT=".ldap_error($ldap->cid)."</pre>");
        if ( ! $r ) {
            $gui->session_error("No se pudo añadir el aula '".$this->cn."'</br>Error:".ldap_error($ldap->cid));
            $ldap->disconnect();
            return false;
        }
        
        $init=array(
            "displayName" => $this->displayName,
            "gidNumber" => $this->gidNumber,
            "sambaSID" => $this->sambaSID,
            "sambaGroupType" => $this->sambaGroupType,
            #"memberUID" => $this->memberUid,
            "objectClass" => array('posixGroup', 'sambaGroupMapping', 'eboxGroup'),
                    );
        $gui->debuga($init);
        $r=ldap_modify($ldap->cid, "cn=".$this->cn.",".LDAP_OU_GROUPS, $init);
        
        $gui->debug("BASE:save() dn=".$this->get_save_dn()." data=<pre>".print_r($init, true)."\nRESULT=".ldap_error($ldap->cid)."</pre>");
        if ( ! $r ) {
            $gui->session_error("No se pudieron modificar los atributos del grupo '".$this->cn."'</br>Error:".ldap_error($ldap->cid));
            $ldap->disconnect();
            return false;
        }
        
        $this->genPXELinux();
        $this->boot('default');
        
        $ldap->disconnect();
        
        return true;
    }
    
    
    
    function delAula() {
        global $gui;
        $res=false;
        
        $ldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        
        
        // borrarlo de LDAP_OU_GROUPS
        $gui->debug("ldap_delete ( $ldap->cid , 'cn=".$this->cn.",".LDAP_OU_GROUPS ."')");
        $r=ldap_delete ( $ldap->cid , "cn=".$this->cn.",".LDAP_OU_GROUPS );
        if ( ! $r)
            $res=false;
        $res=true;
        
        $this->genPXELinux();
        
        
        return $res;
    }
}

class GROUP extends BASE {
    var $cn='';
    var $description='';
    var $displayName='';
    var $gidNumber='';
    
    var $objectClass='';
    var $sambaSID='';
    
    var $memberUid='';
    var $sambaGroupType=''; 
    
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
    
    function get_users() {
        $users=array();
        if ( isset($this->ldapdata['memberUid']) ) {
            $users=$this->ldapdata['memberUid'];
            unset ($users['count']);
        }
        return $users;
    }
    
    function get_save_dn(){
        //cn=aula primaria 1,ou=Groups,dc=max-server
        return 'cn='.$this->cn.','.LDAP_OU_GROUPS;
    }
    
    function newMember($username) {
        global $gui;
        $gui->debug("GROUP:newMember($username)");
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
        $res = $this->save(array('memberUid'));
        $ldap=new LDAP();
        $ldap->updateLogonShares();
        return $res;
    }
    
    function delMember($username) {
        global $gui;
        $gui->debug("GROUP:delMember($username)");
        
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
        $res = $this->save(array('memberUid'));
        $ldap=new LDAP();
        $ldap->updateLogonShares();
        return $res;
    }
    
    function delGroup($delprofile='') {
        global $gui;
        
        
        $ldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        
        if ($delprofile == '1') {
            $ldap->deleteGroupProfile($this->cn);
        }
        
        $ldap->updateLogonShares();
        
        // borrarlo de LDAP_OU_GROUPS
        $gui->debug("ldap_delete ( $ldap->cid , 'cn=".$this->cn.",".LDAP_OU_GROUPS ."')");
        $r=ldap_delete ( $ldap->cid , "cn=".$this->cn.",".LDAP_OU_GROUPS );
        if ( ! $r)
            return false;
        return true;
    }
    
    function addGroup() {
    /*
[grupoprueba]
 comment = "grupoprueba share directory"
 path = /home/samba/groups/grupoprueba
 valid users = @"grupoprueba"
 force group = "grupoprueba"
 force create mode = 0660
 force directory mode = 0660
 printable = No
 read only = No
 browseable = Yes
# FIXME: Removed for samba 3.4.3, add again when a new version fixes it
#% my $objects = 'full_audit';
 vfs objects =  recycle
 recycle: versions = Yes
 recycle: repository = RecycleBin
 recycle: keeptree = Yes
 recycle: excludedir = /tmp|/var/tmp
 recycle: directory_mode = 0700
    */
    }
    
    function newGroup($createshared) {
        global $gui;
        
        $ldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        
        $this->displayName=$this->cn;
        $this->sambaGroupType=2;
        $this->objectClass=array('posixGroup', 'sambaGroupMapping', 'eboxGroup');
        
        
        $this->gidNumber=$ldap->lastGID() + 1 ;
        $rid= 2 * $this->gidNumber + 1001;
        $this->sambaSID=$ldap->getSID(). "-" . $rid;
        
        $this->memberUid=array();
        
        $gui->debuga($this->show());
        
        $init=array(
            "cn" => $this->cn,
            "gidNumber" => $this->gidNumber,
            "objectClass" => array('posixGroup'),
                    );
        $gui->debuga($init);
        $r=ldap_add($ldap->cid, "cn=".$this->cn.",".LDAP_OU_GROUPS, $init);
        
        $gui->debug("BASE:save() dn=".$this->get_save_dn()." data=<pre>".print_r($init, true)."\nRESULT=".ldap_error($ldap->cid)."</pre>");
        if ( ! $r ) {
            $gui->session_error("No se pudo añadir el grupo '".$this->cn."'</br>Error:".ldap_error($ldap->cid));
            $ldap->disconnect();
            return false;
        }
        
        $init=array(
            "displayName" => $this->displayName,
            "gidNumber" => $this->gidNumber,
            "sambaSID" => $this->sambaSID,
            "sambaGroupType" => $this->sambaGroupType,
            #"memberUID" => $this->memberUid,
            "objectClass" => array('posixGroup', 'sambaGroupMapping', 'eboxGroup'),
                    );
        $gui->debuga($init);
        $r=ldap_modify($ldap->cid, "cn=".$this->cn.",".LDAP_OU_GROUPS, $init);
        
        $gui->debug("BASE:save() dn=".$this->get_save_dn()." data=<pre>".print_r($init, true)."\nRESULT=".ldap_error($ldap->cid)."</pre>");
        if ( ! $r ) {
            $gui->session_error("No se pudieron modificar los atributos del grupo '".$this->cn."'</br>Error:".ldap_error($ldap->cid));
            $ldap->disconnect();
            return false;
        }
        
        if ($createshared == '1') {
            $ldap->addGroupProfile($this->cn);
        }
        
        $ldap->updateLogonShares();
        
        $ldap->disconnect();
        
        return true;
    }
}

class ISO extends BASE{
    var $filename='';
    var $size='';
    var $volumeid='';
    
    function save() {
        return;
    }
    
    function init(){
        return;
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
                    // remove '*' from $aula
                    $aula=str_replace('*', '', $aula);
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

    function get_computers_in_and_not_aula($aula) {
        global $gui;
        $allequipos=$this->get_computers();
        $equipos=$this->get_computers_from_aula($aula);
        
        $all=array('ingroup'=>array(), 'outgroup'=>array());
        
        foreach($allequipos as $e) {
            $found=false;
            foreach( $equipos as $a) {
                if( $e->hostname() == $a->hostname())
                    $found=true;
            }
            if ($found)
                $all['ingroup'][]=$e->hostname();
            else {
                // para ser de outgroup no deben tener aula
                if ( $e->sambaProfilePath == '')
                    $all['outgroup'][]=$e->hostname();
            }
        }
        return $all;
    }


    function get_group($cn) {
        $groups=$this->get_groups($cn);
        if ( isset($groups[0]) )
            return true;
        return false;
    }

    function get_groups($groupfilter='*') {
        global $gui;
        if ( ! $this->connect() )
            return false;
        if ( $groupfilter == '' )
            $groupfilter='*';
        
        $groups=array();
        $gui->debug("ldap::get_groups() (cn='$groupfilter')".LDAP_OU_GROUPS);
        $this->search("(cn=$groupfilter)", $basedn=LDAP_OU_GROUPS);
        while($attrs = $this->fetch()) {
            //$gui->debug("<pre>".print_r($attrs, true)."</pre>");
            if ( isset($attrs['sambaGroupType']) && 
                 ($attrs['sambaGroupType'][0] == 2) &&
                 ($attrs['gidNumber'][0] >= 2000) &&
                 ($attrs['cn'][0] != TEACHERS)) {
                $groups[]=new GROUP($attrs);
            }
        }
        return $groups;
    }

    function get_members_in_and_not_group($groupfilter) {
        /*
        ingroup => memberUID
        outgroup => allusers - memberUID
        */
        global $gui;
        $allusers=$this->get_user_uids();
        $group=$this->get_groups($groupfilter);
        
        $all=array('ingroup'=>array(), 'outgroup'=>array());
        
        foreach($allusers as $e) {
            $found=false;
            if ( in_array($e, $group[0]->get_users() ) )
                $all['ingroup'][]=$e;
            
            else
                $all['outgroup'][]=$e;
        }
        return $all;
    }

    function lastUID() {
        global $gui;
        $uidNumbers=array();
        $this->search("(objectclass=posixAccount)", $basedn=LDAP_BASEDN);
        while($attrs = $this->fetch()) {
            //$gui->debug("uid=".$attrs['uid'][0]." uidNumber=".$attrs['uidNumber'][0]);
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
        $this->search("(objectclass=posixGroup)", $basedn=LDAP_BASEDN);
        while($attrs = $this->fetch()) {
            //$gui->debug("uid=".$attrs['cn'][0]." uidNumber=".$attrs['gidNumber'][0]);
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
        global $gui;
        exec('sudo '.MAXCONTROL.' getdefaultquota', &$output);
        //$gui->debug("<pre>".print_r($output, true)."</pre>");
        return $output[0];
    }

    function deleteProfile($uid) {
        global $gui;
        exec("sudo ".MAXCONTROL." deleteprofile '$uid'", &$output);
        $gui->debug("LDAP:deleteProfile($uid)<pre>".print_r($output, true)."</pre>");
        return $output[0];
    }

    function deleteGroupProfile($group) {
        global $gui;
        exec("sudo ".MAXCONTROL." deletegroup '$group'", &$output);
        $gui->debug("LDAP:deleteGroupProfile($group)<pre>".print_r($output, true)."</pre>");
        return $output[0];
    }

    function addGroupProfile($group) {
        global $gui;
        exec("sudo ".MAXCONTROL." addgroup '$group'", &$output);
        $gui->debug("LDAP:addGroupProfile($group)<pre>".print_r($output, true)."</pre>");
        return $output[0];
    }

    function updateLogonShares() {
        /*
        This method call script to generate /home/samba/netlogon/shares.kix 
        to be loaded by logon.kix and mount shares that user is in.
        */
        global $gui;
        exec("sudo ".MAXCONTROL." genlogonshares '".LDAP_OU_GROUPS."'", &$output);
        $gui->debug("LDAP:updateLogonShares()<pre>".print_r($output, true)."</pre>");
        return;
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
        
        if ( in_array($user, $oldmembers['memberUid']) ) {
            // user is in group
            $gui->debug("addUserToGroup(user=$user, group=$group) user is in group, not adding" );
            return true;
        }
        
        $gui->debuga($oldmembers);
        
        $oldmembers['memberUid'][]=$user;
        $gui->debuga($oldmembers['memberUid']);
        
        // save new members
        $r = ldap_modify($this->cid, $group, $oldmembers );
        $gui->debug("addUserToGroup(user=$user, group=$group) result=".ldap_error($this->cid) );
        if ( ! $r)
            return false;
        return true;
    }

    function delUserFromGroup($user, $group) {
        global $gui;
        
        $oldmembers=$this->getGroupMembers($group);
        
        if ( ! in_array($user, $oldmembers['memberUid']) ) {
            // user is not in group
            $gui->debug("delUserFromGroup(user=$user, group=$group) user not in group");
            return true;
        }
        
        $gui->debuga($oldmembers);
        
        $newmembers=array('memberUid'=>array());
        foreach($oldmembers['memberUid'] as $m) {
            if( $m != $user) {
                $newmembers['memberUid'][]=$m;
            }
        }
        $gui->debuga($newmembers);
        
        // save new members
        $r = ldap_modify($this->cid, $group, $newmembers );
        $gui->debug("delUserFromGroup(user=$user, group=$group) result=".ldap_error($this->cid) );
        if ( ! $r)
            return false;
        return true;
    }

    function readMenu($fmenu) {
        global $gui;
        $info="";
        $file_handle = fopen($fmenu, 'r');
        while (!feof($file_handle) ) {
            $line_of_text = fgets($file_handle);
            if (preg_match("/menu label/i", $line_of_text)) {
                $parts = preg_split ("/\s+/", $line_of_text);
                //$gui->debuga($parts);
                $info=implode(" ", array_slice($parts, 3, 7) );
            }
        }
        fclose($file_handle);
        return $info;
    }

    function getBootMenus($aula=False) {
        $menus=array();
        foreach (glob(PXELINUXCFG ."*.menu") as $filename) {
            //echo "$filename size " . filesize($filename) . "\n";
            $menus[basename($filename, '.menu')]=$this->readMenu($filename);
        }
        if ($aula)
            $menus['aula']="Arranque como el aula";
        return $menus;
    }

    function getISOS($filter='') {
        global $gui;
        $isos=array();
        exec("sudo ".MAXCONTROL." isos --getisos", &$output);
        $gui->debug("LDAP:getISOS()<pre>".print_r($output, true)."</pre>");
        foreach($output as $iso) {
            /* test.iso|4.00 MB|CDROM */
            //$gui->debuga($iso);
            list ($filename, $size, $volumeid)=split('\|', $iso);
            if ($filter != '') {
                if (! preg_match("/$filter/i", $filename)) {
                    continue;
                }
            }
            $data=array("filename"=>$filename, 
                        "size"=>$size, 
                        "volumeid"=> $volumeid);
            //$gui->debuga($data);
            $isos[]= new ISO( $data );
        }
        return $isos;
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
