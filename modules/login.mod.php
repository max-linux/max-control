<?php


/*
*
*  Modulo login
*  
*
*
*
* $_SESSION["usuario"] = "";
* $_SESSION["name"]="";
* $_SESSION["id"]="";
*
*/
global $gui;
global $permisos;
global $site;
global $module_actions;


global $url;

$active_module=$url->get("module");
$active_action=$url->get("action");

$is_connected=False;
$is_connecting=False;

if(DEBUG) {
    error_reporting(E_ALL);
}



/**************************************************/
if( $url->get("action") == "logout" ){
    $permisos->desconectar();
    $gui->session_info("Desconectado.");
    $url->ir("", "");
}



// leer valores POST por si esta intentando conectar
$username=leer_datos("username");
$contrasena=leer_datos("password");

if ($username != "" && $contrasena != ""){
    $is_connecting=True;
}


if ($is_connecting){
    
    if( $permisos->conectar($username, $contrasena) ) {
        if(ENABLE_BOOTSTRAP && $permisos->is_admin()) {
            $url->ir("dash", "");
        }
        else {
            $url->ir("miperfil", "");
        }
    }
    else {
        $gui->session_error("Usuario o contraseña incorrectos.");
        $url->ir("");
    }
}

$gui->debug("MODULO LOGIN: action=$active_action module=$active_module");



