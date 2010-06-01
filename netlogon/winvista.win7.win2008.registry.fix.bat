@echo off
rem
rem   ===> Ejecutar con el boton derecho como administrador
rem
rem  Este script aplica permisos en rama especiales del registro para
rem             Windows Vista, Windows 7 y Windows 2008
rem

rem esta regla es para redirigir carpetas y aplicar la ocultación de unidades
subinacl /noverbose /subkeyreg Software\Microsoft\Windows\CurrentVersion\Explorer /grant="EBOX\Domain Users"=f
subinacl /noverbose /subkeyreg HKEY_LOCAL_MACHINE\Software\Microsoft\Windows\CurrentVersion\Policies\Explorer /grant="EBOX\Domain Users"=f

rem fondo de pantalla
rem subinacl /noverbose /subkeyreg  Software\Microsoft\Windows\CurrentVersion\Policies\System /grant="EBOX\Domain Users"=f

rem pagina de inicio de explorer
subinacl /noverbose /subkeyreg  'Software\Microsoft\Internet Explorer\Main' /grant="EBOX\Domain Users"=f

rem no mostrar el ultimo nombre de usuario
subinacl /noverbose /subkeyreg HKEY_LOCAL_MACHINE\SOFTWARE\Microsoft\Windows\CurrentVersion\Policies\System /grant="EBOX\Domain Admins"=f

rem archivos offline
subinacl /noverbose /subkeyreg HKEY_LOCAL_MACHINE\Software\Policies\Microsoft\Windows /grant="EBOX\Domain Admins"=f

