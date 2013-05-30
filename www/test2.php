<?


class GUI {
    function debug($txt) {
        if($txt == '') return;
        echo "D: ".print_r($txt, true)." <br>\n";
    }
}

$gui = new GUI();


class USER {

    var $cn='';
    var $uid='';
    var $uidNumber='';
    var $gidNumber='';
    var $loginShell='';

    function USER(array $parameter = array()) {
        foreach($parameter as $k => $v) {
            if ( isset($this->$k) )
                $this->$k=$v;
        }
    }
    
    function show() {
        global $gui;
        $allvars= get_class_vars(get_class($this));
        foreach($allvars as $k => $v) {
            $allvars[$k]=$this->$k;
        }
        $gui->debug( $allvars );
        return $allvars;
    }
}


$user = new USER( array("cn"=>'cn', 'fail' => 'fail') );
$user->show();

