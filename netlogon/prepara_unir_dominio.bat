@echo off
cls
rem echo Copie este script y el fichero "subinacl.exe" a una carpeta local
rem echo del equipo que quiere unir al dominio y ejecutelo desde ahi.
echo.
echo Este script se debe ejecutar con permisos de administrador(boton
echo derecho "Ejecutar como administrador")
echo.
echo Este script prepara Windows Vista, Windows 7 y Windows 2008 para
echo poder unirse a un dominio. Despues se debe de volver a ejecutar
echo para aplicar permisos adecuados en ciertas claves del registro.
echo.
echo Si tiene problemas para ejecutar este script, desactive el "Control
echo de cuentas de usuario" desde el Panel de control > Cuentas de usuario
echo.
echo.
echo Pulse CTRL+C si desea cancelar, otra tecla para continuar...
pause > nul

rem Compruebo si tengo permisos de administrador
reg add HKLM\Software\Microsoft\Windows /v borrame /t REG_SZ /d "puedes borrarme, entrada creada para comprobar si se tienen derechos de administrador" /f
if errorlevel 1 goto noadministrador
reg delete HKLM\Software\Microsoft\Windows /v borrame /f
goto siadministrador

:noadministrador
cls
echo.
echo ERROR: No tiene derechos de administrador. Haga clic derecho sobre el
echo        icono del script y seleccione "Ejecutar como administrador".
echo.
goto fin

:siadministrador
rem Compruebo si ya est unido al dominio. Si no lo est, en Multipoint Server 2010 falla
rem la asignacin de permisos al no encontrar el grupo "Domain Users" por eso no se pueden
rem ejecutar los subinacl hasta que est en el dominio.

rem if /i %USERDOMAIN%==multiseat goto DOMINIO
reg query "hklm\System\CurrentControlSet\Services\Tcpip\Parameters" /v Domain > %USERPROFILE%\so.txt
find /i "MULTISEAT" %USERPROFILE%\so.txt > nul
if not errorlevel 1 goto DOMINIO
cls
echo Preparando equipo para unirlo al dominio...
echo.
rem En Winsta, Windows 7 y Multipoint 2010 hay que anadir estas dos claves para que se pueda unir al dominio.
rem Si no existen estas claves da un error indicando que el dominio no ha podido ser encontrado.
reg add HKLM\System\CurrentControlSet\services\LanmanWorkstation\Parameters /v DomainCompatibilityMode   /t REG_DWORD /d 1 /f
reg add HKLM\System\CurrentControlSet\services\LanmanWorkstation\Parameters /v DNSNameResolutionRequired /t REG_DWORD /d 0 /f

rem Esta clave es para activar la busqueda de nombres por NETBIOS en el orden adecuado, de lo contrario
rem por ejemplo no se puede acceder a \\max-server por el nombre mientras que por la IP s deja.
rem Este fallo de momento slo se ha detectado en Multipoint Server 2010.
reg query "hklm\Software\Microsoft\Windows NT\CurrentVersion" /v ProductName > %USERPROFILE%\so.txt
find /i "Multipoint Server 2010" %USERPROFILE%\so.txt > nul
if errorlevel 1 goto NoEsMultipoint
   reg add HKLM\System\CurrentControlSet\Services\Netbt\Parameters /v DhcpNodeType /t REG_DWORD /d 1 /f
:NoEsMultipoint
del %USERPROFILE%\so.txt
echo.
echo Primer paso configurado. Es necesario reiniciar el equipo, entonces ya
echo lo podra unir al dominio "multiseat". Despues de unir el equipo y reiniciar,
echo vuelva a ejecutar este script para terminar la configuracion.
echo.
goto fin

:DOMINIO

del %USERPROFILE%\so.txt
rem Mapeo "netlogin" y me cambio a la unidad para que encuentre el programa subinacl.exe
net use w: \\max-server\netlogon
w:
if not exist w:\subinacl.exe goto nosubinacl
goto sisubinacl
:nosubinacl
cls
echo.
echo ERROR: No se encuentra el fichero "subinacl.exe". Probablemente no
echo        se ha podido mapear la unidad w: en \\max-server\netlogon.
echo        Compruebe que la letra w: no pertenece a ninguna unidad.
echo.
goto fin


:sisubinacl

mkdir c:\Windows\educamadrid
w:\unzip -o w:\fondos-iconos.zip -d C:\Windows\educamadrid > nul
w:\unzip -o w:\GroupPolicy.zip -d C:\borrame > nul
xcopy /e /y c:\borrame c:\Windows\System32\GroupPolicy
rmdir /s /q c:\borrame
copy w:\mount.bat c:\Windows\System32


rem Se oculta la unidad C: a todos, incluidos administradores:
reg add HKEY_LOCAL_MACHINE\Software\Microsoft\Windows\CurrentVersion\Policies\Explorer /v NoDrives /t REG_DWORD /d 4 /f

rem Se deniega el acceso la unidad C: a traves del explorer, a todos los ususarios
reg add HKEY_LOCAL_MACHINE\Software\Microsoft\Windows\CurrentVersion\Policies\Explorer /v NoViewOnDrive /t REG_DWORD /d 4 /f

rem Se fuerza al explorer a que no inicie hasta que se termine el script de logon.bat
reg add HKEY_LOCAL_MACHINE\SOFTWARE\Microsoft\Windows\CurrentVersion\Policies\System /v RunLogonScriptSync /t REG_DWORD /d 1 /f

rem No muestra el ultimo nombre de usuario en la pantalla de login:
reg add HKEY_LOCAL_MACHINE\Software\Policies\Microsoft\Windows\NetCache /v NoConfigCache /t REG_DWORD /d 1 /f



pause

rem esta regla es para redirigir carpetas y aplicar la ocultacin de unidades
subinacl /noverbose /subkeyreg Software\Microsoft\Windows\CurrentVersion\Explorer /grant="Domain Users"=f
pause

reg add HKEY_LOCAL_MACHINE\Software\Microsoft\Windows\CurrentVersion\Policies\Explorer /f
subinacl /noverbose /subkeyreg HKEY_LOCAL_MACHINE\Software\Microsoft\Windows\CurrentVersion\Policies\Explorer /grant="Domain Users"=f
pause

rem fondo de pantalla
rem subinacl /noverbose /subkeyreg Software\Microsoft\Windows\CurrentVersion\Policies\System /grant="Domain Users"=f

rem pagina de inicio de explorer
reg add "HKEY_LOCAL_MACHINE\Software\Microsoft\Internet Explorer\Main" /f
subinacl /noverbose /subkeyreg  "Software\Microsoft\Internet Explorer\Main" /grant="Domain Users"=f
pause

rem no mostrar el ultimo nombre de usuario
subinacl /noverbose /subkeyreg HKEY_LOCAL_MACHINE\SOFTWARE\Microsoft\Windows\CurrentVersion\Policies\System /grant="Domain Admins"=f
pause

rem archivos offline
subinacl /noverbose /subkeyreg HKEY_LOCAL_MACHINE\Software\Policies\Microsoft\Windows /grant="Domain Admins"=f

if exist w:\subinacl.exe net use w: /d /y
:fin
pause
