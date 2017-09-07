<?php






function bin_to_str_sid($binsid) {
    $hex_sid = bin2hex($binsid);
    $rev = hexdec(substr($hex_sid, 0, 2));
    $subcount = hexdec(substr($hex_sid, 2, 2));
    $auth = hexdec(substr($hex_sid, 4, 12));
    $result    = "$rev-$auth";

    for ($x=0;$x < $subcount; $x++) {
        $subauth[$x] = 
            hexdec(little_endian(substr($hex_sid, 16 + ($x * 8), 8)));
        $result .= "-" . $subauth[$x];
    }

    // Cheat by tacking on the S-
    return $result;
}

function little_endian($hex) {
    $result='';
    for ($x = strlen($hex) - 2; $x >= 0; $x = $x - 2) {
        $result .= substr($hex, $x, 2);
    }
    return $result;
}


function bin_to_str_guid($object_guid) {
    $hex_guid = bin2hex($object_guid);
    $hex_guid_to_guid_str = '';
    for($k = 1; $k <= 4; ++$k) {
        $hex_guid_to_guid_str .= substr($hex_guid, 8 - 2 * $k, 2);
    }
    $hex_guid_to_guid_str .= '-';
    for($k = 1; $k <= 2; ++$k) {
        $hex_guid_to_guid_str .= substr($hex_guid, 12 - 2 * $k, 2);
    }
    $hex_guid_to_guid_str .= '-';
    for($k = 1; $k <= 2; ++$k) {
        $hex_guid_to_guid_str .= substr($hex_guid, 16 - 2 * $k, 2);
    }
    $hex_guid_to_guid_str .= '-' . substr($hex_guid, 16, 4);
    $hex_guid_to_guid_str .= '-' . substr($hex_guid, 20);

    return strtoupper($hex_guid_to_guid_str);
}




class BASE {
    var $ldapdata=array();
    var $errortxt='';
    
    function __construct(array $parameter = array()) {
        $this->set($parameter);
        $this->init();
    }

    function init(){
        global $gui;
        $gui->debug("empty init()");
        return;
    }

    function set(array $parameter = array()) {
        global $gui;
        foreach($parameter as $k => $v) {
            $this->ldapdata[$k]=$v;
            // $gui->debug("set $k => ".print_r($v, true));
            if ( isset($this->$k) ) {
                if ( is_array($this->$k) ) {
                    unset($v['count']);
                    $this->$k=$v;
                }
                elseif ( isset($v['count']) && $v['count'] == 1) {
                    $this->$k=$v[0];
                }
                else {
                    //unset($v['count']);
                    if( is_array($v) ) {
                        //echo print_r($v, true);
                        unset($v['count']);
                    }
                    $this->$k=$v;
                }
            }
        }
    }

    function split_dn($dn) {
        //global $gui;
        $parts = preg_split('/=|,/', $dn);
        //$gui->debuga($parts);
        return $parts[1];
    }

    function attr($attrname) {
        /*
        * Return value of internal attribute
        */
        if ( ! isset($this->$attrname) )
            return NULL;
        return $this->$attrname;
    }
}


class USER extends BASE {
    var $cn='', $givenname='', $sn='', $dn='', $displayname='', $samaccountname='', $userprincipalname='', $description='';
    var $homedirectory='', $homedrive='', $loginshell='', $profilepath='';
    var $memberof=array(), $objectclass=array(), $quota=0;

    var $role='unset';
    var $password='';
    var $usedSize=0;

    var $background=true;

    public static function attrs() {
         return array('cn', 'givenname', 'sn', 'dn' ,'displayname', 'samaccountname', 'userprincipalname', 'description',
                      'homedirectory', 'homedrive', 'loginshell', 'profilepath',
                      'memberof', 'objectclass', 'quota',
                      );
    }

    function set_role() {
        global $gui, $ldap;

        // $gui->debuga($this);

        $oldrole='';
        foreach ($this->memberof as $g) {
            switch ($g) {
                case LDAP_OU_TICS:
                    $oldrole='tic';
                    break;

                case LDAP_OU_ADMINS:
                    $oldrole='admin';
                    break;

                case LDAP_OU_TEACHERS:
                    $oldrole='teacher';
                    break;
            }
        }
        

        $gui->debuga("set_role() '$oldrole' => '$this->role'");
        if ($this->role != $oldrole) {
            // quitar del rol viejo
            switch ($oldrole) {
                case 'tic':
                    $old = $ldap->get_builtin_groups(TICS);
                    $old->delMember($this->cn);
                    break;

                case 'teacher':
                    $old = $ldap->get_builtin_groups(TEACHERS);
                    $old->delMember($this->cn);
                    break;

                case 'admins':
                    $old = $ldap->get_builtin_groups('Domain Admins');
                    $old->delMember($this->cn);
                    break;
            }

            $quota = DEFAULT_QUOTA;
            switch ($this->role) {
                case 'tic':
                    $old = $ldap->get_builtin_groups(TICS);
                    $old->newMember($this->cn);
                    break;

                case 'teacher':
                    $old = $ldap->get_builtin_groups(TEACHERS);
                    $old->newMember($this->cn);
                    $quota = $quota * 2;
                    break;

                case 'admin':
                    $old = $ldap->get_builtin_groups('Domain Admins');
                    $old->newMember($this->cn);
                    $quota = $quota * 2;
                    break;
            }

            $r = ldap_modify($ldap->cid, $this->dn, array('quota' => $quota));
            $cmd='sudo '.MAXCONTROL.' requota '.$this->cn.' '.$quota.' 2>&1';
            $gui->debug($cmd);
            exec($cmd, $output);
            $gui->debuga($output);
            
            //
            return true;
        }
        return false;
    }

