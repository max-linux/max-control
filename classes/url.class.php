<?php
if(DEBUG)
    error_reporting(E_ALL);

class URLHandler {

    /*
    *
    *
    *  Esta clase lee el querystring y lo almacena en una array para
    *  poder crear enlaces que funcionen con mod_rewrite
    *
    *
    */

    var $url_array;
    var $query_string;
    var $url;


    function clean($value){
      // limpiar variable
      return( sanitizeOne( $value , 'plain' ) );
    }
    
    function URLHandler(){
        global $site;
        global $gui;
        
        // leemos las variables de la URL
        $this->query_string=preg_split("/&/", $_SERVER['QUERY_STRING']);
        $this->post_array=$_POST;
        $this->url_array=array();
        
        if( $this->query_string[0] == ""){
            //exit if query_string is empty
            return;
        }
        foreach($this->query_string as $clave){
            list($key, $value)=preg_split("/=/", $clave);
            $this->url_array[$key]=$this->clean($value);
        }
    }
    
    function write(){
        $this->url="";
        foreach( $this->url_array as $k => $v){
            $this->url.="&amp;$k=$v";
        }
        $this->url=substr( $this->url, 5, strlen($this->url) ); // quitar &amp; del principio
        return "?" . $this->url;
    }
    
    
    function set($key, $value){
        // modificar clave que ya existe o añadir nueva
        $this->url_array[$key]=$value;
    }
    
    function set_action($module, $action){
        $this->url_array["module"]=$module;
        $this->url_array["action"]=$action;
    }
    
    function get($key){
        // devuelve el valor de una clave si existe
        if( isset($this->url_array[$key]) ){
            return $this->url_array[$key];
        }
    }

    function get_url(){
        global $site;
        if( APACHE_MOD_REWRITE ){
            return $site["basedir"] . "/" . $this->get("module") . "/" . $this->get("action");
        }
        else{
            return $site["basedir"] . "/index.php?module=" . $this->get("module") . "&action=" . $this->get("action");
        }
    }
    
    function create_url($module, $action, $subaction="") {
        global $site;
        if( APACHE_MOD_REWRITE ){
            $txt=$site["basedir"] . "/$module/$action" ;
            if($subaction != "")
                $txt.="/$subaction";
            return $txt;
        }
        else{
            $txt=$site["basedir"] . "/index.php?module=$module&action=$action";
            if($subaction != "")
                $txt.="&subaction=$subaction";
            return $txt;
        }
    }
    
    function ir($module, $action, $subaction=''){
        global $site;
        global $gui;
        $a = $this->create_url($module, $action, $subaction);
        $schema = $_SERVER['SERVER_PORT'] == '443' ? 'https' : 'http';
        $host = strlen($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME'];
        if (headers_sent()){
            $gui->alert("Imposible ir a: $a, cabeceras enviadas.");
            return false;
        } 
        else
        {
            header("HTTP/1.1 301 Moved Permanently");
            // header("HTTP/1.1 302 Found");
            // header("HTTP/1.1 303 See Other");
            header("Location: $schema://$host$a");
            exit();
        }
    }
    

} /* fin clase URLHandler */



