#!/bin/bash

# Script de diagnóstico y reparación para GitHub Codespaces

echo "╔══════════════════════════════════════════════════════════════╗"
echo "║     🔧 DIAGNÓSTICO Y REPARACIÓN - GitHub Codespace          ║"
echo "╚══════════════════════════════════════════════════════════════╝"
echo ""

# Función para verificar comando
check_command() {
    if command -v $1 &> /dev/null; then
        echo "✅ $1 está instalado: $(command -v $1)"
        if [ "$1" = "composer" ]; then
            composer --version 2>/dev/null || echo "   (pero no funciona correctamente)"
        fi
        return 0
    else
        echo "❌ $1 NO está instalado"
        return 1
    fi
}

echo "📋 Verificando componentes instalados..."
echo ""

# Verificar PHP
check_command php
PHP_INSTALLED=$?

# Verificar Composer
check_command composer
COMPOSER_INSTALLED=$?

# Verificar git
check_command git

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

if [ $COMPOSER_INSTALLED -ne 0 ]; then
    echo "🔧 INSTALANDO COMPOSER..."
    echo ""

    # Descargar e instalar Composer
    cd /tmp
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

    # Verificar hash (opcional pero recomendado)
    EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
    ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

    if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then
        echo "⚠️  Advertencia: Checksum no coincide. Instalando de todas formas..."
    fi

    # Instalar Composer globalmente
    sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    php -r "unlink('composer-setup.php');"

    # Verificar instalación
    if command -v composer &> /dev/null; then
        echo "✅ Composer instalado exitosamente!"
        composer --version
    else
        echo "❌ Error al instalar Composer"
        exit 1
    fi

    echo ""
fi

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Navegar al directorio del proyecto
PROJECT_DIR="/workspaces/poo-api-citas-medica"
if [ -d "$PROJECT_DIR" ]; then
    echo "📁 Navegando a: $PROJECT_DIR"
    cd "$PROJECT_DIR"
else
    echo "⚠️  Usando directorio actual: $(pwd)"
fi

echo ""
echo "📦 Instalando dependencias del proyecto..."
echo ""

# Instalar dependencias
composer install --no-interaction --prefer-dist
INSTALL_RESULT=$?

if [ $INSTALL_RESULT -eq 0 ]; then
    echo ""
    echo "✅ Dependencias instaladas correctamente"
else
    echo ""
    echo "❌ Error al instalar dependencias"
    exit 1
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "🧪 Verificando instalación de PHPUnit..."
echo ""

if [ -f "vendor/bin/phpunit" ]; then
    echo "✅ PHPUnit instalado en: vendor/bin/phpunit"
    ./vendor/bin/phpunit --version
else
    echo "❌ PHPUnit no encontrado en vendor/bin/"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "🔧 Configurando permisos..."
echo ""

# Dar permisos a scripts
find . -type f -name "*.sh" -exec chmod +x {} \;
echo "✅ Permisos de scripts configurados"

# Permisos para ejercicio
if [ -d "exercises/api-ejercicio" ]; then
    chmod -R 777 exercises/api-ejercicio 2>/dev/null || true
    echo "✅ Permisos del ejercicio configurados"
fi

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "✅ REPARACIÓN COMPLETADA"
echo ""
echo "📝 Comandos disponibles:"
echo ""
echo "   composer --version    # Verificar Composer"
echo "   composer test         # Ejecutar tests"
echo "   composer list         # Ver todos los comandos"
echo ""
echo "🚀 Puedes continuar trabajando normalmente"
echo ""
