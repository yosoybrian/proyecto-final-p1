#!/bin/bash
# Script para normalizar finales de línea antes de commit
# Esto previene errores en GitHub Classroom autograding

echo "🔍 Normalizando finales de línea..."

# Normalizar todos los archivos de texto a LF
find . -type f \( \
    -name "*.php" -o \
    -name "*.sh" -o \
    -name "*.bash" -o \
    -name "*.puml" -o \
    -name "*.md" -o \
    -name "*.json" -o \
    -name "*.yml" -o \
    -name "*.yaml" -o \
    -name "*.xml" -o \
    -name "*.txt" -o \
    -name "*.ini" \
\) -not -path "*/vendor/*" -not -path "*/.git/*" -exec dos2unix {} \; 2>/dev/null || {
    # Si dos2unix no está disponible, usar sed
    find . -type f \( \
        -name "*.php" -o \
        -name "*.sh" -o \
        -name "*.bash" -o \
        -name "*.puml" -o \
        -name "*.md" -o \
        -name "*.json" -o \
        -name "*.yml" -o \
        -name "*.yaml" -o \
        -name "*.xml" -o \
        -name "*.txt" -o \
        -name "*.ini" \
    \) -not -path "*/vendor/*" -not -path "*/.git/*" -exec sed -i 's/\r$//' {} \;
}

# Normalizar archivos ejecutables en vendor/bin (si existe)
if [ -d "vendor/bin" ]; then
    echo "🔧 Normalizando vendor/bin..."
    find vendor/bin -type f -exec sed -i 's/\r$//' {} \; 2>/dev/null || true
fi

echo "✅ Normalización completada"
