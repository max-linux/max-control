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


rem reg query "hklm\System\CurrentControlSet\Services\Tcpip\Parameters" /v Domain > %USERPROFILE%\so.txt
rem find /i "MULTISEAT" %USERPROFILE%\so.txt > nul
rem if not errorlevel 1 goto DOMINIO

rem Averiguo si el equipo esta en el domino detectando Si la clave Domain esta vacia.
reg add "hklm\System\CurrentControlSet\Services\Tcpip\Performance" /v Domain /f
reg compare "hklm\System\CurrentControlSet\Services\Tcpip\Parameters" "hklm\System\CurrentControlSet\Services\Tcpip\Performance" /v Domain
rem Si errorlevel 2 las claves son diferentes, no esta vacia, por lo que esta en un dominio.
if errorlevel 2 goto DOMINIO



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
goto fin2

:DOMINIO

cls
rem Mapeo "netlogin" y me cambio a la unidad para que encuentre el programa subinacl.exe
echo Conectando unidad de red w:(\\max-server\netlogon)...
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
echo Copiando politicas de grupo a C:\Windows\System32\GroupPolicy...
xcopy /e /y c:\borrame c:\Windows\System32\GroupPolicy
rmdir /s /q c:\borrame
echo Copiando mount.bat a C:\Windows\System32...
copy w:\mount.bat c:\Windows\System32


rem Se oculta la unidad C: a todos, incluidos administradores:
reg add HKEY_LOCAL_MACHINE\Software\Microsoft\Windows\CurrentVersion\Policies\Explorer /v NoDrives /t REG_DWORD /d 4 /f > nul

rem Se deniega el acceso la unidad C: a traves del explorer, a todos los ususarios
reg add HKEY_LOCAL_MACHINE\Software\Microsoft\Windows\CurrentVersion\Policies\Explorer /v NoViewOnDrive /t REG_DWORD /d 4 /f > nul

rem Se fuerza al explorer a que no inicie hasta que se termine el script de logon.bat
reg add HKEY_LOCAL_MACHINE\SOFTWARE\Microsoft\Windows\CurrentVersion\Policies\System /v RunLogonScriptSync /t REG_DWORD /d 1 /f > nul

rem No muestra el ultimo nombre de usuario en la pantalla de login:
reg add HKEY_LOCAL_MACHINE\Software\Policies\Microsoft\Windows\NetCache /v NoConfigCache /t REG_DWORD /d 1 /f > nul





if not exist "c:\Program Files\Elaborate Bytes\VirtualCloneDrive" goto x86
subinacl /nostatistic /subdirectories "c:\Program Files\Elaborate Bytes\VirtualCloneDrive" /revoke="Usuarios" > nul
subinacl /nostatistic /subdirectories "c:\Program Files\Elaborate Bytes\VirtualCloneDrive" /grant="Teachers"=f > nul
goto SIVCD
:x86
if not exist "c:\Program Files (x86)\Elaborate Bytes\VirtualCloneDrive" goto NOVCD
subinacl /nostatistic /subdirectories "c:\Program Files (x86)\Elaborate Bytes\VirtualCloneDrive" /revoke="Usuarios" > nul
subinacl /nostatistic /subdirectories "c:\Program Files (x86)\Elaborate Bytes\VirtualCloneDrive" /grant="Teachers"=f > nul
goto SIVCD
:NOVCD
echo.
echo ATENCION: No se ha detectado VirualCloneDrive, instalelo y vuelva a ejecutar
echo           este script.
echo.
pause



:SIVCD
rem esta regla es para redirigir carpetas y aplicar la ocultacin de unidades
subinacl /nostatistic /subkeyreg Software\Microsoft\Windows\CurrentVersion\Explorer /grant="Domain Users"=f > nul

reg add HKEY_LOCAL_MACHINE\Software\Microsoft\Windows\CurrentVersion\Policies\Explorer /f > nul
subinacl /nostatistic /subkeyreg HKEY_LOCAL_MACHINE\Software\Microsoft\Windows\CurrentVersion\Policies\Explorer /grant="Domain Users"=f > nul

rem pagina de inicio de explorer
reg add "HKEY_LOCAL_MACHINE\Software\Microsoft\Internet Explorer\Main" /f > nul
subinacl /nostatistic /subkeyreg  "Software\Microsoft\Internet Explorer\Main" /grant="Domain Users"=f > nul

rem no mostrar el ultimo nombre de usuario
subinacl /nostatistic /subkeyreg HKEY_LOCAL_MACHINE\SOFTWARE\Microsoft\Windows\CurrentVersion\Policies\System /grant="Domain Admins"=f > nul

rem archivos offline
subinacl /nostatistic /subkeyreg HKEY_LOCAL_MACHINE\Software\Policies\Microsoft\Windows /grant="Domain Admins"=f > nul

rem Como voy a eliminar w: me cambio de unidad ya que estoy trabajando ahora sobre ella, si no se producen errores
c:
echo.
echo Desconectando la unidad w:(\\max-server\netlogon)...
if exist w:\subinacl.exe net use w: /d /y

echo.
echo Fase 2 de configuracion terminada.
echo.

:fin2
reg delete "hklm\System\CurrentControlSet\Services\Tcpip\Performance" /v Domain /f > nul

:fin
pause
