@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../deployer/dist/dep
php "%BIN_TARGET%" %*
