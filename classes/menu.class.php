<?php
/*
EWF (Easy Web Framework)
Copyright (c) 2011 Mario Izquierdo mario.izquierdo@thinetic.es>
*/
if(DEBUG)
    error_reporting(E_ALL);



class Menu {
    var $localMenus=array();
    var $plantas=null;

    function Menu(){
        return;
    }

    function addMenu($menu) {
        $this->localMenus[]=$menu;
    }

    function getMenu() {
        global $permisos, $gui;

        // $switchUser=$gui->smarty->get_template_vars('switchUser');

        if( ! $permisos->is_connected() ) {
            return $this->PublicMenu();
        }
        elseif ( $permisos->is_admin() ) {
            return $this->AdminMenu();
        }
        elseif ( $permisos->is_tic() ) {
            return $this->TicMenu();
        }

        elseif ( $permisos->is_teacher() ) {
            return $this->TeacherMenu();
        }

        return $this->PrivateMenu();
    }

    function PublicMenu() {
        return "";
    }
/*

$site["private_modules_admin"]=array(
        "miperfil" => "Mi perfil",
        "usuarios" => "Usuarios y Grupos",
        "equipos" => "Aulas y Equipos",
        "isos" => "Distribuir ISOS",
        "power" => "Apagado y reinicio",
        "boot" => "Programar arranque",
        );

$site["private_modules_tic"]=array(
        "miperfil" => "Mi perfil",
        "usuarios" => "Usuarios y Grupos",
        "equipos" => "Aulas y Profesores",
        "isos" => "Distribuir ISOS",
        "power" => "Apagado y reinicio",
        "boot" => "Programar arranque",
        );

$site["private_modules_teacher"]=array(
        "miperfil" => "Mi perfil",
        "isos" => "Distribuir ISOS",
        "power" => "Apagado y reinicio",
        );

$site["private_modules_none"]=array(
        "miperfil" => "Mi perfil"
        );

 */
    function PrivateMenu() {
        global $permisos, $gui;
        
        //
        $menu=array(
                'dash'   => array('title'=>'Inicio', 'icon'=>'bar-chart-o'),
                // 'users'  => array('title'=>'Usuarios', 'icon'=>'users', 'menu' => array(
                //                             'users'       =>array('title'=>'Lista', 'icon'=>'th'),
                //                             'users/create'=>array('title'=>'Nuevo', 'icon'=>'plus'),
                //                                                                       )),

                'minipc' => array('title'=>'Equipos', 'icon'=>'random'),

                'contadores' => array('title'=>'Contadores', 'icon'=>'tachometer', 'menu' => array(
                                            'contadores'      => array('title'=>'Lista', 'icon'=>'th'),
                                            // 'contadores/add'  => array('title'=>'Nuevo', 'icon'=>'plus'),
                                            'contadores/test' => array('title'=>'Prueba lectura', 'icon'=>'download'),
                                                                                      )),
                'wizard' => array('title'=>'Asistente', 'icon'=>'magic'),

                    );

        return $this->_genMenu($menu);
    }

    function ManagerMenu() {
        $menu=array(
                'dash'   => array('title'=>'Inicio', 'icon'=>'bar-chart-o'),
                'users'  => array('title'=>'Usuarios', 'icon'=>'users'),

                'minipc' => array('title'=>'Equipos', 'icon'=>'random'),

                'contadores' => array('title'=>'Contadores', 'icon'=>'tachometer', 'menu' => array(
                                            'contadores'      => array('title'=>'Lista', 'icon'=>'th'),
                                            'contadores/add'  => array('title'=>'Nuevo', 'icon'=>'plus'),
                                            'contadores/test' => array('title'=>'Prueba lectura', 'icon'=>'download'),
                                                                                      )),

                'contabilidad' => array('title'=>'Contabilidad', 'icon'=>'eur', 'menu' => array(
                                            'contabilidad'      => array('title'=>'Precios', 'icon'=>'th'),
                                            'contabilidad/calculate' => array('title'=>'Facturas', 'icon'=>'money'),
                                                                                      )),
                
                'wizard' => array('title'=>'Asistente', 'icon'=>'magic'),
                    );

        return $this->_genMenu($menu);
    }
/*
$site["private_modules_admin"]=array(
        "miperfil" => "Mi perfil",
        "usuarios" => "Usuarios y Grupos",
            "ver" => "Usuarios",
            "grupos" => "Grupos",
            "importar" => "Importar"
        "equipos" => "Aulas y Equipos",
            "aulas" => "Aulas",
            "ver" => "Equipos"

        "isos" => "Distribuir ISOS",
            "ver" => "Ver ISOS",

        "power" => "Apagado y reinicio",
            "aulas" => "Aulas",
            "equipos" => "Equipos",
        "boot" => "Programar arranque",
            "aula" => "Aulas",
            "equipo" => "Equipos",
        );

 */
    function AdminMenu() {
        $menu=array(
                'dash'          => array('title'=>'Inicio', 'icon'=>'home'),

                'usuarios'  => array('title'=>'Usuarios y grupos', 'icon'=>'users', 'menu' => array(
                                              'usuarios/ver'       => array('title'=>'Usuarios', 'icon'=>'user'),
                                              'usuarios/grupos'    => array('title'=>'Grupos', 'icon'=>'users'),
                                              'usuarios/importar'  => array('title'=>'Importar', 'icon'=>'upload'),
                                                                                      )),

                'equipos' => array('title'=>'Equipos', 'icon'=>'desktop', 'menu' => array(
                                             'equipos/aulas'       => array('title'=>'Aulas', 'icon'=>'sitemap'),
                                             'equipos/ver'         => array('title'=>'Equipos', 'icon'=>'desktop'),
                                                                                       )),

                'isos' => array('title'=>'Distribuir ISOS', 'icon'=>'hdd-o'),

                'power' => array('title'=>'Apagado y reinicio', 'icon'=>'power-off', 'menu' => array(
                                            'power/aulas'   => array('title'=>'Aulas', 'icon'=>'sitemap'),
                                            'power/equipos' => array('title'=>'Equipos', 'icon'=>'desktop'),
                                                                                      )),
                'boot' => array('title'=>'Programar arranque', 'icon'=>'gears', 'menu' => array(
                                          'boot/aula'   => array('title'=>'Aulas', 'icon'=>'sitemap'),
                                          'boot/equipo' => array('title'=>'Equipos', 'icon'=>'desktop'),
                                                                                      )),
                
                    );

        return $this->_genMenu($menu);
    }

    function _genMenu($elems) {
        global $gui;
        // ONLY for smarty3
        // $menu_active='';
        $menu_active=$gui->smarty->getTemplateVars('module');
        $supermenu=preg_split('/\//i', $menu_active);
        $data=array('menu'=>$elems,
                    'menu_active'=>$menu_active,
                    'supermenu'=>$supermenu[0]);
        // if(DEBUG){
        //     $data['debug']=true;
        // }
        return $gui->load_from_template("bootstrap/menuizdo.tpl", $data);
    }

} /* fin clase Menu */

