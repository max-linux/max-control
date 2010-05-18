<?php


class Gui
{

    var $smarty;
    var $content;
    var $javascript;
    var $alert_txt;
    var $debug_txt;
    var $debugger_txt;
    var $main_template;
    
    function Gui() {
        $this->content="";
        $this->init_smarty();
    }
    
    
    function init_smarty() {
        global $site;
        global $path;
        $this->smarty = new Smarty();
        
        $this->main_template="index.tpl";
        
        $this->smarty->template_dir = $path . $site["smarty_templates"];
        $this->smarty->compile_dir =  $path . $site["smarty_templates_c"];
        $this->smarty->cache_dir =    $path . '/cache';
        $this->smarty->config_dir =   $path . '/cache';
        //$this->smarty->caching = true;
        $this->smarty->plugins_dir = array( '/usr/share/php/smarty/plugins/',
                                             $path . '/plugins');
        
        if (pruebas) {
            $this->smarty->assign('pruebas', "1");
            $this->smarty->debugging=true;
            $this->smarty->debug_tpl='debug.tpl';
            $this->smarty->debug_output="html";
            $this->smarty->error_reporting=true;
        }
        $this->smarty->assign('baseurl', $site["basedir"] );
        $this->smarty->assign('basedir', $site["basedir"] );
        
        /*
        $this->smarty->assign('title', $site["title"] );
        $this->smarty->assign('sort_name', $site["sort_name"] );
        $this->smarty->assign('sort_desc', $site["sort_desc"] );
        $this->smarty->assign('template_dir', $site["smarty_templates"] );
        */
    }

    function change_main_template($tpl) {
        $this->main_template=$tpl;
    }

    function add($txt){
        $this->content.="\n$txt";
    }
    
    function get_content() {
        return $this->content;
    }
    
    
    
    function add_br($num){
        for ($i=1; $i<$num; $i++){
            $this->add("<br/>");
        }
    }
    
    function render(){
        if(pruebas) {
            error_reporting(E_ALL);
        }
        global $site;
        global $nav;
        global $module_actions_submenu;
        
        
        $this->smarty->assign('mod_rewrite', $site["enable_mod_rewrite"]);
        
        $this->smarty->assign('module', $nav->get_module() );
        $this->smarty->assign('menuaction', $nav->get_action() );
        $this->smarty->assign('mainmenu', $nav->get_menu("izda"));
        $this->smarty->assign('submenu', $nav->get_menu("dcha"));
        
        if ( isset($this->alert_txt) ){
            $this->smarty->assign('have_alerts', True );
            $this->smarty->assign('alerts', $this->alert_txt );
        }

        if ( pruebas && isset($this->debug_txt) ){
            $this->smarty->assign('debug', $this->debug_txt );
        }
        
        if ( pruebas && isset($this->debugger_txt) ){
            $this->smarty->assign('debugger', $this->debugger_txt );
        }

        if (isset($this->javascript) ){
            $this->smarty->assign("javascript", $this->javascript);
        }

        $this->smarty->assign("content", $this->get_content());
        $this->smarty->display($this->main_template);
    }
    
    
    function add_javascript($txt){
        $this->javascript.="\n$txt";
    }
    
    function alert($txt) {
        $this->alert_txt.="$txt<br/>\n";
    }
    
    function debug($txt){
        if ( ! pruebas )
            return;
        $txt=str_replace('&', '&amp;', $txt);
        $this->debug_txt.="\n$txt<br/>";
    }   
    
    function assign($key, $value){
        $this->smarty->assign($key, $value);
    }
    
    function debugger($txt){
        $this->debugger_txt.=$txt;
    }
    
    function load_from_template($tpl, $data=array()){
        if(pruebas) {
            error_reporting(E_ALL);
        }
        
        global $path;
        global $site;
        $n=new Smarty();
        $n->template_dir = $path . $site["smarty_templates"];
        $n->compile_dir =  $path . $site["smarty_templates_c"];
        $n->cache_dir =    $path . '/cache';
        $n->config_dir =   $path . '/cache';
        $this->smarty->plugins_dir = array( '/usr/share/php/smarty/plugins/',
                                             $path . '/plugins');
        
        $n->assign('baseurl', $site["basedir"] );
        /*
        $n->assign('title', $site["title"] );
        $n->assign('sort_name', $site["sort_name"] );
        $n->assign('sort_desc', $site["sort_desc"] );
        $n->assign('template_dir', $site["smarty_templates"] );
        */
        
        foreach($data as $key => $value){
            $n->assign($key, $value);
        }
        
        if (pruebas) {
            $n->assign('pruebas', "1");
            $n->debugging=true;
            $n->debug_tpl='debug.tpl';
            $n->debug_output="html";
            $n->error_reporting=false;
        }
        
        return $n->fetch($tpl);
    }
    
    function debug_array($thisarray, $from="debug_array()"){
        $this->debug("debug_array() from=$from");
        foreach($thisarray as $key => $value){
            $this->debug ("$from <small>key=<b>$key</b> value=<b>$value</b></small>");
        }
    }
}


?>
