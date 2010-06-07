@echo off

set VCDMOUNT="C:\Archivos de Programa\Elaborate Bytes\VirtualCloneDrive\VCDMount"

if exist "C:\Program Files (x86)\Elaborate Bytes\VirtualCloneDrive" set VCDMOUNT="C:\Program Files (x86)\Elaborate Bytes\VirtualCloneDrive\VCDMount"

if exist "C:\Program Files\Elaborate Bytes\VirtualCloneDrive" set VCDMOUNT="C:\Program Files\Elaborate Bytes\VirtualCloneDrive\VCDMount"


if "%1"=="umount" GOTO UMOUNT
if "%1"=="mount" GOTO MOUNT

exit


:MOUNT
rem net use i: /d
rem net use i: \\max-server\isos
rem %VCDMOUNT% "\\max-server\isos\%2"
%VCDMOUNT% "I:\%2"
exit


:UMOUNT
%VCDMOUNT% "/u"
rem clear mount history
%VCDMOUNT% "/h"
exit