    function save($data) {
        global $ldap, $gui;
        

        /*
        [cn] => pepe6
        [givenname] => Pepe6
        [sn] => Ruiz Lopez
        [password] => pepe6
        [repassword] => pepe6
        [description] => aaaaaa
        [role] => teacher
        [loginshell] => /bin/false
        */
        
        // $gui->debuga("data=".print_r($data, true));

        if ($this->givenname =='') {
            $this->givenname=' ';
        }
        if ($this->sn =='') {
            $this->sn=' ';
        }

        $new=array( 'givenname' => array($this->givenname),
                    'sn' => array($this->sn),
                    'loginShell' => $this->loginshell,
                  );


        if($this->description != '') {
            $new['description']=array($this->description);
        }

        // $gui->debuga($new);
        // $gui->debuga($this->dn);

        $r = ldap_modify($ldap->cid, $this->dn, $new);

        //$gui->debuga($r);
        if ($r) {
            return true;
        }
        else {
            return false;
        }
    }

    function update_password($new, $cn) {
        global $gui;
        $gui->debuga($this);

        $cmd='sudo '.MAXCONTROL." chpasswd '".$this->cn."' '$new' 2>&1";
        $gui->debuga($cmd);
        exec($cmd, $output);
        $gui->debuga($output);
        return true;
    }


    function init(){
        //$this->objectsid = bin_to_str_sid($this->objectsid);
        //$this->objectguid = bin_to_str_guid($this->objectguid);

        unset($this->ldapdata);
        $this->usedSize=$this->getNumericQuota();
        $this->get_role();
    }

    function is_role($role) {
        if($role == 'alumno') {
            $role='';
        }
        //global $gui;
        //$gui->debug("is_role() ".$this->get_role()." <=> $role");
        return $this->get_role() == $role;
    }

    function get_role() {
        if ($this->role != 'unset') {
            return $this->role;
        }
        
        global $ldap;
        
        if ( $ldap->is_tic($this->cn)) {
            $this->role='tic';
        }
        elseif ( $ldap->is_admin($this->cn)) {
            $this->role='admin';
        }
        elseif ( $ldap->is_teacher($this->cn) ) {
            $this->role='teacher';
        }
        else {
            $this->role='';
        }

        return $this->role;
    }

    function is_romaing() {
        if( $this->homedirectory != '' &&
            $this->homedrive != '' && 
            $this->profilepath != '') {
            return true;
        }
        return false;
    }


