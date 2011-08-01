<?php
if(DEBUG)
    error_reporting(E_ALL);
/*

    Roles:
        * Administrador (admin) puede hacer de todo
        * Coordinador TIC (tic) administrador sin permiso en borrar equipos / añadir/quitar equipos de aula
        * Profesor permitir cambiar contraseña a sus alumnos
        * Alumno

*/

class Permisos {
    
    var $errortxt;

    function Permisos(){
        $this->errortxt="";
    }


    function get_error(){
        return $this->errortxt;
    }
    
    function is_connected(){
        global $gui;
        if ( isset($_SESSION["user"]) ) {
            $gui->debug("permisos::is_connected() true");
            return True;
        }
        else {
            $gui->debug("permisos::is_connected() false");
            return False;
        }
    }
    
    function get_humanrole() {
        switch($_SESSION['role']) {
            case "admin": return "Administrador"; break;
            case "tic": return "Coordinador TIC"; break;
            case "teacher": return "Profesor"; break;
            case "": return "Alumno"; break;
        }
    }
    
    function get_rol() {
        if ( ! $this->is_connected() ) return '';
        
        if ( ! isset($_SESSION['role']) ) return '';
        
        return $_SESSION['role'];
    }
    
    function is_admin(){
        $result=false;
        global $gui;
        
        if ( ! isset($_SESSION["username"]) )
            return false;
        
        if ( isset($_SESSION['is_admin']) ) {
            $gui->debug("permisos::is_admin() return session var='".$_SESSION['is_admin']."'");
            return $_SESSION['is_admin'];
        }
        
        global $ldap;
        if ( ! $ldap->connect() ) {
            $gui->debug("permisos::is_admin() Usuario o contraseña incorrecta");
            $result=false;
        }
        if ( ! $ldap->is_admin($uid=$_SESSION["username"])){
            $gui->debug("permisos::is_admin() No es administrador");
            $result=false;
        }
        else {
            $result=true;
        }
        if($result)
            return true;
        return false;
    }
    
    function is_teacher() {
        global $gui;
        
        if ( ! isset($_SESSION["username"]) )
            return false;
        
        if ( isset($_SESSION['is_teacher']) ) {
            $gui->debug("permisos::is_teacher() return session var='".$_SESSION['is_teacher']."'");
            return $_SESSION['is_teacher'];
        }
        
        global $ldap;
        $teachers=$ldap->get_teachers_uids();
        if ( in_array($_SESSION["username"], $teachers) )
            return True;
        return False;
    }

    function is_tic() {
        global $gui;
        
        if ( ! isset($_SESSION["username"]) )
            return false;
        
        if ( isset($_SESSION['is_tic']) ) {
            $gui->debug("permisos::is_tic() return session var='".$_SESSION['is_tic']."'");
            return $_SESSION['is_tic'];
        }
        
        global $ldap;
        $tics=$ldap->get_tics_uids();
        if ( in_array($_SESSION["username"], $tics) )
            return True;
        return False;
    }

    
    function conectar($user, $pass){
        global $gui;
        global $site;
        $userdn="uid=$user,".LDAP_OU_USERS;
        $gui->debug("userdn===> $userdn");
        
        $ldap = new LDAP($binddn=$userdn, $bindpw=$pass);
        if ( ! $ldap->connect() ) {
            $gui->debug("Usuario o contraseña incorrecta");
            return false;
        }
        
        
        $_SESSION["user"]="si";
        $_SESSION["username"]=$user;
        
        unset($_SESSION['is_tic']);
        $_SESSION['is_tic']=$this->is_tic();
        
        unset($_SESSION['is_admin']);
        $_SESSION['is_admin']=$this->is_admin();
        
        unset($_SESSION['is_teacher']);
        $_SESSION['is_teacher']=$this->is_teacher();
        
        if($_SESSION['is_tic']) {
            $_SESSION['role']='tic';
            $_SESSION['is_admin']=False;
            $_SESSION['is_teacher']=False;
        }
        elseif ($_SESSION['is_admin']) {
            $_SESSION['role']='admin';
            $_SESSION['is_teacher']=False;
            $_SESSION['is_tic']=False;
        }
        elseif($_SESSION['is_teacher']) {
            $_SESSION['role']='teacher';
            $_SESSION['is_tic']=False;
            $_SESSION['is_admin']=False;
        }
        else {
            $_SESSION['role']='';
            $_SESSION['is_tic']=False;
            $_SESSION['is_admin']=False;
            $_SESSION['is_teacher']=False;
        }
        
        $_SESSION['info']='';
        $_SESSION['error']='';
        
        $ldap->disconnect('PERMISOS::conectar()');
        
        return true;
    }
    
    function desconectar() {
        global $gui;
        $gui->debug("permisos::desconectar()");
        unset($_SESSION["user"]);
        unset($_SESSION["dni"]);
        unset($_SESSION['is_admin']);
        unset($_SESSION['is_teacher']);
        unset($_SESSION['is_tic']);
        unset($_SESSION['role']);
        session_unset();
        session_destroy();
    }
    
    
    function addUser($user, $pass) {
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
        */
        return;
    }

    function lastUID() {
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
        /*
        /usr/share/perl5/EBox/UsersAndGroups/Passwords.pm
        'sha1,md5,lm,nt,digest,realm'
        my @names = map { 'ebox' . ucfirst($_) . 'Password' } @formats;
        */
    }
    
    function getSID() {
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
}
?>
