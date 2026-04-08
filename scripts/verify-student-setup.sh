#!/bin/bash

# Script de verificación final para estudiantes
# Verifica que el entorno esté completamente configurado y funcional
# Uso: ./scripts/verify-student-setup.sh

set -euo pipefail

echo "🔍 Verificación Final del Entorno de Estudiante"
echo "==============================================="

# Contadores
total_tests=0
passed_tests=0

# Función para test individual
run_test() {
    local test_name="$1"
    local test_command="$2"
    local expected_result="${3:-0}"
    
    total_tests=$((total_tests + 1))
    echo -n "🧪 $test_name... "
    
    if eval "$test_command" >/dev/null 2>&1; then
        if [ "$expected_result" -eq 0 ]; then
            echo "✅"
            passed_tests=$((passed_tests + 1))
        else
            echo "❌"
        fi
    else
        if [ "$expected_result" -eq 1 ]; then
            echo "✅"
            passed_tests=$((passed_tests + 1))
        else
            echo "❌"
        fi
    fi
}

echo "🔧 Verificando dependencias del sistema..."
run_test "jq está disponible" "command -v jq"
run_test "bc está disponible" "command -v bc"
run_test "curl está disponible" "command -v curl"
run_test "git está disponible" "command -v git"

echo ""
echo "🐘 Verificando entorno PHP..."
run_test "PHP está disponible" "command -v php"
run_test "PHP puede ejecutar código básico" "php -d xdebug.mode=off -r 'echo \"test\";'"
run_test "Composer está disponible" "command -v composer"

echo ""
echo "📦 Verificando dependencias del proyecto..."
run_test "vendor/autoload.php existe" "test -f vendor/autoload.php"
run_test "PHPUnit está disponible" "test -f vendor/bin/phpunit"
run_test "PHPUnit puede ejecutarse" "php -d xdebug.mode=off vendor/bin/phpunit --version"

echo ""
echo "🧪 Verificando que los tests pueden ejecutarse..."
run_test "Tests pueden iniciarse (sin fallar por dependencias)" "php -d xdebug.mode=off vendor/bin/phpunit --list-tests --no-coverage"

echo ""
echo "📊 Verificando sistema de autograding..."
run_test "Configuración de autograding existe" "test -f .github/classroom/autograding.json"
run_test "Script de generación de reportes existe" "test -f scripts/generate-grading-report.sh"
run_test "Script es ejecutable" "test -x scripts/generate-grading-report.sh"
run_test "jq puede procesar configuración" "jq '.tests' .github/classroom/autograding.json"

echo ""
echo "🌐 Verificando interfaz web..."
run_test "public/index.php existe" "test -f public/index.php"
run_test "public/run-tests.php existe" "test -f public/run-tests.php"
run_test "PHP puede procesar index.php" "php -d xdebug.mode=off -l public/index.php"
run_test "PHP puede procesar run-tests.php" "php -d xdebug.mode=off -l public/run-tests.php"

echo ""
echo "🏗️ Verificando estructura de archivos..."
run_test "exercises/Calculator.php existe" "test -f exercises/Calculator.php"
run_test "tests/CalculatorTest.php existe" "test -f tests/CalculatorTest.php"
run_test "Calculator.php es válido sintácticamente" "php -d xdebug.mode=off -l exercises/Calculator.php"
run_test "CalculatorTest.php es válido sintácticamente" "php -d xdebug.mode=off -l tests/CalculatorTest.php"

echo ""
echo "⚡ Verificando comandos Composer..."
run_test "composer test está configurado" "composer run-script --list | grep -q test"
run_test "composer serve está configurado" "composer run-script --list | grep -q serve"

echo ""
echo "📈 Resultados de Verificación"
echo "============================="

if [ $passed_tests -eq $total_tests ]; then
    echo "🎉 ¡PERFECTO! Todas las verificaciones pasaron ($passed_tests/$total_tests)"
    echo ""
    echo "✅ El entorno está completamente configurado y listo para usar"
    echo ""
    echo "🚀 Próximos pasos:"
    echo "  1. Implementa tu código en exercises/Calculator.php"
    echo "  2. Ejecuta 'composer test' para ver tu progreso"
    echo "  3. Usa 'composer serve' para la interfaz web"
    echo ""
    exit 0
elif [ $passed_tests -gt $((total_tests * 80 / 100)) ]; then
    echo "⚠️ CASI LISTO: $passed_tests/$total_tests verificaciones pasaron (${passed_tests}0%)"
    echo ""
    echo "🔧 El entorno está mayormente configurado, pero hay algunas advertencias"
    echo "💡 Revisa las pruebas que fallaron arriba"
    echo ""
    exit 0
else
    echo "❌ PROBLEMAS DETECTADOS: Solo $passed_tests/$total_tests verificaciones pasaron"
    echo ""
    echo "🛠️ Acciones recomendadas:"
    echo "  1. Ejecuta './scripts/setup-student-environment.sh' otra vez"
    echo "  2. Revisa las pruebas que fallaron arriba"
    echo "  3. Instala manualmente las dependencias faltantes"
    echo ""
    exit 1
fi
