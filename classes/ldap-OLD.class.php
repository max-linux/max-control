<?php
if(DEBUG)
    error_reporting(E_ALL);
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
global $sort_opts;

class BASE {
    var $ldapdata=array();
    var $errortxt='';
    
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
        $save_ldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        
        
        
        $new_objects=@array_diff($this->objectClass, $this->ldapdata['objectClass']);
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
                $r = ldap_modify($save_ldap->cid, $this->get_save_dn(), $obj );
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
            //$gui->debug("BASE:save() dn=".$this->get_save_dn()." data=<pre>".print_r($tmp, true)."\nRESULT=".ldap_error($save_ldap->cid)."</pre>");
            if (DEBUG)
                $r = ldap_modify($save_ldap->cid, $this->get_save_dn(), $tmp );
            else
                $r = @ldap_modify($save_ldap->cid, $this->get_save_dn(), $tmp );
            $gui->debug("BASE:save() dn=".$this->get_save_dn()." data=<pre>".print_r($tmp, true)."\nRESULT=".ldap_error($save_ldap->cid)."</pre>");
            if ( !$r) {
                $gui->debug( ldap_error($save_ldap->cid) );
                $saveok=false;
            }
        }
        $save_ldap->disconnect('BASE::save()');
        return $saveok;
    }
    
    function empty_attr( $attr ) {
        global $gui;
        $saveok=true;
        $empty_ldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        
        // to empty an attribute set empty array()
        $tmp=array( $attr => array() );
        
        $r = ldap_modify($empty_ldap->cid, $this->get_save_dn(), $tmp );
        $gui->debug("BASE:empty_attr() dn=".$this->get_save_dn()." data=<pre>".print_r($tmp, true)."\RESULT=".ldap_error($empty_ldap->cid)."</pre>");
        if (! $r)
            $saveok=false;
        
        
        $empty_ldap->disconnect('BASE::empty_attr()');
        return $saveok;
    }
}


/***********************************************************************/
class USER extends BASE {
    var $cn='';
    var $uid='';
    var $sn='';
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
    /*
    var $plainPassword='';
    var $userPassword='';
    var $eboxSha1Password='';
    var $eboxMd5Password='';
    var $eboxLmPassword='';
    var $eboxNtPassword='';
    var $eboxDigestPassword='';
    var $eboxRealmPassword='';
    var $sambaNTPassword='';
    var $sambaLMPassword='';
    */
    
    /* passwords expire */
    /*
    var $sambaPwdCanChange=0;
    var $sambaLogonTime=0;
    var $sambaLogoffTime=2147483647;
    
    var $sambaPwdMustChange=2147483647;
    var $sambaPwdLastSet='';
    var $sambaPasswordHistory='00000000000000000000000000000000000000000000000000000000';
    */
    
    var $sambaAcctFlags='[U]';
    var $sambaKickoffTime=2147483647;
    
    var $role='unset';
    var $usedSize=0;
    
    function init(){
        $this->usedSize=$this->getNumericQuota();
        return;
    }
    
    function get_save_dn(){
        return 'uid='.$this->uid.','.LDAP_OU_USERS;
    }
    
    function get_role() {
        //global $gui;
        //$gui->debug("USER:get_role() ='".$this->role."'");
        if ($this->role != 'unset')
            return $this->role;
        
        /* Can't use global $ldap here */
        $ldap = new LDAP();
        if ( $ldap->is_tic($this->uid)) {
            $this->role="tic";
        }
        elseif ( $ldap->is_admin($this->uid)) {
            $this->role="admin";
        }
        elseif ( $ldap->is_teacher($this->uid) ) {
            $this->role="teacher";
        }
        else {
            $this->role="";
        }
        //$ldap->disconnect('USER:get_role()');
        return $this->role;
    }
    
    function is_role($role) {
        if($role == 'alumno')
            $role='';
        return $this->get_role() == $role;
    }
    
    function reQuota(){
        /*
        Call max-control 
        */
        global $gui;
        if ( $this->plainPassword != '' ){
            /* don't run reQuota in importing a lot of users */
            $gui->debug("reQuota disabled");
            return;
        }
        
        global $ldap;
        $cmd='sudo '.MAXCONTROL.' requota '.$this->uid.' '.$ldap->getDefaultQuota().' 2>&1';
        $gui->debug("reQuota(cmd='$cmd')");
        exec($cmd, $output);
        
        $gui->debuga($output);
        
        /* launch rechache in background */
        $cmd='sudo '.MAXCONTROL.' recache > /dev/null 2>&1 &';
        $gui->debug($cmd);
        pclose(popen($cmd, "r"));
    }
    
    function set_role($role) {
        global $gui;
        $gui->debug("USER:set_role('$role')");
        $sldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        
        // add to Domain Users
        $sldap->addUserToGroup($this->uid, LDAP_OU_DUSERS);
        
        
        if ($role == '') {
            // usuario sin permisos
            // debe estar en el grupo Domain Users y __USERS__
            
            //quitar de profesores
            $sldap->delUserFromGroup($this->uid, LDAP_OU_TEACHERS);
            
            
            //quitar de las aulas
            $aulas=$sldap->get_aulas();
            foreach ($aulas as $aula){
                $aula->delMember($this->uid);
            }
            // quitar de administradores
            $sldap->delUserFromGroup($this->uid, LDAP_OU_DADMINS);
            $sldap->delUserFromGroup($this->uid, LDAP_OU_ADMINS);
            $sldap->delUserFromGroup($this->uid, LDAP_OU_TICS);
            $sldap->delUserFromGroup($this->uid, LDAP_OU_INSTALLATORS);
            $this->reQuota();
            return;
        }
        elseif ($role == 'tic') {
            // debe estar en el grupo LDAP_OU_TIC Domain Users y __USERS__
            $sldap->addUserToGroup($this->uid, LDAP_OU_TICS);
            
            // meter en administradores
            $sldap->addUserToGroup($this->uid, LDAP_OU_DADMINS);
            $sldap->addUserToGroup($this->uid, LDAP_OU_ADMINS);
            $sldap->delUserFromGroup($this->uid, LDAP_OU_INSTALLATORS);
            $this->reQuota();
            return;
        }
        elseif ($role == 'teacher') {
            // debe estar en el grupo LDAP_OU_TEACHERS Domain Users y __USERS__
            $sldap->addUserToGroup($this->uid, LDAP_OU_TEACHERS);
            
            // quitar de administradores
            $sldap->delUserFromGroup($this->uid, LDAP_OU_DADMINS);
            $sldap->delUserFromGroup($this->uid, LDAP_OU_ADMINS);
            $sldap->delUserFromGroup($this->uid, LDAP_OU_TICS);
            $sldap->delUserFromGroup($this->uid, LDAP_OU_INSTALLATORS);
            $this->reQuota();
            return;
        }
        elseif ($role == 'admin') {
            // quitar de profesores
            $sldap->delUserFromGroup($this->uid, LDAP_OU_TEACHERS);
            // quitar de TICS
            $sldap->delUserFromGroup($this->uid, LDAP_OU_TICS);
            
            //quitar de aulas
            $aulas=$sldap->get_aulas();
            foreach ($aulas as $aula){
                $aula->delMember($this->uid);
            }
            
            // debe estar en el grupo Domain Administrator y Administrator
            $sldap->addUserToGroup($this->uid, LDAP_OU_DADMINS);
            $sldap->addUserToGroup($this->uid, LDAP_OU_ADMINS);
            $sldap->addUserToGroup($this->uid, LDAP_OU_INSTALLATORS);
            $this->reQuota();
            return;
        }
    }
    
