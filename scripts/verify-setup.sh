#!/bin/bash
# Script de verificación completa del repositorio
# Verifica que toda la configuración anti-CRLF esté correcta

set -euo pipefail

echo "🔍 VERIFICACIÓN COMPLETA DEL REPOSITORIO"
echo "=========================================="
echo ""

ERRORS=0
WARNINGS=0

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

check_pass() {
    echo -e "${GREEN}✅ $1${NC}"
}

check_fail() {
    echo -e "${RED}❌ $1${NC}"
    ERRORS=$((ERRORS + 1))
}

check_warn() {
    echo -e "${YELLOW}⚠️  $1${NC}"
    WARNINGS=$((WARNINGS + 1))
}

# 1. Verificar .gitattributes
echo "📄 Verificando .gitattributes..."
if [ -f ".gitattributes" ]; then
    if grep -q "eol=lf" .gitattributes; then
        check_pass ".gitattributes configurado correctamente"
    else
        check_fail ".gitattributes existe pero no tiene eol=lf"
    fi
else
    check_fail ".gitattributes no encontrado"
fi

# 2. Verificar .editorconfig
echo ""
echo "📝 Verificando .editorconfig..."
if [ -f ".editorconfig" ]; then
    if grep -q "end_of_line = lf" .editorconfig; then
        check_pass ".editorconfig configurado correctamente"
    else
        check_warn ".editorconfig existe pero no especifica end_of_line"
    fi
else
    check_warn ".editorconfig no encontrado"
fi

# 3. Verificar Git hooks
echo ""
echo "🪝 Verificando Git hooks..."
if [ -f "scripts/git-hooks/pre-commit" ]; then
    check_pass "Hook pre-commit template existe"
else
    check_fail "Hook pre-commit template no encontrado"
fi

if [ -f ".git/hooks/pre-commit" ]; then
    check_pass "Hook pre-commit instalado"
else
    check_warn "Hook pre-commit no instalado en .git/hooks/"
fi

if [ -f "scripts/git-hooks/post-checkout" ]; then
    check_pass "Hook post-checkout template existe"
else
    check_warn "Hook post-checkout template no encontrado"
fi

# 4. Verificar scripts
echo ""
echo "📜 Verificando scripts..."
if [ -f "scripts/normalize-line-endings.sh" ]; then
    if [ -x "scripts/normalize-line-endings.sh" ]; then
        check_pass "Script de normalización ejecutable"
    else
        check_warn "Script de normalización existe pero no es ejecutable"
    fi
else
    check_fail "Script de normalización no encontrado"
fi

if [ -f "scripts/setup-student-environment.sh" ]; then
    if [ -x "scripts/setup-student-environment.sh" ]; then
        check_pass "Script de setup ejecutable"
    else
        check_warn "Script de setup existe pero no es ejecutable"
    fi
else
    check_fail "Script de setup no encontrado"
fi

# 5. Verificar finales de línea en archivos críticos
echo ""
echo "🔎 Verificando finales de línea..."

check_crlf_in_files() {
    local pattern="$1"
    local description="$2"
    
    local count=$(find . -type f -name "$pattern" \
        -not -path "*/vendor/*" \
        -not -path "*/.git/*" \
        -not -path "*/node_modules/*" \
        -exec file {} \; 2>/dev/null | grep -c "CRLF" 2>/dev/null || echo "0")
    
    # Asegurar que count es un número
    count=$(echo "$count" | tr -d '[:space:]' | grep -o '[0-9]*' | head -1)
    count=${count:-0}
    
    if [ "$count" -eq 0 ]; then
        check_pass "No hay archivos $description con CRLF"
    else
        check_fail "$count archivo(s) $description con CRLF encontrados"
    fi
}

check_crlf_in_files "*.php" "PHP"
check_crlf_in_files "*.sh" "shell"
check_crlf_in_files "*.json" "JSON"
check_crlf_in_files "*.yml" "YAML"

