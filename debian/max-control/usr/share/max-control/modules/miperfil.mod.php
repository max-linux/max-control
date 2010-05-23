<?php


/*
*
*  Modulo miperfil
*
*/
global $gui;
global $permisos;
global $site;
global $module_actions;


//FIXME borrar esto que no hace falta
#global $permisos;
#$permisos->is_admin();
#$permisos->is_connected();
#$permisos->is_teacher();

$url=new URLHandler();

$active_module=$url->get("module");
$active_action=$url->get("action");
$active_subaction=$url->get("subaction");

$module_actions=array(
        "editar" => "Cambiar contraseña",
);


if ($active_action == "editar") {
    $username=$_SESSION['username'];
    $ldap=new LDAP();
    $user=$ldap->get_user($username);
    
    $urlform=$url->create_url($active_module, 'guardar');
    
    $data=array("username"=>$username, 
                "u"=>$user,
                "urlform"=>$urlform,
                "action" => "Editar");
    
    //$gui->add("<pre>".print_r($data, true)."</pre>");
    
    $gui->add( $gui->load_from_template("miperfil.tpl", $data ) );
}

?>
