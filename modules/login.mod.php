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


$url=new URLHandler();

$active_module=$url->get("module");
$active_action=$url->get("action");

$is_connected=False;
$is_connecting=False;

if(pruebas) {
    error_reporting(E_ALL);
}

$url2=new URLHandler();

/*************************************************/

$module_actions_logged=array(
        "encuestas" => "Mis encuestas",
        "archivos" => "Mis archivos",
        "logout" => "Desconectar"
);

$module_actions_profesor=array(
        "fichas" => "Mis fichas",
        "archivos" => "Mis archivos",
        "logout" => "Desconectar"
);

$module_actions_admin=array(
        "encuestas" => "Mis encuestas",
        "fichas" => "Mis fichas",
        "archivos" => "Mis archivos",
        "logout" => "Desconectar"
);

$module_actions_not_logged=array(
        "login" => "Entrar"     
);


/**************************************************/
if( $url->get("action") == "logout" ){
    $permisos->desconectar();
    $url->ir($active_module, "");
}



// leer valores POST por si esta intentando conectar
$username=leer_datos("username");
$contrasena=leer_datos("password");

if ($username != "" && $contrasena != ""){
    $is_connecting=True;
}


if ($is_connecting){
    
    if( $permisos->conectar($username, $contrasena) ) {
        $gui->add("CAMBIAME
        Edita login.mod.php linea 86<br>
        y descomentala.
        ");
        //$url->ir("portada", "");
    }
    else {
        //$txt=$permisos->get_error();
        //$gui->alert($txt);
        $url->ir("", "");
    }
}

$gui->debug("MODULO LOGIN: action=$active_action module=$active_module");



/*  portada del modulo de administrador */
/*
if ($url->get("action") == "") {
    if ( !$permisos->is_connected() ) {
        $data= array(
            "module" => $active_module,
            "login_url" => $url2->create_url($active_module, "login")
                    );
        $gui->add( $gui->load_from_template("login.tpl", $data) );
    }
    else{
        $url->ir($active_module, "misdatos");
    }
}
*/










?>
