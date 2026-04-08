#!/bin/bash

# Script de gestión de autograding con configuración flexible
# Permite gestionar múltiples configuraciones y validar archivos dinámicamente
# 
# Uso: ./scripts/manage-autograding.sh [comando] [opciones]
# 
# Comandos disponibles:
#   run [config]     - Ejecutar autograding con configuración específica
#   validate [config] - Validar configuración sin ejecutar
#   create-config    - Crear nueva configuración interactiva
#   list-configs     - Listar configuraciones disponibles
#   help             - Mostrar ayuda detallada

set -euo pipefail

# Configuración
readonly SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
readonly PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
readonly CONFIGS_DIR="$PROJECT_ROOT/.github/classroom"
readonly DEFAULT_CONFIG="$CONFIGS_DIR/autograding.json"
readonly SECURE_SCRIPT="$SCRIPT_DIR/secure-grading-report.sh"

# Colores
readonly RED='\033[0;31m'
readonly GREEN='\033[0;32m'
readonly YELLOW='\033[1;33m'
readonly BLUE='\033[0;34m'
readonly CYAN='\033[0;36m'
readonly NC='\033[0m'

# Logging
log_info() { echo -e "${BLUE}[INFO]${NC} $*"; }
log_warn() { echo -e "${YELLOW}[WARN]${NC} $*"; }
log_error() { echo -e "${RED}[ERROR]${NC} $*"; }
log_success() { echo -e "${GREEN}[SUCCESS]${NC} $*"; }
log_header() { echo -e "${CYAN}=== $* ===${NC}"; }

# Mostrar ayuda
show_help() {
    cat << EOF
🎓 Gestor de Autograding para GitHub Classroom

USO:
    $0 [comando] [opciones]

COMANDOS:
    run [config]         - Ejecutar autograding
    validate [config]    - Validar configuración
    create-config       - Crear nueva configuración
    list-configs        - Listar configuraciones disponibles
    scan-files          - Escanear archivos del proyecto
    test-config         - Probar configuración específica
    help                - Mostrar esta ayuda

EJEMPLOS:
    $0 run                              # Usar configuración por defecto
    $0 run custom-config.json           # Usar configuración específica
    $0 validate                         # Validar configuración por defecto
    $0 create-config                    # Crear nueva configuración
    $0 scan-files                       # Ver archivos disponibles

CONFIGURACIONES:
    Por defecto: $DEFAULT_CONFIG
    Directorio:  $CONFIGS_DIR

EOF
}

# Listar configuraciones disponibles
list_configs() {
    log_header "Configuraciones Disponibles"
    
    if [ ! -d "$CONFIGS_DIR" ]; then
        log_warn "Directorio de configuraciones no existe: $CONFIGS_DIR"
        return 1
    fi
    
    local configs=($(find "$CONFIGS_DIR" -name "*.json" -type f))
    
    if [ ${#configs[@]} -eq 0 ]; then
        log_warn "No se encontraron configuraciones JSON en $CONFIGS_DIR"
        return 1
    fi
    
    for config in "${configs[@]}"; do
        local config_name=$(basename "$config")
        local relative_path=$(realpath --relative-to="$PROJECT_ROOT" "$config")
        
        echo -e "  📄 ${GREEN}$config_name${NC}"
        echo -e "     Ruta: $relative_path"
        
        if command -v jq >/dev/null 2>&1 && jq empty "$config" 2>/dev/null; then
            local test_count=$(jq '.tests | length' "$config" 2>/dev/null || echo "?")
            local total_points=$(jq '[.tests[].points] | add' "$config" 2>/dev/null || echo "?")
            echo -e "     Tests: $test_count | Puntos: $total_points"
        else
            echo -e "     ${RED}Formato inválido${NC}"
        fi
        echo ""
    done
}

# Ejecutar autograding
run_autograding() {
    local config_file="${1:-$DEFAULT_CONFIG}"
    
    log_header "Ejecutando Autograding"
    log_info "Configuración: $config_file"
    
    if [ ! -f "$SECURE_SCRIPT" ]; then
        log_error "Script seguro no encontrado: $SECURE_SCRIPT"
        return 1
    fi
    
    if [ ! -f "$config_file" ]; then
        log_error "Configuración no encontrada: $config_file"
        return 1
    fi
    
    # Ejecutar script seguro
    "$SECURE_SCRIPT" "$config_file"
}

# Función principal
main() {
    local command="${1:-help}"
    
    case "$command" in
        "run")
            run_autograding "${2:-$DEFAULT_CONFIG}"
            ;;
        "validate")
            validate_config "${2:-$DEFAULT_CONFIG}"
            ;;
        "list-configs")
            list_configs
            ;;
        "help"|"-h"|"--help")
            show_help
            ;;
        *)
            log_error "Comando desconocido: $command"
            echo ""
            show_help
            exit 1
            ;;
    esac
}

# Ejecutar función principal
main "$@"

# Utilidad para gestionar la configuración de autograding dinámicamente
# Uso: ./scripts/manage-autograding.sh [add|remove|list|validate|test]

