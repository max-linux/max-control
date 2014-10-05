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


global $url;

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
);

global $multiple_actions;
$multiple_actions=array("poweroff" =>         "Apagar seleccionados",
                        "reboot"=>            "Reiniciar seleccionados",
                        "wakeonlan" =>        "Encender seleccionados",
                        "rebootwindows" =>    "Reiniciar en Windows los seleccionados",
                        "rebootmax" =>        "Reiniciar en MAX los seleccionados",
                        );

/*************************************************/

function aulas($module, $action, $subaction) {
    global $gui, $url, $multiple_actions, $permisos;
    // mostrar lista de aulas
    global $ldap;
    $filter=leer_datos('Filter');
    $filter=leer_datos('Filter');
    $filtertxt='';
    // if($filter != '') $filtertxt="*$filter*";
    $aulas=$ldap->get_aulas($filter);
    
    $urlform=$url->create_url($module, $action);
    
    $pager=new PAGER($aulas, $urlform, 0, $args='', NULL);
    $pager->processArgs( array('Filter', 'skip', 'sort') );
    $aulas=$pager->getItems();
    $pager->sortfilter="(cn|cachednumcomputers)";
    
    $mode='admin';
    if ( ! $permisos->is_admin() ) {
        $mode='';
    }
    
    $data=array("aulas" => $aulas, 
                "filter" => $filter,
                "urlform" => $urlform,
                "mode" => $mode,
                "urlpoweroff"=>$url->create_url($module, 'aula_preguntar', 'poweroff'),
                "urlreboot"=>$url->create_url($module, 'aula_preguntar', 'reboot'),
                "urlrebootwindows"=>$url->create_url($module, 'aula_preguntar', 'rebootwindows'),
                "urlrebootmax"=>$url->create_url($module, 'aula_preguntar', 'rebootmax'),
                "urlwakeonlan" => $url->create_url($module, 'aula_preguntar', 'wakeonlan'),
                "urlformmultiple" => $url->create_url($module, 'aulamultiple_preguntar'),
                "multiple_actions" => $multiple_actions,
                "pager"=>$pager);
    $gui->add( $gui->load_from_template("power_aulas.tpl", $data) );
}

function aula_preguntar($module, $action, $subaction) {
    global $gui, $url;
    $aula=leer_datos('args');
    
    global $ldap;
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
    global $ldap;
    
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
    global $gui, $url, $multiple_actions, $permisos;
    // mostrar lista de equipos
    global $ldap;
    $filter=leer_datos('Filter');
    $equipos=$ldap->get_computers( $filter );
    
    $urlform=$url->create_url($module, $action);
    
    $pager=new PAGER($equipos, $urlform, 0, $args='', NULL);
    $pager->processArgs( array('Filter', 'skip', 'sort') );
    $equipos=$pager->getItems();
    $pager->sortfilter="(uid|ipHostNumber|macAddress|aula)";
    
    $mode='admin';
    if ( ! $permisos->is_admin() ) {
        $mode='';
    }
    
    $data=array("equipos" => $equipos, 
                "filter" => $filter,
                "urlform" => $urlform,
                "mode" => $mode,
                "urlpoweroff"=>$url->create_url($module, 'equipo_preguntar', 'poweroff'),
                "urlreboot"=>$url->create_url($module, 'equipo_preguntar', 'reboot'),
                "urlrebootwindows"=>$url->create_url($module, 'equipo_preguntar', 'rebootwindows'),
                "urlrebootmax"=>$url->create_url($module, 'equipo_preguntar', 'rebootmax'),
                "urlwakeonlan" => $url->create_url($module, 'equipo_preguntar', 'wakeonlan'),
                "urlformmultiple" => $url->create_url($module, 'equipomultiple_preguntar'),
                "multiple_actions" => $multiple_actions,
                "pager"=>$pager);
    $gui->add( $gui->load_from_template("power_equipos.tpl", $data) );
}

function equipo_preguntar($module, $action, $subaction) {
    global $gui, $url;
    // 
    $equipo=leer_datos('args');
    
    global $ldap;
    $computers=$ldap->get_computers( $equipo . '$' );
    
    $urlaction=$url->create_url($module, 'docomputer', "$subaction/$equipo");

    $data=array("equipo" => $equipo, 
                "action" => $subaction,
                "computers"=> $computers,
                "urlaction"=>$urlaction);
    $gui->add( $gui->load_from_template("power_equipos_do.tpl", $data) );
}

function docomputer($module, $action, $subaction) {
    global $gui, $url, $permisos;
    // 
    $equipo=leer_datos('args');
    
    global $ldap;
    $computers=$ldap->get_computers( $equipo . '$' );
    
    if ( ! $computers[0]->teacher_in_computer() ) {
        $gui->session_error("No tiene permiso para modificar el equipo '$equipo'");
        $url->ir($module, "equipos");
    }
    //$gui->debuga($computers);
    
    foreach( $computers as $computer) {
        $gui->debug("Acción $subaction en equipo '".$computer->hostname()."' tiempo: " . time_end() );
        //$res[]=$computer->action($action);
        $computer->action($subaction, $computer->macAddress);
    }
    
    
    $gui->debug("Finalizadas acciones tiempo: " . time_end() );
    if (! DEBUG)
        $url->ir($module, "equipos");
}



