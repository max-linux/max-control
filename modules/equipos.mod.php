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

$module=$url->get("module");
$action=$url->get("action");
$subaction=$url->get("subaction");

if(DEBUG) {
    error_reporting(E_ALL);
}

if ( ! $permisos->is_connected() ) {
    $url->ir("","");
}

if ( ! $permisos->is_admin() ) {
    $gui->session_error("Sólo pueden acceder al módulo de equipos los administradores.");
    $url->ir("","");
}
/*************************************************/


$module_actions=array(
        "ver" => "Ver equipos",
        "aulas" => "Ver aulas",
);



/*************************************************/

if ($action == "") {
    $url->ir($module, "ver");
}

function ver($module, $action, $subaction) {
    global $gui, $url;
    $button=leer_datos('button');
    $gui->debug("button='$button'");
    
    
    if( $button == "Limpiar cache WINS"){
        $url->ir($module, "purgewins");
    }
    
    if($button == "Actualizar MAC e IP de todos"){
        $url->ir($module, "update");
    }

    $filter=leer_datos('Filter');
    $aula=leer_datos('aula');
    // mostrar lista de equipos
    $ldap=new LDAP();
    
    if ($aula != '')
        $equipos=$ldap->get_computers_from_aula($aula);
    else
        $equipos=$ldap->get_computers( $filter );

    $urlform=$url->create_url($module, $action);
    
    $pager=new PAGER($equipos, $urlform, 0, $args='', NULL);
    $pager->processArgs( array('Filter', 'skip', 'aula', 'sort') );
    
    $equipos=$pager->getItems();
    $pager->sortfilter="(uid|ipHostNumber|macAddress|sambaProfilePath)";
    
    $aulas=$ldap->get_aulas_cn();
    //$gui->debuga($aulas);
    
    $data=array("equipos" => $equipos, 
                "aulas" => $aulas,
                "aula" => $aula,
                "filter" => $filter, 
                "urlform" => $urlform, 
                "urleditar"=>$url->create_url($module,'editar'),
                "urlborrar"=>$url->create_url($module,'borrar'),
                "pager" => $pager);
    $gui->add( $gui->load_from_template("ver_equipos.tpl", $data) );
}

function editar($module, $action, $subaction){
    global $gui, $url;
    $hostname=$url->get("subaction");
    $ldap=new LDAP();
    $equipo=$ldap->get_computers($hostname.'$');
    
    if( ! $equipo ){
        $gui->session_error("Equipo '$hostname' no encontrado");
        $url->ir($active_module, "ver");
    }
    
    $aulas=$ldap->get_aulas();
    $urlform=$url->create_url($module, 'guardar');
    
    $data=array("hostname"=>$hostname, 
                "aulas" => $aulas,
                "u"=>$equipo[0],
                "urlform"=>$urlform,
                "action" => "Editar");
    
    $gui->add( $gui->load_from_template("editar_equipo.tpl", $data ) );
}


function update($module, $action, $subaction){
    global $gui, $url;
    $data=array("urlaction"=>$url->create_url($module, 'updatedo'));
    $gui->add( $gui->load_from_template("update_equipos.tpl", $data) );
}

function updatedo($module, $action, $subaction){
    global $gui, $url;
    $ldap=new LDAP();
    $equipos=$ldap->get_computers();
    foreach($equipos as $equipo) {
        $equipo->getMACIP();
    }
}


function purgewins($module, $action, $subaction){
    global $gui, $url;
    $data=array("urlaction"=>$url->create_url($module, 'purgewinsdo'));
    $gui->add( $gui->load_from_template("purgewins.tpl", $data) );
}

function purgewinsdo($module, $action, $subaction){
    global $gui, $url;
    $ldap=new LDAP();
    $ldap->purgeWINS();
    $gui->session_info("Cache WINS borrada.");
    if(! DEBUG)
        $url->ir($active_module, "ver");
}



function borrar($module, $action, $subaction){
    global $gui, $url, $permisos;
    if ( $permisos->is_tic() ) {
        $gui->session_error("Los Coordinadores TIC no pueden borrar equipos.");
        $url->ir($module, "ver");
    }
    $equipos=leer_datos("hostnames");
    $equiposarray=preg_split("/,/", $equipos);
    $data=array(
            "urlaction"=>$url->create_url($module, 'borrardo'),
            "equipos" =>$equipos,
            "equiposarray" => $equiposarray
                );
    $gui->add( $gui->load_from_template("borrar_equipo.tpl", $data) );
}

