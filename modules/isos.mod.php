<?php


/*
*
*  Modulo isos
*
*/
global $gui;
global $permisos;
global $site;
global $module_actions;

$url=new URLHandler();



if ( $permisos->get_rol() == '' ) {
    $gui->session_error("No es profesor o administrador para acceder al módulo ISOS");
    $url->ir("", "");
}




$module=$url->get("module");
$action=$url->get("action");
$subaction=$url->get("subaction");

$module_actions=array(
        "ver" => "Ver ISOS",
        #"aula" => "Por aulas",
        #"config" => "Configurar tipos de arranque",
);


/*************************************************************/
function ver($module, $action, $subaction) {
    global $gui, $url;
    
    $gui->add("<h2>Lista de ISOS</h2>");
    $ldap=new LDAP();
    $isos=$ldap->getISOS();
    $gui->add( "<pre>".print_r($isos, true)."</pre>" );
    
}





//$gui->session_info("Accion '$action' en modulo '$module'");
switch($action) {
    case "": $url->ir($module, "ver"); break;
    case "ver": ver($module, $action, $subaction); break;
    
    default: $gui->session_error("Accion desconocida '$action' en modulo $module");
}

?>