function equipomultiple_preguntar($module, $action, $subaction) {
    global $gui, $url;
    $computers=leer_datos('computers');
    $computersarray=preg_split('/,/', leer_datos('computers'));
    
    $gui->debuga($computers);
    if( ! isset($computers[0]) ) {
        $gui->session_error("No se han seleccionado equipos.");
        $url->ir($module, "equipos");
    }
    
    $faction=leer_datos('faction');
    if ( $faction == '' ) {
        $gui->session_error("Accion desconocida.");
        $url->ir($module, "equipos");
    }

    $data=array("computers" => $computers, 
                "computersarray" => $computersarray,
                "faction" => $faction,
                "urlaction"=>$url->create_url($module, 'equipomultiple_preguntardo'));
    $gui->add( $gui->load_from_template("power_equiposmultiple_do.tpl", $data) );
}

function equipomultiple_preguntardo($module, $action, $subaction){
    global $gui, $url;
    $gui->debuga($_POST);
    $computers=preg_split('/,/', leer_datos('computers'));
    $gui->debuga($computers);
    if( ! isset($computers[0]) ) {
        $gui->session_error("No se han seleccionado equipos.");
        $url->ir($module, "equipos");
    }
    
    $faction=leer_datos('faction');
    if ( $faction == '' ) {
        $gui->session_error("Accion desconocida.");
        $url->ir($module, "equipos");
    }
    
    global $ldap;
    $counter=0;
    foreach( $computers as $hostname) {
        $computer=$ldap->get_computers( $hostname . '$' );
        if ( ! isset($computer[0]) )
            continue;
        if ( ! $computer[0]->teacher_in_computer() ) {
            $gui->session_error("No tiene permiso para modificar el equipo '$equipo'");
            continue;
        }
        $gui->debug("Acción $faction en equipo '".$computer[0]->hostname());
        $computer[0]->action($faction, $computer[0]->macAddress);
        $counter++;
    }
    $gui->session_info("Accion '$faction' realizada en $counter equipo/s.");
    if (! DEBUG)
        $url->ir($module, "equipos");
}

function aulamultiple_preguntar($module, $action, $subaction) {
    global $gui, $url;
    $aulas=leer_datos('aulas');
    $aulasarray=preg_split('/,/', leer_datos('aulas'));
    
    $gui->debuga($aulas);
    if( ! isset($aulas[0]) ) {
        $gui->session_error("No se han seleccionado aulas.");
        $url->ir($module, "aulas");
    }
    
    $faction=leer_datos('faction');
    if ( $faction == '' ) {
        $gui->session_error("Accion desconocida.");
        $url->ir($module, "aulas");
    }

    $data=array("aulas" => $aulas, 
                "aulasarray" => $aulasarray,
                "faction" => $faction,
                "urlaction"=>$url->create_url($module, 'aulamultiple_preguntardo'));
    $gui->add( $gui->load_from_template("power_aulasmultiple_do.tpl", $data) );
}

function aulamultiple_preguntardo($module, $action, $subaction){
    global $gui, $url;
    $gui->debuga($_POST);
    $aulas=preg_split('/,/', leer_datos('aulas'));
    $gui->debuga($aulas);
    if( ! isset($aulas[0]) ) {
        $gui->session_error("No se han seleccionado aulas.");
        $url->ir($module, "aulas");
    }
    
    $faction=leer_datos('faction');
    if ( $faction == '' ) {
        $gui->session_error("Accion desconocida.");
        $url->ir($module, "aulas");
    }
    
    global $ldap;
    $counter=0;
    foreach($aulas as $aula) {
        $computers=$ldap->get_computers_from_aula($aula);
        foreach( $computers as $computer) {
            $gui->debug("Acción $faction en equipo '".$computer->hostname());
            $computer->action($faction, $computer->macAddress);
            $counter++;
        }
    }
    $gui->session_info("Accion '$faction' realizada en $counter equipo/s.");
    if (! DEBUG)
        $url->ir($module, "aulas");
}

switch($action) {
    case "": $url->ir($module, "aulas"); break;
    
    case "aulas":            aulas($module, $action, $subaction); break;
    case "aula_preguntar":   aula_preguntar($module, $action, $subaction); break;
    case "do":               aulado($module, $action, $subaction); break;
    case "equipos":          equipos($module, $action, $subaction); break;
    case "equipo_preguntar": equipo_preguntar($module, $action, $subaction); break;
    case "docomputer":       docomputer($module, $action, $subaction); break;
    
    case "equipomultiple_preguntar": equipomultiple_preguntar($module, $action, $subaction); break;
    case "equipomultiple_preguntardo": equipomultiple_preguntardo($module, $action, $subaction); break;
    case "aulamultiple_preguntar": aulamultiple_preguntar($module, $action, $subaction); break;
    case "aulamultiple_preguntardo": aulamultiple_preguntardo($module, $action, $subaction); break;
    
    
    default: $gui->session_error("Accion desconocida '$action' en modulo $module");
    /*default: $url->ir($module, "equipo");*/
}