function borrardo($module, $action, $subaction){
    global $gui, $url, $permisos;
    if ( $permisos->is_tic() ) {
        $gui->session_error("Los Coordinadores TIC no pueden borrar equipos.");
        $url->ir($module, "ver");
    }
    $gui->debug( "<pre>". print_r($_POST, true) . "</pre>" );
    $equipos=leer_datos('equipos');
    
    if ($equipos == '') {
        $gui->session_error("No se han seleccionado equipos");
        $url->ir($module, "ver");
    }
    
    $ldap=new LDAP();
    $equiposarray=preg_split('/,/', $equipos);
    $gui->debuga($equiposarray);
    foreach($equiposarray as $equipo) {
        $obj=$ldap->get_computers($equipo);
        if ( isset($obj[0]) ) {
            $obj[0]->delComputer();
             $gui->session_info("Equipo '$equipo' borrado del dominio.");
        }
        else {
            $gui->session_error("El equipo '$equipo' no se ha encontrado");
        }
    }
    if(! DEBUG)
        $url->ir($module, "ver");
}

function guardar($module, $action, $subaction){
    global $gui, $url;
    $hostname=leer_datos('hostname');
    $ldap=new LDAP();
    $equipos=$ldap->get_computers($hostname.'$');
    if ( ! isset($equipos[0]) )
        $url->ir($module, "ver");
    
    $equipo=$equipos[0];
    $gui->debuga($_POST);
    $equipo->set($_POST);
    $res=$equipo->save( array('sambaProfilePath', 
                         'ipHostNumber', 
                         'ipNetmaskNumber', 
                         'ipNetmaskNumber', 
                         'macAddress', 
                         'bootFile') );
    
    if ($res) {
        $gui->session_info("Equipo guardado correctamente.");
        if(! DEBUG)
            $url->ir($module, "ver");
    }
    else {
        $gui->session_error("Error guardando datos, por favor inténtelo de nuevo.");
        if(! DEBUG)
            $url->ir($module, "editar", $hostname);
    }
}

/*****************   aulas   ************************/

function veraulas($module, $action, $subaction){
    global $gui, $url;
    $button=leer_datos('button');
    $gui->debug("button='$button'");
    if( $button !='' && $button != "Buscar"){
        $url->ir($module, "aulas", "nueva");
    }
    
    $filter=leer_datos('Filter');
    
    // mostrar lista de aulas
    $ldap=new LDAP();
    $filter=leer_datos('Filter');
    $aulas=$ldap->get_aulas($filter);
    $urlform=$url->create_url($module, $action);
    
    $pager=new PAGER($aulas, $urlform, 0, $args='', NULL);
    $pager->processArgs( array('Filter', 'skip', 'sort') );
    $aulas=$pager->getItems();
    $pager->sortfilter="(cn)";
    
    
    $data=array("aulas" => $aulas, 
                "filter" => $filter,
                "urlform" => $urlform,
                "urlprofesores"=>$url->create_url($module,'aulas', 'miembros'),
                "urlequipos"=>$url->create_url($module,'aulas', 'equipos'),
                "urlborrar" =>$url->create_url($module,'aulas', 'borrar'),
                "pager" => $pager);
    $gui->add( $gui->load_from_template("ver_aulas.tpl", $data) );
}

function aulasmiembros($module, $action, $subaction){
    global $gui, $url;
    $aula=leer_datos('args');
    $ldap=new LDAP();
    $miembros=$ldap->get_teacher_from_aula($aula);
    $gui->debuga($miembros);
    
    $urlform=$url->create_url($module, $action, 'guardar');
    
    $data=array("aula"=>$aula, 
                "miembros"=>$miembros, 
                "urlform" => $urlform);
    
    $gui->add( $gui->load_from_template("editar_aula.tpl", $data) );
}

function aulasguardar($module, $action, $subaction){
    global $gui, $url;
    $gui->debug( "<pre>".print_r($_POST, true)."</pre>" );
    /*
    Array
        (
            [addtogroup] => Añadir usuarios al grupo
            [adduser] => profe3
            [aula] => grupoprueba
        )
    */
    
    /*
    Array
    (
        [deluser] => profe2
        [delfromgroup] => Quitar
        [aula] => grupoprueba
    )
    */
    $editaaula=leer_datos('aula');
    
    $addusers=clean_array($_POST, 'adduser');
    $delusers=clean_array($_POST, 'deluser');
    
    $gui->debug("addusers");
    $gui->debuga($addusers);
    $gui->debug("<hr><br>delusers ");
    $gui->debuga($delusers);
    
    $ldap= new LDAP();
    
    if ( count($addusers) > 0 ) {
        $aula=$ldap->get_aula($editaaula);
        foreach($addusers as $adduser) {
            // añadir usuario al grupo $grupo
            $aula->newMember($adduser);
            $gui->session_info("Usuario '$adduser' añadido al aula $editaaula.");
        }
        if (!DEBUG)
          $url->ir($module, "aulas", "miembros/$editaaula");
    }
    elseif ( count($delusers) > 0 ) {
        $aula=$ldap->get_aula($editaaula);
        foreach($delusers as $deluser) {
            // borrar usuario del grupo $grupo
            $aula->delMember($deluser);
            $gui->session_info("Usuario '$deluser' eliminado del aula $editaaula.");
        }
        if (!DEBUG)
          $url->ir($module, "aulas", "miembros/$editaaula");
    }
    else {
        $gui->session_error("No se ha seleccionado ningún profesor.");
        $url->ir($module, "aulas", "miembros/$editaaula");
    }
}