set -euo pipefail

AUTOGRADING_FILE=".github/classroom/autograding.json"

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo -e "${BLUE}🔧 Gestor de Configuración de Autograding Dinámico${NC}"
echo "=================================================="

check_jq() {
    if ! command -v jq >/dev/null 2>&1; then
        log_error "jq no está instalado"
        log_info "Intentando instalar automáticamente..."
        
        if command -v apt-get >/dev/null 2>&1; then
            if apt-get update >/dev/null 2>&1 && apt-get install -y jq >/dev/null 2>&1; then
                log_success "jq instalado exitosamente"
            else
                log_error "No se pudo instalar jq automáticamente"
                log_info "Instala manualmente: sudo apt install -y jq"
                exit 1
            fi
        else
            log_error "Sistema de paquetes no reconocido"
            log_info "Instala manualmente: sudo apt install -y jq"
            exit 1
        fi
    fi
    
    if ! command -v bc >/dev/null 2>&1; then
        log_error "bc no está instalado"
        log_info "Intentando instalar automáticamente..."
        
        if command -v apt-get >/dev/null 2>&1; then
            if apt-get update >/dev/null 2>&1 && apt-get install -y bc >/dev/null 2>&1; then
                log_success "bc instalado exitosamente"
            else
                log_error "No se pudo instalar bc automáticamente"
                log_info "Instala manualmente: sudo apt install -y bc"
                exit 1
            fi
        else
            log_error "Sistema de paquetes no reconocido"
            log_info "Instala manualmente: sudo apt install -y bc"
            exit 1
        fi
    fi
}

validate_config() {
    echo -e "${CYAN}🔍 Validando configuración...${NC}"
    
    if [ ! -f "$AUTOGRADING_FILE" ]; then
        echo -e "${RED}❌ Error: No se encontró $AUTOGRADING_FILE${NC}"
        exit 1
    fi
    
    if ! jq empty "$AUTOGRADING_FILE" 2>/dev/null; then
        echo -e "${RED}❌ Error: $AUTOGRADING_FILE no es un JSON válido${NC}"
        exit 1
    fi
    
    local test_count=$(jq '.tests | length' "$AUTOGRADING_FILE")
    local total_points=$(jq '[.tests[].points] | add' "$AUTOGRADING_FILE")
    
    echo -e "${GREEN}✅ Configuración válida${NC}"
    echo "   • Total de tests: $test_count"
    echo "   • Puntos totales: $total_points"
    echo ""
}

list_tests() {
    echo -e "${PURPLE}📋 Tests configurados:${NC}"
    echo ""
    
    local index=0
    while IFS= read -r test_data; do
        local name=$(echo "$test_data" | jq -r '.name')
        local points=$(echo "$test_data" | jq -r '.points')
        local run=$(echo "$test_data" | jq -r '.run')
        local setup=$(echo "$test_data" | jq -r '.setup // ""')
        
        echo -e "${YELLOW}[$((index + 1))]${NC} $name"
        echo "    • Puntos: $points"
        echo "    • Comando: $run"
        if [ -n "$setup" ] && [ "$setup" != "null" ]; then
            echo "    • Setup: $setup"
        fi
        echo ""
        
        index=$((index + 1))
    done < <(jq -c '.tests[]' "$AUTOGRADING_FILE")
}

add_test_interactive() {
    echo -e "${GREEN}➕ Agregar nuevo test${NC}"
    echo "Ingresa los datos del test (presiona Enter para valores por defecto):"
    echo ""
    
    read -p "Nombre del test: " test_name
    read -p "Comando a ejecutar: " test_run
    read -p "Puntos (decimal): " test_points
    read -p "Setup (opcional): " test_setup
    read -p "Timeout en segundos [10]: " test_timeout
    
    # Valores por defecto
    test_timeout=${test_timeout:-10}
    
    # Validar que los campos requeridos no estén vacíos
    if [ -z "$test_name" ] || [ -z "$test_run" ] || [ -z "$test_points" ]; then
        echo -e "${RED}❌ Error: Nombre, comando y puntos son requeridos${NC}"
        exit 1
    fi
    
    # Crear el objeto JSON del nuevo test
    local new_test=$(jq -n \
        --arg name "$test_name" \
        --arg setup "$test_setup" \
        --arg run "$test_run" \
        --arg points "$test_points" \
        --arg timeout "$test_timeout" \
        '{
            name: $name,
            setup: (if $setup == "" then "" else $setup end),
            run: $run,
            input: "",
            output: "",
            comparison: "included",
            timeout: ($timeout | tonumber),
            points: ($points | tonumber)
        }')
    
    # Agregar el test al archivo
    jq --argjson new_test "$new_test" '.tests += [$new_test]' "$AUTOGRADING_FILE" > tmp_autograding.json
    mv tmp_autograding.json "$AUTOGRADING_FILE"
    
    echo -e "${GREEN}✅ Test agregado exitosamente${NC}"
    
    # Mostrar el nuevo total
    local new_total=$(jq '[.tests[].points] | add' "$AUTOGRADING_FILE")
    echo "Nuevo total de puntos: $new_total"
}

