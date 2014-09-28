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

if(DEBUG) {
    error_reporting(E_ALL);
}

global $url;

if ( ! $permisos->is_admin() ) {
    $url->ir("miperfil", "");
}



$module=$url->get("module");
$action=$url->get("action");
$subaction=$url->get("subaction");




/*************************************************************/
function dash_main($module, $action, $subaction) {
    global $gui, $url, $ldap;
    
    $usuarios=$ldap->get_users('', $group=LDAP_OU_USERS);
    $grupos=$ldap->get_groups('', $include_system=false);
    
    $equipos=$equipos=$ldap->get_computers('');
    $aulas=$ldap->get_aulas('');
    
    $data=array(
            'num_users' => sizeof($usuarios),
            'num_groups' => sizeof($grupos),
            'num_equipos' => sizeof($equipos),
            'num_aulas' => sizeof($aulas),
                );
    $gui->add( $gui->load_from_template("dash.tpl", $data) );
}

dash_main($module, $action, $subaction);
