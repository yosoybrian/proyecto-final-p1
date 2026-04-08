#!/bin/bash

# Detectar directorio del proyecto dinámicamente
PROJECT_ROOT="$(pwd)"


# Script seguro y dinámico para generar reportes de autograding
# ✅ SIN eval - Comandos seguros
# ✅ Configuración externa dinámica  
# ✅ Validación de archivos y dependencias
# ✅ Rutas flexibles desde JSON
# 
# Uso: ./scripts/secure-grading-report.sh [config_file]

set -euo pipefail  # Modo estricto para debugging

# Configuración por defecto (puede sobrescribirse)
DEFAULT_CONFIG=".github/classroom/autograding.json"
REPORT_FILE="reports/autograding-report.md"
MIN_PERCENTAGE=60
MAX_TIMEOUT=300
ALLOWED_COMMANDS="phpunit phpcs phpstan composer php vendor/bin/phpunit vendor/bin/phpcs vendor/bin/phpstan test"

# Colores para output
readonly RED='\033[0;31m'
readonly GREEN='\033[0;32m'
readonly YELLOW='\033[1;33m'
readonly BLUE='\033[0;34m'
readonly NC='\033[0m' # No Color

# Función de logging seguro
log() {
    local level="$1"
    shift
    echo -e "${level}$(date '+%Y-%m-%d %H:%M:%S') - $*${NC}"
}

log_info() { log "${BLUE}[INFO] " "$@"; }
log_warn() { log "${YELLOW}[WARN] " "$@"; }
log_error() { log "${RED}[ERROR] " "$@"; }
log_success() { log "${GREEN}[SUCCESS] " "$@"; }

