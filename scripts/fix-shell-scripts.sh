#!/bin/bash

# Script para corregir terminaciones de línea CRLF en scripts shell
# Este script se ejecuta después de clonar el repositorio o actualizar archivos

echo "🔧 Corrigiendo terminaciones de línea en scripts shell..."

# Verificar si existe el directorio scripts
if [ ! -d "scripts" ]; then
    echo "❌ Directorio scripts no encontrado"
    exit 1
fi

# Contar archivos con CRLF antes de la corrección
CRLF_COUNT=$(find scripts -name "*.sh" -exec file {} \; | grep -c "CRLF" || echo "0")

if [ "$CRLF_COUNT" -gt 0 ]; then
    echo "📝 Encontrados $CRLF_COUNT scripts con terminaciones CRLF"
    
    # Corregir terminaciones de línea en scripts shell
    find scripts -name "*.sh" -exec sed -i 's/\r$//' {} \;
    
    echo "✅ Terminaciones de línea corregidas en scripts shell"
    
    # Verificar corrección
    REMAINING_CRLF=$(find scripts -name "*.sh" -exec file {} \; | grep -c "CRLF" || echo "0")
    if [ "$REMAINING_CRLF" -eq 0 ]; then
        echo "✅ Todos los scripts corregidos exitosamente"
    else
        echo "⚠️  Algunos scripts aún tienen terminaciones CRLF: $REMAINING_CRLF"
    fi
else
    echo "✅ No se encontraron scripts con terminaciones CRLF"
fi

# Asegurar permisos ejecutables en scripts
echo "🔐 Verificando permisos de scripts..."
find scripts -name "*.sh" -exec chmod +x {} \;
echo "✅ Permisos de ejecución aplicados"

# Verificar dependencias del sistema
echo "🔍 Verificando dependencias del sistema..."
MISSING_DEPS=()

if ! command -v bc >/dev/null 2>&1; then
    MISSING_DEPS+=("bc")
fi

if [ ${#MISSING_DEPS[@]} -gt 0 ]; then
    echo "⚠️  Dependencias faltantes: ${MISSING_DEPS[*]}"
    echo "📦 Instalar con: sudo apt install -y ${MISSING_DEPS[*]}"
else
    echo "✅ Todas las dependencias del sistema están disponibles"
fi

echo "🎉 Script completado"