remove_test_interactive() {
    echo -e "${RED}➖ Eliminar test${NC}"
    
    list_tests
    
    echo "Ingresa el número del test a eliminar (1-$(jq '.tests | length' "$AUTOGRADING_FILE")):"
    read -p "Número: " test_index
    
    # Validar que sea un número válido
    if ! [[ "$test_index" =~ ^[0-9]+$ ]] || [ "$test_index" -lt 1 ] || [ "$test_index" -gt $(jq '.tests | length' "$AUTOGRADING_FILE") ]; then
        echo -e "${RED}❌ Error: Número de test inválido${NC}"
        exit 1
    fi
    
    # Mostrar el test que se va a eliminar
    local test_name=$(jq -r ".tests[$((test_index - 1))].name" "$AUTOGRADING_FILE")
    echo -e "${YELLOW}⚠️ Se eliminará: $test_name${NC}"
    
    read -p "¿Estás seguro? (y/N): " confirm
    if [ "$confirm" != "y" ] && [ "$confirm" != "Y" ]; then
        echo "Operación cancelada"
        exit 0
    fi
    
    # Eliminar el test
    jq "del(.tests[$((test_index - 1))])" "$AUTOGRADING_FILE" > tmp_autograding.json
    mv tmp_autograding.json "$AUTOGRADING_FILE"
    
    echo -e "${GREEN}✅ Test eliminado exitosamente${NC}"
    
    # Mostrar el nuevo total
    local new_total=$(jq '[.tests[].points] | add' "$AUTOGRADING_FILE")
    echo "Nuevo total de puntos: $new_total"
}

update_points_proportionally() {
    echo -e "${BLUE}🎯 Actualizar puntos proporcionalmente${NC}"
    
    local current_total=$(jq '[.tests[].points] | add' "$AUTOGRADING_FILE")
    echo "Total actual: $current_total puntos"
    
    read -p "Nuevo total de puntos deseado: " new_total
    
    if ! [[ "$new_total" =~ ^[0-9]+\.?[0-9]*$ ]] || [ "$(echo "$new_total <= 0" | bc)" -eq 1 ]; then
        echo -e "${RED}❌ Error: El nuevo total debe ser un número positivo${NC}"
        exit 1
    fi
    
    echo "Actualizando proporcionalmente..."
    
    # Calcular factor de escala
    local scale_factor=$(echo "scale=10; $new_total / $current_total" | bc)
    
    # Actualizar todos los puntos
    jq --arg scale "$scale_factor" '.tests |= map(.points = ((.points * ($scale | tonumber)) * 100 | round / 100))' "$AUTOGRADING_FILE" > tmp_autograding.json
    mv tmp_autograding.json "$AUTOGRADING_FILE"
    
    local final_total=$(jq '[.tests[].points] | add' "$AUTOGRADING_FILE")
    echo -e "${GREEN}✅ Puntos actualizados${NC}"
    echo "Total final: $final_total puntos"
    
    # Mostrar la nueva distribución
    echo ""
    echo "Nueva distribución:"
    list_tests
}

run_grading_test() {
    echo -e "${GREEN}🧪 Ejecutando test de autograding...${NC}"
    echo ""
    
    if [ -x "./scripts/generate-grading-report.sh" ]; then
        ./scripts/generate-grading-report.sh
    else
        echo -e "${RED}❌ Error: scripts/generate-grading-report.sh no existe o no es ejecutable${NC}"
        exit 1
    fi
}

show_help() {
    echo "Uso: $0 [COMANDO]"
    echo ""
    echo "Comandos disponibles:"
    echo "  list       - Listar todos los tests configurados"
    echo "  add        - Agregar un nuevo test interactivamente"
    echo "  remove     - Eliminar un test existente"
    echo "  validate   - Validar la configuración actual"
    echo "  scale      - Escalar puntos proporcionalmente a un nuevo total"
    echo "  test       - Ejecutar el script de autograding"
    echo "  help       - Mostrar esta ayuda"
    echo ""
    echo "Ejemplos:"
    echo "  $0 list              # Ver todos los tests"
    echo "  $0 add               # Agregar nuevo test"
    echo "  $0 scale             # Cambiar total de puntos"
    echo "  $0 test              # Probar la configuración"
}

# Verificar dependencias
check_jq

# Procesar argumentos
case "${1:-help}" in
    list)
        validate_config
        list_tests
        ;;
    add)
        validate_config
        add_test_interactive
        ;;
    remove)
        validate_config
        remove_test_interactive
        ;;
    validate)
        validate_config
        ;;
    scale)
        validate_config
        update_points_proportionally
        ;;
    test)
        validate_config
        run_grading_test
        ;;
    help|--help|-h)
        show_help
        ;;
    *)
        echo -e "${RED}❌ Comando desconocido: $1${NC}"
        echo ""
        show_help
        exit 1
        ;;
esac
