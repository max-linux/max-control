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
    var $basedir='';
    var $lang='';
    var $redirect=null;

    function clean($value){
      // limpiar variable
      return( sanitizeOne( $value , 'charnum' ) );
    }

    function __construct($basedir, $lang){
        global $gui;

        if($basedir != '' && $basedir != '/') {
            $this->basedir=$basedir;
        }
        if($lang != '') {
            $this->lang=$lang;
        }

        /*
        // leemos las variables de la URL
        $this->query_string=@preg_split("/&/", $_SERVER['QUERY_STRING']);
        //$gui->debuga($this->query_string);
        //$gui->debuga($_SERVER);
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
        */

        if( array_key_exists('REQUEST_URI', $_SERVER) ) {
            $this->data=$this->parse($_SERVER['REQUEST_URI']);
            $this->url_array=$this->data['args'];
        }

        if( isset($_SESSION['redirect']) ) {
            $this->redirect=$_SESSION['redirect'];
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
        if( APACHE_MOD_REWRITE ){
            return $this->basedir . "/" . $this->lang ."/" . $this->get("module") . "/" . $this->get("action");
        }
        else{
            return $this->basedir . "/index.php?lang=".$this->lang."&module=" . $this->get("module") . "&action=" . $this->get("action");
        }
    }

    function create_url($module, $action='', $subaction='') {
        global $relpath, $gui;
        if( APACHE_MOD_REWRITE ){
            $txt=$this->basedir . "/$module" ;
            if($action != '')
                $txt.="/$action";
            if($subaction != "")
                $txt.="/$subaction";
            if($txt == '//')
                $txt='/';
            return $txt;
        }
        else{
            $txt=$relpath . "/index.php?&module=$module";
            //$gui->debuga($txt);
            if($action != "")
                $txt.="&action=$action";
            if($subaction != "")
                $txt.="&subaction=$subaction";
            return $txt;
        }
    }
    function change_language($newlang) {
        $original_language=$this->lang;
        $this->lang=$newlang;
        $newurl=$this->create_url($this->get('module'), $this->get('action'),$this->get('subaction'));
        $this->lang=$original_language;
        return $newurl;
    }


    function ir($module='', $action='', $subaction=''){
        global $gui;
        $a = $this->create_url($module, $action, $subaction);
        //$gui->session_error($a);
        $schema = $_SERVER['SERVER_PORT'] == '443' ? 'https' : 'http';
        $host = strlen($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME'];
        if (headers_sent()){
            echo ("Imposible ir a: $a, cabeceras enviadas.");
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

    function redirectLogin(){
        global $module, $action, $subaction;
        $_SESSION['redirect']=array($module, $action, $subaction);
        $this->ir('login');
    }






    function parse($request_uri) {
        global $gui;
        $data=array(
                    'url'=>$request_uri,
                    'module'=> '',
                    'action'=>'',
                    'subaction'=>'',
                    'args'=>array(),
                    );

        $query_string='';
        // $gui->debuga($request_uri);
        $request_uri = str_replace("/control2", "", $request_uri);
        $request_uri = str_replace("/control", "", $request_uri);

        if (strpos($request_uri, '?') !== false || strpos($request_uri, '&') !== false) {
            $tmpargs=preg_split('/[&?]/', $request_uri,2);
            $request_uri=$tmpargs[0];
            $query_string=$tmpargs[1];
            $data['request']=$request_uri;
            $data['params']=$query_string;
            $_SERVER['QUERY_STRING']=$query_string;
        }
        $request_uri=trim($request_uri, "/");


        $tmp=explode("/", $request_uri);
        // $gui->debuga($tmp);
        foreach ($tmp as $key => $value) {
            if($value=='') {
                continue;
            }
            //$value=utf8_decode(urldecode($value));
            $value=urldecode($value);
            if($key==0) {
                $data['module']=$value;
                $data['args']['module']=$value;
                $_GET['module']=$value; $_REQUEST['module']=$value;
            }
            elseif($key==1) {
                $data['action']=$value;
                $data['args']['action']=$value;
                $_GET['action']=$value; $_REQUEST['action']=$value;
            }
            elseif($key==2) {
                $data['subaction']=$value;
                $data['args']['subaction']=$value;
                $_GET['subaction']=$value; $_REQUEST['subaction']=$value;
            }
            elseif($key==3) {
                // $data['args']=$value;
                $data['args']['args']=$value;
                $_GET['args']=$value; $_REQUEST['args']=$value;
            }
        }

        if($query_string != '') {
            $tmp=preg_split('/[&?]/', $query_string);
            foreach ($tmp as $key => $value) {
                // $tmpargs=explode('=', $value);
                @list($varname, $varvalue)=preg_split("/=/", $value);
                $data['args'][$varname]=$varvalue;
                // overwrite $_GET
                $_GET[$varname]=$varvalue;
                $_REQUEST[$varname]=$varvalue;
            }
        }

        return $data;
    }

} /* fin clase URLHandler */

