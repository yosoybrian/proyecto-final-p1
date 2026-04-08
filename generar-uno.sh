#!/bin/bash

# Script simple para generar un diagrama específico
# Uso: bash generar-uno.sh <archivo.puml>

if [ $# -eq 0 ]; then
    echo "❌ Error: Debes especificar un archivo .puml"
    echo ""
    echo "Uso:"
    echo "  bash generar-uno.sh exercises/diagramas/citas-medicas.puml"
    echo "  bash generar-uno.sh exercises/diagramas/citas-medicas-poo.puml"
    exit 1
fi

PUML_FILE="$1"

if [ ! -f "$PUML_FILE" ]; then
    echo "❌ Error: Archivo no encontrado: $PUML_FILE"
    exit 1
fi

echo "🎨 Generando diagrama SVG..."
echo "Archivo: $(basename "$PUML_FILE")"
echo ""

# Generar SVG directamente (PlantUML maneja el layout internamente)
java -jar tools/plantuml.jar -svg -o "$(realpath exercises/diagramas/output)" "$PUML_FILE" 2>&1

if [ $? -eq 0 ]; then
    OUTPUT_FILE="exercises/diagramas/output/$(basename "$PUML_FILE" .puml).svg"
    echo ""
    echo "✅ ¡Diagrama generado!"
    echo "📂 Ubicación: $OUTPUT_FILE"
    echo ""
    echo "💡 Abre el archivo SVG con:"
    echo "   • Click derecho → Abrir con navegador"
    echo "   • O arrastra el archivo a tu navegador"
else
    echo ""
    echo "❌ Error al generar el diagrama"
    echo "   Verifica que el archivo .puml tenga sintaxis correcta"
fi
