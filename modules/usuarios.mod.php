<?php


/*
*
*  Modulo usuarios
*
*/
global $gui;
global $permisos;
global $site;
global $module_actions;


$url=new URLHandler();

$active_module=$url->get("module");
$active_action=$url->get("action");



if(pruebas) {
    error_reporting(E_ALL);
}

if ( ! $permisos->is_connected() ) {
    $url->ir("","");
}

/*************************************************/


$module_actions=array(
        "ver" => "Ver usuarios",
        "grupos" => "Ver grupos"
);


// si tiene permisos de administrador mostrar submenus
if ($permisos->is_admin() ) {
    $module_actions['admin']="Administrar";
}


if ($active_action == "") {
    $url->ir($active_module, "ver");
}

/*************************************************/
if ($active_action == "ver") {
    // mostrar lista de usuarios
    $ldap=new LDAP();
    /* /control/usuarios/usuarios?Filter=test&button=Buscar */
    $filter=leer_datos('Filter');
    $usuarios=$ldap->get_users( $filter );
    $urlform=$url->create_url($active_module, $active_action);
    $urleditar=$url->create_url($active_module,'editar');
    
    //$gui->debug("<pre>".print_r($usuarios,true)."</pre>");
    
    $data=array("usuarios" => $usuarios, 
                "filter" => $filter, 
                "urlform" => $urlform, 
                "urleditar"=>$urleditar);
    $gui->add( $gui->load_from_template("ver_usuarios.tpl", $data) );
}



if ($active_action == "grupos") {
    $gui->add( "<h1>FIXME PENDIENTE</h1>" );
}

if ($active_action == "admin") {
    $gui->add( "<h1>FIXME PENDIENTE</h1>" );
}



?>
