#!/bin/bash

# Script de ayuda para gestionar el entorno Docker
# Uso: ./docker-helper.sh [comando]

set -e

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para mostrar ayuda
show_help() {
    echo -e "${BLUE}🐳 Docker Helper para PHP Testing Environment${NC}"
    echo ""
    echo "Comandos disponibles:"
    echo ""
    echo -e "${GREEN}  dev${NC}           - Inicia el entorno de desarrollo"
    echo -e "${GREEN}  prod${NC}          - Inicia el entorno de producción"
    echo -e "${GREEN}  stop${NC}          - Detiene todos los servicios"
    echo -e "${GREEN}  restart${NC}       - Reinicia los servicios"
    echo -e "${GREEN}  logs${NC}          - Muestra los logs del servidor web"
    echo -e "${GREEN}  shell${NC}         - Accede al shell del contenedor web"
    echo -e "${GREEN}  test${NC}          - Ejecuta los tests"
    echo -e "${GREEN}  analyze${NC}       - Ejecuta el análisis de código"
    echo -e "${GREEN}  style-check${NC}   - Verifica el estilo de código"
    echo -e "${GREEN}  style-fix${NC}     - Corrige el estilo de código"
    echo -e "${GREEN}  clean${NC}         - Limpia contenedores y volúmenes"
    echo -e "${GREEN}  build${NC}         - Reconstruye las imágenes"
    echo -e "${GREEN}  status${NC}        - Muestra el estado de los servicios"
    echo -e "${GREEN}  fix-line-endings${NC} - Corrige terminaciones de línea CRLF en vendor/bin"
    echo -e "${GREEN}  fix-shell-scripts${NC} - Corrige terminaciones de línea CRLF en scripts shell"
    echo -e "${GREEN}  autograding${NC}   - Ejecuta el sistema de autograding"
    echo ""
}

# Función para verificar si Docker está corriendo
check_docker() {
    if ! docker info > /dev/null 2>&1; then
        echo -e "${RED}❌ Error: Docker no está corriendo${NC}"
        exit 1
    fi
}

# Función para iniciar desarrollo
start_dev() {
    echo -e "${BLUE}🚀 Iniciando entorno de desarrollo...${NC}"
    docker-compose -f docker-compose.dev.yml up -d --build
    echo -e "${GREEN}✅ Entorno de desarrollo iniciado${NC}"
    echo -e "${YELLOW}📱 Aplicación: http://localhost:8000${NC}"
    echo -e "${YELLOW}📧 MailHog: http://localhost:8025${NC}"
}

# Función para iniciar producción
start_prod() {
    echo -e "${BLUE}🚀 Iniciando entorno de producción...${NC}"
    docker-compose up -d --build --target production
    echo -e "${GREEN}✅ Entorno de producción iniciado${NC}"
    echo -e "${YELLOW}📱 Aplicación: http://localhost:8000${NC}"
}

# Función para detener servicios
stop_services() {
    echo -e "${BLUE}🛑 Deteniendo servicios...${NC}"
    docker-compose down
    docker-compose -f docker-compose.dev.yml down 2>/dev/null || true
    echo -e "${GREEN}✅ Servicios detenidos${NC}"
}

# Función para reiniciar
restart_services() {
    echo -e "${BLUE}🔄 Reiniciando servicios...${NC}"
    stop_services
    start_dev
}

# Función para mostrar logs
show_logs() {
    echo -e "${BLUE}📋 Mostrando logs...${NC}"
    docker-compose logs -f web
}

# Función para acceder al shell
access_shell() {
    echo -e "${BLUE}💻 Accediendo al shell...${NC}"
    docker-compose exec web bash
}

# Función para ejecutar tests
run_tests() {
    echo -e "${BLUE}🧪 Ejecutando tests...${NC}"
    docker-compose exec web composer test
}

# Función para análisis de código
analyze_code() {
    echo -e "${BLUE}🔍 Analizando código...${NC}"
    docker-compose exec web composer analyze
}

# Función para verificar estilo
check_style() {
    echo -e "${BLUE}🎨 Verificando estilo de código...${NC}"
    docker-compose exec web composer style-check
}

# Función para corregir estilo
fix_style() {
    echo -e "${BLUE}🎨 Corrigiendo estilo de código...${NC}"
    docker-compose exec web composer style-fix
}

# Función para limpiar
clean_docker() {
    echo -e "${BLUE}🧹 Limpiando Docker...${NC}"
    docker-compose down -v
    docker-compose -f docker-compose.dev.yml down -v 2>/dev/null || true
    docker system prune -f
    echo -e "${GREEN}✅ Limpieza completada${NC}"
}

# Función para construir
build_images() {
    echo -e "${BLUE}🔨 Reconstruyendo imágenes...${NC}"
    docker-compose build --no-cache
    docker-compose -f docker-compose.dev.yml build --no-cache
    echo -e "${GREEN}✅ Imágenes reconstruidas${NC}"
}

# Función para mostrar estado
show_status() {
    echo -e "${BLUE}📊 Estado de los servicios:${NC}"
    echo ""
    docker-compose ps
    echo ""
    docker-compose -f docker-compose.dev.yml ps 2>/dev/null || true
}

# Función para corregir terminaciones de línea
fix_line_endings() {
    echo -e "${BLUE}🔧 Corrigiendo terminaciones de línea CRLF...${NC}"
    docker-compose exec web /var/www/html/scripts/fix-line-endings.sh
    echo -e "${GREEN}✅ Terminaciones de línea corregidas${NC}"
}

# Función para corregir scripts shell
fix_shell_scripts() {
    echo -e "${BLUE}🔧 Corrigiendo scripts shell...${NC}"
    docker-compose exec web /var/www/html/scripts/fix-shell-scripts.sh
    echo -e "${GREEN}✅ Scripts shell corregidos${NC}"
}

# Función para ejecutar autograding
run_autograding() {
    echo -e "${BLUE}🧪 Ejecutando autograding...${NC}"
    docker-compose exec web composer autograding
}

# Verificar Docker
check_docker

# Procesar comandos
case "${1:-help}" in
    "dev")
        start_dev
        ;;
    "prod")
        start_prod
        ;;
    "stop")
        stop_services
        ;;
    "restart")
        restart_services
        ;;
    "logs")
        show_logs
        ;;
    "shell")
        access_shell
        ;;
    "test")
        run_tests
        ;;
    "analyze")
        analyze_code
        ;;
    "style-check")
        check_style
        ;;
    "style-fix")
        fix_style
        ;;
    "clean")
        clean_docker
        ;;
    "build")
        build_images
        ;;
    "status")
        show_status
        ;;
    "fix-line-endings")
        fix_line_endings
        ;;
    "fix-shell-scripts")
        fix_shell_scripts
        ;;
    "autograding")
        run_autograding
        ;;
    "help"|*)
        show_help
        ;;
esac
