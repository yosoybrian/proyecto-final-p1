#!/bin/bash

# Detectar directorio del proyecto dinámicamente
PROJECT_ROOT="$(pwd)"


# Script para automatizar el testing continuo
# Uso: ./watch-tests.sh

echo "🔄 Iniciando testing continuo..."
echo "Presiona Ctrl+C para detener"
echo ""

# Función para ejecutar tests
run_tests() {
    echo "$(date): Ejecutando tests..."
    composer test-watch
    echo "✅ Tests completados - $(date)"
    echo "================================"
    echo ""
}

# Función para limpiar en salida
cleanup() {
    echo ""
    echo "🛑 Deteniendo testing continuo..."
    exit 0
}

# Capturar Ctrl+C
trap cleanup INT

# Ejecutar tests iniciales
run_tests

# Monitorear cambios y re-ejecutar tests
while true; do
    # Esperar cambios en archivos PHP
    inotifywait -r -e modify,create,delete --include='.*\.php$' exercises/ tests/ solutions/ 2>/dev/null
    
    if [ $? -eq 0 ]; then
        sleep 1  # Pequeña pausa para evitar múltiples ejecuciones
        run_tests
    fi
done
