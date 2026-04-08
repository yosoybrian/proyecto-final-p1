#!/bin/bash

# Script para generar diagramas SVG desde archivos PlantUML
# Usa PlantUML JAR directamente sin necesidad de Graphviz

echo "🎨 Generador de Diagramas PlantUML → SVG"
echo "=========================================="
echo ""

# Configuración
PLANTUML_JAR="tools/plantuml.jar"
SOURCE_DIR="exercises/diagramas"
OUTPUT_DIR="exercises/diagramas/output"

# Verificar que existe Java
if ! command -v java &> /dev/null; then
    echo "❌ Error: Java no está instalado"
    echo "   Java es necesario para ejecutar PlantUML"
    exit 1
fi

echo "✓ Java encontrado: $(java -version 2>&1 | head -n 1)"

# Verificar que existe PlantUML JAR
if [ ! -f "$PLANTUML_JAR" ]; then
    echo "❌ Error: PlantUML JAR no encontrado en $PLANTUML_JAR"
    echo "   Ejecuta el script de setup para descargarlo"
    exit 1
fi

echo "✓ PlantUML JAR encontrado"
echo ""

# Crear directorio de salida si no existe
mkdir -p "$OUTPUT_DIR"

# Contar archivos .puml
PUML_COUNT=$(find "$SOURCE_DIR" -maxdepth 2 -name "*.puml" -type f | wc -l)

if [ "$PUML_COUNT" -eq 0 ]; then
    echo "⚠️  No se encontraron archivos .puml en $SOURCE_DIR"
    echo ""
    echo "Crea tus diagramas en:"
    echo "  • $SOURCE_DIR/citas-medicas.puml"
    echo "  • $SOURCE_DIR/citas-medicas-poo.puml"
    exit 0
fi

echo "📁 Archivos .puml encontrados: $PUML_COUNT"
echo ""

# Generar SVGs
echo "🔄 Generando diagramas SVG..."
echo ""

# Opción 1: Generar solo archivos específicos si se pasan como argumentos
if [ $# -gt 0 ]; then
    for file in "$@"; do
        if [ -f "$file" ]; then
            echo "  → Procesando: $(basename "$file")"
            java -jar "$PLANTUML_JAR" -svg -o "$(realpath "$OUTPUT_DIR")" "$file" 2>&1
        else
            echo "  ⚠️  Archivo no encontrado: $file"
        fi
    done
else
    # Opción 2: Generar todos los archivos .puml
    find "$SOURCE_DIR" -maxdepth 2 -name "*.puml" -type f | while read -r puml_file; do
        filename=$(basename "$puml_file" .puml)
        echo "  → Procesando: $filename.puml"
        java -jar "$PLANTUML_JAR" -svg -o "$(realpath "$OUTPUT_DIR")" "$puml_file" 2>&1
    done
fi

echo ""
echo "✅ ¡Diagramas generados exitosamente!"
echo ""
echo "📂 Los archivos SVG están en: $OUTPUT_DIR"
echo ""

# Listar archivos generados
if [ -d "$OUTPUT_DIR" ]; then
    SVG_COUNT=$(find "$OUTPUT_DIR" -name "*.svg" -type f | wc -l)
    if [ "$SVG_COUNT" -gt 0 ]; then
        echo "📊 Diagramas SVG generados ($SVG_COUNT):"
        find "$OUTPUT_DIR" -name "*.svg" -type f -exec basename {} \; | sort | sed 's/^/  • /'
    fi
fi

echo ""
echo "💡 Consejos:"
echo "  • Abre los SVG con cualquier navegador"
echo "  • Los SVG son vectoriales (escalables sin pérdida)"
echo "  • Puedes incluirlos en documentos o presentaciones"
echo ""
echo "🔄 Para regenerar después de cambios:"
echo "   bash generar-diagramas.sh"
echo ""
