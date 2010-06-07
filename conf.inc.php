<?php

// aparecerán cajas con información útil para errores.
define("pruebas", True);

// poner en True cuando se hayan editado todos los valores
define("CONFIGURED", True);

// necesitamos un usuario creado desde EBOX
// con permisos de administrador
define("LDAP_ADMIN", 'test');
define("LDAP_PASS", 'test');
// desde este panel se le puede cambiar el shell a /bin/bash 
// necesario para apagar/reiniciar máquinas linux


// administrador del LDAP (se puede ver desde ebox)
define("LDAP_BINDDN", 'cn=ebox,dc=max-server');
define("LDAP_BINDPW", 'GzxovzAANdxoPux9');



/* Nombre del dominio */
define("LDAP_DOMAIN", "EBOX");




/*********** a partir de aqui puede que ya no hay aque editar nada ********/

// otros datos del dominio
define("LDAP_HOSTNAME", "127.0.0.1");
/* 
*  max-control suele ejecutarse en el mismo servidor que el LDAP 
*  Es mejor usar la IP 127.0.0.1 que la IP pública
*/
define("LDAP_BASEDN", "dc=max-server");

// entidades organizativas del dominio
// para EBOX estas son las que se usan por defecto
define("LDAP_OU_COMPUTERS", "ou=Computers,dc=max-server");
define("LDAP_OU_USERS", "ou=Users,dc=max-server");
define("LDAP_OU_GROUPS", "ou=Groups,dc=max-server");

// Domain Admins
define("LDAP_OU_DADMINS", "cn=Domain Admins,ou=Groups,dc=max-server");

// Administrators
define("LDAP_OU_ADMINS", "cn=Administrators,ou=Groups,dc=max-server");

// si no existe crearlo con EBOX
define("TEACHERS", "Teachers");
define("LDAP_OU_TEACHERS", "cn=Teachers,ou=Groups,dc=max-server");
define("LDAP_OU_DUSERS", "cn=Domain Users,ou=Groups,dc=max-server");

// ruta al comando winexe
define("WINEXE", "/usr/bin/winexe");

// ruta al comando max-control
define("MAXCONTROL", "/usr/bin/max-control");

// ruta al comando pywakeonlan
define("PYWAKEONLAN", "/usr/bin/pywakeonlan");


// rutas a los perfiles
define("HOMES", "/home/samba/users/");
define("SAMBA_HOMES", '\\\\max-server\homes\\');
define("SAMBA_PROFILES", '\\\\max-server\profiles\\');

// RUTA a la clase smarty
define("SMARTY_REQUIRE","/usr/share/php/smarty/Smarty.class.php");

// cache de smart
define("SMARTY_CACHE", "/var/lib/max-control/cache");

// ruta a las plantillas mejor no editar
define("SMARTY_TEMPLATES", "/templates");

define("SMARTY_PLUGINS", '/usr/share/php/smarty/plugins/');

/*
* enable apache mod_rewrite (mejor no tocar)
*/
define("APACHE_MOD_REWRITE", True);

// puerto usado para conectar por ssh y detectar LINUX
define("LINUX_PORT", 22);
/* esperar 2 segundos para ver si el puerto 22 está abierto*/
define("PROBE_TIMEOUT", 2);

/* timeout para apagar y mostrar un mensaje */
define("POWEROFF_REBOOT_TIMEOUT", 20);

/* directorios TFTP */
define("TFTPBOOT", "/var/lib/tftpboot/");
define("PXELINUXCFG", "/var/lib/tftpboot/pxelinux.cfg/");


// compartir ISOS
define("ISOS_PATH", "/home/samba/shares/isos/");


$site["public_modules"]=array();
        
$site["private_modules_admin"]=array(
        "miperfil" => "Mi perfil",
        "usuarios" => "Usuarios y Grupos",
        "equipos" => "Equipos del dominio",
        "isos" => "Distribuir ISOS",
        #"compartir" => "Compartir carpetas",
        "power" => "Apagado y reinicio",
        "boot" => "Programar arranque equipos",
        );

$site["private_modules_teacher"]=array(
        "miperfil" => "Mi perfil",
        "isos" => "Distribuir ISOS",
        #"compartir" => "Compartir carpetas",
        "power" => "Apagado y reinicio",
        );

$site["private_modules_none"]=array(
        "miperfil" => "Mi perfil"
        );

$site["private_modules"]=array();
?>
