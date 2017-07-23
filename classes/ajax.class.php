<?php
if(DEBUG)
    error_reporting(E_ALL);



class Ajax {
    
    var $errortxt;

    function __construct(){
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

    function test_string($uid) {
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

    function usedcn($cn) {
        if ( !$this->test_string($cn) ) {
            $this->output = "invalid";
            return;
        }
        global $ldap;
        if ( ! $ldap->get_user($cn) )
            $this->output="free";
        else
            $this->output="used";
    }

    function usedgroup($cn) {
        if ( !$this->test_string($cn) ) {
            $this->output = "invalid";
            return;
        }
        global $ldap;
        if ( ! $ldap->get_group($cn) )
            $this->output="free";
        else
            $this->output="used";
    }

    function usedaula($cn) {
        if ( !$this->test_string($cn) ) {
            $this->output = "invalid";
            return;
        }
        global $ldap;
        $aula=$ldap->get_aula($cn);
        
        if ( $cn != $aula->cn )
            $this->output="free";
        else
            $this->output="used";
    }

    function importprogress() {
        $importer = new Importer();
        $status=$importer->status();
        if (! $importer->needToRun() ) {
            /* if no pending.txt set 100% */
            $status['done']=$status['number'];
        }
        if( $status['done'] == $status['number'] ) {
            $status['doneDateValue']=$importer->finishedDate();
            $status['timeNeeded']=$importer->timeNeeded();
        }
        $status['longUsernames']=$importer->getLongUsernames();
        $status['ok']=$importer->getNumUsersImported();
        $this->output=json_encode($status);
    }

    function process( $data ) {
        global $permisos;
        if( ! ($permisos->is_admin() || $permisos->is_tic()) )
            return $this->invalid("Acceso denegado, solo Administradores y Coordinadores TIC.");
        
        if ( ! isset($data['accion']) ) {
            return $this->invalid();
        }
        
        switch($data['accion']) {
            case "getip": $this->getip($data['hostname']); break;
            case "getmac": $this->getmac($data['hostname']); break;
            case "usedcn": $this->usedcn($data['cn']); break;
            case "usedgroup": $this->usedgroup($data['cn']); break;
            case "usedaula": $this->usedaula($data['cn']); break;
            case "importprogress": $this->importprogress(); break;
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