    function update_password($new) {
        global $gui;
        $uldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        $newpassword=$uldap->additionalPasswords($new, $this->uid, $samba=true);
        $newpassword['userPassword']="{SHA}".base64_encode(sha1($new, TRUE));
        $gui->debuga($newpassword);
        $r = ldap_modify($uldap->cid, $this->get_save_dn() , $newpassword );
        
        if ($r) {
            $gui->session_info("Contraseña actualizada.");
            $uldap->disconnect('USER::update_password()');
            return true;
        }
        else {
            $gui->session_error("Error cambiando contraseñas: ".ldap_error($uldap->cid));
            $uldap->disconnect('USER::update_password()');
            return false;
        }
    }
    
    function newUser() {
        global $gui;
        
        $nldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        
        if ( $nldap->get_user($this->uid) ) {
            $gui->session_error("El usuario '".$this->uid."' ya existe.");
            return false;
        }
        
        $password=$this->plainPassword;
        if($password == '') {
            $password=leer_datos('password');
        }
        
        //$this->cn=$this->uid. " ".$this->sn;
        
        $this->uidNumber=$nldap->lastUID() +1;
        $this->gidNumber=$nldap->getGID('__USERS__');
        
        
        $rid = (2 * $this->uidNumber) + 1000;
        
        $this->sambaSID=$nldap->getSID()."-$rid";
        $this->sambaPrimaryGroupSID=$nldap->getSID()."-". $nldap->getGID('Domain Users');
        
        $this->homeDirectory=HOMES . $this->uid;
        $this->sambaHomePath=SAMBA_HOMES . $this->uid;
        $this->sambaProfilePath=SAMBA_PROFILES . $this->uid;
        
        $this->objectClass = array('inetOrgPerson', 'posixAccount', 'passwordHolder', 'sambaSamAccount');
        $additionalPasswords=$nldap->additionalPasswords($password , $this->uid, $samba=true);
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
            "userPassword" => "{SHA}".base64_encode(sha1($password, TRUE)),
            "objectClass" => array('inetOrgPerson', 'posixAccount'),
                    );
        //$gui->debuga($init);
        $r=ldap_add($nldap->cid, "uid=".$this->uid.",".LDAP_OU_USERS, $init);
        
        //$gui->debug(ldap_error($nldap->cid));
        
        
        if ( ! $r ) {
            //$gui->session_error("No se ha podido añadir el usuario, compruebe todos los campos.");
            $gui->debuga($init);
            $this->errortxt=ldap_error($nldap->cid);
            return false;
        }
        
        //$gui->debug("INIT DONE save rest");
        $this->ldapdata=$data;
        
        // save passwords
        $init=array(
            "objectClass" => array('inetOrgPerson', 'posixAccount', 'passwordHolder'),
                    );
        $init=array_merge($init, $nldap->additionalPasswords($password , $this->uid, $samba=false));
        //$gui->debuga($init);
        $r=ldap_modify($nldap->cid, "uid=".$this->uid.",".LDAP_OU_USERS, $init);
        
        $gui->debug(ldap_error($nldap->cid));
        if ( ! $r )
            return false;
        //$gui->debug("PASSWORDS DONE save rest");
        
        
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
        //$gui->debuga($init);
        $r=ldap_modify($nldap->cid, "uid=".$this->uid.",".LDAP_OU_USERS, $init);
        
        //$gui->debug(ldap_error($nldap->cid));
        if ( ! $r )
            return false;
        
        if ($this->description == ''){
            $this->description=array();
            $this->ldapdata['description']=array();
        }
        
        $other=array('loginShell', 'description');
        $this->save( $other );
        
        //$gui->debuga($this);
        
        // añadir a domain users
        $nldap->addUserToGroup($this->uid, LDAP_OU_DUSERS);
        $this->set_role($this->role);
        
        // crear home, profiles y aplicar quota en background
        $cmd='sudo '.MAXCONTROL.' createhome '.$this->uid.' '.$nldap->getDefaultQuota().' > /dev/null 2>&1 &';
        $gui->debug($cmd);
        pclose(popen($cmd, "r"));
        
        $nldap->disconnect('USER::newUser()');
        
        $gui->session_info("Usuario '".$this->uid."' creado correctamente.");
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
        
        $dldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        
        if ($delprofile == '1') {
            $dldap->deleteProfile($this->uid);
        }
        
        if( $dldap->is_teacher($this->uid) ) {
            // borrar de las aulas
            $aulas=$dldap->get_aulas();
            foreach($aulas as $aula) {
                $aula->delMember($this->uid);
            }
        }
        
        // delete from Teachers
        $dldap->delUserFromGroup($this->uid, LDAP_OU_TEACHERS);
        
        // delete from TICS
        $dldap->delUserFromGroup($this->uid, LDAP_OU_TICS);
        
        // delete from admins
        $dldap->delUserFromGroup($this->uid, LDAP_OU_ADMINS);
        $dldap->delUserFromGroup($this->uid, LDAP_OU_DADMINS);
        
        // delete from Domain Users
        $dldap->delUserFromGroup($this->uid, LDAP_OU_DUSERS);
        
