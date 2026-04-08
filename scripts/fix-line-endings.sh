#!/bin/bash

# Script para corregir terminaciones de línea CRLF en archivos vendor/bin
# Este script se ejecuta después de composer install/update

echo "🔧 Corrigiendo terminaciones de línea en archivos vendor/bin..."

# Verificar si existe el directorio vendor/bin
if [ ! -d "vendor/bin" ]; then
    echo "❌ Directorio vendor/bin no encontrado"
    exit 1
fi

# Contar archivos con CRLF antes de la corrección
CRLF_COUNT=$(find vendor/bin -type f -exec file {} \; | grep -c "CRLF" || echo "0")

if [ "$CRLF_COUNT" -gt 0 ]; then
    echo "📝 Encontrados $CRLF_COUNT archivos con terminaciones CRLF"
    
    # Corregir terminaciones de línea en archivos ejecutables
    find vendor/bin -type f -exec sed -i 's/\r$//' {} \;
    
    echo "✅ Terminaciones de línea corregidas"
    
    # Verificar corrección
    REMAINING_CRLF=$(find vendor/bin -type f -exec file {} \; | grep -c "CRLF" || echo "0")
    if [ "$REMAINING_CRLF" -eq 0 ]; then
        echo "✅ Todos los archivos corregidos exitosamente"
    else
        echo "⚠️  Algunos archivos aún tienen terminaciones CRLF: $REMAINING_CRLF"
    fi
else
    echo "✅ No se encontraron archivos con terminaciones CRLF"
fi

echo "🎉 Script completado"
