<?php
if(DEBUG)
    error_reporting(E_ALL);

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
        $this->info="";
        $this->error="";
    }
    
    
    function init_smarty() {
        global $site;
        global $path;
        $this->smarty = new Smarty();
        
        $this->main_template="index.tpl";
        
        $this->smarty->template_dir = $path . SMARTY_TEMPLATES;
        $this->smarty->compile_dir =  SMARTY_CACHE;
        $this->smarty->cache_dir =    SMARTY_CACHE;
        $this->smarty->config_dir =   SMARTY_CACHE;
        //$this->smarty->caching = true;
        $this->smarty->plugins_dir = array( SMARTY_PLUGINS, $path . '/plugins');
        
        if (DEBUG) {
            $this->smarty->assign('DEBUG', "1");
            /*$this->smarty->debugging=true;
            $this->smarty->debug_tpl='debug.tpl';
            $this->smarty->debug_output="html";*/
            $this->smarty->error_reporting=true;
        }
        $this->smarty->assign('baseurl', $site["basedir"] );
        $this->smarty->assign('basedir', $site["basedir"] );
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
    
    function render() {
        global $path;
        if(DEBUG) {
            error_reporting(E_ALL);
        }
        global $site;
        global $nav;
        global $module_actions_submenu;
        
        
        $this->smarty->assign('mod_rewrite', APACHE_MOD_REWRITE);
        
        $this->smarty->assign('module', $nav->get_module() );
        $this->smarty->assign('menuaction', $nav->get_action() );
        $this->smarty->assign('mainmenu', $nav->get_menu("izda"));
        $this->smarty->assign('submenu', $nav->get_menu("dcha"));
        
        if ( isset($this->alert_txt) ){
            $this->smarty->assign('have_alerts', True );
            $this->smarty->assign('alerts', $this->alert_txt );
        }

        if ( DEBUG && isset($this->debug_txt) ){
            $this->smarty->assign('debug', $this->debug_txt );
        }
        
        if ( DEBUG && isset($this->debugger_txt) ){
            $this->smarty->assign('debugger', $this->debugger_txt );
        }

        if (isset($this->javascript) ){
            $this->smarty->assign("javascript", $this->javascript);
        }
        
        if ( isset($_SESSION['info']) && $_SESSION['info'] != '') {
            $this->smarty->assign('session_info', $_SESSION['info'] );
            $_SESSION['info']='';
        }
        if (isset($_SESSION['error']) && $_SESSION['error'] != '') {
            $this->smarty->assign('session_error', $_SESSION['error'] );
            $_SESSION['error']='';
        }

        if (defined('VERSION') ) {
            $this->smarty->assign('max_control_version', VERSION );
        }
        else {
            $this->smarty->assign('max_control_version', "DESCONOCIDA" );
        }
        $this->smarty->assign("content", $this->get_content());

        // use bootstrap template if avalaible
        if( ENABLE_BOOTSTRAP && is_file($path . SMARTY_TEMPLATES . "/bootstrap/" . $this->main_template)) {
            $this->main_template = "bootstrap/" . $this->main_template;
        }

        $this->smarty->display($this->main_template);
    }
    
    
    function add_javascript($txt){
        $this->javascript.="\n$txt";
    }
    
    function alert($txt) {
        $this->alert_txt.="$txt<br/>\n";
    }
    
    function session_error($txt) {
        @$_SESSION['error'].="$txt<br/>\n";
    }
    
    function session_info($txt) {
        @$_SESSION['info'].="$txt<br/>\n";
    }
    
    function debug($txt){
        if ( ! DEBUG )
            return;
        $txt=str_replace('&', '&amp;', $txt);
        $this->debug_txt.="\n$txt<br/>";
    }   
    
    function debuga($a){
        if ( ! DEBUG )
            return;
        $this->debug_txt.="<pre>".print_r($a, true)."</pre>";
    } 
    
    function assign($key, $value){
        $this->smarty->assign($key, $value);
    }
    
    function debugger($txt){
        $this->debugger_txt.=$txt;
    }
    
    function load_from_template($tpl, $data=array()){
        if(DEBUG) {
            error_reporting(E_ALL);
        }
        
        global $path;
        global $site;
        $n=new Smarty();
        $n->template_dir = $path . SMARTY_TEMPLATES;
        $n->compile_dir =  SMARTY_CACHE;
        $n->cache_dir =    SMARTY_CACHE;
        $n->config_dir =   SMARTY_CACHE;
        $this->smarty->plugins_dir = array( SMARTY_PLUGINS, $path . '/plugins');
        
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
        
        if (DEBUG) {
            $n->assign('DEBUG', "1");
            $n->debugging=true;
            $n->debug_tpl='debug.tpl';
            // $n->debug_output="html";
            $n->error_reporting=true;
        }
        
        if( ENABLE_BOOTSTRAP && is_file($path . SMARTY_TEMPLATES . "/bootstrap/" . $tpl)) {
            $tpl = "bootstrap/$tpl";
        }
        return $n->fetch($tpl);
    }
    
    function debug_array($thisarray, $from="debug_array()"){
        $this->debug("debug_array() from=$from");
        foreach($thisarray as $key => $value){
            $this->debug ("$from <small>key=<b>$key</b> value=<b>".print_r($value, true)."</b></small>");
        }
    }
}

