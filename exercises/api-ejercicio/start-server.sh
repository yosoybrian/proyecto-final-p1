#!/bin/bash

# Script para iniciar el servidor de desarrollo del API REST

# Colores para mensajes
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}  API REST - Sistema de Citas Médicas${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

# Verificar si ya hay un servidor corriendo en el puerto
if lsof -Pi :8080 -sTCP:LISTEN -t >/dev/null 2>&1 ; then
    echo -e "${YELLOW}⚠️  Ya hay un servidor corriendo en el puerto 8080${NC}"
    echo -e "${YELLOW}   Deteniendo servidor anterior...${NC}"
    pkill -f "php -S 0.0.0.0:8080" 2>/dev/null
    sleep 1
fi

# Iniciar el servidor
echo -e "${GREEN}🚀 Iniciando servidor en http://0.0.0.0:8080${NC}"
echo ""
echo -e "${BLUE}📋 URLs disponibles:${NC}"
echo -e "   Documentación: ${GREEN}http://localhost:8080/${NC}"
echo -e "   API Endpoint:  ${GREEN}http://localhost:8080/api.php${NC}"
echo ""
echo -e "${BLUE}💡 Nota: El servidor escucha en 0.0.0.0 para ser accesible desde el navegador${NC}"
echo -e "${YELLOW}   Presiona Ctrl+C para detener el servidor${NC}"
echo ""

cd "$(dirname "$0")/public" && php -S 0.0.0.0:8080
