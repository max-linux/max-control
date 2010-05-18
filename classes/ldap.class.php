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
        foreach($allvars as $k => $v) {
            if ($k != 'ldapdata')
                $allvars[$k]=$this->$k;
        }
        return $allvars;
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
        $this->pre_save();
        
        $gui->debug("ldap::BASE::save() dn=".$this->get_save_dn());
        
        //$gui->debug( $this->ldapdata );
        
        //$gui->debug("======================================================");


        $ldap=new LDAP($binddn=LDAP_BINDDN,$bindpw=LDAP_BINDPW);
        
        
        
        $new_objects=array_diff($this->objectClass, $this->ldapdata['objectClass']);
        //add new objectclass
        if ( $new_objects ) {
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
            }
        }
        
        // save new values
        foreach($attrs as $k) {
            if ( $this->$k == '')
                continue;
            if ( $this->is_restricted($k) )
                continue;
            $tmp=array();
            $tmp[$k]=$this->ldapdata[$k];
            $r = ldap_modify($ldap->cid, $this->get_save_dn(), $tmp );
        }
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
    var $loginShell='';    /* /bin/bash or /bin/false */
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
    
    function get_save_dn(){
        return 'uid='.$this->uid.','.LDAP_OU_USERS;
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
    var $sambaProfilePath='';
    
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
        $this->connect($binddn, $bindpw, $hostname);
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
                    $aulas[]=$attrs['cn'][0];
                    $gui->debug("ldap::get_aulas() ADD aula='".$attrs['cn'][0]."'");
                }
                else {
                    if (preg_match("/$aula/i", $attrs['cn'][0])) {
                        $aulas[]=$attrs['cn'][0];
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
        $gui->debug("ldap::get_macs_from_aula() (uid='*')".LDAP_OU_COMPUTERS);
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

#    function desconectar() {
#        unset($_SESSION["user"]);
#        unset($_SESSION["dni"]);
#        unset($_SESSION["ldap"]);
#    }


    function addUser($user, $pass) {
        return;
        /*
        /usr/share/perl5/EBox/UsersAndGroups.pm
            my @attr =  (
                'cn'            => $user->{'fullname'},
                'uid'           => $user->{'user'},
                'sn'            => $user->{'surname'},
                'uidNumber'     => $uid,
                'gidNumber'     => $gid,
                'homeDirectory' => HOMEPATH,
                'userPassword'  => $passwd,
                'objectclass'   => ['inetOrgPerson', 'posixAccount', 'passwordHolder'],
                @additionalPasswords
            );
        
        
        
        /usr/share/perl5/EBox/SambaLdapUser.pm
        
        
        # Add user to Domain Users group
        unless ($self->_domainUser($user)) {
            $users->addUserToGroup($user, 'Domain Users');
        }

        
        luego hay que crear el home y el profile
        
        $self->_createDir(USERSPATH . "/$user", $unixuid, USERGROUP, '0701');
        $self->_createDir(PROFILESPATH . "/$user", $unixuid, USERGROUP, '0700');
        $self->_createDir(PROFILESPATH . "/$user.V2", $unixuid, USERGROUP, '0700');
        $self->{samba}->setUserQuota($unixuid, $samba->defaultUserQuota());

        
        my $quota = $userQuota * 1024;
        use constant QUOTA_PROGRAM => '/usr/share/ebox-samba/ebox-samba-quota';
        root(QUOTA_PROGRAM . " -s $user $quota");
        
        */
        return;
    }

    function lastUID() {
        return;
        /*
            my %args = (
                base =>  $self->ldap->dn(),
                filter => '(objectclass=posixAccount)',
                scope => 'sub',
                attrs => ['uidNumber']
               );

            my $result = $self->ldap->search(\%args);

            my @users = $result->sorted('uidNumber');

        */
    }
    
    function additionalPasswords() {
        return;
        /*
        /usr/share/perl5/EBox/UsersAndGroups/Passwords.pm
        'sha1,md5,lm,nt,digest,realm'
        my @names = map { 'ebox' . ucfirst($_) . 'Password' } @formats;
        */
        /*
        
        $user='test';
        $pass='test';
        $realm='ebox';

        echo "        userpassword={SHA}".base64_encode(sha1('test', TRUE))."\n";
        echo "    eboxsha1password={SHA}".base64_encode(sha1('test', TRUE))."\n";
        echo "     eboxmd5password={MD5}".base64_encode(md5('test', TRUE))."\n";
        echo "      eboxlmpassword=".LMhash('test')."\n";
        echo "      eboxntpassword=".NTLMHash('test')."\n";
        echo "  eboxdigestpassword={MD5}".base64_encode(md5("$user:$realm:$pass", TRUE))."\n";
        echo "   eboxrealmpassword={MD5}".md5("$user:$realm:$pass")."\n";
        
        
        */
    }
    
    function getSID() {
        return;
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
