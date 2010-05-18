<?php

/* activar debug */
//define("pruebas", True);
//define ("pruebas", False);

/*
if ( isset($_SESSION["dni"]) && $_SESSION["dni"] == 'e71126181S' ) {
    define("pruebas", True);
}
else {
    define("pruebas", False);
}*/

//define("pruebas", True);
define("pruebas", False);



/* editar estas variables */
define("LDAP_DOMAIN", "EBOX");
define("LDAP_HOSTNAME", "127.0.0.1");
define("LDAP_BASEDN", "dc=max-server");
define("LDAP_BINDDN", 'cn=ebox,dc=max-server');
define("LDAP_BINDPW", 'GzxovzAANdxoPux9');

define("LDAP_OU_COMPUTERS", "ou=Computers,dc=max-server");
define("LDAP_OU_USERS", "ou=Users,dc=max-server");
define("LDAP_OU_GROUPS", "ou=Groups,dc=max-server");
define("LDAP_OU_TEACHERS", "cn=Teachers,ou=Groups,dc=max-server");

/*
* path of Smarty.class.php
* En Debian está en: /usr/share/php/smarty/libs/Smarty.class.php
*
* si no tenemos el paquete instalado descargarlo de su web
* descomprimirlo y editar la ruta a esta variable
*/
$site["smarty_require"]="/usr/share/php/smarty/Smarty.class.php";

/* 
* ruta a la cache de smarty debe tener permisos 777 o pertenecer 
* al usuario www-data
*/
$site["smarty_templates_c"]="/cache";

/* ruta a las plantillas mejor no editar */
$site["smarty_templates"]="/templates";


# enable apache mod_rewrite
$site["enable_mod_rewrite"]=True;


$site["public_modules"]=array();
        
$site["private_modules"]=array(
        "portada" => "Portada",
        "usuarios" => "Usuarios y Grupos",
        "equipos" => "Equipos del dominio",
        "isos" => "Distribuir ISOS",
        "compartir" => "Compartir carpetas",
        "power" => "Apagado y reinicio",
        "boot" => "Programar arranque equipos",
        );


?>
