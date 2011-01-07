<?php


class Programer {
    function Programer($aula=NULL) {
        global $gui;
        $this->aula=$aula;
        $gui->debug("Programer($aula)");
        $this->weekdays=array('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo');
        $this->minutes=array('00','10', '20', '30', '40', '50');
        $this->hours=range(0,23);
        $this->config=$this->readIni();
        return;
    }
    
    function weekDays() {
        return $this->weekdays;
    }
    
    function getTimers($varprefix) {
        global $gui, $site;
        $confdata=NULL;
        if ( isset($this->config[$this->aula]) )
            $confdata=$this->config[$this->aula];
        $timers=array();
        for($i=0; $i<7; $i++) {
            $html="<select class='$varprefix' name='".$varprefix.$i."' id='".$varprefix.$i."'>";
            //$gui->debug($confdata[$varprefix.$i]." ¿=? off");
            if( $confdata && isset($confdata[$varprefix.$i]) && $confdata[$varprefix.$i] == 'off') {
                $html.="<option value='off' selected='selected'>off</option>\n";
            }
            else {
                $html.="<option value='off'>off</option>\n";
            }
            foreach($this->hours as $hour) {
                if (intval($hour) < 10) {
                    $hour="0$hour";
                }
                foreach($this->minutes as $minute) {
                    if( $confdata && isset($confdata[$varprefix.$i]) && $confdata[$varprefix.$i] == $hour.":".$minute) {
                        $html.="<option value='".$hour.":".$minute."' selected='selected'>".$hour.":".$minute."</option>\n";
                    }
                    else {
                        $html.="<option value='".$hour.":".$minute."'>".$hour.":".$minute."</option>\n";
                    }
                }
            }
            $html.="</select>";
            if( $i==0) {
                $html.="<a class='marginl5' href='javascript:programer(\"$varprefix\", \"".$varprefix."0\");'>";
                $html.="<img src='".$site["basedir"]."/img/right.gif' alt='=&gt;' title='Copiar a toda la semana' /></a>";
            }
            $timers[]=$html;
        }
        return $timers;
    }
    
    function getSO($varname, $types) {
        $html="<select name='".$varname."_menu' id='".$varname."_menu' > ";
        $html.="<option value=''>-----------</option>";
        foreach($types as $k => $v) {
            $selected="";
            if ( isset($this->config[$this->aula]["$varname"."_menu"]) ) {
                $selected=$this->config[$this->aula]["$varname"."_menu"]== $k ? 'selected="selected"' :'';
            }
            $html.="<option value='$k' $selected>$v</option>";
        }
        $html.="</select>";
        return $html;
    }
    
    
    function readIni() {
        $data=parse_ini_file(PROGRAMER_INI, true);
        return($data);
    }
    
    function saveAula($aula, $data, $faction) {
        global $gui;
        if ($faction == 'save') {
            $this->config[$aula]=$data;
        }
        elseif($faction == 'delete') {
            if ( isset($this->config[$aula] ) ) {
                unset($this->config[$aula]);
            }
            else {
                /* no existe el aula, nada que guardar */
                return true;
            }
        }
        else {
            $gui->session_error("Acción desconocida para el programador '$faction'");
            return;
        }
        $gui->debuga($this->config);
        return $this->write_ini_file($this->config, PROGRAMER_INI, true);
    }
    
    
    /* http://www.php.net/manual/en/function.parse-ini-file.php#91623 */
    function write_ini_file($assoc_arr, $path, $has_sections=FALSE) {
        global $gui;
        $content = "";
        
        if ($has_sections) {
            foreach ($assoc_arr as $key=>$elem) {
                $content .= "[".$key."]\n";
                foreach ($elem as $key2=>$elem2) {
                    if(is_array($elem2)) {
                        for($i=0;$i<count($elem2);$i++) {
                            $content .= $key2."[] = \"".$elem2[$i]."\"\n";
                        }
                    }
                    else if($elem2=="") $content .= $key2." = \n";
                    else $content .= $key2." = \"".$elem2."\"\n";
                } 
            } 
        } 
        else {
            foreach ($assoc_arr as $key=>$elem) {
                if(is_array($elem)) {
                    for($i=0;$i<count($elem);$i++) {
                        $content .= $key2."[] = \"".$elem[$i]."\"\n";
                    }
                }
                else if($elem=="") $content .= $key2." = \n";
                else $content .= $key2." = \"".$elem."\"\n";
            } 
        } 
        
        if (!$handle = fopen($path, 'w')) {
            $gui->session_error("No se puede abrir el archivo '$path' para escribir.");
            return false;
        }
        if (!fwrite($handle, $content)) {
            if ( $content != '' ) {
                $gui->session_error("No se puede escribir en el archivo '$path'");
                return false;
            }
        } 
        fclose($handle);
        //$gui->session_info("Archivo '$path' guardado y cerrado correctamente.");
        return true;
    }
    
