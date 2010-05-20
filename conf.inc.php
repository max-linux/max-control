<?php

// aparecerán cajas con información útil para errores.
define("pruebas", True);
//define("pruebas", False);



/* EDITAR según los datos del dominio */
define("LDAP_DOMAIN", "EBOX");
define("LDAP_HOSTNAME", "127.0.0.1");
define("LDAP_BASEDN", "dc=max-server");
define("LDAP_BINDDN", 'cn=ebox,dc=max-server');
define("LDAP_BINDPW", 'GzxovzAANdxoPux9');

// entidades organizativas del dominio
// para EBOX estas son las que se usan por defecto
define("LDAP_OU_COMPUTERS", "ou=Computers,dc=max-server");
define("LDAP_OU_USERS", "ou=Users,dc=max-server");
define("LDAP_OU_GROUPS", "ou=Groups,dc=max-server");
define("LDAP_OU_TEACHERS", "cn=Teachers,ou=Groups,dc=max-server");


// necesitamos un usuario del grupo 
// LDAP_OU_USERS con permisos de administrador
define("LDAP_ADMIN", 'test');
define("LDAP_PASS", 'test');

// ruta al comando winexe
define("WINEXE", "/home/madrid/max-control/bin/winexe");
//define("WINEXE", "/usr/bin/winexe");


// RUTA a la clase smarty
define("SMARTY_REQUIRE","/usr/share/php/smarty/Smarty.class.php");
// cache de smart
define("SMARTY_CACHE", "/cache");
//define("SMARTY_CACHE", "/var/lib/max-control/cache");

// ruta a las plantillas mejor no editar
define("SMARTY_TEMPLATES", "/templates");

define("SMARTY_PLUGINS", '/usr/share/php/smarty/plugins/');

# enable apache mod_rewrite
define("APACHE_MOD_REWRITE", true);



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
