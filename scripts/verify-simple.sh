#!/bin/bash

# Script de verificación simplificado para debugging
set -euo pipefail

echo "🔍 Verificación del Entorno"

total=0
passed=0

test_simple() {
    local name="$1"
    local cmd="$2"
    
    total=$((total + 1))
    echo -n "Test: $name... "
    
    if $cmd >/dev/null 2>&1; then
        echo "✅"
        passed=$((passed + 1))
    else
        echo "❌"
    fi
}

echo "Probando dependencias básicas:"
test_simple "jq disponible" "command -v jq"
test_simple "bc disponible" "command -v bc"
test_simple "php disponible" "command -v php"

echo ""
echo "Resultado: $passed/$total tests pasaron"
