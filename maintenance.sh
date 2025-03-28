#!/bin/bash

# Script para habilitar/deshabilitar el modo de mantenimiento

# Función para mostrar el uso del script
show_usage() {
    echo "Uso: ./maintenance.sh [up|down]"
    echo "  up   - Desactiva el modo de mantenimiento"
    echo "  down - Activa el modo de mantenimiento"
    exit 1
}

# Verificar si se proporcionó un argumento
if [ $# -ne 1 ]; then
    show_usage
fi

# Rutas de los archivos
MAINTENANCE_FILE="storage/framework/maintenance.php"

# Procesar el argumento
case "$1" in
    down)
        echo "Activando el modo de mantenimiento..."
        cp -f "storage/framework/maintenance.php.template" "$MAINTENANCE_FILE" 2>/dev/null
        if [ ! -f "$MAINTENANCE_FILE" ]; then
            echo "Error: No se pudo crear el archivo de mantenimiento en $MAINTENANCE_FILE"
            exit 1
        fi
        echo "Modo de mantenimiento activado. El sitio ahora muestra la página de mantenimiento."
        ;;  # <-- Se agregó el cierre del bloque down
    up)
        echo "Desactivando el modo de mantenimiento..."
        if [ -f "$MAINTENANCE_FILE" ]; then
            mv "$MAINTENANCE_FILE" "${MAINTENANCE_FILE}.disabled"
            echo "Archivo de mantenimiento desactivado."
        else
            echo "El modo de mantenimiento ya está desactivado."
        fi
        echo "Modo de mantenimiento desactivado. El sitio ahora está en línea."
        ;;
    *)
        show_usage
        ;;
esac

exit 0
