<?php
// 10 minutos de session
ini_set("session.gc_maxlifetime", "600");
ini_set('memory_limit','128M');
set_time_limit(30);
session_start();
$path=dirname(dirname(__FILE__));

if ( ! is_readable($path . '/conf.inc.php') ) {
    include($path . '/templates/no-configurado.html');
    die();
}

// cargamos la configuracion y el modulo comun
include($path . '/conf.inc.php');
include($path . '/modules/common.inc.php');

if(DEBUG) {
    ini_set('display_errors', 'On');
    ini_set('display_startup_errors', 'On');
    ini_set("session.gc_maxlifetime", "120000");
    error_reporting(E_ALL);
}

$site['path']=$path;

// temporizador
time_start();


// set_error_handler(function($errno, $errstr, $errfile, $errline, array $errcontext) {
//     // error was suppressed with the @-operator
//     if (0 === error_reporting()) {
//         return false;
//     }
//     throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
// });


// cargamos classes
include($path . '/classes/navigator.class.php');
include($path . '/classes/url.class.php');
include($path . '/classes/module.class.php');
require(SMARTY_REQUIRE);

include($path . '/classes/gui.class.php');
include($path . '/classes/permisos.class.php');
include($path . '/classes/ldap.class.php');
include($path . '/classes/pager.class.php');
include($path . '/classes/programer.class.php');
include($path . '/classes/importer.class.php');
include($path . '/classes/winexe.class.php');
require $path . '/classes/menu.class.php';


global $module_actions;
global $module_name;

$site["basedir"]=dirname($_SERVER["PHP_SELF"]);

global $permisos;
$permisos = new Permisos();


// cargar interfaz
global $gui;
$gui= new Gui();
if(DEBUG) {
    $gui->smarty->clearAllCache();
}


global $url;
$url= new URLHandler($site["basedir"], null);

global $menu;
$menu=new Menu();
$gui->assign('menuObj', $menu);

global $ldap;
$ldap = new LDAP();



// ver si es peticion ajax
$ajaxurl=new URLHandler($site["basedir"], null);
if ( $ajaxurl->get("ajax") == "1" ) {
    include($path . '/classes/ajax.class.php');
    $ajax= new Ajax();
    $ajax->process($_POST);
    $ajax->show();
    die();
}


//cargamos la clase navegador
$nav = new Navigator();


// test valid max-control credentials
if( isset($gui) && ! $ldap->connected ) {
  $gui->content='';
  $gui->disable_menu=true;
  $gui->add( $gui->load_from_template("no-configurado.html") );
  $gui->render();
  die();
}


/***************   cargar login o index **************/
if (isset($gui) && isset($_SESSION["user"]) ){
  $gui->assign("logout", True );
  $gui->assign("user", $_SESSION["user"] );
  $gui->assign("role", $permisos->get_humanrole() );
  $gui->assign("logout_url", $nav->url->create_url("login", "logout") );

  if( ! is_file(FIRST_RUN) && $nav->get_module() != "templogin" && $permisos->is_templogin() ) {
    $url->ir('templogin');
  }

  // logueado cargar modulo por defecto
  if( $nav->get_module() == "") {
    if( ENABLE_BOOTSTRAP && $permisos->is_admin() ) {
      $url->ir("dash", "");
    }
    else {
      $url->ir("miperfil", "");
    }

  }

}
elseif ( isset($gui) ) {
  if( ! is_file(FIRST_RUN) ) {
    // login as admintemp user
    $permisos->tempLogin();
    $url->ir('templogin');
  }
  else {
    $gui->assign('login', True );
    $gui->assign("login_url", $nav->url->create_url("login", "login") );
    $gui->main_template="login.tpl";
  }
}
/************************************************************/




if (isset($gui)) {
    $gui->debug("Session cache=" .  ini_get("session.gc_maxlifetime") );
    $gui->debug("Memory  limit=" .  ini_get("memory_limit") );
    if (defined('VERSION') ) {
        $gui->debug("VERSION=".VERSION);
    }
    $gui->debug("QUERY_STRING=".$_SERVER['QUERY_STRING']);
    // $gui->debuga($_SESSION);
    $gui->debug_array($_POST, "index.php POST");
    $gui->debug_array($_GET, "index.php GET");
    $gui->debug("Tiempo de ejecución: " . time_end() );
    $ldap->disconnect($txt='global');
    $gui->render();
}


