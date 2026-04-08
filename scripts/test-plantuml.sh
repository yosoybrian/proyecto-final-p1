#!/bin/bash

echo "🔍 Verificando configuración de PlantUML..."
echo ""

# 1. Verificar Java
echo "1️⃣ Java:"
java -version 2>&1 | head -n 1
echo "   JAVA_HOME: $JAVA_HOME"
echo ""

# 2. Verificar Graphviz
echo "2️⃣ Graphviz:"
if command -v dot &> /dev/null; then
    dot -V
else
    echo "   ❌ Graphviz no está instalado"
fi
echo ""

# 3. Verificar libfreetype
echo "3️⃣ libfreetype:"
if ldconfig -p | grep -q libfreetype; then
    echo "   ✅ libfreetype encontrada:"
    ldconfig -p | grep freetype | head -n 1
else
    echo "   ❌ libfreetype NO encontrada"
fi
echo ""

# 4. Verificar fontconfig
echo "4️⃣ fontconfig:"
if command -v fc-cache &> /dev/null; then
    echo "   ✅ fontconfig instalado"
else
    echo "   ❌ fontconfig NO instalado"
fi
echo ""

# 5. Verificar LD_LIBRARY_PATH
echo "5️⃣ LD_LIBRARY_PATH:"
if [[ -n "$LD_LIBRARY_PATH" ]]; then
    echo "   $LD_LIBRARY_PATH"
else
    echo "   ⚠️  No configurado (puede causar problemas)"
fi
echo ""

echo "✅ Verificación completa"
