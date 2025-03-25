@echo off
:: Script para habilitar/deshabilitar el modo de mantenimiento

:: Verificar si se proporcionó un argumento
if "%~1"=="" (
    echo Uso: maintenance.bat [up^|down]
    echo   up   - Desactiva el modo de mantenimiento
    echo   down - Activa el modo de mantenimiento
    exit /b 1
)

:: Rutas de los archivos
set MAINTENANCE_FILE=storage\framework\maintenance.php

:: Procesar el argumento
if /i "%~1"=="down" (
    echo Activando el modo de mantenimiento...
    copy /Y "storage\framework\maintenance.php.template" "%MAINTENANCE_FILE%" > nul 2>&1
    if not exist "%MAINTENANCE_FILE%" (
        echo Error: No se pudo crear el archivo de mantenimiento en %MAINTENANCE_FILE%
        exit /b 1
    )
    echo Modo de mantenimiento activado. El sitio ahora muestra la página de mantenimiento.
) else if /i "%~1"=="up" (
    echo Desactivando el modo de mantenimiento...
    if exist "%MAINTENANCE_FILE%" (
        move "%MAINTENANCE_FILE%" "%MAINTENANCE_FILE%.disabled"
        echo Archivo de mantenimiento desactivado.
    ) else (
        echo El modo de mantenimiento ya está desactivado.
    )
    echo Modo de mantenimiento desactivado. El sitio ahora está en línea.
) else (
    echo Uso: maintenance.bat [up^|down]
    echo   up   - Desactiva el modo de mantenimiento
    echo   down - Activa el modo de mantenimiento
    exit /b 1
)

exit /b 0