        // borrarlo de LDAP_OU_USERS
        $gui->debug("ldap_delete ( $dldap->cid , 'uid=".$this->uid.",".LDAP_OU_USERS ."')");
        $r=ldap_delete ( $dldap->cid , "uid=".$this->uid.",".LDAP_OU_USERS );
        if ( ! $r)
            return false;
        $dldap->disconnect('USER::delUser()');
        return true;
    }
    
    function getNumericQuota() {
        global $quotaArray, $gui;
        /* try to read cached quota */
        if ( isset($quotaArray[$this->uid]) ) {
            //$gui->debug("<h2>getNumericQuota() CACHED</h2>");
            return $quotaArray[$this->uid]['size'];
        }
        if (is_readable("/var/lib/max-control/quota.cache.php")) {
            include("/var/lib/max-control/quota.cache.php");
            if ( isset($quotaArray[$this->uid]) ) {
                return $quotaArray[$this->uid]['size'];
            }
        }
        return 0;
    }
    
    function getquota() {
        global $gui;
        if (file_exists("/etc/max-control/quota.disabled")) {
            return "<b>disabled</b>";
        }
        global $quotaArray;
        /* try to read cached quota */
        if ( isset($quotaArray[$this->uid]) ) {
            //$gui->debug("<h2>getquota() CACHED</h2>");
            $color="black";
            if($quotaArray[$this->uid]['overQuota'])
                $color="red";
            
            $size=$quotaArray[$this->uid]['size'];
            $maxsize=$quotaArray[$this->uid]['maxsize'];
            $percent=$quotaArray[$this->uid]['percent'];
            
            return "<span style='color:$color'>$size MB / $maxsize MB ($percent)</span>";
        }
        
        /* read /var/lib/max-control/quota.cache.php */
        if (is_readable("/var/lib/max-control/quota.cache.php")) {
            include("/var/lib/max-control/quota.cache.php");
            
            if ( isset($quotaArray[$this->uid]) ) {
                
                $color="black";
                if($quotaArray[$this->uid]['overQuota'])
                    $color="red";
                
                $size=$quotaArray[$this->uid]['size'];
                $maxsize=$quotaArray[$this->uid]['maxsize'];
                $percent=$quotaArray[$this->uid]['percent'];
                
                return "<span style='color:$color'>$size MB / $maxsize MB ($percent)</span>";
            }
            else {
                exec("sudo ".MAXCONTROL." getquota '".$this->uid."' 2>&1", $output);
                return $output[0];
            }
        }
        else {
            exec("sudo ".MAXCONTROL." getquota '".$this->uid."' 2>&1", $output);
            //$gui->debug("<pre>getquota(".$this->uid.")".print_r($output, true)."</pre>");
            return $output[0];
        }
    }
    
    function resetProfile() {
        global $gui;
        
        exec("sudo ".MAXCONTROL." resetprofile '".$this->uid."' 2>&1", $output);
        $gui->debug("<pre>resetProfile(".$this->uid.") output=".print_r($output, true)."</pre>");
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
    
    
    function init() {
        global $gui;
        $this->exe=new WINEXE($this->hostname());
        /* try to use LDAP IP if exists */
        if ($this->ipHostNumber != '' && 
            $this->exe->checkIP($this->ipHostNumber) == $this->ipHostNumber) {
                //$gui->debug("COMPUTER:init(".$this->hostname().") using LDAP IP=". $this->ipHostNumber);
                $this->exe->ip=$this->ipHostNumber;
        }
        return;
    }
    
    function hostname() {
        return str_replace('$', '', $this->uid);
    }
    
    function get_save_dn(){
        return 'uid='.$this->uid.','.LDAP_OU_COMPUTERS;
    }
    
    function rnd() {
        /* return a uniq HASH to pass to status.php and no cache images */
        return md5(microtime());
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
        
        if (  $this->sambaProfilePath == '') {
            $this->sambaProfilePath=array();
            $this->ldapdata['sambaProfilePath']=array();
        }
        
        if (  $this->bootFile == '') {
            $this->bootFile=array();
            $this->ldapdata['bootFile']=array();
        }
        
    }
    
    function action($actionname, $mac){
        global $gui;
        $gui->debug("COMPUTER:action($actionname) mac=$mac uid=".$this->uid);
        if ( method_exists($this->exe, $actionname) ) {
            $this->exe->hostname=$this->hostname();
            $gui->debug("   COMPUTER:action($actionname) method exists");
            if (FORK_ACTIONS)
                return $this->exe->fork($actionname);
            else
                return $this->exe->$actionname($mac);
        }
        else {
            if ( $actionname == 'rebootwindows' ) {
                $this->action('wakeonlan', $mac);
                // cambiar MAC a windows.menu
                // python bin/pyboot --cronadd --boot=windows --mac=08:00:27:96:0D:E6
                $gui->debug("sudo ".MAXCONTROL." pxe --cronadd --boot=windows --mac=$mac 2>&1");
                exec("sudo ".MAXCONTROL." pxe --cronadd --boot=windows --mac=$mac 2>&1", $output);
                $gui->debuga($output);
                // llamar a reiniciar
                return $this->action('reboot', $mac);
            }
            elseif ( $actionname == 'rebootmax' ) {
                $this->action('wakeonlan', $mac);
                // cambiar MAC a max-extlinux.menu
                // python bin/pyboot --cronadd --boot=max-extlinux --mac=08:00:27:96:0D:E6
                $gui->debug("sudo ".MAXCONTROL." pxe --cronadd --boot=max-extlinux --mac=$mac 2>&1");
                exec("sudo ".MAXCONTROL." pxe --cronadd --boot=max-extlinux --mac=$mac 2>&1", $output);
                $gui->debuga($output);
                // llamar a reiniciar
                return $this->action('reboot', $mac);
            }
            elseif ( $actionname == 'rebootbackharddi' ) {
                $this->action('wakeonlan', $mac);
                // cambiar MAC a backharddi-ng-text.menu
                // python bin/pyboot --cronadd --boot=backharddi-ng-text --mac=08:00:27:96:0D:E6
                $gui->debug("sudo ".MAXCONTROL." pxe --cronadd --boot=backharddi-ng-text --mac=$mac 2>&1");
                exec("sudo ".MAXCONTROL." pxe --cronadd --boot=backharddi-ng-text --mac=$mac 2>&1", $output);
                $gui->debuga($output);
                // llamar a reiniciar
                return $this->action('reboot', $mac);
            }
            else {
                $gui->session_error("Acción desconocida '$actionname' en equipo ". $this->hostname());
                $gui->debug("method '$actionname' don't exists");
            }
        }
        return false;
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
        exec("sudo ".MAXCONTROL." pxe --genpxelinux 2>&1", $output);
        return;
    }
    
    function cleanPXELinux() {
        global $gui;
        //bin/max-control pxe --clean
        exec("sudo ".MAXCONTROL." pxe --clean 2>&1", $output);
        return;
    }
    
    function resetBoot() {
        global $gui;
        $gui->debug("COMPUTER:resetBoot() bootFile=".$this->bootFile);
        // reset boot file in this computer (empty)
        $this->ldapdata["bootFile"] = array();
        $this->bootFile = array();
        
        $res = $this->save( array('bootFile') );
        
        exec("sudo ".MAXCONTROL." pxe --delete='".$this->macAddress."' 2>&1", $output);
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
        $gui->debug("sudo ".MAXCONTROL." pxe --boot='$conffile' --mac='$mac' ");
        exec("sudo ".MAXCONTROL." pxe --boot='$conffile' --mac='$mac' ", $output);
        $gui->debug("LDAP:boot($conffile, $mac)<pre>".print_r($output, true)."</pre>");
        if ( ! isset($result[0]) ) {
            $gui->session_info("Arranque PXE de '".$this->hostname()."' actualizado.");
        }
        return true;
    }
    
    function getBoot() {
        global $gui;
        /*
        python way
        macfile=os.path.join( PXELINUXCFG , convertMAC(mac) )
        if os.path.exists(macfile):
            boot=os.path.basename(os.readlink(macfile))
            return boot.replace('.menu', '')
        else:
            return 'default'
        
        */
        $fname=PXELINUXCFG.$this->pxeMAC();
        $sublinktxt='';
        if (is_readable($fname)) {
            $target=basename(readlink($fname));
            if ( is_link(readlink($fname)) ) {
                $sublink=str_replace('.menu' , '' , basename(readlink(readlink($fname))));
                if ($sublink != 'default') {
                    $sublinktxt=" =&gt; $sublink";
                }
            }
            return str_replace('.menu' , '' , $target).$sublinktxt;
        }
        
        return 'default';
    }
    
    function pxeMAC() {
        /* return pxelinux.cfg filename for TFTP */
        return "01-" . strtolower(str_replace ( ':' , '-' , $this->macAddress )); 
    }
    
    function teacher_in_computer() {
        if ( $_SESSION['role']=='admin' || $_SESSION['role']=='tic' ) {
            return true;
        }
        elseif ( $_SESSION['role']=='teacher' ) {
            $teacher=$_SESSION['username'];
            // if computer in aula and teacher in aula
            
            if ( isset($this->sambaProfilePath) && $this->sambaProfilePath != '' ) {
                global $ldap;
                $members=$ldap->get_teacher_from_aula($this->sambaProfilePath);
                if ( in_array($teacher, $members['ingroup']) ) {
                    return true;
                }
            }
            return false;
        }
        return false;
    }
    
    function delComputer() {
        global $gui;
        /*
        *  smbpasswd -x 'wxp64$'
        */
        $result=false;
        
        // delete MAC
        if ( $this->macAddress != '') {
            $mac=$this->macAddress;
            $gui->debug("sudo ".MAXCONTROL." pxe --delete='$mac' ");
            exec("sudo ".MAXCONTROL." pxe --delete='$mac' ", $output);
            $gui->debug("delComputer($mac)<pre>".print_r($output, true)."</pre>");
        }
        
        //borrar del LDAP
        $dldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        $r=ldap_delete ( $dldap->cid , "uid=".$this->uid.",".LDAP_OU_COMPUTERS );
        if ( ! $r)
            $result=false;
        
        $result=true;
        
        // forzar borrado de samba
        $gui->debug("sudo ".MAXCONTROL." delcomputer '".$this->uid."' ");
        exec("sudo ".MAXCONTROL." delcomputer '".$this->uid."' ", $output);
        $gui->debuga($output);
        
        $dldap->disconnect('COMPUTER::delComputer()');
        
        return $result;
    }

    function newComputer($data) {
        /* used by create_hosts.php */
        global $gui;
        
        $nldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        
        if ( $nldap->get_computers($data['hostname'].'$') ) {
            $gui->session_error("El equipo '".$data['hostname']."' ya existe.");
            return false;
        }
        $this->ldapdata=array();
        $this->uid=$data['hostname'].'$';
        $this->cn=$data['hostname'].'$';
        
        $this->description="Computer";
        $this->displayName="Computer";
        $this->gecos="Computer";
        $this->uidNumber=$nldap->lastUID() +1;
        $this->gidNumber="515"; /*Domain Computers ()*/
        
        
        $rid = (2 * $this->uidNumber) + 1000;
        
        $this->sambaSID=$nldap->getSID()."-$rid";
        //$this->sambaPrimaryGroupSID=$nldap->getSID()."-". $nldap->getGID('Domain Users');
        
        $this->homeDirectory="/dev/null";
        $this->loginShell="/bin/false";
        $this->sambaAcctFlags="[WX         ]";
        $this->sambaHomePath=array();
        $this->sambaProfilePath=array(); /* aqui se guarda el aula */
        
        $this->objectClass = array('top', 'account', 'posixAccount', 'sambaSamAccount');
        $this->sambaNTPassword="E44AA99B52BB7604F9DC31D6298D4EFB";
        //$additionalPasswords=$nldap->additionalPasswords(leer_datos('password') , $this->uid, $samba=true);
        //$this->set( $additionalPasswords );
        
        $this->sambaPwdLastSet=time();
        
        $newdata=$this->show();
        
        $init=array(
            "uid" => $this->uid,
            "cn" => $this->cn,
            "uidNumber" => $this->uidNumber,
            "gidNumber" => $this->gidNumber,
            "description" => $this->description,
            #"displayName" => $this->displayName,
            "gecos" => $this->gecos,
            "homeDirectory" => $this->homeDirectory,
            "loginShell" => $this->loginShell,
            #"sambaAcctFlags" => $this->sambaAcctFlags,
            #"sambaProfilePath" => $this->sambaProfilePath,
            #"userPassword" => "{SHA}".base64_encode(sha1(leer_datos('password'), TRUE)),
            "objectClass" => array('top', 'account', 'posixAccount'),
                    );
        $gui->debuga($init);
        $r=ldap_add($nldap->cid, "uid=".$this->uid.",".LDAP_OU_COMPUTERS, $init);
        
        $gui->debug(ldap_error($nldap->cid));
        
        
        if ( ! $r ) {
            $gui->session_error("No se ha podido añadir el equipo, compruebe todos los campos.");
            return false;
        }
        
        $gui->debug("INIT DONE save rest");
        $this->ldapdata=$newdata;
        
        
        // save SAMBA attributes
        $init=array(
            "objectClass" => array('top', 'account', 'posixAccount', 'sambaSamAccount'),
            
            "sambaNTPassword" => $this->sambaNTPassword,
            #"sambaLMPassword" => $this->sambaLMPassword,
            
            #"sambaPwdCanChange" => $this->sambaPwdCanChange,
            #"sambaLogonTime" => $this->sambaLogonTime,
            #"sambaLogoffTime" => $this->sambaLogoffTime,
            
            #"sambaPwdMustChange" => $this->sambaPwdMustChange,
            "sambaPwdLastSet" => $this->sambaPwdLastSet,
            #"sambaPasswordHistory" => $this->sambaPasswordHistory,
            
            "sambaAcctFlags" => $this->sambaAcctFlags,
            #"sambaKickoffTime" => $this->sambaKickoffTime,
            
            #"sambaNTPassword" => $this->sambaNTPassword,
            #"sambaLMPassword" => $this->sambaLMPassword,
            
            #"sambaPrimaryGroupSID" => $this->sambaPrimaryGroupSID,
            "sambaHomePath" => $this->sambaHomePath,
            #"sambaProfilePath" => $this->sambaProfilePath,
            "sambaSID" => $this->sambaSID,
                    );
        $gui->debuga($init);
        $r=ldap_modify($nldap->cid, "uid=".$this->uid.",".LDAP_OU_COMPUTERS, $init);
        
        $gui->debug(ldap_error($nldap->cid));
        if ( ! $r )
            return false;
        
        $other=array('displayName');
        $this->save( $other );
        
        //$gui->debuga($this);
        
        // guardar MAC e IP
        $new=array("macAddress" => $data['macAddress'],
                    "ipHostNumber" => $data['ipHostNumber'],
                    "bootFile" => "",
                    "sambaProfilePath" => "",
                    "hostname" => $data['hostname']);
        $this->set($new);
        $this->ldapdata['macAddress']=$data['macAddress'];
        $this->ldapdata['ipHostNumber']=$data['ipHostNumber'];
        
        $res=$this->save( array('sambaProfilePath', 
                         'ipHostNumber', 
                         'ipNetmaskNumber', 
                         'ipNetmaskNumber', 
                         'macAddress', 
                         'bootFile') );
        
        
        $nldap->disconnect('COMPUTER::newComputer()');
        $gui->session_info("Equipo añadido correctamente.");
        return true;
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
    var $cachedBoot=NULL;
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
        $this->get_num_computers();
        $this->getBoot();
        return;
    }
    
    function get_num_users() {
        //global $gui;
        //$gui->debug("get_num_users() aula=".$this->cn . "<pre>".print_r($this->ldapdata['memberUid'], true)."</pre>");
        //if ( isset($this->ldapdata['memberUid']) )
        //    return $this->ldapdata['memberUid']['count'];
        //return 0;
        $i=0;
        if ( isset($this->ldapdata['memberUid']) ) {
            global $ldap;
            unset($this->ldapdata['memberUid']['count']);
            foreach($this->ldapdata['memberUid'] as $username) {
                $user=$ldap->user_exists($username);
                if ($user) {
                    $i++;
                }
            }
            return $i;
        }
        return 0;
    }
    
    function safecn() {
        return preg_replace('/\s+/', '_', $this->cn);
    }
    
    function get_users() {
        $users=array();
        if ( isset($this->ldapdata['memberUid']) ) {
            global $ldap;
            unset($this->ldapdata['memberUid']['count']);
            foreach($this->ldapdata['memberUid'] as $username) {
                $user=$ldap->user_exists($username);
                if ($user) {
                    $users[]=$username;
                }
            }
        }
        return $users;
    }
    
    function teacher_in_aula() {
        global $gui;
        if ( $_SESSION['role']=='teacher' ) {
            $teacher=$_SESSION['username'];
            if ( in_array($teacher, $this->get_users() ) ) {
                $gui->debug("Teacher '$teacher' in aula '".$this->cn."'");
                return true;
            }
            $gui->debug("Teacher '$teacher' not in aula '".$this->cn."'");
            return false;
        }
        return true;
    }
    
    function get_num_computers() {
        if ( isset($this->cachednumcomputers) )
            return $this->cachednumcomputers;
        /* don't use global LDAP here */
        $ldap=new LDAP();
        $this->cachednumcomputers=count($ldap->get_macs_from_aula($this->cn));
        $ldap->disconnect("AULA::get_num_computers() from init()");
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
        if ($this->cachedBoot)
            return $this->cachedBoot;
        /*
        aulafile=os.path.join( PXELINUXCFG , safe_aula(aula) )
        if not os.path.exists(aulafile):
            if os.path.exists( aulafile + ".menu"):
                aulafile=aulafile+".menu"
        if not os.path.exists(aulafile):
            return 'default'
        return os.path.basename(os.readlink(aulafile)).replace('.menu', '')
        
        */
        $aulafile=PXELINUXCFG.$this->safecn();
        if ( !is_file($aulafile) ) {
            return 'default';
        }
        elseif (is_readable($aulafile)) {
            $target=basename(readlink($aulafile));
            $this->cachedBoot=str_replace('.menu' , '' , $target);
        }
        elseif (is_readable($aulafile.".menu")) {
            $target=basename(readlink($aulafile.".menu"));
            $this->cachedBoot=str_replace('.menu' , '' , $target);
        }
        else {
            $this->cachedBoot='default';
        }
        return $this->cachedBoot;
    }
    
    
    function genPXELinux() {
        global $gui;
        //bin/max-control pxe --genpxelinux
        exec("sudo ".MAXCONTROL." pxe --genpxelinux 2>&1", $output);
        return;
    }
    
    function boot($conffile) {
        /*
        $conffile must be windows, max, backhardding or aula name
        */
        
        global $gui;
        $aula=$this->cn;
        
        if ($conffile == '') {
            $conffile='default';
        }
        
        //max-control pxe --aula=aula_primaria_1 --boot=windows
        exec("sudo ".MAXCONTROL." pxe --boot='$conffile' --aula='".$this->safecn()."' ", $output);
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
        
        if ($this->description == ''){
            $this->description=' ';
            $this->ldapdata['description']=' ';
        }
        
        $init=array(
            "cn" => $this->cn,
            "gidNumber" => $this->gidNumber,
            "description" => $this->description,
            "objectClass" => array('posixGroup'),
                    );
        $gui->debuga($init);
        $r=ldap_add($ldap->cid, "cn=".$this->cn.",".LDAP_OU_GROUPS, $init);
        
        $gui->debug("AULA:newAula() dn=".$this->get_save_dn()." data=<pre>".print_r($init, true)."\nRESULT=".ldap_error($ldap->cid)."</pre>");
        if ( ! $r ) {
            $gui->session_error("No se pudo añadir el aula '".$this->cn."'</br>Error:".ldap_error($ldap->cid));
            $ldap->disconnect('AULA::newAula()');
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
        
        //$gui->debug("BASE:save() dn=".$this->get_save_dn()." data=<pre>".print_r($init, true)."\nRESULT=".ldap_error($ldap->cid)."</pre>");
        if ( ! $r ) {
            $gui->session_error("No se pudieron modificar los atributos del aula '".$this->cn."'</br>Error:".ldap_error($ldap->cid));
            $ldap->disconnect('AULA::newAula()');
            return false;
        }
        
        $this->genPXELinux();
        $this->boot('default');
        
        $ldap->disconnect('AULA::newAula()');
        
        return true;
    }
    
    
    
    function delAula() {
        global $gui;
        $res=false;
        
        /* borrar aula de los equipos que pertenezcan a ella */
        global $ldap;
        $computers=$ldap->get_computers_from_aula($this->cn);
        foreach($computers as $computer) {
            $computer->empty_attr( 'sambaProfilePath' );
        }
        
        $local_ldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        
        
        // borrarlo de LDAP_OU_GROUPS
        $gui->debug("ldap_delete ( $local_ldap->cid , 'cn=".$this->cn.",".LDAP_OU_GROUPS ."')");
        $r=ldap_delete ( $local_ldap->cid , "cn=".$this->cn.",".LDAP_OU_GROUPS );
        if ( ! $r)
            $res=false;
        $res=true;
        
        /* remove /var/lib/tftpboot/pxelinux.cfg/$AULA$ */
        /* read all pxe of hosts and delete linked to aula */
        exec("sudo ".MAXCONTROL." pxe --delaula='".$this->safecn()."' ", $output);
        $gui->debug("AULA:delAula(".$this->safecn().")<pre>".print_r($output, true)."</pre>");
        
        
        $this->genPXELinux();
        $local_ldap->disconnect('AULA::delAula()');
        
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
    
    var $numUsers=0;
    
    function init(){
        $this->numUsers=$this->get_num_users();
    }
    
    function get_num_users() {
        $i=0;
        if ( isset($this->ldapdata['memberUid']) ) {
            /* don't use global LDAP here */
            $local_ldap = new LDAP();
            unset($this->ldapdata['memberUid']['count']);
            foreach($this->ldapdata['memberUid'] as $username) {
                $user=$local_ldap->user_exists($username);
                if ($user) {
                    $i++;
                }
            }
            $local_ldap->disconnect("GROUP::get_num_users() from init()");
            return $i;
        }
        return 0;
    }
    
    function get_users() {
        $users=array();
        if ( isset($this->ldapdata['memberUid']) ) {
            global $ldap;
            unset($this->ldapdata['memberUid']['count']);
            foreach($this->ldapdata['memberUid'] as $username) {
                $user=$ldap->user_exists($username);
                if ($user) {
                    $users[]=$username;
                }
            }
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
        if ( in_array($username, $this->ldapdata['memberUid']) ) {
            // user is in group
            $gui->debug("newMember(user=$username, group=".$this->cn.") user is in group, not adding" );
            return true;
        }
        $members[]=$username;
        unset($members['count']);
        $gui->debuga($members);
        $this->ldapdata['memberUid']=$members;
        $this->memberUid=$members;
        $res = $this->save(array('memberUid'));
        global $ldap;
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
        global $ldap;
        $ldap->updateLogonShares();
        return $res;
    }
    
    function delGroup($delprofile='') {
        global $gui;
        
        
        $dldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        
        if ($delprofile == '1') {
            $dldap->deleteGroupProfile($this->cn);
            $dldap->genSamba();
        }
        
        $dldap->updateLogonShares();
        
        // borrarlo de LDAP_OU_GROUPS
        $gui->debug("ldap_delete ( $dldap->cid , 'cn=".$this->cn.",".LDAP_OU_GROUPS ."')");
        $r=ldap_delete ( $dldap->cid , "cn=".$this->cn.",".LDAP_OU_GROUPS );
        if ( ! $r)
            return false;
        $dldap->disconnect('GROUP::delGroup()');
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
    
    function newGroup($createshared, $readonly, $grouptype=2) {
        global $gui;
        
        $nldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        
        $this->displayName=$this->cn;
        $this->sambaGroupType=$grouptype;
        $this->objectClass=array('posixGroup', 'sambaGroupMapping', 'eboxGroup');
        
        
        $this->gidNumber=$nldap->lastGID() + 1 ;
        $rid= 2 * $this->gidNumber + 1001;
        $this->sambaSID=$nldap->getSID(). "-" . $rid;
        
        $this->memberUid=array();
        
        $gui->debuga($this->show());
        
        $init=array(
            "cn" => $this->cn,
            "gidNumber" => $this->gidNumber,
            "objectClass" => array('posixGroup'),
                    );
        $gui->debuga($init);
        $r=ldap_add($nldap->cid, "cn=".$this->cn.",".LDAP_OU_GROUPS, $init);
        
        //$gui->debug("BASE:save() dn=".$this->get_save_dn()." data=<pre>".print_r($init, true)."\nRESULT=".ldap_error($nldap->cid)."</pre>");
        if ( ! $r ) {
            $gui->session_error("No se pudo añadir el grupo '".$this->cn."'</br>Error:".ldap_error($nldap->cid));
            $nldap->disconnect('GROUP::addGroup()');
            return false;
        }

        if ($this->description == ''){
            $this->description=array();
            $this->ldapdata['description']=array();
        }

        $init=array(
            "displayName" => $this->displayName,
            "gidNumber" => $this->gidNumber,
            "sambaSID" => $this->sambaSID,
            "sambaGroupType" => $this->sambaGroupType,
            "description" => $this->description,
            #"memberUID" => $this->memberUid,
            "objectClass" => array('posixGroup', 'sambaGroupMapping', 'eboxGroup'),
                    );
        $gui->debuga($init);
        $r=ldap_modify($nldap->cid, "cn=".$this->cn.",".LDAP_OU_GROUPS, $init);
        
        //$gui->debug("BASE:save() dn=".$this->get_save_dn()." data=<pre>".print_r($init, true)."\nRESULT=".ldap_error($nldap->cid)."</pre>");
        if ( ! $r ) {
            $gui->session_error("No se pudieron modificar los atributos del grupo '".$this->cn."'</br>Error:".ldap_error($nldap->cid));
            $nldap->disconnect('GROUP::addGroup()');
            return false;
        }
        
        if ($createshared == '1') {
            $nldap->addGroupProfile($this->cn, $readonly);
            $nldap->genSamba();
        }
        
        $nldap->updateLogonShares();
        
        $nldap->disconnect('GROUP::addGroup()');
        
        return true;
    }
    
    
    function renameGroup($newname) {
        global $gui;
        /*
        [cn] => grupo3
        [description] => 
        [displayName] => grupo3
        [gidNumber] => 2009
        [objectClass] => Array
            (
                [count] => 3
                [0] => posixGroup
                [1] => sambaGroupMapping
                [2] => eboxGroup
            )

        [sambaSID] => S-1-5-21-3818554400-921237426-3143208535-5019
        [memberUid] => 
        [sambaGroupType] => 2
        [numUsers] => 0
        */
        $oldname=$this->cn;
        $this->displayName=$newname;
        $this->ldapdata['displayName']=$newname;
        $this->description=$newname;
        $this->ldapdata['description']=$newname;
        $this->save( array('displayName', 'description') );
        //$gui->debuga($this);
        
        $full_old_dn= "cn=".$this->cn.",".LDAP_OU_GROUPS;
        $new_rdn= "cn=$newname";
        
        $gui->debug("OLD=$full_old_dn NEW=$new_rdn");
        
        $nldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        $r=ldap_rename( $nldap->cid, $full_old_dn, $new_rdn, NULL, TRUE);
        if ( ! $r ) {
            $gui->session_error("No se pudo renombrar el grupo '$oldname'</br>Error:".ldap_error($nldap->cid));
            $nldap->disconnect();
            return false;
        }
        $nldap->updateLogonShares();
        $nldap->disconnect('GROUP::renameGroup()');
        $gui->session_info("Grupo '$oldname' renombrado a '$newname'.");
        
        /* rename shared folder if exists */
        exec("sudo ".MAXCONTROL." renamegroup '$oldname' '$newname' 2>&1", $output);
        $gui->debug("GROUP:renameGroup('$oldname' '$newname')<pre>".print_r($output, true)."</pre>");
        
        if ($output[0] == 'ok')
            $gui->session_info("Carpeta compartida renombrada para el grupo '$oldname' a '$newname'.");
        elseif ($output[0] == 'new exists')
            $gui->session_error("La carpeta compartida '$newname' existe.");
        elseif ($output[0] == 'no changes')
            $gui->session_info("El grupo '$newname' no tiene carpeta compartida.");
        else
            $gui->session_info("Error desconocido al renombrar recurso compartido.<br/><pre>".print_r($output, true)."</pre>");
        return true;
    }
}

class ISO extends BASE{
    var $filename='';
    var $size='';
    var $volumeid='';
    
    function save($attrs=array()) {
        return;
    }
    
    function init(){
        $this->volumeid = $this->ldapdata['volumeid'];
        $this->size = $this->ldapdata['size'];
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
        var $cachedAdmins=NULL;
        var $cachedUIDs=NULL;

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

    function get_users($filter='*', $group=LDAP_OU_USERS, $ignore="max-control", $filterrole='') {
        global $gui;
        if ( $filter == '' )
            $filter='*';
        else
            $filter="*$filter*";
        
        $users=array();
        $gui->debug("ldap::get_users() (uid='$filter') basedn='$group' ignore='$ignore' role='$filterrole'");
        $gui->debug("(|(uid=$filter)(cn=$filter)(sn=$filter))");
        $this->search("(|(uid=$filter)(cn=$filter)(sn=$filter))", $basedn=$group);
        while($attrs = $this->fetch())
        {
            //$gui->debug("<pre>".print_r($attrs, true)." </pre><br>\n");
            if ($ignore != "" && $attrs['cn'][0] == $ignore ) {
                continue;
            }
            
            $user= new USER($attrs);
            
            /* si pasamos role y si no coincide con el que pasamos, no lo añadimos */
            if( $filterrole != '' && ! $user->is_role($filterrole) ) {
                continue;
            }
            $users[]=$user;
        }
        $gui->debug("ldap::get_users() num users ".sizeof($users));
        return $users;
    }

    function get_user($uid='') {
        global $gui;
        
        
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

    function user_exists($uid) {
        global $gui;
        
        if($this->cachedUIDs == NULL) {
            $this->cachedUIDs=$this->get_user_uids();
            
        }
        return in_array($uid, $this->cachedUIDs);
        /*
        $filter = "(&(objectClass=posixAccount)(uid=$uid))";
        if (! ($search=ldap_search($this->cid, LDAP_OU_USERS, $filter))) {
            $this->error="Error: búsqueda incorrecta.\n".ldap_error($this->cid);
            return false;
        }
        $number_returned = ldap_count_entries($this->cid, $search);
        if ($number_returned != 1) {
            return false;
        }
        
        return true;
        */
    }

    function get_user_uids($group=LDAP_OU_USERS) {
        global $gui;
        $uids=array();
        $users=$this->get_users($filter='', $group=$group);
        //$gui->debuga($users);
        foreach($users as $user) {
            $uids[]=$user->uid;
        }
        /* sort users */
        sort($uids);
        return $uids;
    }

    function get_tics_uids($filter='*') {
        global $gui;
        if ( $filter == '' )
            $filter='*';
        $tics=array();
        //$gui->debug("ldap::get_tics_uids() ".LDAP_OU_TICS);
        @$this->search("(cn=*)", $basedn=LDAP_OU_TICS);
        
        if (! $this->sr ) {
            return $tics;
        }
        //$gui->debug("res=".$this->sr." ".ldap_error($this->cid));
        
        while($attrs = $this->fetch()) {
            if ( isset($attrs['memberUid']) ) {
                $tics=$attrs['memberUid'];
                unset($tics['count']);
            }
        }
        /* sort tics */
        sort($tics);
        return $tics;
    }

    function is_tic($uid='') {
        $tics=$this->get_tics_uids();
        foreach ($tics as $tic) {
            if ( $uid == $tic )
                return true;
        }
        return false;
    }

    function get_teachers_uids($filter='*') {
        global $gui;
        if ( $filter == '' )
            $filter='*';
        $teachers=array();
        //$gui->debug("ldap::get_teachers_uids() ".LDAP_OU_TEACHERS);
        @$this->search("(cn=*)", $basedn=LDAP_OU_TEACHERS);
        
        if (! $this->sr ) {
            return $teachers;
        }
        //$gui->debug("res=".$this->sr." ".ldap_error($this->cid));
        
        while($attrs = $this->fetch()) {
            if ( isset($attrs['memberUid']) ) {
                $teachers=$attrs['memberUid'];
                unset($teachers['count']);
            }
        }
        /* sort teachers */
        sort($teachers);
        return $teachers;
    }


    function is_teacher($uid='') {
        //global $gui;
        $teachers=$this->get_teachers_uids();
        //$gui->debuga($teachers);
        if ( in_array($uid, $teachers) ) {
            return true;
        }
        return false;
    }

    function is_admin($uid='') {
        global $gui;
        
        if($this->cachedAdmins) {
            if ( in_array($uid, $this->cachedAdmins) ) {
                return true;
            }
            return false;
        }
        $this->cachedAdmins=array();
        
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
        $gui->debug("<pre>".print_r($attrs, true)." </pre><br>\n");
        
        if ( array_key_exists("memberUid", $attrs) ) {
            $this->cachedAdmins=$attrs["memberUid"];
            //$gui->debug("<pre>".print_r($members, true)." </pre><br>\n");
            if ( in_array($uid, $this->cachedAdmins) ) {
                //$gui->debug("ldap::is_admin() user $uid is admin");
                return true;
            }
        }
        //$gui->debug("ldap::is_admin() user $uid is NOT admin");
        return false;
    }


    function get_computers($uid='') {
        global $gui;
        
        if ( $uid == '' )
            $uid='*';
        elseif ( preg_match('/\$$/', $uid) )
            $uid=$uid;
        else
            $uid="*$uid*";
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

    function get_computer_by_ip($ip='') {
        global $gui;
        
        $computer=array();
        $gui->debug("ldap::get_computers() (uid='*')".LDAP_OU_COMPUTERS);
        $this->search("(uid=*)", $basedn=LDAP_OU_COMPUTERS);
        while($attrs = $this->fetch()) {
            if ( isset($attrs['ipHostNumber'][0]) ) {
                if ( $attrs['ipHostNumber'][0] == $ip ) {
                    $computer=new COMPUTER($attrs);
                }
            }
            
        }
        return $computer;
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
        
        $aulas=array();
        $gui->debug("ldap::get_aulas(aula='$aula') (cn='*')".LDAP_OU_GROUPS);
        $this->search("(cn=*)", $basedn=LDAP_OU_GROUPS);
        
        while($attrs = $this->fetch()) {
            //$gui->debug("<pre>".print_r($attrs, true)."</pre>");
            if ( isset($attrs['sambaGroupType']) && ($attrs['sambaGroupType'][0] == 9) ) {
                $gui->debuga("filter='$aula' found=".$attrs['cn'][0]);
                if ($aula == '' || $aula == '*') {
                    //$aulas[]=$attrs['cn'][0];
                    $aulas[]=new AULA($attrs);
                    $gui->debug("ldap::get_aulas() ADD aula='".$attrs['cn'][0]."'");
                }
                else {
                    if ( strpos($aula, '*') === false ) {
                        /* return exact aula */
                        if ( $aula == $attrs['cn'][0]) {
                            $aulas[]=new AULA($attrs);
                            $gui->debug("ldap::get_aulas() ADD exact '$aula' '".$attrs['cn'][0]."'");
                        }
                    }
                    else {
                        // remove '*' from $aula
                        $aulatxt=str_replace('*', '', $aula);
                        if (preg_match("/$aulatxt/i", $attrs['cn'][0])) {
                            //$aulas[]=$attrs['cn'][0];
                            $aulas[]=new AULA($attrs);
                            $gui->debug("ldap::get_aulas() ADD '$aula' pattern match '".$attrs['cn'][0]."'");
                        }
                        else {
                            $gui->debug("ldap::get_aulas() '$aula' don't match '".$attrs['cn'][0]."'");
                        }
                    }
                }
            }
        }
        return $aulas;
    }

    function get_aula($aulafilter) {
        global $gui;
        
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
            return array(
                    "ingroup" => $ingroup,
                    "outgroup" => $uids
                    );
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

    function get_aulas_cn($aula='') {
        global $gui;
        $aulas=array();
        $gui->debug("ldap::get_aula_cn() (cn='*')".LDAP_OU_GROUPS);
        $this->search("(cn=*)", $basedn=LDAP_OU_GROUPS);
        
        while($attrs = $this->fetch()) {
            //$gui->debug("<pre>".print_r($attrs, true)."</pre>");
            if ( isset($attrs['sambaGroupType']) && ($attrs['sambaGroupType'][0] == 9) ) {
                if ($aula == '') {
                    //$aulas[]=$attrs['cn'][0];
                    $aulas[]=$attrs['cn'][0];
                }
                else {
                    // remove '*' from $aula
                    $aula=str_replace('*', '', $aula);
                    if (preg_match("/$aula/i", $attrs['cn'][0])) {
                        //$aulas[]=$attrs['cn'][0];
                        $aulas[]=$attrs['cn'][0];
                        $gui->debug("ldap::get_aulas() ADD '$aula' match '".$attrs['cn'][0]."'");
                    }
                }
            }
        }
        
        return $aulas;
    }

    function get_group($cn) {
        $groups=$this->get_groups($cn);
        if ( isset($groups[0]) )
            return true;
        return false;
    }

    function get_groups($groupfilter='*', $include_system=false) {
        global $gui;
        
        if ( $groupfilter == '' )
            $groupfilter='*';
        elseif ( $groupfilter == '*' )
            $groupfilter='*';
        else {
            //$groupfilter="*$groupfilter*";
            $groupfilter="$groupfilter";
        }
        
        $groups=array();
        $gui->debug("ldap::get_groups() (cn='$groupfilter')".LDAP_OU_GROUPS);
        $this->search("(cn=$groupfilter)", $basedn=LDAP_OU_GROUPS);
        while($attrs = $this->fetch()) {
            //$gui->debug("<pre>".print_r($attrs, true)."</pre>");
            if ( isset($attrs['sambaGroupType']) && 
                 ($attrs['sambaGroupType'][0] == 2) &&
                 ($attrs['gidNumber'][0] >= 2000) ) {
                 
                    if (!$include_system && 
                         ($attrs['cn'][0] == TEACHERS || $attrs['cn'][0] == TICS || $attrs['cn'][0] == INSTALLATORS)) {
                        continue;
                    }
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
        $allusers=$this->get_user_uids($group=LDAP_OU_USERS);
        $group=$this->get_groups($groupfilter);
        $users=$group[0]->get_users();
        
        $all=array('ingroup'=>array(), 'outgroup'=>array());
        
        foreach($allusers as $e) {
            $found=false;
            if ( in_array($e, $users ) )
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
        if (sizeof($uidNumbers) < 1)
            return 2000;

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
        if (sizeof($gidNumbers) < 1)
            return 2000;

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
        /* FIXME eBox/Zentyal hardcode this */
        $sid='S-1-5-21-3818554400-921237426-3143208535';
        return $sid;
        /*global $gui;
        exec('sudo '.MAXCONTROL.' getdomainsid', $output);
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
        return $sid;*/
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
        return DEFAULT_QUOTA;
        /*
        global $gui;
        exec('sudo '.MAXCONTROL.' getdefaultquota', $output);
        //$gui->debug("<pre>".print_r($output, true)."</pre>");
        return $output[0];
        */
    }

    function deleteProfile($uid) {
        //global $gui;
        /* delete profile in background */
        $cmd="sudo ".MAXCONTROL." deleteprofile '$uid' > /dev/null 2>&1 &";
        //$gui->debug($cmd);
        pclose(popen($cmd, "r"));
    }

    function deleteGroupProfile($group) {
        global $gui;
        /* delete profile in background */
        $cmd="sudo ".MAXCONTROL." deletegroup '$group' > /dev/null 2>&1 &";
        $gui->debug($cmd);
        pclose(popen($cmd, "r"));
    }

    function addGroupProfile($group, $readonly=0) {
        global $gui;
        exec("sudo ".MAXCONTROL." addgroup '$group' '$readonly'", $output);
        $gui->debug("LDAP:addGroupProfile($group, $readonly)<pre>".print_r($output, true)."</pre>");
        return $output[0];
    }

    function genSamba() {
        global $gui;
        /* launch gensamba in background */
        $cmd='sudo '.MAXCONTROL.' gensamba > /dev/null 2>&1 &';
        $gui->debug($cmd);
        pclose(popen($cmd, "r"));
    }

    function updateLogonShares() {
        /*
        This method call script to generate /home/samba/netlogon/shares.kix 
        to be loaded by logon.kix and mount shares that user is in.
        */
        global $gui;
        /* launch genlogonshares in background */
        $cmd='sudo '.MAXCONTROL.' genlogonshares > /dev/null 2>&1 &';
        $gui->debug($cmd);
        pclose(popen($cmd, "r"));
    }

    function purgeWINS() {
        global $gui;
        exec("sudo ".MAXCONTROL." purgewins", $output);
        $gui->debug("LDAP:purgeWINS()<pre>".print_r($output, true)."</pre>");
        return;
    }

    function getGroupMembers($group) {
        $members=array('memberUid' => array());
        
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
        
        //$gui->debuga($oldmembers);
        
        $newmembers=array('memberUid'=>array());
        foreach($oldmembers['memberUid'] as $m) {
            if( $m != $user) {
                $newmembers['memberUid'][]=$m;
            }
        }
        //$gui->debuga($newmembers);
        
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
        global $gui;
        $menus=array();
        foreach (glob(PXELINUXCFG ."*.menu") as $filename) {
            $gui->debug("$filename size " . filesize($filename). " ".basename($filename, '.menu'));
            if( ! backharddi_installed() && 
                  (basename($filename, '.menu') == 'backharddi-ng-text' || 
                   basename($filename, '.menu') == 'backharddi-ng') ) {
                $gui->debug("$filename continue NO BACKHARDDI-NG");
                continue;
            }
            $menus[basename($filename, '.menu')]=$this->readMenu($filename);
        }
        if ($aula)
            $menus['aula']="Arranque como el aula";
        return $menus;
    }

    function getISOS($filter='') {
        global $gui;
        $isos=array();
        exec("sudo ".MAXCONTROL." isos --getisos", $output);
        $gui->debug("LDAP:getISOS()<pre>".print_r($output, true)."</pre>");
        foreach($output as $iso) {
            /* test.iso|4.00 MB|CDROM */
            //$gui->debuga($iso);
            list ($filename, $size, $volumeid)=preg_split('/\|/', $iso);
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
        
        $this->sr = ldap_search($this->cid, $localbasedn, $filter);
        $this->error = ldap_error($this->cid);
        $this->resetResult();
        return($this->sr);
    }
    
    function fetch() {
        //global $gui;
        $att=null;
        if ($this->start == 0) {
            $this->start = 1;
            //$gui->debug("FIRST entry");
            $this->re = ldap_first_entry($this->cid, $this->sr);
        } 
        else {
            //$gui->debug("NO FIRTS");
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

    function disconnect($txt='') {
        global $gui;
        //$gui->debug("<h2>ldap->disconnect() ".$this->$binddn."</h2>");
        if($this->error != '' && $this->error != 'Success') {
            $gui->debug("LDAP::error ". $this->error);
        }
        $gui->debug("<h4>\$ldap->disconnect('$txt')</h4>");
        ldap_close($this->cid);
    }
}




