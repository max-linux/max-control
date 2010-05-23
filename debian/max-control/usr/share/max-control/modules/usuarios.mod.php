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
#if ($permisos->is_admin() ) {
#    $module_actions['admin']="Administrar";
#}


if ($active_action == "") {
    $url->ir($active_module, "ver");
}

/*************************************************/
if ($active_action == "ver") {
    // mostrar lista de usuarios
    $action=leer_datos('action');
    $gui->debug("action='$action'");
    if($action == "add"){
        $url->ir($active_module, "add");
    }
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

if ($active_action == "editar") {
    $username=$url->get("subaction");
    $ldap=new LDAP();
    $user=$ldap->get_user($username);
    
    $urlform=$url->create_url($active_module, 'guardar');
    
    $data=array("username"=>$username, 
                "u"=>$user,
                "urlform"=>$urlform,
                "action" => "Editar");
    
    $gui->add( $gui->load_from_template("editar_usuario.tpl", $data ) );
}

if ($active_action == "guardar") {
    /* role:
    *        empty => alumno
    *        teacher => Profesor
    *        admin => Administrador
    */
    $gui->add( "<pre>" . print_r($_POST,true) . "</pre>");
    /*
    Array
        (
            [cn] => mario izquierdo
            [uid] => mario
            [role] => teacher
            [loginShell] => /bin/bash
            [Editar] => Guardar
        )
    */
    $useruid=leer_datos('uid');
    $ldap=new LDAP();
    
    //$gui->debug("<pre>".print_r($ldap->additionalPasswords('test', 'test'), true)."</pre>");
    
    $usuario=$ldap->get_user($useruid);
    
    $usuario->set($_POST);
    $res=$usuario->save( array('cn', 'loginShell') );
    
    if ($res)
        $gui->add("<h2>Guardado correctamente</h2>");
    else
        $gui->add("<h2>Error guardando datos, por favor inténtelo de nuevo.</h2>");
    
    // guardar grupo
    $usuario->set_role(leer_datos('role'));
    
    // guardar contraseña
    $new=leer_datos('newpwd');
    if ( $new != '') {
        $new2=leer_datos('newpwd2');
        if ($new != $new2) {
            $gui->alert("Las contraseñas no coinciden");
        }
        else {
            $usuario->update_password($new, $usuario->uid);
        }
    }
}


if ($active_action == "add") {
    $user=new USER();
    
    $urlform=$url->create_url($active_module, 'guardarnuevo');
    
    $data=array("u"=>$user,
                "urlform"=>$urlform,
                "action" => "Editar");
    
    $gui->add( $gui->load_from_template("add_usuario.tpl", $data ) );
}


if ($active_action == "guardarnuevo") {
    /* role:
    *        empty => alumno
    *        teacher => Profesor
    *        admin => Administrador
    */
    $gui->add( "<pre>" . print_r($_POST,true) . "</pre>");
    /*
    Array
    (
        [uid] => aaa
        [givenName] => aa
        [sn] => ee
        [description] => aassqq
        [password] => 12345
        [repassword] => 12345
        [role] => 
        [loginShell] => /bin/bash
        [add] => Añadir
    )
    */
    
    // comprobar contraseñas
    if ( leer_datos('password') != leer_datos('repassword') ) {
        $gui->session_error("Las contraseñas no coinciden.");
        $url->ir($active_module, "add");
    }
    if ( leer_datos('uid') == '' ) {
        $gui->session_error("Identificador vacío.");
        $url->ir($active_module, "add");
    }
    
    $user = new USER($_POST);
    $user->newUser();
    //if ( ! $user->newUser() ) 
    //    $url->ir($active_module, "add");
    
    //$url->ir($active_module, "ver");
}
/*****************************************************************************/


if ($active_action == "grupos") {
    $gui->add( "<h1>FIXME PENDIENTE</h1>" );
}

if ($active_action == "admin") {
    $gui->add( "<h1>FIXME PENDIENTE</h1>" );
}



?>
