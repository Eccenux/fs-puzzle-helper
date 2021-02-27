@ECHO OFF

rem Load settings
CALL __settings.bat

rem Execute
"%tmpset_PHP_PATH%" _cut.php


echo.
echo Done
PAUSE
