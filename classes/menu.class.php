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
        // 
        if( $permisos->is_templogin() ) {
            return $this->PublicMenu();
        }

        if( ! $permisos->is_connected() ) {
            return $this->PublicMenu();
        }
        elseif ( $permisos->is_admin() ) {
            return $this->AdminMenu();
        }
        elseif ( $permisos->is_tic() ) {
            return $this->AdminMenu();
        }

        elseif ( $permisos->is_teacher() ) {
            return $this->TeacherMenu();
        }

        return $this->PrivateMenu();
    }

    function PublicMenu() {
        return "";
    }

    function PrivateMenu() {
        global $permisos, $gui;
        
        //
        $menu=array();

        return $this->_genMenu($menu);
    }

    function TeacherMenu() {
        global $permisos, $gui;
        
        //
        $menu=array(
                    'isos' => array('title'=>'Distribuir ISOS', 'icon'=>'hdd-o'),

                    'power' => array('title'=>'Apagado y reinicio', 'icon'=>'power-off', 'menu' => array(
                                                'power/aulas'   => array('title'=>'Aulas', 'icon'=>'sitemap'),
                                                'power/equipos' => array('title'=>'Equipos', 'icon'=>'desktop'),
                                                                                      )),
                    );

        return $this->_genMenu($menu);
    }

    function ManagerMenu() {
        $menu=array();

        return $this->_genMenu($menu);
    }

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
        return $gui->load_from_template("menuizdo.tpl", $data);
    }

} /* fin clase Menu */