/****************************************************/
function aulasequipos($module, $action, $subaction){
    global $gui, $url, $permisos;
    if ( $permisos->is_tic() ) {
        $gui->session_error("Los Coordinadores TIC no pueden añadir o quitar equipos de aulas.");
        $url->ir($module, "aulas");
    }
    $aula=leer_datos('args');
    $ldap=new LDAP();
    $all=$ldap->get_computers_in_and_not_aula($aula);
    
    $gui->debuga($all);
    
    $urlform=$url->create_url($module, $subaction, 'guardar');
    
    $data=array("aula"=>$aula, 
                "equipos"=>$all, 
                "urlform" => $urlform);
    
    $gui->add( $gui->load_from_template("editar_aula_equipos.tpl", $data) );
}

function equiposguardar($module, $action, $subaction){
    global $gui, $url, $permisos;
    if ( $permisos->is_tic() ) {
        $gui->session_error("Los Coordinadores TIC no pueden añadir o quitar equipos de aulas.");
        $url->ir($module, "aulas");
    }
    $gui->debug( "<pre>".print_r($_POST, true)."</pre>" );
    
    /* Add computer
    Array
    (
        [addtogroup] => Añadir usuarios al grupo
        [addcomputer] => wxp64
        [aula] => grupoprueba
    )
    */
    /* del computer
    Array
    (
        [delcomputer] => mario-desktop
        [delfromgroup] => Quitar
        [aula] => aula primaria 1
    )
    */
    
    $aula=leer_datos('aula');
    $addcomputers=clean_array($_POST, 'addcomputer');
    $delcomputers=clean_array($_POST, 'delcomputer');
    
    $gui->debug("addcomputers");
    $gui->debuga($addcomputers);
    $gui->debug("<hr><br>delcomputers ");
    $gui->debuga($delcomputers);
    
    $ldap=new LDAP();


    if ( count($addcomputers) > 0 ) {
        foreach($addcomputers as $addcomputer) {
             // equitar el sambaProfilePath del equipo con el aula
            $equipo=$ldap->get_computers($addcomputer .'$');
            $equipo[0]->sambaProfilePath=$aula;
            $equipo[0]->ldapdata['sambaProfilePath']=$aula;
            $res=$equipo[0]->save( array('sambaProfilePath') );
            if ($res) {
                $gui->session_info("Equipo '$addcomputer' añadido al aula '$aula' correctamente.");
                $equipo[0]->boot($aula);
            }
            else
                $gui->session_error("No se puedo añadir el equipo '$addcomputer' al aula '$aula'.");
        }
        if (!DEBUG)
          $url->ir($module, "aulas", "equipos/$aula");
    }
    elseif ( count($delcomputers) > 0 ) {
        foreach($delcomputers as $delcomputer) {
            // borrar el sambaProfilePath
            $equipo=$ldap->get_computers($delcomputer .'$');
            if ( isset($equipo[0]) ) {
                $res = $equipo[0]->empty_attr( 'sambaProfilePath' );
                if ($res) {
                    $gui->session_info("Equipo '$delcomputer' quitado del aula '$aula' correctamente.");
                    $equipo[0]->boot('default');
                }
                else
                    $gui->session_error("No se puedo quitar el equipo '$delcomputer' del aula '$aula'.");
            }
            else {
                $gui->session_error("No se pudo encontrar el equipo '$delcomputer'");
            }
        }
        if (!DEBUG)
          $url->ir($module, "aulas", "equipos/$aula");
    }
    else {
        $gui->session_error("No se ha seleccionado ningún equipo.");
        $url->ir($module, "aulas", "equipos/$aula");
    }
}


