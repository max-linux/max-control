<?php

// aparecerán cajas con información útil para errores.
//define("pruebas", True);
define("pruebas", False);

// poner en True cuando se hayan editado todos los valores
define("CONFIGURED", True);


/* EDITAR según los datos del dominio */
define("LDAP_DOMAIN", "EBOX");
define("LDAP_HOSTNAME", "127.0.0.1");
define("LDAP_BASEDN", "dc=max-server");
// administrador del LDAP (se puede ver desde ebox)
define("LDAP_BINDDN", 'cn=ebox,dc=max-server');
define("LDAP_BINDPW", 'GzxovzAANdxoPux9');

// entidades organizativas del dominio
// para EBOX estas son las que se usan por defecto
define("LDAP_OU_COMPUTERS", "ou=Computers,dc=max-server");
define("LDAP_OU_USERS", "ou=Users,dc=max-server");
define("LDAP_OU_GROUPS", "ou=Groups,dc=max-server");

// si no existe crearlo con EBOX
define("LDAP_OU_TEACHERS", "cn=Teachers,ou=Groups,dc=max-server");

define("LDAP_OU_DUSERS", "cn=Domain Users,ou=Groups,dc=max-server");

// Domain Admins
define("LDAP_OU_DADMINS", "cn=Domain Admins,ou=Groups,dc=max-server");
// Administrators
define("LDAP_OU_ADMINS", "cn=Administrators,ou=Groups,dc=max-server");

// necesitamos un usuario del grupo 
// LDAP_OU_USERS con permisos de administrador para winexe y SSH
define("LDAP_ADMIN", 'test');
define("LDAP_PASS", 'test');


// ruta al comando winexe
define("WINEXE", "/home/madrid/max-control/bin/winexe");
//define("WINEXE", "/usr/bin/winexe");


define("HOMES", "/home/samba/users/");
define("SAMBA_HOMES", '\\\\max-server\homes\\');
define("SAMBA_PROFILES", '\\\\max-server\profiles\\');


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

// puerto usado para conectar por ssh y detectar LINUX
define("LINUX_PORT", 22);

define("POWEROFF_REBOOT_TIMEOUT", 30);


$site["public_modules"]=array();
        
$site["private_modules_admin"]=array(
        "miperfil" => "Mi perfil",
        "usuarios" => "Usuarios y Grupos",
        "equipos" => "Equipos del dominio",
        #"isos" => "Distribuir ISOS",
        #"compartir" => "Compartir carpetas",
        "power" => "Apagado y reinicio",
        #"boot" => "Programar arranque equipos",
        );

$site["private_modules_teacher"]=array(
        "miperfil" => "Mi perfil",
        #"isos" => "Distribuir ISOS",
        #"compartir" => "Compartir carpetas",
        "power" => "Apagado y reinicio",
        );

$site["private_modules_none"]=array(
        "miperfil" => "Mi perfil"
        );

$site["private_modules"]=array();
?>
