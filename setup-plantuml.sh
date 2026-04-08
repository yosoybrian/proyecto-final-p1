#!/bin/bash

# Script para descargar/actualizar PlantUML JAR

echo "📦 Setup de PlantUML JAR"
echo "========================"
echo ""

PLANTUML_JAR="tools/plantuml.jar"
PLANTUML_VERSION="1.2024.7"
PLANTUML_URL="https://github.com/plantuml/plantuml/releases/download/v${PLANTUML_VERSION}/plantuml-${PLANTUML_VERSION}.jar"

# Crear directorio tools si no existe
mkdir -p tools

# Verificar Java
if ! command -v java &> /dev/null; then
    echo "❌ Error: Java no está instalado"
    echo ""
    echo "Java 17 debería estar instalado automáticamente en este devcontainer."
    echo "Si no es así, reinicia el codespace."
    exit 1
fi

echo "✓ Java encontrado: $(java -version 2>&1 | head -n 1)"
echo ""

# Verificar si ya existe PlantUML JAR
if [ -f "$PLANTUML_JAR" ]; then
    echo "✓ PlantUML JAR ya existe"
    
    # Verificar versión
    CURRENT_VERSION=$(java -jar "$PLANTUML_JAR" -version 2>&1 | grep -i "plantuml" | head -n 1)
    echo "  Versión: $CURRENT_VERSION"
    echo ""
    
    read -p "¿Deseas descargar la última versión? (s/N): " -n 1 -r
    echo ""
    
    if [[ ! $REPLY =~ ^[Ss]$ ]]; then
        echo "✓ Usando versión existente"
        exit 0
    fi
    
    echo ""
fi

# Descargar PlantUML JAR
echo "📥 Descargando PlantUML v${PLANTUML_VERSION}..."
echo "URL: $PLANTUML_URL"
echo ""

if curl -L -o "$PLANTUML_JAR" "$PLANTUML_URL"; then
    echo ""
    echo "✅ PlantUML JAR descargado exitosamente"
    echo ""
    
    # Verificar que funciona
    echo "🧪 Verificando instalación..."
    java -jar "$PLANTUML_JAR" -version
    
    echo ""
    echo "✅ ¡Todo listo!"
    echo ""
    echo "Ahora puedes:"
    echo "  • Generar todos los diagramas: bash generar-diagramas.sh"
    echo "  • Generar uno específico: bash generar-uno.sh <archivo.puml>"
else
    echo ""
    echo "❌ Error al descargar PlantUML JAR"
    echo ""
    echo "Intenta descargarlo manualmente:"
    echo "  $PLANTUML_URL"
    echo ""
    echo "Y guárdalo en: $PLANTUML_JAR"
    exit 1
fi
