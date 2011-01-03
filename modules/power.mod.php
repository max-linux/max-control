<?php


/*
*
*  Modulo power
*
*/
global $gui;
global $permisos;
global $site;
global $module_actions;


$url=new URLHandler();

$module=$url->get("module");
$action=$url->get("action");
$subaction=$url->get("subaction");


if(DEBUG) {
    error_reporting(E_ALL);
}

if ( ! $permisos->is_connected() ) {
    $url->ir("","");
}

if ( $permisos->get_rol() == '' ) {
    $gui->session_error("No es profesor o administrador para acceder al módulo power");
    $url->ir("", "");
}

/*************************************************/


$module_actions=array(
        "aulas" => "Aulas",
        "equipos" => "Equipos",
        #"backharddi" => "Backharddi-NG",
);



/*************************************************/

function aulas($module, $action, $subaction) {
    global $gui, $url;
    // mostrar lista de aulas
    $ldap=new LDAP();
    $filter=leer_datos('Filter');
    $aulas=$ldap->get_aulas($filter);
    
    $urlform=$url->create_url($module, $action);
    
    $pager=new PAGER($aulas, $urlform, 0, $args='', NULL);
    $pager->processArgs( array('Filter', 'skip', 'sort') );
    $aulas=$pager->getItems();
    $pager->sortfilter="(cn|cachednumcomputers)";
    
    $data=array("aulas" => $aulas, 
                "filter" => $filter,
                "urlform" => $urlform,
                "urlpoweroff"=>$url->create_url($module, 'aula_preguntar', 'poweroff'),
                "urlreboot"=>$url->create_url($module, 'aula_preguntar', 'reboot'),
                "urlrebootwindows"=>$url->create_url($module, 'aula_preguntar', 'rebootwindows'),
                "urlrebootmax"=>$url->create_url($module, 'aula_preguntar', 'rebootmax'),
                "urlbackharddi"=>$url->create_url($module, 'aula_preguntar', 'rebootbackharddi'),
                "urlwakeonlan" => $url->create_url($module, 'aula_preguntar', 'wakeonlan'),
                "pager"=>$pager);
    $gui->add( $gui->load_from_template("power_aulas.tpl", $data) );
}

function aula_preguntar($module, $action, $subaction) {
    global $gui, $url;
    $aula=leer_datos('args');
    
    $ldap = new LDAP();
    $computers=$ldap->get_computers_from_aula($aula);
    
    $urlaction=$url->create_url($module, 'do', "$subaction/$aula");

    $data=array("aula" => $aula, 
                "action" => $subaction,
                "computers"=> $computers,
                "urlaction"=>$urlaction);
    $gui->add( $gui->load_from_template("power_aulas_do.tpl", $data) );
}

function aulado($module, $action, $subaction) {
    global $gui, $url;
    $aula=leer_datos('args');
    $ldap = new LDAP();
    
    /* ver si el profesor tiene permiso en el aula */
    $aulas=$ldap->get_aulas($aula);
    
    if ( count($aulas) != 1 ) {
        $gui->session_error("Aula '$aula' no encontrado");
        $url->ir($module, "aulas");
    }
    if ( ! $aulas[0]->teacher_in_aula() ) {
        $gui->session_error("No se tiene permiso para modificar el aula '$aula'");
        $url->ir($module, "aulas");
    }
    /***************************/
    
    
    $computers=$ldap->get_computers_from_aula($aula);
    foreach( $computers as $computer) {
        $gui->debug("Acción $subaction en equipo '".$computer->hostname()."' tiempo: " . time_end() );
        //$res[]=$computer->action($action);
        $computer->action($subaction, $computer->macAddress);
    }
    $gui->debug("Finalizadas acciones tiempo: " . time_end() );
    
    $gui->session_info("Finalizada acción '$subaction' en aula '$aula'");
    if ( ! DEBUG)
        $url->ir($module, "aulas");
}

