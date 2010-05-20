<?php


/*
*
*  Modulo equipos
*
*/
global $gui;
global $permisos;
global $site;
global $module_actions;


$url=new URLHandler();

$active_module=$url->get("module");
$active_action=$url->get("action");
$active_subaction=$url->get("subaction");


if(pruebas) {
    error_reporting(E_ALL);
}

if ( ! $permisos->is_connected() ) {
    $url->ir("","");
}

/*************************************************/


$module_actions=array(
        "ver" => "Ver equipos",
        "aulas" => "Ver aulas",
);


// si tiene permisos de administrador mostrar submenus
/*
if ($permisos->is_admin() ) {
    $module_actions['register']="Registrar equipo";
}*/


/*************************************************/

if ($active_action == "") {
    $url->ir($active_module, "ver");
}

if ($active_action == "ver") {
    // mostrar lista de equipos
    $ldap=new LDAP();
    $filter=leer_datos('Filter');
    if ( ($filter != '') && (substr($filter, -1) != '$') )
        $filter.='$';
    $equipos=$ldap->get_computers( $filter );
    $urlform=$url->create_url($active_module, $active_action);
    $urleditar=$url->create_url($active_module,'editar');
    
    $data=array("equipos" => $equipos, 
                "filter" => $filter, 
                "urlform" => $urlform, 
                "urleditar"=>$urleditar);
    $gui->add( $gui->load_from_template("ver_equipos.tpl", $data) );
}

if ($active_action == "editar") {
    $hostname=$url->get("subaction");
    $ldap=new LDAP();
    $equipo=$ldap->get_computers($hostname.'$');
    $aulas=$ldap->get_aulas();
    $urlform=$url->create_url($active_module, 'guardar');
    
    $data=array("hostname"=>$hostname, 
                "aulas" => $aulas,
                "u"=>$equipo[0],
                "urlform"=>$urlform,
                "action" => "Editar");
    
    $gui->add( $gui->load_from_template("editar_equipo.tpl", $data ) );
}



if ($active_action == "guardar") {
    $hostname=leer_datos('hostname');
    $ldap=new LDAP();
    $equipos=$ldap->get_computers($hostname.'$');
    if ( ! isset($equipos[0]) )
        $url->ir($active_module, "ver");
    
    $equipo=$equipos[0];
    //$gui->add( "<pre>". print_r($_POST, true) . "</pre>" );
    $equipo->set($_POST);
    $res=$equipo->save( array('sambaProfilePath', 
                         'ipHostNumber', 
                         'ipNetmaskNumber', 
                         'ipNetmaskNumber', 
                         'macAddress', 
                         'bootFile') );
    
    //$gui->add( "<pre>guardado=$res". print_r($equipo, true) . "</pre>" );
    if ($res)
        $gui->add("<h2>Guardado correctamente</h2>");
    else
        $gui->add("<h2>Error guardando datos, por favor inténtelo de nuevo.</h2>");
}

/*****************   aulas   ************************/


if ($active_action == "aulas" && $active_subaction == '') {
    // mostrar lista de aulas
    $ldap=new LDAP();
    $filter=leer_datos('Filter');
    $aulas=$ldap->get_aulas($filter);
    $urlform=$url->create_url($active_module, $active_action);
    $urleditar=$url->create_url($active_module,'aulas', 'miembros');
    
    $data=array("aulas" => $aulas, 
                "filter" => $filter,
                "urlform" => $urlform,
                "urleditar"=>$urleditar);
    $gui->add( $gui->load_from_template("ver_aulas.tpl", $data) );
}


if ($active_action == "aulas" && $active_subaction == 'miembros') {
    $aula=leer_datos('args');
    $ldap=new LDAP();
    $miembros=$ldap->get_teacher_from_aula($aula);
    //$gui->add( "<pre>". print_r($miembros, true) . "</pre>" );
    
    $urlform=$url->create_url($active_module, $active_subaction, 'guardar');
    
    $data=array("aula"=>$aula, 
                "miembros"=>$miembros, 
                "urlform" => $urlform);
    
    $gui->add( $gui->load_from_template("editar_aula.tpl", $data) );
}

if ($active_action == "miembros" && $active_subaction == 'guardar') {

    $gui->add( "<pre>".print_r($_POST, true)."</pre>" );
}



?>
