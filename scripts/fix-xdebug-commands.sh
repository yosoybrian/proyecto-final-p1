#!/bin/bash

# Detectar directorio del proyecto dinámicamente
PROJECT_ROOT="$(pwd)"


# Script para actualizar autograding.json con comandos que no generen errores de Xdebug

echo "🔧 Actualizando comandos en autograding.json para evitar errores de Xdebug..."

# Crear una copia de respaldo
cp .github/classroom/autograding.json .github/classroom/autograding.json.bak

# Actualizar todos los comandos de phpunit para usar php -d xdebug.mode=off
jq '.tests |= map(
    if .run | contains("vendor/bin/phpunit") then
        .run = "php -d xdebug.mode=off " + .run
    else
        .
    end
)' .github/classroom/autograding.json > tmp_autograding.json

mv tmp_autograding.json .github/classroom/autograding.json

echo "✅ Comandos actualizados en autograding.json"
echo ""
echo "📋 Comandos modificados:"
jq -r '.tests[] | select(.run | contains("xdebug.mode")) | "- " + .name + ": " + .run' .github/classroom/autograding.json
