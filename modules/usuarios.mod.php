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

if ( ! $permisos->is_admin() ) {
    $gui->session_error("Sólo pueden acceder al módulo de usuarios los administradores.");
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
    /* /control/usuarios/usuarios?Filter=test&button=Buscar */

    $filteruri='';
    $filter=leer_datos('Filter');
    if ($filter != '') {
       $filteruri="&Filter=$filter";
    }
    $sortarray=NULL;
    $sort=leer_datos('sort');
    $sortmode=leer_datos('mode');
    if ($sort != '') {
       if($sortmode=="dsc") {
         $sortarray=array($sort, SORT_DESC);
         $filteruri.="&sort=$sort&mode=dsc";
        }
       else {
         $sortarray=array($sort, SORT_ASC);
         $filteruri.="&sort=$sort&mode=asc";
       }
    }
    $skip=leer_datos('skip');
    if ($skip != '') {
       $filteruri.="&skip=$skip";
    }
    
    $role=leer_datos('role');
    if ($role != '') {
       $filteruri.="&role=$role";
    }

    /* get_users($filter='*', $group=LDAP_OU_USERS, $ignore="max-control", $role='') */
    $usuarios=$ldap->get_users( $filter, $group=LDAP_OU_USERS, $ignore="max-control", $filterrole=$role);
    
    $urlform=$url->create_url($module, $action);
    //$urleditar=$url->create_url($module,'editar');
    //$urlborrar=$url->create_url($module,'delete');
    
    $numusuarios=sizeof($usuarios);
    
    $pager=new PAGER($usuarios, $urlform, $skip, $args=$filteruri, $sortarray);
    $usuarios=$pager->getItems();
    
    $data=array("usuarios" => $usuarios, 
                "numusuarios" => $numusuarios,
                "filter" => $filter, 
                "role" => $role,
                "urlform" => $urlform, 
                "urlformmultiple" => $url->create_url($module, 'deletemultiple'),
                "urleditar"=>$url->create_url($module,'editar'),
                "urlborrar"=>$url->create_url($module,'delete'),
                "pager"=>$pager);
    $gui->add( $gui->load_from_template("ver_usuarios.tpl", $data) );
}

function editar($module, $action, $subaction){
    global $gui, $url;
    $username=$url->get("subaction");
    $ldap=new LDAP();
    $user=$ldap->get_user($username);
    
    if( ! $user ){
        $gui->session_error("Usuario '$username' no encontrado");
        $url->ir($module, "ver");
    }
    
    $urlform=$url->create_url($module, 'guardar');
    
    $data=array("username"=>$username, 
                "u"=>$user,
                "urlform"=>$urlform,
                "action" => "Editar");
    
    $gui->add( $gui->load_from_template("editar_usuario.tpl", $data ) );
}

function guardar($module, $action, $subaction) {
    global $gui, $url;
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
    $ldap=new LDAP();
    
    //$gui->debug("<pre>".print_r($ldap->additionalPasswords('test', 'test'), true)."</pre>");
    
    $usuario=$ldap->get_user($useruid);
    
    $usuario->set($_POST);
    $res=$usuario->save( array('cn', 'sn', 'loginShell') );
    
    if ($res)
        $gui->session_info("Datos guardados correctamente");
    else
        $gui->session_error("Error guardando datos, por favor inténtelo de nuevo.");
    
    // guardar grupo
    $usuario->set_role(leer_datos('role'));
    
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
    if(! DEBUG)
        $url->ir($module, "ver");
}

function delete($module, $action, $subaction) {
    global $gui, $url;
    $user=leer_datos('subaction');
    $data=array("user" => $user,
                "urlform"=>$url->create_url($module, 'deletedo', $user));
    
    $gui->add( $gui->load_from_template("del_usuario.tpl", $data) );
}

function deletedo($module, $action, $subaction) {
    global $gui, $url;
    $gui->debug( "<pre>". print_r($_POST, true) . "</pre>" );
    $username=leer_datos('username');
    $deleteprofile=leer_datos('deleteprofile'); /* 1 o vacio */

    if ($username == '') {
        $gui->session_error("No se pudo encontrar el usuario '$username'");
        $url->ir($module, "ver");
    }

    $ldap=new LDAP();
    $user=$ldap->get_user($username);
    if ( ! $user ){
        $gui->session_error(" El usuario '$username' no existe.");
        $url->ir($module, "ver");
    }
    
    if ( $user->delUser($deleteprofile) )
        $gui->session_info("Usuario '$username' borrado.");
    
    if(! DEBUG)
        $url->ir($module, "ver");
}