function equipos($module, $action, $subaction) {
    global $gui, $url;
    // mostrar lista de equipos
    $ldap=new LDAP();
    $filter=leer_datos('Filter');
    $equipos=$ldap->get_computers( $filter );
    
    $urlform=$url->create_url($module, $action);
    
    $pager=new PAGER($equipos, $urlform, 0, $args='', NULL);
    $pager->processArgs( array('Filter', 'skip', 'sort') );
    $equipos=$pager->getItems();
    $pager->sortfilter="(uid|ipHostNumber|macAddress|sambaProfilePath)";
    
    $data=array("equipos" => $equipos, 
                "filter" => $filter,
                "urlform" => $urlform,
                "urlpoweroff"=>$url->create_url($module, 'equipo_preguntar', 'poweroff'),
                "urlreboot"=>$url->create_url($module, 'equipo_preguntar', 'reboot'),
                "urlrebootwindows"=>$url->create_url($module, 'equipo_preguntar', 'rebootwindows'),
                "urlrebootmax"=>$url->create_url($module, 'equipo_preguntar', 'rebootmax'),
                "urlbackharddi"=>$url->create_url($module, 'equipo_preguntar', 'rebootbackharddi'),
                "urlwakeonlan" => $url->create_url($module, 'equipo_preguntar', 'wakeonlan'),
                "pager"=>$pager);
    $gui->add( $gui->load_from_template("power_equipos.tpl", $data) );
}

function equipo_preguntar($module, $action, $subaction) {
    global $gui, $url;
    // 
    $equipo=leer_datos('args');
    
    $ldap = new LDAP();
    $computers=$ldap->get_computers( $equipo . '$' );
    
    $urlaction=$url->create_url($module, 'docomputer', "$subaction/$equipo");

    $data=array("equipo" => $equipo, 
                "action" => $subaction,
                "computers"=> $computers,
                "urlaction"=>$urlaction);
    $gui->add( $gui->load_from_template("power_equipos_do.tpl", $data) );
}

function docomputer($module, $action, $subaction) {
    global $gui, $url;
    // 
    $equipo=leer_datos('args');
    
    $ldap = new LDAP();
    $computers=$ldap->get_computers( $equipo . '$' );
    
    if ( ! $computers[0]->teacher_in_computer() ) {
        $gui->session_error("No se tiene permiso para modificar el equipo '$equipo'");
        $url->ir($module, "equipos");
    }
    //$gui->debuga($computers);
    
    foreach( $computers as $computer) {
        $gui->debug("Acción $subaction en equipo '".$computer->hostname()."' tiempo: " . time_end() );
        //$res[]=$computer->action($action);
        $computer->action($subaction, $computer->macAddress);
    }
    // si es backharddi redirigir a un iframe
    if ( $permisos->is_admin() && ($action == 'rebootbackharddi') ) {
        $url->ir($module, "backharddi");
    }
    
    
    $gui->debug("Finalizadas acciones tiempo: " . time_end() );
    if (! DEBUG)
        $url->ir($module, "equipos");
}

function backharddi($module, $action, $subaction) {
    global $gui, $url;
    if ( ! $permisos->is_admin() ) {
        $gui->session_error("Sólo pueden acceder al clonado los administradores.");
        $url->ir($module,"");
    }
    //$gui->debuga($_SERVER);
    $urliframe="http://".$_SERVER['SERVER_NAME'].":9091/";
    $data=array("urliframe"=>$urliframe);
    $gui->add( $gui->load_from_template("backharddi.tpl", $data) );
}



switch($action) {
    case "": $url->ir($module, "aulas"); break;
    
    case "aulas":            aulas($module, $action, $subaction); break;
    case "aula_preguntar":   aula_preguntar($module, $action, $subaction); break;
    case "do":               aulado($module, $action, $subaction); break;
    case "equipos":          equipos($module, $action, $subaction); break;
    case "equipo_preguntar": equipo_preguntar($module, $action, $subaction); break;
    case "docomputer":       docomputer($module, $action, $subaction); break;
    case "backharddi":       backharddi($module, $action, $subaction); break;
    
    default: $gui->session_error("Accion desconocida '$action' en modulo $module");
    /*default: $url->ir($module, "equipo");*/
}
?>
