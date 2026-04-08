#!/bin/bash

# Script para validar y corregir rutas absolutas problemáticas
# Uso: ./scripts/fix-absolute-paths.sh

echo "🔍 Buscando rutas absolutas problemáticas..."

# Definir patrones problemáticos
PATTERNS=(
    "$PROJECT_ROOT"
    "/workspace/prueba"
    "/home/vscode/workspace"
)

# Archivos a revisar (excluyendo algunos directorios)
FILES_TO_CHECK="$(find . -type f \( -name "*.php" -o -name "*.sh" -o -name "*.md" -o -name "*.json" \) \
    -not -path "./.git/*" \
    -not -path "./vendor/*" \
    -not -path "./node_modules/*" \
    2>/dev/null)"

found_issues=false

echo ""
echo "📋 Revisando archivos..."

for pattern in "${PATTERNS[@]}"; do
    echo ""
    echo "🔍 Buscando: $pattern"
    
    while IFS= read -r file; do
        if [ -f "$file" ] && grep -l "$pattern" "$file" >/dev/null 2>&1; then
            found_issues=true
            echo "  ⚠️  Encontrado en: $file"
            
            # Mostrar las líneas que contienen el patrón
            grep -n "$pattern" "$file" | head -3 | while IFS= read -r line; do
                echo "      $line"
            done
        fi
    done <<< "$FILES_TO_CHECK"
done

echo ""

if [ "$found_issues" = true ]; then
    echo "❌ Se encontraron rutas absolutas problemáticas."
    echo ""
    echo "💡 Recomendaciones de corrección:"
    echo ""
    echo "Para archivos PHP:"
    echo "  - Usar: \$projectRoot = dirname(__DIR__);"
    echo "  - Usar: __DIR__ . '/ruta/relativa'"
    echo "  - Usar: basename(dirname(__DIR__)) para nombre del proyecto"
    echo ""
    echo "Para scripts bash:"
    echo "  - Usar: PROJECT_ROOT=\"\$(pwd)\""
    echo "  - Usar: \$PROJECT_ROOT/ruta/relativa"
    echo ""
    echo "Para documentación:"
    echo "  - Usar: tu-repositorio/ o nombre-proyecto/"
    echo ""
    echo "🔧 ¿Quieres aplicar correcciones automáticas? (y/N)"
    read -p "Respuesta: " apply_fixes
    
    if [ "$apply_fixes" = "y" ] || [ "$apply_fixes" = "Y" ]; then
        echo ""
        echo "🔧 Aplicando correcciones automáticas..."
        
        # Aplicar correcciones básicas
        for file in $FILES_TO_CHECK; do
            if [ -f "$file" ] && grep -l "$PROJECT_ROOT" "$file" >/dev/null 2>&1; then
                case "$file" in
                    *.md)
                        sed -i 's|$PROJECT_ROOT/|tu-repositorio/|g' "$file"
                        sed -i 's|$PROJECT_ROOT|tu-repositorio|g' "$file"
                        echo "  ✅ Corregido: $file (documentación)"
                        ;;
                    *.sh)
                        if ! grep -q "PROJECT_ROOT=" "$file"; then
                            # Solo agregar si no existe la variable
                            sed -i '1a\\n# Detectar directorio del proyecto dinámicamente\nPROJECT_ROOT="$(pwd)"\n' "$file"
                        fi
                        sed -i 's|$PROJECT_ROOT|\$PROJECT_ROOT|g' "$file"
                        echo "  ✅ Corregido: $file (script)"
                        ;;
                    *.php)
                        echo "  ⚠️  $file necesita corrección manual (PHP)"
                        ;;
                esac
            fi
        done
        
        echo ""
        echo "✅ Correcciones automáticas aplicadas."
        echo "📝 Revisa los archivos PHP manualmente usando las recomendaciones arriba."
    fi
    
    exit 1
else
    echo "✅ No se encontraron rutas absolutas problemáticas."
    echo "🎉 Todos los archivos usan rutas relativas o dinámicas correctamente."
    exit 0
fi
