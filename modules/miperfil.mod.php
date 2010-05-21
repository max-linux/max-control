<?php


/*
*
*  Modulo miperfil
*
*/
global $gui;


/*
global $module_actions;
$module_actions=array(
        "roles" => "Roles",
        "encuestas" => "Ver encuestas",
        "resultados" => "Resultados de encuestas",
        "resultadosfichas" => "Resultados de fichas",
        "archivos" => "Ver archivos"
);
*/

$gui->add( $gui->load_from_template("miperfil.tpl") );

//FIXME borrar esto que no hace falta
global $permisos;
$permisos->is_admin();
$permisos->is_connected();
$permisos->is_teacher();

?>
