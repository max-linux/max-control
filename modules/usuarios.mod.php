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
global $url;

if(DEBUG) {
    error_reporting(E_ALL);
}


$module=$url->get("module");
$action=$url->get("action");
$subaction=$url->get("subaction");


if ( ! $permisos->is_connected() ) {
    $url->ir("","");
}

if ( ! ( $permisos->is_admin() || $permisos->is_tic() ) ) {
    $gui->session_error("Sólo pueden acceder al módulo de usuarios los administradores o coordinadores TIC.");
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


if ($url->get("action") == "") {
    $url->ir($url->get("module"), "ver");
}

function ver($module, $action, $subaction) {
    global $gui, $url;
    // mostrar lista de usuarios
    
    $button=leer_datos('button');
    $gui->debug("button='$button'");
    if( $button !='' && $button != "Buscar"){
        $url->ir($module, "add");
    }
    
    $ldap=new LDAP();
    
    /* get_users($filter='*', $group=LDAP_OU_USERS, $ignore="max-control", $role='') */
    $usuarios=$ldap->get_users( leer_datos('Filter'),
                                $group=LDAP_OU_USERS,
                                $ignore="max-control",
                                $filterrole=leer_datos('role'));
    
    $urlform=$url->create_url($module, $action);
    
    
    $pager=new PAGER($usuarios, $urlform, 0, $args='', NULL);
    $pager->processArgs( array('Filter', 'skip', 'role', 'sort') );
    $usuarios=$pager->getItems();
    $pager->sortfilter="(uid|cn|sn|usedSize)";
    
    /*  overquota 
    *   try to load /var/lib/max-control/quota.cache.php
    */
    $overQuota=array();
    $overQuotaEnabled=False;
    $quotaTime="";
    if (is_readable("/var/lib/max-control/quota.cache.php")) {
        require("/var/lib/max-control/quota.cache.php");
        if (sizeof($overQuota) > 0) {
            $overQuotaEnabled=True;
        }
    }
    /*******************************************************/
    
    $data=array("usuarios" => $usuarios, 
                "filter" => leer_datos('Filter'), 
                "role" => leer_datos('role'),
                "overQuota" => $overQuota,
                "overQuotaEnabled" => $overQuotaEnabled,
                "overQuotaLimit"=> OVERQUOTA_LIMIT,
                "quotaTime" => $quotaTime,
                "urlform" => $urlform, 
                "urlformmultiple" => $url->create_url($module, 'deletemultiple'),
                "urleditar"=>$url->create_url($module,'editar'),
                "urlborrar"=>$url->create_url($module,'delete'),
                "resetprofilebase" => $url->create_url($module, 'resetprofile'),
                "pager"=>$pager);
    $gui->add( $gui->load_from_template("ver_usuarios.tpl", $data) );
}

function editar($module, $action, $subaction){
    global $gui, $url,$permisos;
    $username=$url->get("subaction");
    
    if ($username == $_SESSION["username"]) {
        $gui->session_error("No se puede editar la cuenta con la que se está conectado.");
        $url->ir($module, "ver");
    }
    
    $ldap=new LDAP();
    $user=$ldap->get_user($username);
    
    if( ! $user ){
        $gui->session_error("Usuario '$username' no encontrado");
        $url->ir($module, "ver");
    }
    
    if( ! $permisos->is_admin() && $user->get_role() == 'admin') {
        $gui->session_error("Sólo los Administradores pueden editar cuentas de Administradores.");
        $url->ir($module, "ver");
    }
    
    $urlform=$url->create_url($module, 'guardar');
    
    $data=array("username"=>$username, 
                "u"=>$user,
                "urlform"=>$urlform,
                "permisos"=> $permisos,
                "action" => "Editar");
    
    $gui->add( $gui->load_from_template("editar_usuario.tpl", $data ) );
}

function guardar($module, $action, $subaction) {
    global $gui, $url,$permisos;
    /* role:
    *        empty => alumno
    *        teacher => Profesor
    *        admin => Administrador
    */
    $gui->debug( "<pre>" . print_r($_POST,true) . "</pre>");
    /*
        Array
        (
            [cn] => Juan
            [sn] => Lopez Gómez
            [role] => 
            [loginShell] => /bin/bash
            [newpwd] => 
            [newpwd2] => 
            [Editar] => Guardar
            [uid] => juan12
        )
    */
    $useruid=leer_datos('uid');
    if ($useruid == $_SESSION["username"]) {
        $gui->session_error("No se puede editar la cuenta con la que se está conectado.");
        $url->ir($module, "ver");
    }
    $ldap=new LDAP();
    
    //$gui->debug("<pre>".print_r($ldap->additionalPasswords('test', 'test'), true)."</pre>");
    
    $usuario=$ldap->get_user($useruid);
    if( ! $permisos->is_admin() && $usuario->get_role() == 'admin') {
        $gui->session_error("Sólo los Administradores pueden editar cuentas de Administradores.");
        $url->ir($module, "ver");
    }
    
    if( !$permisos->is_admin() && leer_datos('role') == 'admin') {
        $gui->session_error("Sólo los Administradores pueden elevar permisos a administrador.");
        $url->ir($module, "ver");
    }
    
    // guardar contraseña
    $new=leer_datos('newpwd');
    if ( $new != '') {
        $new2=leer_datos('newpwd2');
        if ($new != $new2) {
            $gui->session_error("Las contraseñas no coinciden");
        }
        else {
            $usuario->update_password($new, $usuario->uid);
        }
    }
    
    sanitize($_POST, array('uid' => 'str',
                           'cn'=>'charnum',
                           'sn' => 'charnum',
                           'description' => 'charnum',
                           'loginShell' => 'shell',
                           'role' => 'role'));
    $gui->debug( "<pre>" . print_r($_POST,true) . "</pre>");
    $usuario->set($_POST);
    if( $usuario->description == '' ) {
        $usuario->description=array();
        $usuario->ldapdata['description']=array();
    }
    $res=$usuario->save( array('cn', 'sn', 'loginShell', 'description') );
    
    if ($res)
        $gui->session_info("Datos guardados correctamente");
    else
        $gui->session_error("Error guardando datos, por favor inténtelo de nuevo.");
    
    // guardar rol/grupo
    $usuario->set_role(leer_datos('role'));
    
    if(! DEBUG)
        $url->ir($module, "ver");
}

function deletemultiple($module, $action, $subaction) {
    global $gui, $url;
    $users=leer_datos('usernames');
    $action=leer_datos('action');
    if( ! $users) {
        $gui->session_error("No se han seleccionado usuarios");
        $url->ir($module, "ver");
    }
    if( !($action == 'clean' || $action == 'delete') ) {
        $gui->session_error("No se han seleccionado una acción");
        $url->ir($module, "ver");
    }
    
    $usersarray=preg_split('/,/', $users);
    $data=array("users" => $users,
                "action" => $action,
                "usersarray"=>$usersarray,
                "urlform"=>$url->create_url($module, 'deletemultipledo'));
    
    $gui->add( $gui->load_from_template("delmultiple_usuarios.tpl", $data) );
}

function deletemultipledo($module, $action, $subaction) {
    global $gui, $url,$permisos;
    $gui->debug( "<pre>". print_r($_POST, true) . "</pre>" );
    $usernames=leer_datos('usernames');
    $action=leer_datos('action');
    $deleteprofile=leer_datos('deleteprofile'); /* 1 o vacio */

    if ($usernames == '') {
        $gui->session_error("No se han seleccionado usuarios: '$usernames'");
        $url->ir($module, "ver");
    }
    if( !($action == 'clean' || $action == 'delete') ) {
        $gui->session_error("No se han seleccionado una acción");
        $url->ir($module, "ver");
    }

    $ldap=new LDAP();
    $users=preg_split('/,/', $usernames);
    $gui->debuga($users);
    foreach($users as $username) {
        $user=$ldap->get_user($username);
        if ( ! $user ){
            $gui->session_error(" El usuario '$username' no existe.");
        }
        if( ! $permisos->is_admin() && $user->get_role() == 'admin') {
            $gui->session_error("Usuario '$username' no borrado, se necesita ser Administrador para borrar Administradores.");
            continue;
        }
        if ($username == $_SESSION["username"] && $action == 'delete') {
            $gui->session_error("Usuario '$username' no borrado, no se puede borrar la cuenta con la que se está conectado.");
            continue;
        }
        if ($action == 'delete') {
            if ( $user->delUser($deleteprofile) )
                $gui->session_info("Usuario '$username' borrado.");
        }
        elseif ($action == 'clean') {
            if ( $user->resetProfile() )
                $gui->session_info("Perfil del usuario '$username' borrado.");
        }
        else {
            $gui->session_error("Acción no válida ($action).");
        }
    }
    if(! DEBUG)
        $url->ir($module, "ver");
}

function add($module, $action, $subaction) {
    global $gui, $url, $permisos;
    $user=new USER();
    $url=new URLHandler();
    $urlform=$url->create_url($module, 'guardarnuevo');
    
    $data=array("u"=>$user,
                "urlform"=>$urlform,
                "permisos"=> $permisos,
                "action" => "Editar");
    
    $gui->add( $gui->load_from_template("add_usuario.tpl", $data ) );
}

function guardarnuevo($module, $action, $subaction) {
    global $gui, $url,$permisos;
    /* role:
    *        empty => alumno
    *        teacher => Profesor
    *        admin => Administrador
    */
    $gui->debug( "<pre>" . print_r($_POST,true) . "</pre>");
    /*
        Array
        (
            [uid] => x6523
            [cn] => Pedro
            [sn] => Lopez González
            [description] => comentario de pedro
            [password] => 1234
            [repassword] => 1234
            [role] => 
            [loginShell] => /bin/bash
            [add] => Añadir
        )
    */
    if( !$permisos->is_admin() && leer_datos('role') == 'admin') {
        $gui->session_error("Sólo los Administradores pueden crear Administradores.");
        $url->ir($module, "ver");
    }
    // comprobar contraseñas
    if ( leer_datos('password') != leer_datos('repassword') ) {
        $gui->session_error("Las contraseñas no coinciden.");
        $url->ir($module, "add");
    }
    if ( leer_datos('uid') == '' ) {
        $gui->session_error("Identificador vacío.");
        $url->ir($module, "add");
    }
    
    sanitize($_POST, array('uid' => 'str',
                           'cn'=>'charnum',
                           'sn' => 'charnum',
                           'description' => 'charnum',
                           'loginShell' => 'shell',
                           'role' => 'role',
                           'password' => 'str',
                           'repassword' => 'str'));
    $gui->debug( "<pre>" . print_r($_POST,true) . "</pre>");
    $user = new USER($_POST);
    if ( ! $user->newUser() ) 
        $url->ir($module, "add");
    
    if(! DEBUG)
        $url->ir($module, "ver");
}

function groups($module, $action, $subaction) {
    global $gui, $url;
    
    $button=leer_datos('button');
    $gui->debug("button='$button'");
    if( $button !='' && $button != "Buscar"){
        $url->ir($module, "groupadd");
    }
    
    $filter=leer_datos('Filter');
    
    $ldap=new LDAP();
    $groups=$ldap->get_groups($filter, $include_system=false);
    
    
    $urlform=$url->create_url($module, 'grupos');
    
    $pager=new PAGER($groups, $urlform, 0, $args='', NULL);
    $pager->processArgs( array('Filter', 'skip', 'sort') );
    $groups=$pager->getItems();
    
    $pager->sortfilter="(cn|numUsers)";

    $data=array("groups" => $groups, 
                "filter" => $filter, 
                "urlform" => $urlform, 
                "urleditar"=>$url->create_url($module, 'groupeditar'),
                "urlborrar"=>$url->create_url($module, 'groupdelete'),
                "urlmiembros"=> $url->create_url($module,'groupmembers'),
                "pager"=>$pager);
    $gui->add( $gui->load_from_template("ver_grupos.tpl", $data) );
}

function groupmembers($module, $action, $subaction) {
    global $gui, $url;
    $group= $subaction;
    $ldap=new LDAP();
    $members=$ldap->get_members_in_and_not_group($group);
    $urlform=$url->create_url($module, 'groupmembersguardar', $group );
    $data=array("group"=>$group, 
                "members"=>$members, 
                "urlform" => $urlform);
    $gui->add( $gui->load_from_template("editar_groupmembers.tpl", $data) );
}

function groupmembersguardar($module, $action, $subaction) {
    global $gui, $url;
    $gui->debug( "<pre>" . print_r($_POST,true) . "</pre>");
    /*
    Array
    (
        [addtogroup] => Añadir usuarios al grupo
        [adduser] => Array
            (
                [0] => mario
                [1] => alumno
                [2] => max-control
                [3] => profe4
            )

        [deluser] => Array
            (
                [0] => mario
                [1] => alumno
                [2] => max-control
                [3] => profe4
            )
        [group] => Teachers
    )
    */
    
    $group=leer_datos('group');
    
    $addusers=clean_array($_POST, 'adduser');
    $delusers=clean_array($_POST, 'deluser');
    
    $gui->debug("addusers");
    $gui->debuga($addusers);
    $gui->debug("<hr><br>delusers ");
    $gui->debuga($delusers);
    
    $ldap= new LDAP();
    
    if ( count($addusers) > 0 ) {
        $groups=$ldap->get_groups($group);
        foreach($addusers as $adduser) {
            // añadir usuario al grupo $grupo
            $groups[0]->newMember($adduser);
            $gui->session_info("Usuario '$adduser' añadido al grupo $group.");
        }
        $url->ir($module, "groupmembers", $group);
    }
    elseif ( count($delusers) > 0 ) {
        $groups=$ldap->get_groups($group);
        foreach($delusers as $deluser) {
            // borrar usuario del grupo $grupo
            $groups[0]->delMember($deluser);
            $gui->session_info("Usuario '$deluser' eliminado del grupo $group.");
        }
        if(! DEBUG)
            $url->ir($module, "groupmembers", $group);
    }
    else {
        $gui->session_error("No ha seleccionado ningún usuario.");
        if(! DEBUG)
            $url->ir($module, "groupmembers", $group);
    }
}

function groupdelete($module, $action, $subaction) {
    global $gui, $url;
    $gui->debug( "<pre>". print_r($_POST, true) . "</pre>" );
    $groups=leer_datos("groupnames");
    $groupsarray=preg_split('/,/', $groups);
    $data=array("groups" => $groups,
                "groupsarray" => $groupsarray,
                "urlform"=>$url->create_url($module, 'groupdeletedo', $groups));
    
    $gui->add( $gui->load_from_template("del_group.tpl", $data) );
}

function groupdeletedo($module, $action, $subaction) {
    global $gui, $url;
    $gui->debug( "<pre>". print_r($_POST, true) . "</pre>" );
    /*
    Array
    (
        [groups] => grupoprueba,grupo2,grupo3
        [deleteprofile] => 1
        [confirm] => Confirmar
    )
    */
    
    $groups=leer_datos('groups');
    $deleteprofile=leer_datos('deleteprofile'); /* 1 o vacio */

    if ($groups == '') {
        $gui->session_error("No se han seleccionado grupos");
        $url->ir($module, "grupos");
    }
    $groupsarray=preg_split('/,/', $groups);
    $ldap=new LDAP();
    foreach($groupsarray as $group) {
        $todelete=$ldap->get_groups($group);
        if ( ! $todelete[0] ){
            $gui->session_error(" El grupo '$group' no existe.");
            continue;
        }
        if ( $todelete[0]->delGroup($deleteprofile) )
            $gui->session_info("Grupo '$group' borrado.");
    }
    if(! DEBUG)
        $url->ir($module, "grupos");
}

function groupadd($module, $action, $subaction) {
    global $gui, $url;
    $group=new GROUP();
    $url=new URLHandler();
    $urlform=$url->create_url($module, 'groupsavenew');
    
    $data=array("u"=>$group,
                "urlform"=>$urlform,
                "action" => "Editar");
    
    $gui->add( $gui->load_from_template("add_group.tpl", $data ) );
}

function groupsavenew($module, $action, $subaction) {
    global $gui, $url;
    
    $gui->debug( "<pre>" . print_r($_POST,true) . "</pre>");
    /*
    Array
    (
        [cn] => aaaa
        [description] => aaaaa
        [createshared] => 1
        [add] => Añadir
    )
    */
    if ( leer_datos('cn') == '' ) {
        $gui->session_error("Identificador de grupo vacío.");
        $url->ir($module, "groupadd");
    }
    
    $createshared=leer_datos('createshared');
    $group=new GROUP($_POST);
    if ( $group->newGroup($createshared) )
        $gui->session_info("Grupo '".$group->cn."' añadido correctamente.");
    
    if(! DEBUG)
        $url->ir($module, "grupos");
}
/*************************************************/

function resetprofile ($module, $action, $subaction) {
    global $url, $gui;
    $user=$subaction;
    $data=array("user" => $user,
                "urlform"=>$url->create_url($module, 'resetprofiledo', $user));
    $gui->add( $gui->load_from_template("resetprofile.tpl", $data) );
}

function resetprofiledo ($module, $action, $subaction) {
    global $url, $gui;

    $gui->debug( "<pre>" . print_r($_POST,true) . "</pre>");
    $username=leer_datos('user');
    
    if ( $username == '') {
        $gui->session_error("Usuario incorrecto");
        $url->ir($module, "ver");
    }
    
    $ldap=new LDAP();
    $user=$ldap->get_user($username);
    //$gui->debuga($user);
    
    if ( $user->resetProfile() )
        $gui->session_info("Perfil del usuario '$username' borrado correctamente.");
    
    if(! DEBUG)
        $url->ir($module, "ver");
}







/*****************************************************************************/




//$gui->session_info("Accion '$action' en modulo '$module'");
switch($action) {
    case "ver": ver($module, $action, $subaction); break; /* ver lista de usuarios */
    case "editar": editar($module, $action, $subaction); break; /* editar usuario */
    case "guardar": guardar($module, $action, $subaction); break; /* guardar usuario */
    /*case "delete": delete($module, $action, $subaction); break; */ /* avisar del borrado de usuario */
    /*case "deletedo": deletedo($module, $action, $subaction); break; */ /* borrar usuario */
    
    case "deletemultiple": deletemultiple($module, $action, $subaction); break; /* avisar del borrado de varios usuarios */
    case "deletemultipledo": deletemultipledo($module, $action, $subaction); break; /* borrar varios usuarios */
    
    case "resetprofile": resetprofile($module, $action, $subaction); break; /* avisar del borrado del perfil del usuario */
    case "resetprofiledo": resetprofiledo($module, $action, $subaction); break; /* borrado del perfil del usuario */
    
    case "add": add($module, $action, $subaction); break; /* formulario añadir usuario */
    case "guardarnuevo": guardarnuevo($module, $action, $subaction); break; /* guardar nuevo usuario */
    
    case "grupos": groups($module, $action, $subaction); break; /* ver lista de grupos */
    case "groupmembers": groupmembers($module, $action, $subaction); break; /* ver lista de miembros en un grupo */
    case "groupmembersguardar": groupmembersguardar($module, $action, $subaction); break; /* ver lista de miembros en un grupo */
    case "groupdelete": groupdelete($module, $action, $subaction); break; /* avisar de borrar grupo */
    case "groupdeletedo": groupdeletedo($module, $action, $subaction); break; /* borrar grupo */
    case "groupadd": groupadd($module, $action, $subaction); break; /* formulario nuevo grupo */
    case "groupsavenew": groupsavenew($module, $action, $subaction); break; /* formulario nuevo grupo */
    
    
    default: $gui->session_error("Accion desconocida '$action' en modulo usuarios");
}



?>
