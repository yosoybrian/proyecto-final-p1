#!/bin/bash

# Script para limpiar la base de datos del ejercicio

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo ""
echo -e "${YELLOW}⚠️  ADVERTENCIA: Este script eliminará TODOS los datos${NC}"
echo -e "${YELLOW}    de la base de datos del ejercicio.${NC}"
echo ""
read -p "¿Estás seguro de que quieres continuar? (s/N): " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[SsYy]$ ]]; then
    echo -e "${GREEN}✅ Operación cancelada${NC}"
    echo ""
    exit 0
fi

echo ""
echo -e "${RED}🗑️  Eliminando base de datos...${NC}"

# Ruta al archivo de base de datos
DB_FILE="$(dirname "$0")/ejercicio.db"

if [ -f "$DB_FILE" ]; then
    rm "$DB_FILE"
    echo -e "${GREEN}✅ Base de datos eliminada${NC}"
else
    echo -e "${YELLOW}⚠️  No se encontró base de datos${NC}"
fi

echo ""
echo -e "${GREEN}✅ Limpieza completada${NC}"
echo ""
echo "Para crear datos de prueba nuevamente, ejecuta:"
echo -e "${GREEN}  php poblar-datos.php${NC}"
echo ""
