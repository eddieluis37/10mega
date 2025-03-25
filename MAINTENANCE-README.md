# Modo de Mantenimiento

Este directorio contiene archivos para gestionar el modo de mantenimiento de la aplicación.

## Archivos incluidos

- `maintenance.bat` - Script para Windows para activar/desactivar el modo de mantenimiento
- `maintenance.sh` - Script para Linux/Mac para activar/desactivar el modo de mantenimiento
- `resources/views/maintenance.blade.php` - Página HTML que se muestra durante el mantenimiento
- `storage/framework/maintenance.php` - Archivo que activa el modo de mantenimiento
- `storage/framework/maintenance.php.template` - Plantilla para el archivo de mantenimiento

## Cómo usar

### Para activar el modo de mantenimiento:

En Windows:
```
maintenance.bat down
```

En Linux/Mac:
```
./maintenance.sh down
```

### Para desactivar el modo de mantenimiento:

En Windows:
```
maintenance.bat up
```

En Linux/Mac:
```
./maintenance.sh up
```

## Personalización

Puedes personalizar la página de mantenimiento editando el archivo `resources/views/maintenance.blade.php`.

## Permitir IPs específicas

Si necesitas acceder al sitio durante el mantenimiento, puedes editar el archivo `storage/framework/maintenance.php.template` 
y agregar tu dirección IP en el arreglo `$allowedIPs`. Luego debes activar nuevamente el modo de mantenimiento para que los cambios surtan efecto.

## Notas

- El modo de mantenimiento devuelve un código de estado HTTP 503 (Servicio no disponible)
- Incluye un encabezado "Retry-After" configurado a 3600 segundos (1 hora)