function deletemultiple($module, $action, $subaction) {
    global $gui, $url;
    $users=leer_datos('usernames');
    $usersarray=split(',', $users);
    $data=array("users" => $users,
                "usersarray"=>$usersarray,
                "urlform"=>$url->create_url($module, 'deletemultipledo'));
    
    $gui->add( $gui->load_from_template("delmultiple_usuarios.tpl", $data) );
}

function deletemultipledo($module, $action, $subaction) {
    global $gui, $url;
    $gui->debug( "<pre>". print_r($_POST, true) . "</pre>" );
    $usernames=leer_datos('usernames');
    $deleteprofile=leer_datos('deleteprofile'); /* 1 o vacio */

    if ($usernames == '') {
        $gui->session_error("No se han pasado usuarios: '$usernames'");
        $url->ir($module, "ver");
    }

    $ldap=new LDAP();
    $users=split(',', $usernames);
    $gui->debuga($users);
    foreach($users as $username) {
        $user=$ldap->get_user($username);
        if ( ! $user ){
            $gui->session_error(" El usuario '$username' no existe.");
        }
        if ( $user->delUser($deleteprofile) )
            $gui->session_info("Usuario '$username' borrado.");
    }
    if(! DEBUG)
        $url->ir($module, "ver");
}

function add($module, $action, $subaction) {
    global $gui, $url;
    $user=new USER();
    $url=new URLHandler();
    $urlform=$url->create_url($module, 'guardarnuevo');
    
    $data=array("u"=>$user,
                "urlform"=>$urlform,
                "action" => "Editar");
    
    $gui->add( $gui->load_from_template("add_usuario.tpl", $data ) );
}

function guardarnuevo($module, $action, $subaction) {
    global $gui, $url;
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
    
    // comprobar contraseñas
    if ( leer_datos('password') != leer_datos('repassword') ) {
        $gui->session_error("Las contraseñas no coinciden.");
        $url->ir($module, "add");
    }
    if ( leer_datos('uid') == '' ) {
        $gui->session_error("Identificador vacío.");
        $url->ir($module, "add");
    }
    
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
    $groups=$ldap->get_groups($filter, $include_teachers=false);
    //$gui->debug("<pre>".print_r($groups, true)."</pre>");
    
    
    
    $urlform=$url->create_url($module, 'grupos');
    $urleditar=$url->create_url($module, 'groupeditar');
    $urlborrar=$url->create_url($module, 'groupdelete');
    $urlmiembros=$url->create_url($module,'groupmembers');
    
    $data=array("groups" => $groups, 
                "filter" => $filter, 
                "urlform" => $urlform, 
                "urleditar"=>$urleditar,
                "urlborrar"=>$urlborrar,
                "urlmiembros"=> $urlmiembros);
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
    $group=$subaction;
    $data=array("group" => $group,
                "urlform"=>$url->create_url($module, 'groupdeletedo', $group));
    
    $gui->add( $gui->load_from_template("del_group.tpl", $data) );
}

function groupdeletedo($module, $action, $subaction) {
    global $gui, $url;
    $gui->debug( "<pre>". print_r($_POST, true) . "</pre>" );
    /*
    Array
    (
        [group] => grupoprueba
        [deleteprofile] => 1
        [confirm] => Confirmar
    )
    */
    
    $group=leer_datos('group');
    $deleteprofile=leer_datos('deleteprofile'); /* 1 o vacio */

    if ($group == '') {
        $gui->session_error("No se pudo encontrar el grupo '$group'");
        $url->ir($module, "grupos");
    }

    $ldap=new LDAP();
    $groups=$ldap->get_groups($group);
    
    $gui->debug( "<pre>". print_r($groups, true) . "</pre>" );
    
    if ( ! $groups[0] ){
        $gui->session_error(" El grupo '$group' no existe.");
        $url->ir($module, "grupos");
    }
    
    if ( $groups[0]->delGroup($deleteprofile) )
        $gui->session_info("Grupo '$group' borrado.");
    
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












/*****************************************************************************/




//$gui->session_info("Accion '$action' en modulo '$module'");
switch($action) {
    case "ver": ver($module, $action, $subaction); break; /* ver lista de usuarios */
    case "editar": editar($module, $action, $subaction); break; /* editar usuario */
    case "guardar": guardar($module, $action, $subaction); break; /* guardar usuario */
    case "delete": delete($module, $action, $subaction); break; /* avisar del borrado de usuario */
    case "deletedo": deletedo($module, $action, $subaction); break; /* borrar usuario */
    
    case "deletemultiple": deletemultiple($module, $action, $subaction); break; /* avisar del borrado de varios usuarios */
    case "deletemultipledo": deletemultipledo($module, $action, $subaction); break; /* borrar varios usuarios */
    
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
