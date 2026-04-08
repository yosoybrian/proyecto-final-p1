#!/bin/bash

# Detectar directorio del proyecto dinámicamente
PROJECT_ROOT="$(pwd)"


# Script wrapper para ejecutar PHPUnit sin step debugging de Xdebug
# Evita el error: "Could not connect to debugging client"

# Configurar Xdebug solo para coverage, no para step debugging
export XDEBUG_MODE=coverage

# Ejecutar el comando PHPUnit que se pase como parámetro
exec vendor/bin/phpunit "$@"
