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
        global $gui;
        $data=parse_ini_file("/home2/madrid/max-control/programer.ini", true);
        //$gui->debuga($data);
        return($data);
    }
    
    /*
    &#9650; ▲ &#9651; △
    &#9660; ▼ &#9661; ▽
    */

}

?>
