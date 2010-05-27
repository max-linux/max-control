
rem @echo off

net use z: %LOGONSERVER%\netlogon
rem START /WAIT %LOGONSERVER%\netlogon\kix32.exe %LOGONSERVER%\netlogon\logon.kix
Z:\kix32.exe Z:\logon.kix
net use z: /d

rem sleep 10
