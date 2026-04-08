#!/bin/bash

# Script para normalizar finales de línea (CRLF → LF)
# Soluciona el error: /usr/bin/env: 'php\r': No such file or directory

echo "🔧 Normalizando finales de línea (CRLF → LF)..."
echo ""

# Verificar si dos2unix está disponible
if ! command -v dos2unix &> /dev/null; then
    echo "⚠️  'dos2unix' no está instalado. Instalando..."
    
    # Detectar el sistema operativo
    if [[ "$OSTYPE" == "linux-gnu"* ]]; then
        sudo apt-get update -qq && sudo apt-get install -y dos2unix
    elif [[ "$OSTYPE" == "darwin"* ]]; then
        brew install dos2unix
    else
        echo "❌ Sistema operativo no soportado para instalación automática"
        echo "   Instala 'dos2unix' manualmente y vuelve a ejecutar este script"
        exit 1
    fi
    
    echo "✅ dos2unix instalado"
    echo ""
fi

# Contar archivos antes de la conversión
echo "🔍 Buscando archivos con CRLF..."
TOTAL_FILES=0

# Normalizar archivos PHP
if find . -type f -name "*.php" -not -path "*/vendor/*" -not -path "*/.git/*" 2>/dev/null | grep -q .; then
    PHP_COUNT=$(find . -type f -name "*.php" -not -path "*/vendor/*" -not -path "*/.git/*" | wc -l)
    echo "   📄 Archivos PHP: $PHP_COUNT"
    find . -type f -name "*.php" -not -path "*/vendor/*" -not -path "*/.git/*" -exec dos2unix {} \; 2>/dev/null
    TOTAL_FILES=$((TOTAL_FILES + PHP_COUNT))
fi

# Normalizar scripts shell
if find . -type f -name "*.sh" -not -path "*/vendor/*" -not -path "*/.git/*" 2>/dev/null | grep -q .; then
    SH_COUNT=$(find . -type f -name "*.sh" -not -path "*/vendor/*" -not -path "*/.git/*" | wc -l)
    echo "   🔧 Scripts shell: $SH_COUNT"
    find . -type f -name "*.sh" -not -path "*/vendor/*" -not -path "*/.git/*" -exec dos2unix {} \; 2>/dev/null
    TOTAL_FILES=$((TOTAL_FILES + SH_COUNT))
fi

# Normalizar archivos PlantUML
if find . -type f -name "*.puml" -not -path "*/vendor/*" -not -path "*/.git/*" 2>/dev/null | grep -q .; then
    PUML_COUNT=$(find . -type f -name "*.puml" -not -path "*/vendor/*" -not -path "*/.git/*" | wc -l)
    echo "   🎨 Archivos PlantUML: $PUML_COUNT"
    find . -type f -name "*.puml" -not -path "*/vendor/*" -not -path "*/.git/*" -exec dos2unix {} \; 2>/dev/null
    TOTAL_FILES=$((TOTAL_FILES + PUML_COUNT))
fi

# Normalizar archivos de documentación
if find . -type f -name "*.md" -not -path "*/vendor/*" -not -path "*/.git/*" 2>/dev/null | grep -q .; then
    MD_COUNT=$(find . -type f -name "*.md" -not -path "*/vendor/*" -not -path "*/.git/*" | wc -l)
    echo "   📚 Archivos Markdown: $MD_COUNT"
    find . -type f -name "*.md" -not -path "*/vendor/*" -not -path "*/.git/*" -exec dos2unix {} \; 2>/dev/null
    TOTAL_FILES=$((TOTAL_FILES + MD_COUNT))
fi

# Normalizar archivos de configuración
if find . -type f \( -name "*.json" -o -name "*.yml" -o -name "*.yaml" \) -not -path "*/vendor/*" -not -path "*/.git/*" 2>/dev/null | grep -q .; then
    CONFIG_COUNT=$(find . -type f \( -name "*.json" -o -name "*.yml" -o -name "*.yaml" \) -not -path "*/vendor/*" -not -path "*/.git/*" | wc -l)
    echo "   ⚙️  Archivos de configuración: $CONFIG_COUNT"
    find . -type f \( -name "*.json" -o -name "*.yml" -o -name "*.yaml" \) -not -path "*/vendor/*" -not -path "*/.git/*" -exec dos2unix {} \; 2>/dev/null
    TOTAL_FILES=$((TOTAL_FILES + CONFIG_COUNT))
fi

# Normalizar archivos sin extensión que pueden ser scripts
for file in .gitignore .gitattributes .editorconfig Dockerfile; do
    if [ -f "$file" ]; then
        dos2unix "$file" 2>/dev/null
        TOTAL_FILES=$((TOTAL_FILES + 1))
    fi
done

echo ""
echo "✅ $TOTAL_FILES archivos normalizados"
echo ""

# Hacer ejecutables todos los scripts .sh
echo "🔐 Configurando permisos de ejecución en scripts..."
SCRIPT_COUNT=$(find . -type f -name "*.sh" -not -path "*/vendor/*" -not -path "*/.git/*" | wc -l)
find . -type f -name "*.sh" -not -path "*/vendor/*" -not -path "*/.git/*" -exec chmod +x {} \;
echo "✅ $SCRIPT_COUNT scripts marcados como ejecutables"
echo ""

# Normalizar vendor/bin si existe
if [ -d "vendor/bin" ]; then
    echo "🔧 Normalizando archivos en vendor/bin..."
    find vendor/bin -type f -exec dos2unix {} \; 2>/dev/null
    find vendor/bin -type f -exec chmod +x {} \;
    echo "✅ vendor/bin normalizado"
    echo ""
fi

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ ¡Normalización completada!"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📋 Próximos pasos:"
echo "   1. Hacer commit de los cambios:"
echo "      git add -u"
echo "      git commit -m 'Normalizar finales de línea para autograding'"
echo ""
echo "   2. Hacer push al repositorio:"
echo "      git push"
echo ""
echo "   3. Verificar que el autograding pase ✅"
echo ""
echo "💡 Prevención futura:"
echo "   • El archivo .gitattributes ahora previene este problema"
echo "   • Los archivos se normalizarán automáticamente al hacer 'git add'"
echo ""
