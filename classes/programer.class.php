<?php


class Programer {
    function Programer($aula) {
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
            if( $confdata && $confdata[$varprefix.$i] == 'off') {
                $html.="<option value='off' selected>off</option>\n";
            }
            else {
                $html.="<option value='off'>off</option>\n";
            }
            foreach($this->hours as $hour) {
                if (intval($hour) < 10) {
                    $hour="0$hour";
                }
                foreach($this->minutes as $minute) {
                    if( $confdata && $confdata[$varprefix.$i] == $hour.":".$minute) {
                        $html.="<option value='".$hour.":".$minute."' selected>".$hour.":".$minute."</option>\n";
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
    
    
    function readIni() {
        $data=parse_ini_file(PROGRAMER_INI, true);
        return($data);
    }
    
    /*
    &#9650; ▲ &#9651; △
    &#9660; ▼ &#9661; ▽
    */
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
        return abs(intval($timeDiff/60));
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
                $gui->debug("aula obj don't have 'cn' element");
                continue;
            }
            /* load aula object */
            $aulas=$ldap->get_aulas($aula['cn']);
            if ( isset($aulas[0]) && $aulas[0]->get_num_computers() < 1) {
                $gui->debug("EMPTY aula ".$aula['cn']);
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
                *        /    |   \ 
                *     (-10)   0    (+10)
                *       /     |       \
                *   [15:00] [15:10]  [15:20] (cron calls)
                *
                *   15:00-15:10 => (-10) nothing
                *   15:10-15:10 =>   (0) do it
                *   15:20-15:10 => (-10) nothing
                */
                /* check time +- 9 minutes */
                $diff=$this->timeDiff($v, "$hour:$minute");
                $gui->debug("diff($v, $hour:$minute)=$diff");
                if ( $diff >= 9 ) {
                    /* diff > 9 minutes nothing to do */
                    continue;
                }
                
                $gui->debug("diff=$diff do the JOB");
                
                //FIXME delete continue
                continue;
                $action=NULL;
                $iniaction=preg_replace("/([0-9])/",'',$k);
                switch($iniaction) {
                    case "start": $action='wakeonlan'; break;
                    case "stop": $action='poweroff'; break;
                    case "reboot": $action='reboot'; break;
                }
                
                $computers=$ldap->get_computers_from_aula($aula['cn']);
                foreach( $computers as $computer) {
                    $gui->debug("Acción '$action' en equipo '".$computer->hostname());
                    //$res[]=$computer->action($action);
                    //$computer->action($subaction, $computer->macAddress);
                }
            }
        } /* foreach $this->config */
        $ldap->disconnect();
        unset($ldap);
    }
    

}

?>
