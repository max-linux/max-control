<?php
/*
*  1.- Instalación del servicio
*     1.1.- Entrar al Windows como administrador del dominio
*     1.2.- Abrir una consola "cmd" y escribir:
*                net share admin$
*     1.3.- Desde el max-server ejecutar
*                winexe -U EBOX/admin //192.168.1.132 "ipconfig /all"
*
*     1.4.- Deberíamos haber visto la salida de ipconfig en la consola
*     1.5.- En el windows, Mi PC -> Propiedades -> Administrar ->
*                       -> Servicios y aplicaciones -> Servicios
*     1.6.- Buscar servicio "winexesvc" (suele ser el'último), 
*           botón derecho -> Propiedades
*     1.7.- Tipo de Inicio => Automático (iniciar si estuviera parado)
*
*
*                http://eol.ovh.org/winexe/
*/

class WINEXE {
    var $ip='';
    var $hostname='';
    
    
    function WINEXE($ip='') {
        global $gui;
        $this->ip=$ip;
        $this->initialized=false;
    }
    
    function init() {
        global $gui;
        if ( ($this->ip != $this->checkIP($this->ip) ) ){
            $this->hostname=$this->ip;
            $this->ip=$this->getipaddress($this->hostname);
        }
        
        $this->basecmd=WINEXE ." -U " . LDAP_DOMAIN . '/' . LDAP_ADMIN . '%' . LDAP_PASS;
        $this->basecmd.= ' --interactive=0 ';
        if ( $this->ip != '' )
            $this->basecmd.= " //" . $this->ip . " ";
        else
            $this->basecmd.= " //" . $this->hostname . " ";
        $this->initialized=true;
    }
    
    function exe( $targetcmd ) {
        global $gui;
        if ( ! $this->initialized )
            $this->init();
        $cmd=$this->basecmd . "'". $targetcmd ."'";
        $gui->debug($cmd);
        exec($cmd, &$output);
        $gui->debug("<pre>".print_r($output, true)."</pre>");
        return $output;
    }
    
    
    function getipaddress($hostname) {
        /*
        * net lookup wxp
            192.168.1.132
        */
        global $gui;
        $cmd="net lookup $hostname";
        exec($cmd, &$output);
        if ( isset($output[0]) ) {
            $gui->debug("WINEXE:getipaddress($hostname)=".$output[0]);
            return $this->checkIP($output[0]);
        }
        $gui->debug("WINEXE:getipaddress($hostname) ERROR, can't resolve hostname");
        return "";
    }
    
    
    function checkIP($ip) {
        $cIP = ip2long($ip);
        $fIP = long2ip($cIP);
        return $fIP;
    }
    
    
    function poweroff() {
        return $this->exe('shutdown -s -t 30 -c "Apagado remoto"');
    }
    
    function reboot() {
        return $this->exe('shutdown -r -t 30 -c "Reinicio remoto"');
    }
}



?>