function aulasnueva($module, $action, $subaction){
    global $gui, $url, $permisos;
    if ( $permisos->is_tic() ) {
        $gui->session_error("Los Coordinadores TIC no pueden añadir aulas.");
        $url->ir($module, "aulas");
    }
    $group=new GROUP();
    $url=new URLHandler();
    $urlform=$url->create_url($module, $action, 'aulaguardar');
    
    $data=array("u"=>$group,
                "urlform"=>$urlform,
                "action" => "Editar");
    
    $gui->add( $gui->load_from_template("add_aula.tpl", $data ) );
}

function aulasaulaguardar($module, $action, $subaction){
    global $gui, $url, $permisos;
    if ( $permisos->is_tic() ) {
        $gui->session_error("Los Coordinadores TIC no pueden añadir aulas.");
        $url->ir($module, "aulas");
    }
    $gui->debuga($_POST);
    /*
    Array
    (
        [cn] => aaaaa
        [description] => 
        [add] => Añadir
    )
    */
    
    if ( leer_datos('cn') == '' ) {
        $gui->session_error("Error, identificador de aula vacío.");
        $url->ir($module, "nueva");
    }
    
    $group=new AULA($_POST);
    if ( $group->newAula() )
        $gui->session_info("Aula '".$group->cn."' añadida correctamente.");
    
    if (!DEBUG)
        $url->ir($module, "aulas");
}

function aulasborrar($module, $action, $subaction){
    global $gui, $url, $permisos;
    if ( $permisos->is_tic() ) {
        $gui->session_error("Los Coordinadores TIC no pueden borrar aulas.");
        $url->ir($module, "aulas");
    }
    $aula=leer_datos('args');
    $urlform=$url->create_url($module, $action, 'aulaborrar');
    $data=array("aula" => $aula,
                "urlform"=>$urlform);
    
    $gui->add( $gui->load_from_template("del_aula.tpl", $data) );
}

function aulasborrardo($module, $action, $subaction){
    global $gui, $url, $permisos;
    if ( $permisos->is_tic() ) {
        $gui->session_error("Los Coordinadores TIC no pueden borrar aulas.");
        $url->ir($module, "aulas");
    }
    $gui->debug( "<pre>".print_r($_POST, true)."</pre>" );
    /*
    Array
    (
        [aula] => aula primaria 4
        [confirm] => Confirmar
    )
    */
    $aula=leer_datos('aula');

    if ($aula == '') {
        $gui->session_error("No se pudo encontrar el aula '$aula'");
        $url->ir($module, "aulas");
    }

    $ldap=new LDAP();
    $aulas=$ldap->get_aula($aula);
    
    $gui->debug( "<pre>". print_r($aulas, true) . "</pre>" );
    
    if ($aulas->cn != $aula) {
        $gui->session_error(" El aula '$aula' no existe.");
        $url->ir($module, "aulas");
    }
    
    if ( $aulas->delAula() )
        $gui->session_info("Aula '$aula' borrada.");
    
    if (!DEBUG)
        $url->ir($module, "aulas");
}



switch($action) {
    case "ver":          ver($module, $action, $subaction); break;
    case "editar":       editar($module, $action, $subaction); break;
    case "update":       update($module, $action, $subaction); break;
    case "updatedo":     updatedo($module, $action, $subaction); break;
    case "purgewins":    purgewins($module, $action, $subaction); break;
    case "purgewinsdo":  purgewinsdo($module, $action, $subaction); break;
    case "borrar":       borrar($module, $action, $subaction); break;
    case "borrardo":     borrardo($module, $action, $subaction); break;
    case "guardar":      guardar($module, $action, $subaction); break;
    
    case "aulas":
            switch($subaction) {
                case "":            veraulas($module, $action, $subaction); break;
                case "miembros":    aulasmiembros($module, $action, $subaction); break;
                case "guardar":     aulasguardar($module, $action, $subaction); break;
                case "equipos":     aulasequipos($module, $action, $subaction); break;
                case "nueva":       aulasnueva($module, $action, $subaction); break;
                case "aulaguardar": aulasaulaguardar($module, $action, $subaction); break;
                case "borrar":      aulasborrar($module, $action, $subaction); break;
                case "aulaborrar":  aulasborrardo($module, $action, $subaction); break;
                
                default: $gui->session_error("Subaccion desconocida '$subaction' en módulo equipos/aulas");
            }
            break;
    
    case "equipos":
            switch($subaction) {
                case "guardar":     equiposguardar($module, $action, $subaction); break;
                
                default: $gui->session_error("Subaccion desconocida '$subaction' en módulo equipos/equipos");
            }
            break;
    
    default: $gui->session_error("Accion desconocida '$action' en modulo equipos");
}


?>