    function getNumericQuota() {
        global $quotaArray, $gui;
        /* try to read cached quota */
        if ( isset($quotaArray[$this->cn]) ) {
            //$gui->debug("<h2>getNumericQuota() CACHED</h2>");
            return $quotaArray[$this->cn]['size'];
        }
        if (is_readable("/var/lib/max-control/quota.cache.php")) {
            include("/var/lib/max-control/quota.cache.php");
            if ( isset($quotaArray[$this->cn]) ) {
                return $quotaArray[$this->cn]['size'];
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

        if( ! isset($quotaArray[$this->cn]) ) {
            if (is_readable("/var/lib/max-control/quota.cache.php")) {
                include("/var/lib/max-control/quota.cache.php");
            }
        }

        /* try to read cached quota */
        if ( isset($quotaArray[$this->cn]) ) {
            $color="black";
            if( isset($quotaArray[$this->cn]['overQuota']) &&
                $quotaArray[$this->cn]['overQuota']) {
                $color="red";
            }
            
            $size=$quotaArray[$this->cn]['size'];
            $maxsize=$quotaArray[$this->cn]['maxsize'];
            $percent=$quotaArray[$this->cn]['percent'];

            //$gui->debug("getquota(".$this->cn.") CACHED size=$size maxsize=$maxsize percent=$percent");
            
            return "<span style='color:$color'>$size MB / $maxsize MB ($percent)</span>";
        }
        else {
            exec("sudo ".MAXCONTROL." getquota '".$this->cn."' 2>&1", $output);
            return $output[0];
        }
    }


    function newUser($data=array()) {
        global $gui;

        // if( ! isset($data['password'])) {
        //     $gui->session_info("No ha indicado contraseña.");
        //     return false;
        // }
        /*
        [cn] => pepe6
        [givenname] => Pepe6
        [sn] => Ruiz Lopez
        [password] => pepe6
        [repassword] => pepe6
        [description] => aaaaaa
        [role] => teacher
        [loginshell] => /bin/false
        */

        // bin/max-control adduser pepe4 Pepe4 'Lopez Gomez' pepe4

        //$gui->debug($_POST);

        $cn=$this->cn;
        $givenname=$this->givenname;
        $sn=$this->sn;
        $password=$this->password;
        $role=$this->role;
        $loginshell=$this->loginshell;
        $description=$this->description;

        // crear home, profiles y aplicar quota en background
        if($this->background) {
            $cmd='sudo '.MAXCONTROL." adduser '$cn' '$givenname' '$sn' '$password' '$role' '$loginshell' '$description' > /dev/null 2>&1 &";
            $gui->debug($cmd);
            pclose(popen($cmd, "r"));
            $gui->session_info("Usuario '".$this->cn."' creado correctamente.");
        }
        else {
            $cmd='sudo '.MAXCONTROL." adduser '$cn' '$givenname' '$sn' '$password' '$role' '$loginshell' '$description' 2>&1";
            $gui->debug($cmd);
            exec($cmd, $output);
            $gui->debug("newUser<pre>".print_r($output, true)."</pre>");
            if( end($output) != 'OK' ) {
                $gui->session_error("Error creando usuario '".$this->cn."'.");
                return false;
            }
        }
        
        
        return true;
    }


    function delUser($delprofile='') {
        global $gui;
        
        $deleted=false;

        $gui->debug("sudo ".MAXCONTROL." deluser '".$this->cn."' ");
        exec("sudo ".MAXCONTROL." deluser '".$this->cn."' ", $output);
        $gui->debug("delUser<pre>".print_r($output, true)."</pre>");
        if ( isset($result[0]) && $result[0] == 'OK' ) {
            $deleted=true;
        }

        if($delprofile) {
            $cmd="sudo ".MAXCONTROL." deleteprofile '".$this->cn."' >/dev/null 2>&1 &";
            $gui->debug($cmd);
            pclose(popen($cmd, "r"));
        }
        
        return $deleted;
    }


    function resetProfile() {
        global $gui;
        
        exec("sudo ".MAXCONTROL." resetprofile '".$this->cn."' 2>&1", $output);
        $gui->debug("<pre>resetProfile(".$this->cn.") output=".print_r($output, true)."</pre>");
        if( end($output) != 'OK' ) {
            return true;
        }
        return false;
    }

}

class GROUP extends BASE {
    var $cn='', $dn='', $name='', $samaccountname='', $description='';
    var $member=array(), $memberof=array(), $objectclass=array();
    //var $objectguid='', $objectsid='';

    var $numUsers=0;

    public static function attrs() {
         return array('cn', 'dn' ,'name', 'samaccountname', 'description',
                      'member', 'memberof', 'objectclass',
                      //'objectguid', 'objectsid'
                      );
    }


    function init(){
        //$this->objectsid = bin_to_str_sid($this->objectsid);
        //$this->objectguid = bin_to_str_guid($this->objectguid);

        unset($this->ldapdata);
        $this->get_users();
    }

    function get_num_users() {
        return sizeof($this->get_users());
    }

    function get_users() {
        $users=array();
        foreach ($this->member as $k => $v) {
            if( endsWith(strtolower($v), strtolower(LDAP_OU_USERS) ) ) {
                $users[] = $this->split_dn($v);
            }
        }
        $this->numUsers = sizeof($users);
        return $users;
    }

    function newGroup($createshared, $readonly, $grouptype=2) {
        global $gui;
        $gui->debuga($this);
        /*
        [cn] => test4
        [description] => comentario test 4
        [createshared] => 1
        [readonly] => 1
        */
        if($this->description == '') {
            $this->description=$this->cn;
        }
        $saved=false;

        $cmd="sudo ".MAXCONTROL." addgroup '".$this->cn."' '$createshared' '$readonly' '".$this->description."' 2>&1";
        $gui->debuga($cmd);
        exec($cmd, $output);
        $gui->debuga($output);

        if( isset($result[0]) && $result[0] == 'OK' ) {
            $saved=true;
        }

        if($createshared == '1') {
            $cmd='sudo '.MAXCONTROL.' gensamba > /dev/null 2>&1 &';
            $gui->debug($cmd);
            pclose(popen($cmd, "r"));
        }

        return $saved;
    }

    function delGroup($delprofile='') {
        global $gui;
        
        $cmd="sudo ".MAXCONTROL." deletegroup '".$this->cn."' '$delprofile' 2>&1";
        $gui->debuga($cmd);
        exec($cmd, $output);
        $gui->debuga($output);
        
        return true;
    }


    function newMember($username) {
        global $gui, $ldap;

        // $members=$this->member;
        // $members[]="CN=$username,".LDAP_OU_USERS;

        // $r = ldap_modify($ldap->cid, $this->dn, array('member' => $members) );
        // if(!$r) return false;
        // $this->member=$members;
        // return true;

        $cmd="sudo ".MAXCONTROL." addmember '".$this->cn."' '$username' 2>&1";
        $gui->debuga($cmd);
        exec($cmd, $output);
        $gui->debuga($output);
        
        return true;
    }


    function delMember($username) {
        global $gui, $ldap;

        // $members=$this->member;
        // $udel="CN=$username,".LDAP_OU_USERS;


        // $newmembers=array();
        // foreach ($members as $m) {
        //     if ( $m == $udel || $m == $username ) {
        //         continue;
        //     }
        //     $newmembers[]=$m;
        // }
        // $members=$newmembers;
        

        // $r = ldap_modify($ldap->cid, $this->dn, array('member' => $members) );
        // if(!$r) return false;
        // $this->member=$members;
        // return true;

        $cmd="sudo ".MAXCONTROL." delmember '".$this->cn."' '$username' 2>&1";
        $gui->debuga($cmd);
        exec($cmd, $output);
        $gui->debuga($output);
        
        return true;
    }

    function renameGroup($newname) {
        global $gui;
        $gui->session_error("SAMBA 4 no permite renombrar grupos, pruebe a borrar y añadir uno nuevo");
        return false;
    } 
}


class AULA extends BASE {
    var $cn='', $dn='', $name='', $samaccountname='', $description='';
    var $member=array(), $memberof=array(), $objectclass=array();
    //var $objectguid='', $objectsid='', $gidnumber='';

    private $users=NULL, $computers=NULL;
    var $cachedBoot=NULL;

    public static function attrs() {
         return array('cn', 'dn' ,'name', 'samaccountname', 'description',
                      'member', 'memberof', 'objectclass',
                      //'objectguid', 'objectsid', 'gidnumber'
                      );
    }


    function init(){
        //$this->objectsid = bin_to_str_sid($this->objectsid);
        //$this->objectguid = bin_to_str_guid($this->objectguid);

        unset($this->ldapdata);
        $this->getBoot();
    }

    function safecn() {
        return preg_replace('/\s+/', '_', $this->cn);
    }

    function get_num_users() {
        return sizeof($this->get_users());
    }

    function get_num_computers() {
        if ( isset($this->cachednumcomputers) )
            return $this->cachednumcomputers;

        /*
        // don't use global LDAP here
        $ldap=new LDAP();
        $this->cachednumcomputers=count($ldap->get_macs_from_aula($this->cn));
        $ldap->disconnect("AULA::get_num_computers() from init()");
        return $this->cachednumcomputers;
        */
        $this->cachednumcomputers = sizeof($this->get_computers());
        return $this->cachednumcomputers;
    }

    function get_users() {
        if( $this->users != NULL ) {
            return $this->users;
        }
        $this->users=array();
        foreach ($this->member as $k => $v) {
            if( endsWith(strtolower($v), strtolower(LDAP_OU_USERS) ) ) {
                $this->users[] = $this->split_dn($v);
            }
        }
        return $this->users;
    }

    function get_computers() {
        if( $this->computers != NULL ) {
            return $this->computers;
        }
        $this->computers=array();
        foreach ($this->member as $k => $v) {
            if( endsWith(strtolower($v), strtolower(LDAP_OU_COMPUTERS) ) ) {
                $this->computers[] = $this->split_dn($v);
                
            }
        }
        return $this->computers;
    }


    function newMember($username) {
        global $gui, $ldap;

        $members=$this->member;
        $members[]="CN=$username,".LDAP_OU_USERS;

        $r = ldap_modify($ldap->cid, $this->dn, array('member' => $members) );
        if(!$r) return false;
        $this->member=$members;
        return true;
    }

    function delMember($username) {
        global $gui, $ldap;

        $members=$this->member;
        $udel="CN=$username,".LDAP_OU_USERS;


        $newmembers=array();
        foreach ($members as $m) {
            if ( $m == $udel || $m == $username ) {
                continue;
            }
            $newmembers[]=$m;
        }
        $members=$newmembers;
        

        $r = ldap_modify($ldap->cid, $this->dn, array('member' => $members) );
        if(!$r) return false;
        $this->member=$members;
        return true;
    }

    function add_computer($computer) {
        global $gui, $ldap;

        $members=$this->member;
        $members[]="CN=$computer,".LDAP_OU_COMPUTERS;

        $r = ldap_modify($ldap->cid, $this->dn, array('member' => $members) );
        if(!$r) return false;
        $this->member=$members;
        return true;
    }


    function del_computer($computer) {
        global $gui, $ldap;

        $members=$this->member;
        $cdel="CN=$computer,".LDAP_OU_COMPUTERS;


        $newmembers=array();
        foreach ($members as $m) {
            if ( $m == $cdel || $m == $computer ) {
                continue;
            }
            $newmembers[]=$m;
        }
        $members=$newmembers;
        

        $r = ldap_modify($ldap->cid, $this->dn, array('member' => $members) );
        if(!$r) return false;
        $this->member=$members;
        return true;
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
        $conffile must be windows, max or aula name
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
        
        if( $this->description == '' ) {
            $this->description=$this->cn;
        }

        $cmd="sudo ".MAXCONTROL." addaula '".$this->cn."' '".$this->description."' 2>&1";
        $gui->debuga($cmd);
        exec($cmd, $output);
        $gui->debuga($output);

        if( isset($result[0]) && end($result) == 'OK' ) {
            return true;
        }
        
        return false;
    }


    function delAula() {
        global $gui;
        
        $cmd="sudo ".MAXCONTROL." delaula '".$this->cn."' 2>&1";
        $gui->debuga($cmd);
        exec($cmd, $output);
        $gui->debuga($output);

        if( isset($result[0]) && end($result) == 'OK' ) {
            return true;
        }
        
        return false;
    }
}



class COMPUTER extends BASE {
    var $cn='', $dn='', $name='', $samaccountname='', $displayname='', $description='';
    var $member=array(), $memberof=array(), $objectclass=array();
    var $operatingsystem='', $operatingsystemservicepack='', $operatingsystemversion='', $dnshostname='';
    

    var $aula='';
    var $ipHostNumber='';
    var $macAddress='';
    

    public static function attrs() {
         return array('cn', 'dn' ,'name', 'samaccountname', 'displayname', 'description',
                      'member', 'memberof', 'objectclass',
                      'operatingsystem', 'operatingsystemservicepack', 'operatingsystemversion', 'dnshostname');
    }

    // add computer to aula
    // samba-tool group addmembers Aula23 WINXPLDAP

    function init(){
        global $gui;
        //$this->objectsid = bin_to_str_sid($this->objectsid);
        //$this->objectguid = bin_to_str_guid($this->objectguid);

        unset($this->ldapdata);

        $this->aula=$this->get_aula();

        if($this->displayname == '') {
            $this->displayname=$this->cn;
        }

        //$gui->debuga(" init() ".$this->description);
        //{$u->attr('ipHostNumber')} / {$u->attr('macAddress')}
        if(strpos($this->description, "/") !== false) {
            $tmp=preg_split('/\//', $this->description);
            //$gui->debuga($tmp);
            $this->ipHostNumber=$tmp[0];
            $this->macAddress=$tmp[1];
        }
        $this->exe=new WINEXE($this->hostname());
        // $gui->debuga(" init() ".$this->ipHostNumber);
    }

    function save($data=array()) {
        global $gui, $ldap;
        // $gui->debuga($this);
        $saved=$this->saveIPMAC();

        // save aula
        if($this->get_aula() != $this->aula) {
            $gui->debuga("SAVE AULA=".$this->aula);


            if($this->aula == '') {
                $aulas=$ldap->get_aulas( $this->get_aula() );
                if(sizeof($aulas) != 1) {
                    return false;
                }
                $gui->debuga($this->dn ." borrar del aula=".$this->get_aula());
                // quitar miembros
                $saved = $aulas[0]->del_computer( $this->name );
            }
            else {
                $aulas=$ldap->get_aulas( $this->aula );
                if(sizeof($aulas) != 1) {
                    return false;
                }
                $gui->debuga($this->dn ." añadir al aula=".$this->aula);
                $saved = $aulas[0]->add_computer( $this->name );
            }
        }

        $this->genPXELinux();

        return $saved;
    }

    function saveIPMAC() {
        global $ldap, $gui;

        //$new=array( 'description' => array($this->ipHostNumber . "/" . $this->macAddress) );
        if( $this->description == '' ) {
            $this->description = $this->ipHostNumber . "/" . $this->macAddress;
        }
        $new=array( 'description' => array($this->description) );

        // $gui->debuga($new);

        $r = ldap_modify($ldap->cid, $this->dn, $new);
        //$gui->debuga($r);
        if ($r) {
            return true;
        }
        else {
            return false;
        }
    }

    function hostname() {
        return str_replace('$', '', $this->displayname);
    }

    function rnd() {
        /* return a uniq HASH to pass to status.php and no cache images */
        return md5(microtime());
    }

    function get_aula() {
        if( sizeof($this->memberof) < 1 ) {
            return '';
        }
        if( endsWith(strtolower($this->memberof[0]), strtolower(LDAP_OU_COMPUTERS) ) ) {
            return $this->split_dn($this->memberof[0]);
        }
        return '';
    }


    function teacher_in_computer() {
        if ( $_SESSION['role']=='admin' || $_SESSION['role']=='tic' ) {
            return true;
        }
        elseif ( $_SESSION['role']=='teacher' ) {
            $teacher=$_SESSION['username'];
            // if computer in aula and teacher in aula
            global $ldap;
            $aula = $this->get_aula();
            if ($aula != '') {
                $members=$ldap->get_teacher_from_aula($aula);
                if ( in_array($teacher, $members['ingroup']) ) {
                    return true;
                }
            }
        }
        return false;
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

    function boot($conffile) {
        /*
        $conffile must be windows, max, or aula name
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
            if ( isset($this->aula) && $this->aula != '' ) {
                $conffile = preg_replace('/\s+/', '_', $this->aula);
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


    function getMACIP() {
        global $gui;
        $ip=$this->exe->getIpAddress($this->hostname());
        $gui->debug("ip=$ip");
        $mac=$this->exe->getMacAddress($this->hostname());
        $gui->debug("mac=$mac");
        
        if ($ip != '' && $mac != '') {
            $this->ipHostNumber=$ip;
            $this->macAddress=$mac;
            
            
            $res=$this->saveIPMAC();
            
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

    function action($actionname, $mac){
        global $gui;
        $gui->debug("COMPUTER:action($actionname) mac=$mac uid=".$this->cn);
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
            else {
                $gui->session_error("Acción desconocida '$actionname' en equipo ". $this->hostname());
                $gui->debug("method '$actionname' don't exists");
            }
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
        $result=true;
        // forzar borrado de samba
        $gui->debug("sudo ".MAXCONTROL." delcomputer '".$this->hostname()."$' ");
        exec("sudo ".MAXCONTROL." delcomputer '".$this->hostname()."$' ", $output);
        $gui->debuga($output);
        return $result;
    }


}


class LDAP {
        var $hostname = LDAP_HOST;
        var $basedn = LDAP_BASEDN;
        var $binddn = LDAP_BINDDN;
        var $bindpw = LDAP_BINDPW;
        var $cid = 0; // LDAP Server Connection ID
        var $bid = 0; // LDAP Server Bind ID
        var $error = "";
        var $cachedAdmins=NULL;
        var $cachedUIDs=NULL;

        var $cachedAllGroups=NULL;
        var $cachedTeachers=NULL;
        var $cachedTics=NULL;

        var $connected=false;

    function __construct($binddn = "", $bindpw = "", $hostname = LDAP_HOST) {
        
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

    function is_connected() {
        if (! $this->cid) 
            return false;
        return true;
    }

    function connect(){
        global $gui;
        
        // activar el debug de libldap
        //ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
        
        if (! @($this->cid=ldap_connect($this->hostname, LDAP_PORT))) {
            $this->error = "Error: No se pudo conectar con el servidor de autenticación.\n".ldap_error($this->cid);
            $gui->debug("==> ".$this->error."<====");
            return false;
        }

        ldap_set_option($this->cid, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($this->cid, LDAP_OPT_PROTOCOL_VERSION, 3);
        // $gui->debug("=============> ldap_bind($this->cid, $this->binddn, $this->bindpw)<=================");
        if ( ! @($this->bid=ldap_bind($this->cid, $this->binddn, $this->bindpw)) ) {
            $this->error = "Error: Usuario o contraseña incorrectos.\n".ldap_error($this->cid);
            $gui->debug("===> ".$this->error."<========");
            return false;
        }
        $this->connected=true;
        return true;
    }


    function search($filter, $basedn='', $attrs=array('*'))  {
        // global $gui;
        if( ! $this->connected ) {
            return array();
        }
        $localbasedn=$this->basedn;
        if ($basedn != '')
            $localbasedn=$basedn;
        
        $result = array();
        
        // $gui->debug("ldap_search('".$this->cid."', '$localbasedn', '$filter')");
        $sr = ldap_search($this->cid, $localbasedn, $filter, $attrs);
        if(! $sr) {
            return array();
        }
        $info = ldap_get_entries($this->cid, $sr);
        unset($info['count']);
        return $info;
    }

    function disconnect($txt='') {
        global $gui;
        if( ! $this->connected ) {
            return array();
        }
        //$gui->debug("<h2>ldap->disconnect() ".$this->$binddn."</h2>");
        if($this->error != '' && $this->error != 'Success') {
            $gui->debug("LDAP::error ". $this->error);
        }
        //$gui->debug("<h4>\$ldap->disconnect('$txt')</h4>");
        ldap_close($this->cid);
    }


    function get_users($filter='', $group=LDAP_OU_USERS, $filterrole='') {
        global $gui;
        if ( $filter == '' ) {
            $filter='*';
        }
        else {
            $filter="*$filter*";
        }

        $ignore = array('max-control', 'Administrator',
                        'proxy-zentyal3', 'dns-zentyal3',
                        'proxy-max-server', 'dns-max-server',
                        'proxy-max-control', 'dns-max-control',
                        'krbtgt', 'Guest', 'zentyal-squid-max-server');
        // $ignore = array();
        
        $users=array();
        $class="posixAccount";
        $class="user";
        $data=$this->search("(&(objectclass=$class)(|(CN=$filter)(name=$filter)(sAMAccountName=$filter)))",
                            $basedn=$group,
                            $attrs= USER::attrs());
        //$gui->debuga($data);
        foreach ($data as $i => $u) {
            //$gui->debuga($u);
            if( in_array($u['cn'][0], $ignore) ) {
                continue;
            }
            $user = new USER($u);
            /* si pasamos role y si no coincide con el que pasamos, no lo añadimos */
            //$gui->debug($user->is_role($filterrole));
            if( $filterrole != '' && ! $user->is_role($filterrole) ) {
                continue;
            }
            $users[] = $user;
        }
        //$gui->debuga($users);
        return $users;
    }


    function get_user($cn='') {
        global $gui;
        $users=$this->get_users($cn);
        foreach ($users as $k => $user) {
            //$gui->debug("ldap->get_user($cn) ".$user->cn." == " . $cn);
            if($user->cn == $cn) {
                return $user;
            }
        }
        return false;
    }


    function user_exists($cn) {
        if($this->cachedUIDs == NULL) {
            $this->cachedUIDs=$this->get_user_uids();
        }
        return in_array($cn, $this->cachedUIDs);
    }


    function get_user_uids($group=LDAP_OU_USERS) {
        $uids=array();
        $users=$this->get_users($filter='', $group=$group);
        foreach($users as $user) {
            $uids[]=$user->cn;
        }
        /* sort users */
        sort($uids);
        return $uids;
    }


    function get_groups($filter='', $include_system=false) {
        global $gui;
        
        if ( $filter == '' ) {
            $filter='*';
        }
        else {
            $filter="*$filter*";
        }
        
        $ignore = array('Allowed RODC Password Replication Group',
                        'Enterprise Read-only Domain Controllers',
                        'Denied RODC Password Replication Group',
                        'Read-only Domain Controllers',
                        'Group Policy Creator Owners',
                        'RAS and IAS Servers',
                        'Domain Controllers',
                        'Enterprise Admins',
                        'Domain Computers',
                        'Cert Publishers',
                        'DnsUpdateProxy',
                        'Domain Admins',
                        'Domain Guests',
                        'Schema Admins',
                        'Domain Users',
                        'DnsAdmins'
                        );
        
        $groups=array();
        $data=$this->search("(&(objectclass=group)(|(CN=$filter)(name=$filter)(sAMAccountName=$filter)))",
                            $basedn=LDAP_OU_USERS,
                            $attrs= GROUP::attrs());
        //$gui->debuga($data);
        foreach ($data as $i => $g) {
            $group = new GROUP($g);
            if( ! in_array($group->name, $ignore) ) {
                $groups[] = $group;
            }
        }
        //$gui->debug($groups);
        return $groups;
    }

    function get_group($cn) {
        $groups=$this->get_groups($cn);
        if ( isset($groups[0]) )
            return $groups[0];
        return false;
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
        //$gui->debug(print_r($all, true));
        return $all;
    }


    function get_tics_uids() {
        global $gui;
        if( $this->cachedTics != NULL ) {
            return $this->cachedTics;
        }

        // member of LDAP_OU_TICS
        $data=$this->search("(&(objectclass=group)(CN=".TICS."))",
                            $basedn=LDAP_OU_BUILTINS,
                            $attrs=GROUP::attrs());
        if( sizeof($data) != 1 ) {
            return array();
        }
        $group = new GROUP($data[0]);
        //$gui->debuga($group->get_users());
        $this->cachedTics = $group->get_users();
        sort($this->cachedTics);
        return $this->cachedTics;
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
        if( $this->cachedTeachers != NULL ) {
            return $this->cachedTeachers;
        }
        global $gui;
        // member LDAP_OU_TEACHERS
        $data=$this->search("(&(objectclass=group)(CN=".TEACHERS."))",
                            $basedn=LDAP_OU_BUILTINS,
                            $attrs=GROUP::attrs());
        if( sizeof($data) != 1 ) {
            return array();
        }
        $group = new GROUP($data[0]);
        //$gui->debuga($group->get_users());
        $this->cachedTeachers = $group->get_users();
        return $this->cachedTeachers;
    }

    function is_teacher($uid='') {
        $teachers=$this->get_teachers_uids();
        foreach ($teachers as $t) {
            if ( $uid == $t )
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
        
        $data=$this->search("(CN=Domain Admins)",
                            $basedn=LDAP_OU_USERS,
                            $attrs= GROUP::attrs());
        //$gui->debug($data);
        if( sizeof($data) != 1 ) {
            $gui->debug("ERROR: is_admin() not found 'Domain Admins' group");
            return false;
        }
        $group = new GROUP($data[0]);
        $this->cachedAdmins = $group->get_users();
        //$gui->debuga($this->cachedAdmins);
        return in_array($uid, $this->cachedAdmins);
    }

    function get_builtin_groups($cn) {
        global $gui;
        $basedn=LDAP_OU_BUILTINS;
        if($cn == 'Domain Admins') {
            $basedn=LDAP_OU_USERS;
        }
        //
        $data=$this->search("(&(objectclass=group)(CN=".$cn."))",
                            $basedn=$basedn,
                            $attrs=GROUP::attrs());
        if( sizeof($data) != 1 ) {
            return array();
        }
        return new GROUP($data[0]);
    }

    function get_aulas($aula='') {
        global $gui;

        if ( $aula == '' ) {
            $aula='*';
        }
        else {
            $aula="*$aula*";
        }
        
        $aulas = array();
        $ignore = array();

        //$gui->debug("(&(objectclass=group)(|(CN=$aula)(name=$aula)(sAMAccountName=$aula)))");
        $data=$this->search("(&(objectclass=group)(|(CN=$aula)(name=$aula)(sAMAccountName=$aula)))",
                            $basedn=LDAP_OU_COMPUTERS,
                            $attrs= AULA::attrs());

        //$gui->debuga($data);
        foreach ($data as $i => $a) {
            $aula = new AULA($a);
            if( ! in_array($aula->name, $ignore) ) {
                $aulas[] = $aula;
            }
        }

        return $aulas;
        /*
        $gui->debug("ldap::get_aulas(aula='$aula') (cn='*')".LDAP_OU_COMPUTERS);
        $this->search("(cn=*)", $basedn=LDAP_OU_COMPUTERS);
        
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
                        // return exact aula
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
        */
    }


    function get_aulas_cn($aula='') {
        $aulas=array();
        $data = $this->get_aulas($aula);
        foreach ($data as $k => $v) {
            $aulas[] = $v->cn;
        }
        return $aulas;
    }


    function get_computers($com='') {
        global $gui;
        
        if ( $com == '' ) {
            $com='*';
        }
        elseif ( preg_match('/\$$/', $com) ) {
            $com=$com;
        }
        else {
            $com="*$com*";
        }
        $computers=array();
        $ignore=array();
        

        $data=$this->search("(&(objectclass=computer)(|(CN=$com)(name=$com)(sAMAccountName=$com)))",
                            $basedn=LDAP_OU_COMPUTERS,
                            $attrs= COMPUTER::attrs());

        //$gui->debuga($data);
        foreach ($data as $i => $c) {
            $com = new COMPUTER($c);
            if( ! in_array($com->name, $ignore) ) {
                $computers[] = $com;
            }
        }

        return $computers;
    }

    function get_aula($aulafilter) {
        global $gui;

        $aulas = $this->get_aulas($aulafilter);
        if( sizeof($aulas) == 1 ) {
            return $aulas[0];
        }
        return new AULA();
    }

    function get_teacher_from_aula($aulafilter) {
        global $gui;
        $aula=$this->get_aula($aulafilter);
        //$gui->debuga($aula);
        // return empty array if aula not found
        $uids=$this->get_teachers_uids();
        //$gui->debuga($uids);
        $ingroup=array();
        $outgroup=array();
        
        if( $aula->cn=='' ) {
            $gui->debug("aula not found");
            return array(
                    "ingroup" => $ingroup,
                    "outgroup" => $uids
                    );
        }
        
        $members = $aula->get_users();
        if ( sizeof($members) < 1 ) {
            $gui->debug("aula without members");
            return array(
                    "ingroup" => $ingroup,
                    "outgroup" => $uids
                    );
        }
        
        foreach($uids as $uid) {
            if( in_array($uid, $members) ){
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
        // FIXME
        $macs=array();
        $computers=$this->get_computers_from_aula($aula);

        foreach ($computers as $c) {
            if($c->macAddress != '') {
                $gui->debuga($c);
                $macs[]=$c->macAddress;
            }
        }

        return $macs;
    }

    function get_computers_from_aula($aula) {
        global $gui;
        
        $computers=array();
        $ignore=array();
        
        $data=$this->search("(&(objectclass=computer)(memberof=CN=$aula,".LDAP_OU_COMPUTERS."))",
                            $basedn=LDAP_OU_COMPUTERS,
                            $attrs= COMPUTER::attrs());

        //$gui->debuga($data);
        foreach ($data as $i => $c) {
            $com = new COMPUTER($c);
            if( ! in_array($com->name, $ignore) ) {
                $computers[] = $com;
            }
        }

        return $computers;
    }

    function get_computers_in_and_not_aula($aula) {
        global $gui;
        $allequipos=$this->get_computers();
        $equipos=$this->get_computers_from_aula($aula);

        //$gui->debuga($equipos);
        //$gui->debuga($allequipos);

        
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
                // $gui->debug("equipo in allequipos");
                // $gui->debuga($e);
                if ( sizeof($e->memberof) < 1 )
                      $all['outgroup'][]=$e->hostname();
            }
        }
        return $all;
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




}


class ISO extends BASE{
    var $filename='';
    var $size='';
    var $volumeid='';
    
    function save() {
        return;
    }
    
    function init(){
        $this->volumeid = $this->ldapdata['volumeid'];
        $this->size = $this->ldapdata['size'];
        return;
    }

}


