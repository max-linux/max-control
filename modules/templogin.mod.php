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

if ( ! $permisos->is_templogin() ) {
    $url->ir("");
}

if( is_file(FIRST_RUN) ) {
    $url->ir('login', 'logout');
}


$module=$url->get("module");
$action=$url->get("action");
$subaction=$url->get("subaction");




/*************************************************************/
function templogin($module, $action, $subaction) {
    global $gui, $url, $permisos;

    $user=new USER();
    $urlform=$url->create_url($module, 'save');
    
    $data=array("u"=>$user,
                "urlform"=>$urlform,
                "permisos"=> $permisos,
                "action" => "Editar");
    
    $gui->add( $gui->load_from_template("templogin_add_user.tpl", $data ) );
}

function templogin_save($module, $action, $subaction) {
    global $gui, $url,$permisos;
    $gui->debug( "<pre>" . print_r($_POST,true) . "</pre>");
    // comprobar contraseñas
    if ( leer_datos('password') != leer_datos('repassword') ) {
        $gui->session_error("Las contraseñas no coinciden.");
        $url->ir($module);
    }
    if ( leer_datos('cn') == '' ) {
        $gui->session_error("Identificador vacío.");
        $url->ir($module);
    }
    
    sanitize($_POST, array('cn'=>'cn',
                           'givenname'=>'givenname',
                           'sn' => 'cn',
                           'description' => 'charnum',
                           'loginshell' => 'shell',
                           'role' => 'role',
                           'password' => 'str',
                           'repassword' => 'str'));
    $gui->debug( "<pre>" . print_r($_POST,true) . "</pre>");
    $user = new USER($_POST);
    $user->password=$_POST['password'];
    $user->background=false;

    if ( ! $user->newUser() )  {
        $gui->session_error("No se ha podido añadir el usuario, compruebe todos los campos.");
        $url->ir($module);
    }
    else {
        file_put_contents(FIRST_RUN, $user->cn);
        $gui->session_info("Usuario ".$user->cn." creado correctamente.");
        

        $data=array(
            'urllogin'=>$url->create_url('login', 'logout'),
                );

        $gui->add( $gui->load_from_template("templogin_done.tpl", $data ) );
    }

    
}



switch($action) {
    case "":     templogin($module, $action, $subaction); break;
    case "save": templogin_save($module, $action, $subaction); break;
    
    default: $gui->session_error("Accion desconocida '$action' en modulo $module");
}

