#!/bin/bash

# Detectar directorio del proyecto dinámicamente
PROJECT_ROOT="$(pwd)"


# Script dinámico para generar reportes de autograding para GitHub Classroom
# ⚡ LEE AUTOMÁTICAMENTE LA CONFIGURACIÓN DESDE autograding.json
# Uso: ./scripts/generate-grading-report.sh
# 
# CARACTERÍSTICAS DINÁMICAS:
# - Calcula puntos totales automáticamente
# - Ejecuta todos los tests definidos en autograding.json
# - Se adapta a cambios en nombres, comandos y puntos
# - Genera reportes detallados basados en la configuración JSON

set -euo pipefail  # Modo estricto

echo "📊 Generando Reporte de Autograding Dinámico..."
echo "================================================"

# Configuración
AUTOGRADING_FILE=".github/classroom/autograding.json"
REPORT_FILE="reports/autograding-report.md"

# Función para formatear números flotantes a 2 decimales
format_float() {
    local number="$1"
    # Usar printf para formatear a 2 decimales con redondeo
    printf "%.2f" "$number"
}

# Verificar dependencias
check_dependencies() {
    local missing_deps=()
    
    command -v jq >/dev/null 2>&1 || missing_deps+=("jq")
    command -v bc >/dev/null 2>&1 || missing_deps+=("bc")
    
    if [ ${#missing_deps[@]} -ne 0 ]; then
        echo "⚠️ Dependencias faltantes: ${missing_deps[*]}"
        echo "🔧 Intentando instalar automáticamente..."
        
        # Intentar instalar automáticamente
        if command -v apt-get >/dev/null 2>&1; then
            if apt-get update >/dev/null 2>&1 && apt-get install -y "${missing_deps[@]}" >/dev/null 2>&1; then
                echo "✅ Dependencias instaladas exitosamente"
            else
                echo "❌ Error: No se pudieron instalar las dependencias automáticamente"
                echo "💡 Ejecuta manualmente: sudo apt update && sudo apt install -y ${missing_deps[*]}"
                exit 1
            fi
        elif command -v apk >/dev/null 2>&1; then
            # Alpine Linux
            if apk update >/dev/null 2>&1 && apk add "${missing_deps[@]}" >/dev/null 2>&1; then
                echo "✅ Dependencias instaladas exitosamente"
            else
                echo "❌ Error: No se pudieron instalar las dependencias automáticamente"
                echo "💡 Ejecuta manualmente: sudo apk add ${missing_deps[*]}"
                exit 1
            fi
        elif command -v yum >/dev/null 2>&1; then
            # CentOS/RHEL
            if yum install -y "${missing_deps[@]}" >/dev/null 2>&1; then
                echo "✅ Dependencias instaladas exitosamente"
            else
                echo "❌ Error: No se pudieron instalar las dependencias automáticamente"
                echo "💡 Ejecuta manualmente: sudo yum install -y ${missing_deps[*]}"
                exit 1
            fi
        else
            echo "❌ Error: Sistema de paquetes no reconocido"
            echo "💡 Instala manualmente: jq y bc"
            exit 1
        fi
        
        # Verificar que se instalaron correctamente
        for dep in "${missing_deps[@]}"; do
            if ! command -v "$dep" >/dev/null 2>&1; then
                echo "❌ Error: $dep no se instaló correctamente"
                exit 1
            fi
        done
    fi
}

# Verificar que existe el archivo de configuración
check_config() {
    if [ ! -f "$AUTOGRADING_FILE" ]; then
        echo "❌ Error: No se encontró $AUTOGRADING_FILE"
        exit 1
    fi
    
    if ! jq empty "$AUTOGRADING_FILE" 2>/dev/null; then
        echo "❌ Error: $AUTOGRADING_FILE no es un JSON válido"
        exit 1
    fi
}

# Crear directorio de reportes
mkdir -p reports

# Verificaciones iniciales
check_dependencies
check_config

# Leer configuración dinámica desde JSON
echo "🔍 Leyendo configuración desde $AUTOGRADING_FILE..."

# Calcular puntos totales dinámicamente
TOTAL_POINTS=$(jq '[.tests[].points] | add' "$AUTOGRADING_FILE" | xargs printf "%.2f")
EARNED_POINTS=0
TEST_COUNT=$(jq '.tests | length' "$AUTOGRADING_FILE")

echo "📋 Configuración detectada:"
echo "   • Total de tests: $TEST_COUNT"
echo "   • Puntos máximos: $TOTAL_POINTS"
echo ""

# Función mejorada para ejecutar tests dinámicamente
run_dynamic_test() {
    local test_index="$1"
    local test_data="$2"
    
    # Extraer datos del test usando jq
    local test_name=$(echo "$test_data" | jq -r '.name')
    local test_run=$(echo "$test_data" | jq -r '.run')
    local test_points=$(echo "$test_data" | jq -r '.points')
    local test_setup=$(echo "$test_data" | jq -r '.setup // ""')
    local test_timeout=$(echo "$test_data" | jq -r '.timeout // 10')
    
    echo "🧪 [$((test_index + 1))/$TEST_COUNT] Ejecutando: $test_name"
    echo "   • Comando: $test_run"
    echo "   • Puntos: $test_points"
    
    # Ejecutar setup si existe
    if [ -n "$test_setup" ] && [ "$test_setup" != "null" ] && [ "$test_setup" != "" ]; then
        echo "   • Ejecutando setup: $test_setup"
        if ! timeout "${test_timeout}s" bash -c "$test_setup" >/dev/null 2>&1; then
            echo "   ⚠️ Warning: Setup falló, continuando con el test"
        fi
    fi
    
    # Ejecutar el test con timeout
    if timeout "${test_timeout}s" bash -c "$test_run" >/dev/null 2>&1; then
        echo "   ✅ PASSED: $test_name ($test_points puntos)"
        echo "- ✅ **$test_name** ($test_points/$test_points puntos)" >> "$REPORT_FILE"
        EARNED_POINTS=$(echo "scale=4; $EARNED_POINTS + $test_points" | bc | xargs printf "%.2f")
    else
        echo "   ❌ FAILED: $test_name (0/$test_points puntos)"
        echo "- ❌ **$test_name** (0/$test_points puntos)" >> "$REPORT_FILE"
    fi
    echo ""
}

# Inicializar reporte dinámico
cat > "$REPORT_FILE" << EOF
# 📊 Reporte de Autograding Dinámico

**Fecha:** $(date '+%Y-%m-%d %H:%M:%S')  
**Tests ejecutados:** $TEST_COUNT  
**Puntuación máxima:** $TOTAL_POINTS puntos  

## Resultados por Test

EOF

echo "🏁 EJECUTANDO TODOS LOS TESTS DINÁMICAMENTE"
echo "=============================================="

# Iterar dinámicamente sobre todos los tests en el JSON
test_index=0
while IFS= read -r test_data; do
    run_dynamic_test "$test_index" "$test_data"
    test_index=$((test_index + 1))
done < <(jq -c '.tests[]' "$AUTOGRADING_FILE")

# Calcular estadísticas
PERCENTAGE=$(echo "scale=4; $EARNED_POINTS * 100 / $TOTAL_POINTS" | bc | xargs printf "%.2f")
PERCENTAGE_INT=$(echo "scale=0; $EARNED_POINTS * 100 / $TOTAL_POINTS" | bc)

# Generar resumen dinámico
cat >> "$REPORT_FILE" << EOF

## Resumen Final

**Puntuación obtenida:** $EARNED_POINTS/$TOTAL_POINTS puntos ($PERCENTAGE%)

### Análisis de Rendimiento

EOF

# Análisis dinámico por categorías (detecta automáticamente patrones)
echo "### Distribución de Puntos por Categoría" >> "$REPORT_FILE"
echo "" >> "$REPORT_FILE"

# Agrupar tests por categorías detectadas automáticamente
declare -A categories
declare -A category_earned
declare -A category_total

test_index=0
while IFS= read -r test_data; do
    test_name=$(echo "$test_data" | jq -r '.name')
    test_points=$(echo "$test_data" | jq -r '.points')
    test_run=$(echo "$test_data" | jq -r '.run')
    
    # Detectar categoría basada en el nombre y comando
    category="Otros"
    if [[ "$test_name" =~ (Suma|Resta|Multiplicación|División) ]]; then
        category="Tests Funcionales"
    elif [[ "$test_name" =~ (PSR|Estilo|Style) ]] || [[ "$test_run" =~ phpcs ]]; then
        category="Calidad - Estilo de Código"
    elif [[ "$test_name" =~ (PHPStan|Análisis|Static) ]] || [[ "$test_run" =~ phpstan ]]; then
        category="Calidad - Análisis Estático"
    elif [[ "$test_name" =~ (Data Provider|Provider) ]]; then
        category="Tests Avanzados"
    elif [[ "$test_name" =~ (Final|Todos|All) ]]; then
        category="Verificación Final"
    fi
    
    # Verificar si el test pasó
    test_passed=false
    timeout "10s" bash -c "$test_run" >/dev/null 2>&1 && test_passed=true
    
    # Acumular estadísticas por categoría
    if [ -z "${categories[$category]:-}" ]; then
        categories[$category]=1
        category_total[$category]=$test_points
        if $test_passed; then
            category_earned[$category]=$test_points
        else
            category_earned[$category]=0
        fi
    else
        categories[$category]=$((categories[$category] + 1))
        category_total[$category]=$(echo "scale=4; ${category_total[$category]} + $test_points" | bc | xargs printf "%.2f")
        if $test_passed; then
            category_earned[$category]=$(echo "scale=4; ${category_earned[$category]} + $test_points" | bc | xargs printf "%.2f")
        fi
    fi
    
    test_index=$((test_index + 1))
done < <(jq -c '.tests[]' "$AUTOGRADING_FILE")

# Mostrar estadísticas por categoría
for category in "${!categories[@]}"; do
    earned=${category_earned[$category]}
    total=${category_total[$category]}
    count=${categories[$category]}
    percentage=$(echo "scale=4; $earned * 100 / $total" | bc 2>/dev/null | xargs printf "%.2f" 2>/dev/null || echo "0.00")
    
    echo "#### $category" >> "$REPORT_FILE"
    echo "- **Tests:** $count" >> "$REPORT_FILE"
    echo "- **Puntos:** $earned/$total ($percentage%)" >> "$REPORT_FILE"
    echo "" >> "$REPORT_FILE"
done

# Evaluación cualitativa dinámica
echo "### Evaluación General" >> "$REPORT_FILE"
echo "" >> "$REPORT_FILE"

if [ "$PERCENTAGE_INT" -ge 95 ]; then
    echo "🎉 **EXCEPCIONAL** (95-100%) - Implementación perfecta o casi perfecta" >> "$REPORT_FILE"
elif [ "$PERCENTAGE_INT" -ge 90 ]; then
    echo "� **EXCELENTE** (90-94%) - Trabajo de muy alta calidad" >> "$REPORT_FILE"
elif [ "$PERCENTAGE_INT" -ge 85 ]; then
    echo "✨ **MUY BUENO** (85-89%) - Buen trabajo con detalles menores" >> "$REPORT_FILE"
elif [ "$PERCENTAGE_INT" -ge 80 ]; then
    echo "✅ **BUENO** (80-84%) - Cumple bien con los requisitos" >> "$REPORT_FILE"
elif [ "$PERCENTAGE_INT" -ge 70 ]; then
    echo "👍 **SATISFACTORIO** (70-79%) - Cumple los requisitos básicos" >> "$REPORT_FILE"
elif [ "$PERCENTAGE_INT" -ge 60 ]; then
    echo "⚠️ **SUFICIENTE** (60-69%) - Necesita algunas mejoras" >> "$REPORT_FILE"
elif [ "$PERCENTAGE_INT" -ge 50 ]; then
    echo "🔄 **INSUFICIENTE** (50-59%) - Requiere trabajo adicional" >> "$REPORT_FILE"
else
    echo "❌ **DEFICIENTE** (<50%) - Necesita revisión completa" >> "$REPORT_FILE"
fi

# Agregar detalles de ejecución dinámicos
echo "" >> "$REPORT_FILE"
echo "## Detalles de Ejecución" >> "$REPORT_FILE"
echo "" >> "$REPORT_FILE"

# Generar output detallado para tests que fallaron
failed_tests=()
test_index=0
while IFS= read -r test_data; do
    test_name=$(echo "$test_data" | jq -r '.name')
    test_run=$(echo "$test_data" | jq -r '.run')
    
    if ! timeout "10s" bash -c "$test_run" >/dev/null 2>&1; then
        failed_tests+=("$test_name|$test_run")
    fi
    
    test_index=$((test_index + 1))
done < <(jq -c '.tests[]' "$AUTOGRADING_FILE")

if [ ${#failed_tests[@]} -gt 0 ]; then
    echo "### Tests Fallidos - Detalles" >> "$REPORT_FILE"
    echo "" >> "$REPORT_FILE"
    
    for failed_test in "${failed_tests[@]}"; do
        IFS='|' read -ra test_info <<< "$failed_test"
        test_name="${test_info[0]}"
        test_command="${test_info[1]}"
        
        echo "#### $test_name" >> "$REPORT_FILE"
        echo '```bash' >> "$REPORT_FILE"
        echo "# Comando ejecutado:" >> "$REPORT_FILE"
        echo "$test_command" >> "$REPORT_FILE"
        echo "" >> "$REPORT_FILE"
        echo "# Output del error:" >> "$REPORT_FILE"
        timeout "10s" bash -c "$test_command" >> "$REPORT_FILE" 2>&1 || echo "Error en la ejecución" >> "$REPORT_FILE"
        echo '```' >> "$REPORT_FILE"
        echo "" >> "$REPORT_FILE"
    done
else
    echo "### ✅ Todos los Tests Pasaron" >> "$REPORT_FILE"
    echo "" >> "$REPORT_FILE"
    echo "¡Felicitaciones! Todos los tests se ejecutaron exitosamente." >> "$REPORT_FILE"
fi

# Agregar información de configuración al final
echo "" >> "$REPORT_FILE"
echo "---" >> "$REPORT_FILE"
echo "" >> "$REPORT_FILE"
echo "### Información Técnica" >> "$REPORT_FILE"
echo "" >> "$REPORT_FILE"
echo "- **Archivo de configuración:** \`$AUTOGRADING_FILE\`" >> "$REPORT_FILE"
echo "- **Total de tests configurados:** $TEST_COUNT" >> "$REPORT_FILE"
echo "- **Generado:** $(date)" >> "$REPORT_FILE"
echo "- **Script:** \`$0\` (dinámico)" >> "$REPORT_FILE"

echo "=============================================="
echo "🎯 RESUMEN FINAL DINÁMICO"
echo "=============================================="
echo "Tests ejecutados: $test_index/$TEST_COUNT"
echo "Puntuación: $EARNED_POINTS/$TOTAL_POINTS puntos ($PERCENTAGE%)"
echo "Reporte guardado en: $REPORT_FILE"
echo ""

# Mostrar estadísticas por categoría en consola
echo "📊 ESTADÍSTICAS POR CATEGORÍA:"
for category in "${!categories[@]}"; do
    earned=${category_earned[$category]}
    total=${category_total[$category]}
    count=${categories[$category]}
    percentage=$(echo "scale=4; $earned * 100 / $total" | bc 2>/dev/null | xargs printf "%.2f" 2>/dev/null || echo "0.00")
    echo "   • $category: $earned/$total pts ($percentage%) [$count tests]"
done

echo ""

# Código de salida dinámico
if [ "$PERCENTAGE_INT" -ge 60 ]; then
    echo "✅ Puntuación suficiente alcanzada ($PERCENTAGE% ≥ 60%)"
    exit 0
else
    echo "❌ No se alcanzó la puntuación mínima ($PERCENTAGE% < 60%)"
    exit 1
fi
