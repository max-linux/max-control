<?php
if(DEBUG)
    error_reporting(E_ALL);
/*
*  1.- Instalación del servicio
*     1.1.- Entrar al Windows como administrador del dominio
*     1.2.- Abrir una consola "cmd" y escribir:
*                net share admin$
*     1.3.- Desde el max-server ejecutar
*                winexe -U EBOX/admin //192.168.1.132 "ipconfig /all"
*                winexe -U DOMINIO/usuario%contraseña --interactive=0 //192.168.x.x 'cmd -c ipconfig'
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
    var $mac='';
    var $hostname='';
    var $alive=false;
    var $return_code=-1;
    var $output=array();
    var $timeout=False;
    var $stderr=array();
    var $msg='';
    
    
    function WINEXE($ip='') {
        global $gui;
        $this->ip=$ip;
        $this->initialized=false;
    }
    
    function init() {
        global $gui;
        if ( ($this->ip != $this->checkIP($this->ip) ) ){
            $this->hostname=$this->ip;
            $this->ip=$this->getIpAddress($this->hostname);
        }
        
        $this->basecmd=WINEXE ." -U " . LDAP_DOMAIN . '/' . LDAP_ADMIN . '%' . LDAP_PASS;
        $this->basecmd.= ' --interactive=0 ';
        if ( $this->ip != '' )
            $this->basecmd.= " //" . $this->ip . " ";
        else
            $this->basecmd.= " //" . $this->hostname . " ";
        $this->initialized=true;
    }
    
    function process_output($out) {
        global $gui;
        $this->return_code=-1;
        $this->output=array();
        $this->stderr=array();
        $this->timeout=False;
        foreach ($out as $line){
            //$gui->debug("process_output:".print_r($line, true));
            if ( preg_match('/RETURN/', $line) ) {
                $tmp=preg_split('/\s/', $line);
                $this->return_code=$tmp[1];
            }
            elseif ( preg_match('/STDOUT/', $line) ) {
                $tmp=preg_split('/STDOUT /', $line);
                $this->output[]=$tmp[1];
            }
            elseif ( preg_match('/TIMEOUT/', $line) ) {
                $this->timeout=True;
            }
            else {
                if ($line != '') {
                    $this->stderr[]=$line;
                }
            }
        }
        
        $gui->debug("process_output: return_code=".print_r($this->return_code, true));
        $gui->debug("process_output: timeout=".$this->timeout);
        $gui->debug("process_output: output=<pre>".print_r($this->output, true)."</pre>");
        $gui->debug("process_output: stderr=<pre>".print_r($this->stderr, true)."</pre>");
        
        if ( $this->timeout ) {
            $gui->session_error("Tiempo de espera agotado al ejecutar comando en '".$this->hostname."'");
            return;
        }
        elseif ($this->return_code != '0') {
            $gui->session_error("Error ejecutando comando en '".$this->hostname."', código devuelto: ".$this->return_code);
            $gui->session_error("<pre>");
            foreach($this->stderr as $line) {
                $gui->session_error($line);
            }
            $gui->session_error("</pre>\n<br/><br/>");
            return;
        }
        else {
            $gui->debug("process_output: ejecución correcta, imprimiendo msg OK");
            $gui->session_info($this->msg);
        }
    }
    
    function windowsexe( $targetcmd ) {
        global $gui;
        if ( ! $this->initialized )
            $this->init();
        
        if ($this->ip == '') {
            $this->return_code=-1;
            $this->stderr="ERROR, can't resolve hostname";
            $gui->session_error("Error resolviendo IP del equipo '".$this->hostname."'");
            return False;
        }
        $cmd=$this->basecmd . "'". $targetcmd ."'";
        $gui->debug("WINEXE:windowsexe cmd=".$cmd);
        exec($cmd, $output);
        $this->process_output($output);
        return $output;
    }
    
    
    function linuxexe($targetcmd) {
        global $gui;
        if ( ! $this->initialized )
            $this->init();
        
        $targetcmd="sudo max-control $targetcmd";
        $gui->debug("WINEXE:linuxexe() cmd='$targetcmd'");
        
        // test for libssh2-php
        if (!function_exists("ssh2_connect")) {
            die("WINEXE:linuxexe() function ssh2_connect doesn't exist, install libssh2-php package and restart apache");
            return false;
        }
        
        if(!($con = ssh2_connect($this->ip, LINUX_PORT))){
            $gui->debug("WINEXE:linuxexe() fail: unable to establish connection");
            return false;
        }
        
        //$gui->debug("WINEXE:linuxexe() connected, now auth".print_r($con, true));
        
        // try to authenticate with username root, password secretpassword
        if( ! ssh2_auth_password($con, LDAP_ADMIN, LDAP_PASS) ) {
            $gui->debug("WINEXE:linuxexe() fail: unable to authenticate");
            $gui->session_error("Error ejecutando comando en '".$this->hostname."', fallo de autenticación.");
            return false;
        }
        
        //$gui->debug("WINEXE:linuxexe() auth ok, now exec command");
        
        // logged, execute the command
        $gui->debug("WINEXE:linuxexe(".$this->ip.") cmd='$targetcmd'");
        if ( ! ($stream = ssh2_exec($con, $targetcmd)) ) {
            $gui->debug("WINEXE:linuxexe()fail: unable to execute command");
            $gui->session_error("Error ejecutando comando en '".$this->hostname."', no se pudo ejecutar el comando.");
            return false;
        }
        
        // collect returning data from command
        stream_set_blocking($stream, true);
        $data = "";
        while ($buf = fread($stream,4096)) {
            $data .= $buf;
        }
        fclose($stream);
        $gui->session_info($this->msg);
        return preg_split("#\\n#i",$data);
    }
    
    function getIpAddress($hostname) {
        /*
        * net lookup wxp
            192.168.1.132
        */
        global $gui;
        $cmd="net lookup $hostname";
        $gui->debug("WINEXE:getIpAddress($hostname) cmd='$cmd'");
        exec($cmd, $output);
        if ( isset($output[0]) ) {
            $gui->debug("WINEXE:getIpAddress($hostname)=".$output[0]);
            return $this->checkIP($output[0]);
        }
        $gui->debug("WINEXE:getIpAddress($hostname) ERROR, can't resolve hostname");
        return "";
    }
    
    function getMacAddress($hostname) {
        global $gui;
        $ip=$hostname;
        if ( ($hostname != $this->checkIP($hostname) ) ){
            // se nos ha pasado algo que no es una IP
            $ip=$this->getIpAddress($hostname);
        }
        // leemos /proc/net/arp
        /*
        cat /proc/net/arp 
        IP address       HW type     Flags       HW address            Mask     Device
        10.0.2.2         0x1         0x2         52:54:00:12:35:02     *        eth0
        192.168.1.2      0x1         0x2         00:1a:6b:6a:be:c9     *        eth1
        192.168.1.132    0x1         0x2         08:00:27:2e:50:ff     *        eth1
        */
        $mac='';
        $file_handle = fopen('/proc/net/arp', 'r');
        while (!feof($file_handle) ) {
            $line_of_text = fgets($file_handle);
            $parts = preg_split ("/\s+/", $line_of_text);
            $gui->debuga($parts);
            /*
            Array
            (
                [0] => 192.168.1.132
                [1] => 0x1
                [2] => 0x2
                [3] => 08:00:27:2e:50:ff
                [4] => *
                [5] => eth1
                [6] => 
            )
            */
            if ( (count($parts) == 7) && ($parts[0] == $ip) ) {
                $mac=$parts[3];
                break;
            }
        }
        fclose($file_handle);
        
        return $mac;
    }
    
    function checkIP($ip) {
        $cIP = ip2long($ip);
        $fIP = long2ip($cIP);
        return $fIP;
    }
    
    function isLinux() {
        global $gui;
        $open=false;
        
        if (! $this->is_alive())
            return false;
        
        //$gui->debug("isLinux(): try to open ".LINUX_PORT." port in".$this->ip);
        $fp = @fsockopen($this->ip, LINUX_PORT, $errno, $errstr, $timeout=PROBE_TIMEOUT);
        if (!$fp) {
            $gui->debug("isLinux(".$this->ip."):ERROR: $errno - $errstr, time: ". time_end());
        } else {
            $gui->debug("isLinux(".$this->ip."):port open, time: ". time_end());
            $open=true;
            fclose($fp);
        }
        return $open;
    }
    
    function is_alive () {
        global $gui;
        $this->alive=false;
        $this->init();
        // no ejecutar pings a cosas que no existen
        if ($this->ip == '' || $this->ip =='0.0.0.0')
            return false;
        
        //$gui->debug("is_alive()".$this->ip);
        $str = exec("ping -c 1 -w 1 ".$this->ip, $input, $result);
        if ($result == 0) {
            $gui->debug("is_live(".$this->ip.") host is alive, time: ". time_end() );
            $this->alive=true;
        }
        else {
            $gui->debug("is_live(".$this->ip.") host unreachable, time: ". time_end() );
        }
        return $this->alive;
    }
    
    function poweroff( $mac ) {
        global $gui;
        if ( ! $this->is_alive() ) {
            $gui->session_error("No se puede apagar '".$this->hostname."', el equipo no responde.");
            return false;
        }
        
        //$gui->session_info("Equipo '".$this->hostname."' apagado.");
        $this->msg="Equipo '".$this->hostname."' apagado.";
        if (! $this->isLinux() )
            return $this->windowsexe('shutdown -s -t '.POWEROFF_REBOOT_TIMEOUT.' -c "Apagado remoto desde max-control"');
        else {
            return $this->linuxexe('poweroff '.POWEROFF_REBOOT_TIMEOUT);
        }
    }
    
    function reboot( $mac ) {
        global $gui;
        if ( ! $this->is_alive() ) {
            $gui->session_error("No se puede reiniciar '".$this->hostname."', el equipo no responde.");
            return false;
        }
        //$gui->session_info("Equipo '".$this->hostname."' reiniciado.");
        $this->msg="Equipo '".$this->hostname."' reiniciado.";
        if (! $this->isLinux() )
            return $this->windowsexe('shutdown -r -t '.POWEROFF_REBOOT_TIMEOUT.' -c "Reinicio remoto desde max-control"');
        else {
            return $this->linuxexe('reboot '.POWEROFF_REBOOT_TIMEOUT);
        }
    }
    
    function wakeonlan( $mac ) {
        global $gui;
        // need MAC address to pass to pywakeonlan
        if ($mac == '')
            $mac=$this->mac;
        
        $cmd=PYWAKEONLAN . " $mac";
        exec($cmd, $output);
        // $output[0] can be OK or ERROR
        $gui->debug("WINEXE:wakeonlan($mac)<pre>".print_r($output, true)."</pre>");
        if ( isset($output[0]) && ($output[0] == 'OK') ) {
            $gui->session_info("Equipo '".$this->hostname."' enviado paquete WAKEONLAN.");
            return true;
        }
        $gui->session_error("Error al enviar paquete WOL al equipo '".$this->hostname."<pre>".print_r($output, true)."</pre>");
        return false;
    }
    
    function mount( $iso ) {
        global $gui;
        if (! $this->isLinux() ) {
            return $this->windowsexe("mount.bat mount '$iso'");
        }
        else {
            return $this->linuxexe("mount '$iso'");
        }
    }
    
    function umount() {
        global $gui;
        if (! $this->isLinux() ){
            return $this->windowsexe("mount.bat umount");
        }
        else {
            return $this->linuxexe("umount");
        }
    }

    function fork($action) {
        global $gui;
        /* call action.php in background */
        //$cmd="php /usr/share/max-control/action.php";
        $cmd="cd /usr/share/max-control && ";
        $cmd.="php action.php action=$action ip='".$this->ip. "' ";
        $cmd.=">> ".FORK_LOGFILE." 2>&1 &";
        
        $gui->debug($cmd);
        
        // launch in background
        pclose(popen($cmd, "r"));
    }

/* end of class WINEXE */
}

