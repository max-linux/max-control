@echo off
rem prepara_dominio_paso1.bat - Version 03-03-2011-1
cls
echo.
echo Este script se debe ejecutar con permisos de administrador(boton
echo derecho "Ejecutar como administrador")
echo.
echo Este script prepara Windows 7 y Multipoint Server 2010/2011 para
echo poder unirse a un dominio. Despues se debe de ejecutar el paso2
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
echo ejecute el paso2 para terminar la configuracion.
echo.


:fin
pause
