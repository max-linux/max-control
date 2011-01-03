<?php


/*
*
*  Modulo boot
*
*/
global $gui;
global $permisos;
global $site;
global $module_actions;

$url=new URLHandler();

if ( ! $permisos->is_connected() ) {
    $url->ir("","");
}

if ( ! $permisos->is_admin() ) {
    $gui->session_error("Sólo pueden acceder al módulo de arranque los administradores.");
    $url->ir("","");
}



$module=$url->get("module");
$action=$url->get("action");
$subaction=$url->get("subaction");

$module_actions=array(
        "aula" => "Por aulas",
        "equipo" => "Por equipos",
);

function refresh($module, $action, $subaction) {
    global $gui, $url;
    $gui->debug("sudo ".MAXCONTROL." pxe --genpxelinux 2>&1");
    exec("sudo ".MAXCONTROL." pxe --genpxelinux 2>&1", &$output);
    if ( ! isset($output[0]) )
        $gui->session_info("Actualizados aulas y equipos para arranque PXE.");
    else
        $gui->session_error("Error actualizando aulas y equipos para arranque PXE:<br/><pre>". implode("\n<br/>", $output). "</pre>");
    if(! DEBUG)
        $url->ir($module, "");
}

function clean($module, $action, $subaction) {
    global $gui, $url;
    $gui->debug("sudo ".MAXCONTROL." pxe --clean 2>&1");
    exec("sudo ".MAXCONTROL." pxe --clean 2>&1", &$output);
    refresh($module, $action, $subaction);
    if(! DEBUG)
        $url->ir($module, "");
}

function equipo($module, $action, $subaction) {
    global $gui, $url;
    
    $button=leer_datos('button');
    $gui->debug("button='$button'");
    if( $button =='Limpiar archivos PXE'){
        $url->ir($module, "clean");
    }
    if( $button =='Actualizar archivos PXE'){
        $url->ir($module, "refresh");
    }
    
    // mostrar lista de equipos con macAddress
    $ldap=new LDAP();
    $filter=leer_datos('Filter');
    if ( ($filter != '') && (substr($filter, -1) != '$') )
        $filter.='$';
    $equipos=$ldap->get_computers( $filter );
    $urlform=$url->create_url($module, $action);
    
    
    $pager=new PAGER($equipos, $urlform, 0, $args='', NULL);
    $pager->processArgs( array('Filter', 'skip', 'sort') );
    $equipos=$pager->getItems();
    $pager->sortfilter="(uid|ipHostNumber|macAddress)";
    
    $data=array("equipos" => $equipos,
                "filter" => $filter, 
                "urlform" => $urlform, 
                "urleditar"=>$url->create_url($module,'editarequipo'),
                "pager"=>$pager);
    $gui->add( $gui->load_from_template("ver_equipos_boot.tpl", $data) );
}

function editarequipo($module, $action, $subaction) {
    global $gui, $url;
    $ldap=new LDAP();
    $equipos=$ldap->get_computers($subaction.'$');
    //$gui->debuga($equipos);
    if ( count($equipos) != 1 ) {
        $gui->session_error("Equipo '$subaction' no encontrado");
        $url->ir($module, "equipo");
    }
    $aulas=$ldap->get_aulas();
    
    $tipos=$ldap->getBootMenus($aula=True);
    
    $urlform=$url->create_url($module, "editarequipodo");
    
    $data=array("u" => $equipos[0], 
                "aulas" => $aulas,
                "tipos" => $tipos,
                "filter" => $filter, 
                "urlform" => $urlform);
    
    $gui->add( $gui->load_from_template("bootequipodo.tpl", $data) );
}

function editarequipodo($module, $action, $subaction) {
    global $gui, $url;
    $gui->debug( "<pre>" . print_r($_POST,true) . "</pre>");
    $ldap=new LDAP();
    $hostname=leer_datos('hostname');
    $equipos=$ldap->get_computers($hostname.'$');
    $boot=leer_datos('boot');
    $gui->debuga($equipos[0]->show());
    
    if ( count($equipos) != 1 ) {
        $gui->session_error("Equipo '$hostname' no encontrado");
        $url->ir($module, "equipo");
    }
    
    if ( ! $equipos[0]->teacher_in_computer() ) {
        $gui->session_error("No se tiene permiso para modificar el equipo '$hostname'");
        $url->ir($module, "equipo");
    }
    
    $equipos[0]->boot($boot);
    if ( leer_datos('reboot') == '1' ) {
        $equipos[0]->action('reboot');
    }
    $url->ir($module, "equipo");
}


