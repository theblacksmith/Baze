@echo off

if "%OS%"=="Windows_NT" @setlocal

rem %~dp0 is expanded pathname of the current script under NT
set BAT_HOME=%~dp0..

goto init
goto cleanup

:init

if "%PHP_COMMAND%" == "" goto no_phpcommand

goto run
goto cleanup

:run
REM exportVars.php
%PHP_COMMAND% exportVars.php %1 %2
goto cleanup

:no_phpcommand
REM echo ------------------------------------------------------------------------
REM echo WARNING: Set environment var PHP_COMMAND to the location of your php.exe
REM echo          executable (e.g. C:\PHP\php.exe).  (Assuming php.exe on Path)
REM echo ------------------------------------------------------------------------
set PHP_COMMAND=php.exe
goto init

:err_home
echo ERROR: Environment var PHING_HOME not set. Please point this
echo variable to your local phing installation!
goto cleanup


:cleanup
if "%OS%"=="Windows_NT" @endlocal
REM pause