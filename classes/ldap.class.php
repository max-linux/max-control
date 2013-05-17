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
    
    function BASE(array $parameter = array()) {
        $this->set($parameter);
        $this->init();
    }

    function init(){
        global $gui;
        $gui->debug("empty init()");
        return;
    }

    function set(array $parameter = array()) {
        foreach($parameter as $k => $v) {
            $this->ldapdata[$k]=$v;
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
    var $cn='', $sn='', $dn='', $name='', $samaccountname='', $userprincipalname='';
    var $homedirectory='', $homedrive='', $loginshell='', $profilepath='';
    var $memberof=array(), $objectclass=array();
    //var $objectguid='', $objectsid='', $primarygroupid='', $uidnumber='';

    var $role='unset';
    var $usedSize=0;

    public static function attrs() {
         return array('cn', 'sn', 'dn' ,'name', 'samaccountname', 'userprincipalname',
                      'homedirectory', 'homedrive', 'loginshell', 'profilepath',
                      'memberof', 'objectclass',
                      //'objectguid', 'objectsid', 'primarygroupid', 'uidnumber'
                      );
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

            $gui->debug("getquota(".$this->cn.") CACHED size=$size maxsize=$maxsize percent=$percent");
            
            return "<span style='color:$color'>$size MB / $maxsize MB ($percent)</span>";
        }
        else {
            exec("sudo ".MAXCONTROL." getquota '".$this->cn."' 2>&1", $output);
            return $output[0];
        }
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
        //samba-tool group add "Aula23" --groupou=CN=Computers --group-scope=Domain --group-type=Security --description="Aula 23"
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
}



class COMPUTER extends BASE {
    var $cn='', $dn='', $name='', $samaccountname='', $displayname='', $description='';
    var $member=array(), $memberof=array(), $objectclass=array();
    //var $objectguid='', $objectsid='', $gidnumber='';
    var $operatingsystem='', $operatingsystemservicepack='', $operatingsystemversion='', $dnshostname='';
    var $aula='';

    var $ipHostNumber='';
    var $macAddress='';
    

    public static function attrs() {
         return array('cn', 'dn' ,'name', 'samaccountname', 'displayname', 'description',
                      'member', 'memberof', 'objectclass',
                      //'objectguid', 'objectsid', 'gidnumber',
                      'operatingsystem', 'operatingsystemservicepack', 'operatingsystemversion', 'dnshostname');
    }

    // add computer to aula
    // samba-tool group addmembers Aula23 WINXPLDAP

    function init(){
        //$this->objectsid = bin_to_str_sid($this->objectsid);
        //$this->objectguid = bin_to_str_guid($this->objectguid);

        unset($this->ldapdata);

        $this->aula=$this->get_aula();


        //{$u->attr('ipHostNumber')} / {$u->attr('macAddress')}
        if($this->description != '') {
            $tmp=preg_split('/\//', $this->description);
            $this->ipHostNumber=$tmp[0];
            $this->macAddress=$tmp[1];
        }
        $this->exe=new WINEXE($this->hostname());
    }

    function saveIPMAC() {
        global $ldap, $gui;

        $new=array( 'description' => array($this->ipHostNumber . "/" . $this->macAddress) );

        $r = ldap_modify($ldap->cid, $this->dn, $new);
        $gui->debuga($r);
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





}


class LDAP {
        var $hostname = LDAP_HOSTNAME;
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

        ldap_set_option($this->cid, LDAP_OPT_PROTOCOL_VERSION, 3);
        //$gui->debug("=============> ldap_bind($this->cid, $this->binddn, $this->bindpw)<=================");
        if ( ! @($this->bid=ldap_bind($this->cid, $this->binddn, $this->bindpw)) ) {
            $this->error = "Error: Usuario o contraseña incorrectos.\n".ldap_error($this->cid);
            $gui->debug("===> ".$this->error."<========");
            return false;
        }
        return true;
    }


    function search($filter, $basedn='', $attrs=array('*'))  {
        //global $gui;
        $localbasedn=$this->basedn;
        if ($basedn != '')
            $localbasedn=$basedn;
        
        $result = array();
        
        //$gui->debug("ldap_search('".$this->cid."', '$localbasedn', '$filter')");
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
        //$gui->debug("<h2>ldap->disconnect() ".$this->$binddn."</h2>");
        if($this->error != '' && $this->error != 'Success') {
            $gui->debug("LDAP::error ". $this->error);
        }
        $gui->debug("<h4>\$ldap->disconnect('$txt')</h4>");
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

        $ignore = array('proxy-zentyal3', 'dns-zentyal3', 'krbtgt', 'Guest');
        
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
            $gui->debug("ldap->get_user($cn) ".$user->cn." == " . $cn);
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
        
        $ignore = array();
        
        $groups=array();
        $data=$this->search("(&(objectclass=posixGroup)(|(CN=$filter)(name=$filter)(sAMAccountName=$filter)))",
                            $basedn=LDAP_OU_USERS,
                            $attrs= GROUP::attrs());
        //$gui->debug($data);
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
        if( $this->cachedTeachers != NULL ) {
            return $this->cachedTeachers;
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


    function get_computers_from_aula($aula) {
        $aula = $this->get_aula($aula);

        if( ! $aula ) {
            return array();
        }
        return $aula->get_computers();
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




}





