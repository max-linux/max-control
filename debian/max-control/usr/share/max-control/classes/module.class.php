<?php



class ModuleLoader
{
	function ModuleLoader() {
	    return;
	}


    function load_module(){
        global $path;
        global $gui;
        $url=new URLHandler();
        $thismodule=$url->get("module");
        
        if($thismodule == "") return;
        
        $gui->debug("cargando $thismodule...");
        
        if ((@include "$path/modules/$thismodule.mod.php") == true) {
            $this->start_module($thismodule);
        }
        else{
            
            $gui->alert("ERROR: sección $thismodule no encontrada.<br/>Compruebe que escribió bien la dirección.");
            $gui->debug("ERROR: sección $thismodule no encontrada.<br/>Compruebe que escribió bien la dirección.");
        }
    }


    function read_modules(){
        global $site;
        global $permisos;
        global $gui;
        if( $permisos->is_connected() ){
            if( $_SESSION['role'] == 'admin')
                return $site["private_modules_admin"];
            elseif( $_SESSION['role'] == 'teacher')
                return $site["private_modules_teacher"];
            else
                return $site["private_modules_none"];
        }
        else {
            /*$gui->debug(print_r($site["public_modules"], true));*/
            return $site["public_modules"];
        }
    }

    function start_module($thismodule){
        //global $module_actions;
        //debug ("ModuleHandler::start_module(\"$thismodule\")");
        //print_array($module_actions, $from="ModuleHandler::start_module()");
    }


}
###

?>