# 6. Verificar vendor/bin (si existe)
echo ""
echo "📦 Verificando vendor/bin..."
if [ -d "vendor/bin" ]; then
    VENDOR_CRLF=$(find vendor/bin -type f -exec file {} \; 2>/dev/null | grep -c "CRLF" 2>/dev/null || echo "0")
    # Limpiar salida
    VENDOR_CRLF=$(echo "$VENDOR_CRLF" | tr -d '[:space:]' | grep -o '[0-9]*' | head -1)
    VENDOR_CRLF=${VENDOR_CRLF:-0}
    
    if [ "$VENDOR_CRLF" -eq 0 ]; then
        check_pass "vendor/bin no tiene archivos con CRLF"
    else
        check_fail "$VENDOR_CRLF archivo(s) en vendor/bin con CRLF"
        echo "  💡 Ejecuta: bash scripts/normalize-line-endings.sh"
    fi
else
    check_warn "vendor/bin no existe (ejecuta 'composer install')"
fi

# 7. Verificar composer.json
echo ""
echo "🎼 Verificando composer.json..."
if [ -f "composer.json" ]; then
    if grep -q "php -d xdebug.mode=off" composer.json; then
        check_pass "composer.json usa comandos PHP explícitos"
    else
        check_warn "composer.json no usa 'php -d xdebug.mode=off' explícitamente"
    fi
else
    check_fail "composer.json no encontrado"
fi

# 8. Verificar autograding.json
echo ""
echo "📊 Verificando autograding.json..."
if [ -f ".github/classroom/autograding.json" ]; then
    if grep -q "php -d xdebug.mode=off" .github/classroom/autograding.json; then
        check_pass "autograding.json usa comandos PHP explícitos"
    else
        check_warn "autograding.json no usa 'php -d xdebug.mode=off'"
    fi
    
    # Verificar sintaxis JSON
    if command -v jq >/dev/null 2>&1; then
        if jq empty .github/classroom/autograding.json 2>/dev/null; then
            check_pass "autograding.json tiene sintaxis JSON válida"
        else
            check_fail "autograding.json tiene sintaxis JSON inválida"
        fi
    fi
else
    check_warn "autograding.json no encontrado"
fi

# 9. Verificar documentación
echo ""
echo "📚 Verificando documentación..."
for doc in "GUIA-ESTUDIANTES.md" "GUIA-PROFESORES.md" "README.md"; do
    if [ -f "$doc" ]; then
        check_pass "$doc existe"
    else
        check_warn "$doc no encontrado"
    fi
done

# 10. Verificar configuración Git local
echo ""
echo "⚙️  Verificando configuración Git..."
AUTOCRLF=$(git config core.autocrlf 2>/dev/null || echo "not set")
EOL=$(git config core.eol 2>/dev/null || echo "not set")

if [ "$AUTOCRLF" = "input" ] || [ "$AUTOCRLF" = "false" ]; then
    check_pass "core.autocrlf configurado correctamente ($AUTOCRLF)"
elif [ "$AUTOCRLF" = "true" ]; then
    check_fail "core.autocrlf=true (debería ser 'input' o 'false')"
else
    check_warn "core.autocrlf no configurado (usa .gitattributes)"
fi

if [ "$EOL" = "lf" ]; then
    check_pass "core.eol configurado a lf"
elif [ "$EOL" = "not set" ]; then
    check_warn "core.eol no configurado (usa .gitattributes)"
else
    check_warn "core.eol=$EOL (recomendado: lf)"
fi

# Resumen
echo ""
echo "=========================================="
echo "📊 RESUMEN"
echo "=========================================="

if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo -e "${GREEN}🎉 ¡Perfecto! Todas las verificaciones pasaron.${NC}"
    echo ""
    echo "El repositorio está correctamente configurado para evitar"
    echo "problemas de finales de línea en GitHub Classroom."
    exit 0
elif [ $ERRORS -eq 0 ]; then
    echo -e "${YELLOW}⚠️  $WARNINGS advertencia(s) encontrada(s).${NC}"
    echo ""
    echo "El repositorio debería funcionar, pero hay algunas"
    echo "configuraciones opcionales que podrías mejorar."
    exit 0
else
    echo -e "${RED}❌ $ERRORS error(es) encontrado(s), $WARNINGS advertencia(s).${NC}"
    echo ""
    echo "Hay problemas que deben corregirse antes de usar"
    echo "el repositorio en GitHub Classroom."
    echo ""
    echo "💡 Sugerencia: Ejecuta 'bash scripts/setup-student-environment.sh'"
    exit 1
fi
