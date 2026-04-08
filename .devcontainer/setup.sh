#!/bin/bash

# Configuración inicial del entorno PHP para ejercicios y testing

echo "🚀 Configurando entorno PHP para ejercicios y testing..."

# Actualizar el sistema
echo "📦 Actualizando repositorios del sistema..."
sudo apt-get update -qq

# Instalar dependencias básicas (sin Java, Graphviz ni libfreetype - usaremos servidor PlantUML remoto)
echo "📥 Instalando dependencias del sistema..."
sudo apt-get install -y \
    zip \
    unzip \
    git \
    curl \
    wget \
    vim \
    nano \
    htop \
    tree \
    sqlite3 \
    jq \
    bc \
    dos2unix

echo "🔧 Dependencias básicas instaladas"
echo ""
echo "ℹ️  NOTA: PlantUML se renderiza desde servidor remoto oficial"
echo "   No necesitas Java, Graphviz ni libfreetype localmente"
echo ""
echo "ℹ️  NOTA: NO se instala Graphviz ni libfreetype"
echo "   PlantUML generará SVG directamente con Java (más confiable)"
echo ""

# Instalar Composer globalmente
echo "📦 Instalando Composer..."
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"

# Verificar instalación de Composer
composer --version

# Instalar PHPUnit globalmente
echo "🧪 Configurando PHPUnit..."
composer global require phpunit/phpunit

# Detectar el directorio del proyecto dinámicamente
PROJECT_ROOT="${GITHUB_WORKSPACE:-$PROJECT_ROOT}"

# Si estamos en un entorno diferente, usar el directorio actual
if [ ! -d "$PROJECT_ROOT" ]; then
    PROJECT_ROOT="$(pwd)"
fi

echo "📁 Usando directorio del proyecto: $PROJECT_ROOT"

# Instalar dependencias del proyecto
echo "📚 Instalando dependencias del proyecto..."
cd $PROJECT_ROOT
composer install

# Crear directorio de salida para diagramas (aunque usaremos servidor remoto)
mkdir -p $PROJECT_ROOT/exercises/diagramas/output

# Normalizar finales de línea automáticamente
echo ""
echo "🔧 Normalizando finales de línea..."
find $PROJECT_ROOT -type f \( \
    -name "*.php" -o \
    -name "*.sh" -o \
    -name "*.puml" -o \
    -name "*.md" -o \
    -name "*.json" -o \
    -name "*.yml" \
\) -not -path "*/vendor/*" -not -path "*/.git/*" -exec dos2unix {} \; 2>/dev/null

# Hacer ejecutables todos los scripts
find $PROJECT_ROOT -type f -name "*.sh" -not -path "*/vendor/*" -exec chmod +x {} \;

# Normalizar vendor/bin
if [ -d "$PROJECT_ROOT/vendor/bin" ]; then
    find $PROJECT_ROOT/vendor/bin -type f -exec dos2unix {} \; 2>/dev/null
    find $PROJECT_ROOT/vendor/bin -type f -exec chmod +x {} \;
fi

echo "✅ Finales de línea normalizados (CRLF → LF)"

# Mensaje final
echo "✅ Configuración completada!"
echo ""
echo "🎉 Entorno PHP listo para ejercicios y testing automático"
echo ""
echo "📝 Próximos pasos:"
echo "   1. Crea tus diagramas .puml en exercises/diagramas/"
echo "   2. Preview en VS Code: Alt+D (renderizado remoto automático)"
echo "   3. Exportar: Click derecho en .puml → PlantUML: Export Current Diagram"
echo "   4. Ejecuta tests: composer test"
echo ""
echo "🎨 PlantUML configurado:"
echo "   • Servidor remoto: https://www.plantuml.com/plantuml"
echo "   • Sin dependencias locales (Java, Graphviz, etc.)"
echo "   • Preview instantáneo en VS Code"
echo "   • Exportación automática a SVG"
echo ""
echo "🚀 ¡Happy coding!"
