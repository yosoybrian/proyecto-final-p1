#!/bin/bash

# Script simplificado de gestión de autograding
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
CONFIGS_DIR="$PROJECT_ROOT/.github/classroom"
DEFAULT_CONFIG="$CONFIGS_DIR/autograding.json"
SECURE_SCRIPT="$SCRIPT_DIR/secure-grading-report.sh"

# Funciones de logging simples
log_info() { echo "[INFO] $*"; }
log_warn() { echo "[WARN] $*"; }
log_error() { echo "[ERROR] $*"; }
log_success() { echo "[SUCCESS] $*"; }
log_header() { echo "=== $* ==="; }

show_help() {
    cat << EOF
🎓 Gestor de Autograding

COMANDOS:
    run [config]         - Ejecutar autograding
    validate [config]    - Validar configuración
    list-configs        - Listar configuraciones
    help                - Mostrar ayuda

EJEMPLOS:
    $0 run
    $0 validate
    $0 list-configs
EOF
}

list_configs() {
    log_header "Configuraciones Disponibles"
    
    if [ ! -d "$CONFIGS_DIR" ]; then
        log_warn "Directorio no existe: $CONFIGS_DIR"
        return 1
    fi
    
    find "$CONFIGS_DIR" -name "*.json" -type f | while read -r config; do
        config_name=$(basename "$config")
        relative_path=$(realpath --relative-to="$PROJECT_ROOT" "$config")
        
        echo "  📄 $config_name"
        echo "     Ruta: $relative_path"
        
        if command -v jq >/dev/null 2>&1 && jq empty "$config" 2>/dev/null; then
            test_count=$(jq '.tests | length' "$config" 2>/dev/null || echo "?")
            total_points=$(jq '[.tests[].points] | add' "$config" 2>/dev/null || echo "?")
            echo "     Tests: $test_count | Puntos: $total_points"
        else
            echo "     Formato inválido"
        fi
        echo ""
    done
}

validate_config() {
    local config_file="${1:-$DEFAULT_CONFIG}"
    
    log_header "Validando Configuración"
    log_info "Archivo: $config_file"
    
    if [ ! -f "$config_file" ]; then
        log_error "Configuración no encontrada: $config_file"
        return 1
    fi
    
    if ! command -v jq >/dev/null 2>&1; then
        log_error "jq requerido para validación"
        return 1
    fi
    
    if ! jq empty "$config_file" 2>/dev/null; then
        log_error "Archivo no es JSON válido"
        return 1
    fi
    
    log_success "Configuración válida"
    return 0
}

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
    
    "$SECURE_SCRIPT" "$config_file"
}

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
            show_help
            exit 1
            ;;
    esac
}

main "$@"
