<?php
if(DEBUG)
    error_reporting(E_ALL);

class Navigator
{
  
    var $menu_izdo=array();
    var $allmodules=array();
    var $allmodules_name=array();
    var $mod;
    var $url;


    function __construct(){
        global $gui, $url;
        $this->mod=new ModuleLoader();
        $this->mod->load_module();

        // get modules
        /*$modules=$this->mod->read_modules();
        if ($modules) {
            foreach($this->mod->read_modules() as $module){
                $this->menu_izdo[$module['name']]=$module['description'];
            }
        }*/
        $this->menu_izdo=$this->mod->read_modules();
        // inicializar clase manejadora url
        $this->url= $url;

        return;
    }

    function get_menu($lado) {
        global $site;
        global $gui;
        global $module_actions;
        
        if ($lado == "izda"){
            return $this->menu_izdo;
        }
        else {
            //$gui->debug_array($module_actions, "nav->get_menu(dcho)");
            return $module_actions;
        }
    }

    function get_module(){
        return $this->url->get("module");
    }
    
    function get_action(){
        return $this->url->get("action");
    }

    function set_url($module, $action){
        return $url->create_url($module, $action);
    }
    
    

}


