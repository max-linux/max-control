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

    function useduid($uid) {
        $ldap=new LDAP();
        if ( ! $ldap->get_user($uid) )
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
            default: $this->invalid();
        }
    }


    function show() {
        echo $this->output;
    }
}
?>