    function isProgramed($aula) {
        if ( isset($this->config[$aula] ) )
            return true;
        return false;
    }
}

class CronProgramer {
    function CronProgramer() {
        /* read PROGRAMER_INI */
        $this->config=$this->readIni();
    }

    function readIni() {
        $data=parse_ini_file(PROGRAMER_INI, true);
        return($data);
    }

    function timeDiff($ini,$loop) {
        $firstTime=strftime("%Y-%m-%d $ini:00", time());
        $lastTime=strftime("%Y-%m-%d $loop:00", time());
        $firstTime=strtotime($firstTime);
        $lastTime=strtotime($lastTime);
        $timeDiff=$lastTime-$firstTime;
        // return minutes
        return intval($timeDiff/60);
    }

    function doJobs() {
        /* called every 10 minutes */
        global $gui;
        $hour=intval(strftime("%H"));
        if ( $hour < 10 )
            $hour="0$hour";
        $minute=intval(strftime("%M"));
        if ( $minute < 10 )
            $minute="0$minute";
        $weekday=intval(strftime("%u") -1);
        
        $ldap=new LDAP();
        
        foreach($this->config as $aula) {
            if ( ! isset($aula['cn']) ) {
                $gui->info("aula obj don't have 'cn' element");
                continue;
            }
            /* load aula object */
            $aulas=$ldap->get_aulas($aula['cn']);
            if ( isset($aulas[0]) && $aulas[0]->get_num_computers() < 1) {
                $gui->info("EMPTY aula ".$aula['cn']);
                /* empty aula / no computers */
                continue;
            }
            $thisaula=$aulas[0];
            //$gui->debug($thisaula);
            
            foreach($aula as $k=>$v) {
                if ($k == 'cn' || $k == 'safecn') {
                    /* no start/reboot/stop event */
                    continue;
                }
                if ( $v == 'off') {
                    /* no action in this event */
                    continue;
                }
                if ( ! preg_match("/$weekday\$/", $k) ) {
                    /* other week day */
                    continue;
                }
                $gui->debug("(".$aula['cn'].") $k => $v (now=$hour:$minute) (weekday=$weekday)");
                /*
                *
                *          [15:10] event in INI file
                *        /    |    \ 
                *     (-10)   0    (+10)
                *      /      |       \
                *  [15:00]  [15:10]  [15:20] (cron calls)
                *
                *   15:00-15:10 => (-10) nothing
                *   15:10-15:10 =>   (0) do it
                *   15:20-15:10 => (-10) nothing
                */
                /* check time +- 9 minutes */
                $diff=$this->timeDiff($v, "$hour:$minute");
                $gui->debug("diff($v, $hour:$minute)=$diff");
                if ( $diff < 0 || $diff >= 9 ) {
                    /* diff > 9 minutes nothing to do */
                    continue;
                }
                
                $action=preg_replace("/([0-9])/",'',$k);
                $gui->info("(".$aula['cn'].") $k => $v (now=$hour:$minute) (weekday=$weekday) diff=$diff, DO THE JOB action=$action");
                
                $os='';
                if ( isset($aula[$action.'_menu']) ) {
                    $os=$aula[$action.'_menu'];
                    if ($os != '') {
                        $gui->debug("aula boot($os)");
                        /* change pxelinux.cfg/xxxx to menu $os */
                        $gui->info("Change boot aula '".$aula['cn']."' to '$os'");
                        $aulas[0]->boot($os);
                    }
                }
                
                $computers=$ldap->get_computers_from_aula($aula['cn']);
                foreach( $computers as $computer) {
                    $gui->info("Acción '$action' en equipo '".$computer->hostname());
                    $computer->action($action, $computer->macAddress);
                }
            }
        } /* foreach $this->config */
        $ldap->disconnect();
        unset($ldap);
    }
    

}

?>