# Validación de seguridad para comandos
validate_command() {
    local command="$1"
    local first_word=$(echo "$command" | awk '{print $1}')
    
    # Verificar que el comando esté en la lista permitida
    if ! echo "$ALLOWED_COMMANDS" | grep -q "\b$first_word\b"; then
        log_error "Comando no permitido: $first_word"
        return 1
    fi
    
    # Verificar caracteres peligrosos
    if [[ "$command" =~ [\;\|\&\`\$\(] ]]; then
        log_error "Comando contiene caracteres peligrosos: $command"
        return 1
    fi
    
    # Verificar redirecciones peligrosas
    if [[ "$command" =~ \>/[^/] ]] || [[ "$command" =~ \<[^/] ]]; then
        log_error "Comando contiene redirecciones peligrosas: $command"
        return 1
    fi
    
    return 0
}

# Función segura para ejecutar comandos sin eval
execute_safe_command() {
    local command="$1"
    local timeout_seconds="$2"
    local description="$3"
    
    # Validar el comando antes de ejecutar
    if ! validate_command "$command"; then
        log_error "Comando rechazado por seguridad: $command"
        return 1
    fi
    
    # Verificar timeout razonable
    if [ "$timeout_seconds" -gt "$MAX_TIMEOUT" ]; then
        log_warn "Timeout reducido de $timeout_seconds a $MAX_TIMEOUT segundos"
        timeout_seconds="$MAX_TIMEOUT"
    fi
    
    log_info "Ejecutando de forma segura: $description"
    log_info "Comando: $command (timeout: ${timeout_seconds}s)"
    
    # Ejecutar con timeout y captura de errores
    local exit_code=0
    timeout "${timeout_seconds}s" bash -c "$command" >/dev/null 2>&1 || exit_code=$?
    
    return $exit_code
}

# Verificar dependencias del sistema
check_system_dependencies() {
    local missing_deps=()
    
    command -v jq >/dev/null 2>&1 || missing_deps+=("jq")
    command -v bc >/dev/null 2>&1 || missing_deps+=("bc")
    command -v timeout >/dev/null 2>&1 || missing_deps+=("timeout")
    
    if [ ${#missing_deps[@]} -ne 0 ]; then
        log_error "Dependencias faltantes: ${missing_deps[*]}"
        log_info "Instalar con: sudo apt install -y ${missing_deps[*]}"
        return 1
    fi
    
    log_success "Todas las dependencias del sistema están disponibles"
    return 0
}

# Validar archivo de configuración
validate_config_file() {
    local config_file="$1"
    
    # Verificar existencia
    if [ ! -f "$config_file" ]; then
        log_error "Archivo de configuración no encontrado: $config_file"
        return 1
    fi
    
    # Verificar permisos de lectura
    if [ ! -r "$config_file" ]; then
        log_error "No se puede leer el archivo de configuración: $config_file"
        return 1
    fi
    
    # Validar formato JSON
    if ! jq empty "$config_file" 2>/dev/null; then
        log_error "Archivo de configuración no es un JSON válido: $config_file"
        return 1
    fi
    
    # Verificar estructura requerida
    if ! jq -e '.tests | type == "array"' "$config_file" >/dev/null 2>&1; then
        log_error "Configuración debe tener un array 'tests'"
        return 1
    fi
    
    # Verificar que cada test tenga campos requeridos
    local test_count=$(jq '.tests | length' "$config_file")
    for ((i=0; i<test_count; i++)); do
        local test_data=$(jq ".tests[$i]" "$config_file")
        
        if ! echo "$test_data" | jq -e '.name' >/dev/null 2>&1 || \
           ! echo "$test_data" | jq -e '.run' >/dev/null 2>&1 || \
           ! echo "$test_data" | jq -e '.points' >/dev/null 2>&1; then
            log_error "Test $i está mal configurado - requiere 'name', 'run' y 'points'"
            return 1
        fi
    done
    
    log_success "Archivo de configuración validado correctamente"
    return 0
}

# Detectar y validar archivos de proyecto dinámicamente
detect_project_files() {
    local config_file="$1"
    local -a project_files=()
    
    log_info "Detectando archivos de proyecto desde configuración..."
    
    # Extraer rutas de archivos desde comandos de test
    while IFS= read -r test_command; do
        # Buscar patrones de archivos PHP
        local php_files=$(echo "$test_command" | grep -oE '[a-zA-Z0-9_/-]+\.php' || true)
        if [ -n "$php_files" ]; then
            while IFS= read -r php_file; do
                if [ -n "$php_file" ]; then
                    project_files+=("$php_file")
                fi
            done <<< "$php_files"
        fi
    done < <(jq -r '.tests[].run' "$config_file")
    
    # Remover duplicados
    local -a unique_files=($(printf "%s\n" "${project_files[@]}" | sort -u))
    
    log_info "Archivos detectados: ${unique_files[*]:-ninguno}"
    
    # Validar existencia de archivos críticos
    local missing_files=()
    for file in "${unique_files[@]}"; do
        if [ ! -f "$file" ]; then
            missing_files+=("$file")
        fi
    done
    
    if [ ${#missing_files[@]} -gt 0 ]; then
        log_warn "Archivos faltantes detectados: ${missing_files[*]}"
        log_warn "Algunos tests podrían fallar"
    else
        log_success "Todos los archivos de proyecto están disponibles"
    fi
    
    return 0
}

# Función principal de procesamiento
main() {
    local config_file="${1:-$DEFAULT_CONFIG}"
    
    log_info "=== Iniciando Autograding Seguro ==="
    log_info "Archivo de configuración: $config_file"
    
    # Crear directorio de reportes de forma segura
    if ! mkdir -p "$(dirname "$REPORT_FILE")" 2>/dev/null; then
        log_error "No se puede crear directorio de reportes"
        exit 1
    fi
    
    # Verificaciones de seguridad
    check_system_dependencies || exit 1
    validate_config_file "$config_file" || exit 1
    detect_project_files "$config_file"
    
    # Leer configuración
    local total_points=$(jq '[.tests[].points] | add' "$config_file")
    local test_count=$(jq '.tests | length' "$config_file")
    local earned_points=0
    
    # Leer configuración 
    local test_count=$(jq '.tests | length' "$config_file")
    local total_points=$(jq '[.tests[].points] | add' "$config_file" | xargs printf "%.2f")
    
    log_info "Configuración cargada:"
    log_info "  • Tests: $test_count"
    log_info "  • Puntos totales: $total_points"
    
    # Inicializar reporte
    cat > "$REPORT_FILE" << EOF
# 📊 Reporte de Autograding Seguro

**Fecha:** $(date '+%Y-%m-%d %H:%M:%S')  
**Configuración:** \`$config_file\`  
**Tests:** $test_count  
**Puntos máximos:** $total_points  

## Resultados Detallados

EOF

    # Procesar tests de forma segura
    log_info "=== Ejecutando Tests Seguros ==="
    
    local test_index=0
    while IFS= read -r test_data; do
        local test_name=$(echo "$test_data" | jq -r '.name')
        local test_run=$(echo "$test_data" | jq -r '.run')
        local test_points=$(echo "$test_data" | jq -r '.points')
        local test_setup=$(echo "$test_data" | jq -r '.setup // ""')
        local test_timeout=$(echo "$test_data" | jq -r '.timeout // 30')
        
        echo ""
        log_info "[$((test_index + 1))/$test_count] $test_name ($test_points pts)"
        
        # Ejecutar setup de forma segura si existe
        local setup_success=true
        if [ -n "$test_setup" ] && [ "$test_setup" != "null" ] && [ "$test_setup" != "" ]; then
            if ! execute_safe_command "$test_setup" "$test_timeout" "Setup para $test_name"; then
                log_warn "Setup falló para $test_name"
                setup_success=false
            fi
        fi
        
        # Ejecutar test principal
        if execute_safe_command "$test_run" "$test_timeout" "$test_name"; then
            log_success "PASSED: $test_name (+$test_points pts)"
            echo "- ✅ **$test_name** ($test_points/$test_points puntos)" >> "$REPORT_FILE"
            earned_points=$(echo "scale=4; $earned_points + $test_points" | bc | xargs printf "%.2f")
        else
            log_error "FAILED: $test_name (0 pts)"
            echo "- ❌ **$test_name** (0/$test_points puntos)" >> "$REPORT_FILE"
        fi
        
        test_index=$((test_index + 1))
    done < <(jq -c '.tests[]' "$config_file")
    
    # Calcular estadísticas
    local percentage=$(echo "scale=4; $earned_points * 100 / $total_points" | bc | xargs printf "%.2f")
    local percentage_int=$(echo "scale=0; $earned_points * 100 / $total_points / 1" | bc)
    
    # Completar reporte
    cat >> "$REPORT_FILE" << EOF

## Resumen Final

**Puntuación:** $earned_points/$total_points puntos ($percentage%)

### Evaluación
EOF

    # Evaluación cualitativa
    if [ "$percentage_int" -ge 90 ]; then
        echo "🎉 **EXCELENTE** - Trabajo excepcional" >> "$REPORT_FILE"
    elif [ "$percentage_int" -ge 80 ]; then
        echo "✅ **MUY BIEN** - Buen trabajo" >> "$REPORT_FILE"
    elif [ "$percentage_int" -ge 70 ]; then
        echo "👍 **BIEN** - Cumple requisitos" >> "$REPORT_FILE"
    elif [ "$percentage_int" -ge "$MIN_PERCENTAGE" ]; then
        echo "⚠️ **SUFICIENTE** - Necesita mejoras" >> "$REPORT_FILE"
    else
        echo "❌ **INSUFICIENTE** - Requiere trabajo adicional" >> "$REPORT_FILE"
    fi
    
    cat >> "$REPORT_FILE" << EOF

### Información Técnica
- **Script:** Versión segura (sin eval)
- **Comandos validados:** Sí
- **Archivos verificados:** Sí  
- **Timeout máximo:** ${MAX_TIMEOUT}s
- **Generado:** $(date)

EOF

    # Resumen final
    echo ""
    log_info "=== Resumen Final ==="
    log_info "Puntuación: $earned_points/$total_points ($percentage%)"
    log_info "Reporte: $REPORT_FILE"
    
    # Código de salida
    if [ "$percentage_int" -ge "$MIN_PERCENTAGE" ]; then
        log_success "Puntuación suficiente ($percentage% >= $MIN_PERCENTAGE%)"
        exit 0
    else
        log_error "Puntuación insuficiente ($percentage% < $MIN_PERCENTAGE%)"
        exit 1
    fi
}

# Manejo de errores global
trap 'log_error "Script interrumpido en línea $LINENO"' ERR

# Ejecutar función principal
main "$@"
