#!/bin/bash

# Script para validar rutas dinámicas
# Uso: ./scripts/validate-dynamic-paths.sh

echo "🔍 Validando que todas las rutas sean dinámicas..."

# Función para verificar archivos específicos
check_file() {
    local file="$1"
    local file_type="$2"
    
    case "$file_type" in
        "php")
            if grep -q "/workspaces/" "$file"; then
                echo "  ❌ $file contiene rutas absolutas"
                grep -n "/workspaces/" "$file" | head -3
                return 1
            else
                echo "  ✅ $file usa rutas dinámicas"
                return 0
            fi
            ;;
        "sh")
            if grep -q "/workspaces/" "$file" && ! grep -q "PROJECT_ROOT" "$file"; then
                echo "  ❌ $file contiene rutas absolutas sin PROJECT_ROOT"
                return 1
            else
                echo "  ✅ $file usa rutas dinámicas o contiene PROJECT_ROOT"
                return 0
            fi
            ;;
        "md")
            if grep -q "/workspaces/prueba" "$file"; then
                echo "  ❌ $file contiene rutas específicas del entorno"
                return 1
            else
                echo "  ✅ $file usa nombres genéricos"
                return 0
            fi
            ;;
    esac
}

echo ""
echo "📋 Validando archivos PHP críticos..."
php_files=(
    "public/run-autograding.php"
    "public/run-tests.php"
    "public/index.php"
)

all_good=true

for file in "${php_files[@]}"; do
    if [ -f "$file" ]; then
        if ! check_file "$file" "php"; then
            all_good=false
        fi
    fi
done

echo ""
echo "📋 Validando scripts bash..."
bash_files=(
    ".devcontainer/setup.sh"
    "scripts/generate-grading-report.sh"
)

for file in "${bash_files[@]}"; do
    if [ -f "$file" ]; then
        if ! check_file "$file" "sh"; then
            all_good=false
        fi
    fi
done

echo ""
echo "📋 Validando documentación..."
doc_files=(
    "README.md"
)

for file in "${doc_files[@]}"; do
    if [ -f "$file" ]; then
        if ! check_file "$file" "md"; then
            all_good=false
        fi
    fi
done

echo ""
if [ "$all_good" = true ]; then
    echo "🎉 ¡Todas las rutas son dinámicas y portables!"
    echo "✅ El repositorio funcionará en cualquier entorno"
else
    echo "⚠️  Algunos archivos necesitan corrección"
    echo ""
    echo "💡 Patrones recomendados:"
    echo "  PHP: \$projectRoot = dirname(__DIR__);"
    echo "  PHP: __DIR__ . '/ruta/relativa'"
    echo "  Bash: PROJECT_ROOT=\"\$(pwd)\""
    echo "  Docs: tu-repositorio/ o \${nombre-del-repo}/"
fi

exit $([ "$all_good" = true ] && echo 0 || echo 1)