function aula($module, $action, $subaction) {
    global $gui, $url;
    
    $button=leer_datos('button');
    $gui->debug("button='$button'");
    
    if( $button =='Limpiar archivos PXE'){
        $url->ir($module, "clean");
    }
    if( $button =='Actualizar archivos PXE'){
        $url->ir($module, "refresh");
    }
    
    
    // mostrar lista de aulas
    $ldap=new LDAP();
    $filter=leer_datos('Filter');
    $aulas=$ldap->get_aulas($filter);
    //$gui->debuga($aulas);
    $urlform=$url->create_url($module, $action);
    
    $pager=new PAGER($aulas, $urlform, 0, $args='', NULL);
    $pager->processArgs( array('Filter', 'skip', 'sort') );
    $aulas=$pager->getItems();
    $pager->sortfilter="(cn|cachedBoot)";
    
    
    $data=array("aulas" => $aulas, 
                "filter" => $filter,
                "urlform" => $urlform,
                "urleditar"=>$url->create_url($module, 'editaaula', 'arranque'),
                "pager" => $pager);
    $gui->add( $gui->load_from_template("bootaulas.tpl", $data) );
}

function editaaula($module, $action, $subaction) {
    global $gui, $url;
    $ldap=new LDAP();
    $aulas=$ldap->get_aulas(leer_datos('args'));
    //$gui->debuga($aulas);
    if ( count($aulas) != 1 ) {
        $gui->session_error("Aula '".leer_datos('args')."' no encontrada.");
        $url->ir($module, "aula");
    }
    
    $tipos=$ldap->getBootMenus($aula=False);
    
    $urlform=$url->create_url($module, "editaaulado");
    
    $data=array("aula" => $aulas[0],
                "aulaboot" => $aulas[0]->getBoot(),
                "tipos" => $tipos,
                "filter" => $filter, 
                "urlform" => $urlform);
    
    $gui->add( $gui->load_from_template("bootaulado.tpl", $data) );
}

function editaaulado($module, $action, $subaction) {
    global $gui, $url;
    $ldap=new LDAP();
    $gui->debug( "<pre>" . print_r($_POST,true) . "</pre>");
    $aulas=$ldap->get_aulas(leer_datos('aula'));
    $boot=leer_datos('boot');
    //$gui->debuga($aulas);
    
    if ( count($aulas) != 1 ) {
        $gui->session_error("Aulas '".leer_datos('aula')."' no encontrado");
        $url->ir($module, "aula");
    }
    if ( ! $aulas[0]->teacher_in_aula() ) {
        $gui->session_error("No se tiene permiso para editar el aula '".leer_datos('aula')."'");
        $url->ir($module, "aula");
    }
    
    $aulas[0]->boot($boot);
    
    if ( leer_datos('reboot') == '1' ) {
        $computers=$ldap->get_computers_from_aula($aula);
        foreach( $computers as $computer) {
            $computer->action('reboot');
        }
        $gui->session_info("Aula '".leer_datos('aula')."' reiniciada");
    }
    if(!DEBUG)
        $url->ir($module, "aula");
}




//$gui->session_info("Accion '$action' en modulo '$module'");
switch($action) {
    case "": $url->ir($module, "aula"); break;
    
    case "refresh": refresh($module, $action, $subaction); break;
    case "clean": clean($module, $action, $subaction); break;
    
    case "equipo": equipo($module, $action, $subaction); break;
    case "editarequipo": editarequipo($module, $action, $subaction); break;
    case "editarequipodo": editarequipodo($module, $action, $subaction); break;
    
    case "aula": aula($module, $action, $subaction); break;
    case "editaaula": editaaula($module, $action, $subaction); break;
    case "editaaulado": editaaulado($module, $action, $subaction); break;
    
    default: $gui->session_error("Accion desconocida '$action' en modulo $module");
    /*default: $url->ir($module, "equipo");*/
}

?>
