<?php




class Ajax {
    
    var $errortxt;

    function Ajax(){
        $this->errortxt="";
        $this->output="";
    }


    function get_error(){
        return $this->errortxt;
    }

    function getip($hostname) {
        $exe= new WINEXE($hostname);
        $this->output=$exe->getIpAddress($hostname);
        //echo "ip=".print_r($exe, true);
    }

    function getmac($hostname) {
        $exe= new WINEXE($hostname);
        $this->output=$exe->getMacAddress($hostname);
    }

    function test_username($uid) {
        /* comprobar no espacios, empiece por letra y no caracteres raros */
        $re='/(^[A-Za-z])([A-Za-z0-9-._]+)$/';
        
        /* si cumple la anterior no tiene que tener caracteres no ASCII */
        $noascii='~[^\x00-\x7F]~u';
        if ( preg_match($re, $uid) ) {
            if ( preg_match($noascii, $uid)) {
                //echo "no ascii<br/>";
                return false;
            }
            //echo "ok<br/>";
            return true;
        }
        //echo "mal formato<br/>";
        return false;
    }

    function useduid($uid) {
        if ( !$this->test_username($uid) ) {
            $this->output = "invalid";
            return;
        }
        $ldap=new LDAP();
        if ( ! $ldap->get_user($uid) )
            $this->output="free";
        else
            $this->output="used";
    }

    function usedcn($cn) {
        $ldap=new LDAP();
        if ( ! $ldap->get_group($cn) )
            $this->output="free";
        else
            $this->output="used";
    }

    function usedaula($cn) {
        $ldap=new LDAP();
        $aula=$ldap->get_aula($cn);
        
        if ( $cn != $aula->cn )
            $this->output="free";
        else
            $this->output="used";
    }

    function process( $data ) {
        global $permisos;
        if( ! $permisos->is_admin() )
            return $this->invalid("Acceso denegado");
        
        if ( ! isset($data['accion']) ) {
            return $this->invalid();
        }
        
        switch($data['accion']) {
            case "getip": $this->getip($data['hostname']); break;
            case "getmac": $this->getmac($data['hostname']); break;
            case "useduid": $this->useduid($data['uid']); break;
            case "usedcn": $this->usedcn($data['cn']); break;
            case "usedaula": $this->usedaula($data['cn']); break;
            default: $this->invalid();
        }
    }


    function show() {
        echo $this->output;
    }
    
    function invalid($errtxt="") {
        die($errtxt);
    }
}
?>
