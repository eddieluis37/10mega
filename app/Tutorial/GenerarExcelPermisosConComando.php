<?php

Una hoja Módulos y Permisos, listando cada módulo y sus permisos (ver, crear, editar, etc.).

Una hoja Roles y Permisos, mostrando cada rol y la lista de permisos asignados.


Instala el paquete:

composer require maatwebsite/excel
Crea los exports y el comando:


php artisan make:export PermissionsExport
php artisan make:export ModulesSheet
php artisan make:export RolesSheet
php artisan make:command ExportPermissionsToExcel

Ejecuta el comando para crear el Excel:


php artisan export:permissions
// Uso:
// php artisan export:permissions
// Luego descarga el archivo desde storage/app/permissions_export_YYYYMMDD_HHMM.xlsx
