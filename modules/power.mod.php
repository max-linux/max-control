<?php


/*
*
*  Modulo power
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


if(DEBUG) {
    error_reporting(E_ALL);
}

if ( ! $permisos->is_connected() ) {
    $url->ir("","");
}

/*************************************************/


$module_actions=array(
        "aulas" => "Aulas",
        "equipos" => "Equipos",
);


// si tiene permisos de administrador mostrar submenus
/*
if ($permisos->is_admin() ) {
    $module_actions['register']="Registrar equipo";
}*/


/*************************************************/

if ($active_action == "") {
    $url->ir($active_module, "aulas");
}

if ($active_action == "aulas" && $active_subaction == '') {
    // mostrar lista de aulas
    $ldap=new LDAP();
    $filter=leer_datos('Filter');
    $aulas=$ldap->get_aulas($filter);
    $urlform=$url->create_url($active_module, $active_action);
    $urlpoweroff=$url->create_url($active_module, 'aula_preguntar', 'poweroff');
    $urlreboot=$url->create_url($active_module, 'aula_preguntar', 'reboot');
    $urlwakeonlan=$url->create_url($active_module, 'aula_preguntar', 'wakeonlan');
    
    $data=array("aulas" => $aulas, 
                "filter" => $filter,
                "urlform" => $urlform,
                "urlpoweroff"=>$urlpoweroff,
                "urlreboot"=>$urlreboot,
                "urlwakeonlan" => $urlwakeonlan);
    $gui->add( $gui->load_from_template("power_aulas.tpl", $data) );
}


if ($active_action == "aula_preguntar" && $active_subaction != '') {
    // 
    $action=$active_subaction;
    $aula=leer_datos('args');
    
    $ldap = new LDAP();
    $computers=$ldap->get_computers_from_aula($aula);
    
    $urlaction=$url->create_url($active_module, 'do', "$action/$aula");

    $data=array("aula" => $aula, 
                "action" => $action,
                "computers"=> $computers,
                "urlaction"=>$urlaction);
    $gui->add( $gui->load_from_template("power_aulas_do.tpl", $data) );
}

if ($active_action == "do" && $active_subaction != '') {
    // 
    $action=$active_subaction;
    $aula=leer_datos('args');
    
    $ldap = new LDAP();
    $computers=$ldap->get_computers_from_aula($aula);
    foreach( $computers as $computer) {
        $gui->debug("Acción $action en equipo '".$computer->hostname()."' tiempo: " . time_end() );
        //$res[]=$computer->action($action);
        if ($action == 'wakeonlan') {
            $computer->action($action, $computer->macAddress);
        }
        elseif ( $computer->exe->is_alive() ) {
            $computer->action($action, $computer->macAddress);
        }
        else {
            $gui->session_error("No se puede realizar la acción solicitada en '".$computer->hostname()."', el equipo está apagado");
        }
    }
    $gui->debug("Finalizadas acciones tiempo: " . time_end() );
    
    if ( ! DEBUG)
        $url->ir($active_module, "aulas");
}


if ($active_action == "equipos" && $active_subaction == '') {
    // mostrar lista de equipos
    $ldap=new LDAP();
    $filter=leer_datos('Filter');
    $equipos=$ldap->get_computers( $filter );
    
    $urlform=$url->create_url($active_module, $active_action);
    $urlpoweroff=$url->create_url($active_module, 'equipo_preguntar', 'poweroff');
    $urlreboot=$url->create_url($active_module, 'equipo_preguntar', 'reboot');
    $urlwakeonlan=$url->create_url($active_module, 'equipo_preguntar', 'wakeonlan');
    
    $data=array("equipos" => $equipos, 
                "filter" => $filter,
                "urlform" => $urlform,
                "urlpoweroff"=>$urlpoweroff,
                "urlreboot"=>$urlreboot,
                "urlwakeonlan" => $urlwakeonlan);
    $gui->add( $gui->load_from_template("power_equipos.tpl", $data) );
}

if ($active_action == "equipo_preguntar" && $active_subaction != '') {
    // 
    $action=$active_subaction;
    $equipo=leer_datos('args');
    
    $ldap = new LDAP();
    $computers=$ldap->get_computers( $equipo . '$' );
    
    $urlaction=$url->create_url($active_module, 'docomputer', "$action/$equipo");

    $data=array("equipo" => $equipo, 
                "action" => $action,
                "computers"=> $computers,
                "urlaction"=>$urlaction);
    $gui->add( $gui->load_from_template("power_equipos_do.tpl", $data) );
}

if ($active_action == "docomputer" && $active_subaction != '') {
    // 
    $action=$active_subaction;
    $equipo=leer_datos('args');
    
    $ldap = new LDAP();
    $computers=$ldap->get_computers( $equipo . '$' );
    
    //$gui->debuga($computers);
    
    foreach( $computers as $computer) {
        $gui->debug("Acción $action en equipo '".$computer->hostname()."' tiempo: " . time_end() );
        //$res[]=$computer->action($action);
        if ($action == 'wakeonlan') {
            $computer->action($action, $computer->macAddress);
        }
        elseif ( $computer->exe->is_alive() ) {
            $computer->action($action, $computer->macAddress);
        }
        else {
            $gui->session_error("No se puede realizar la acción solicitada en '".$computer->hostname()."', el equipo está apagado");
        }
    }
    $gui->debug("Finalizadas acciones tiempo: " . time_end() );
    if (! DEBUG)
        $url->ir($active_module, "equipos");
}
?>
