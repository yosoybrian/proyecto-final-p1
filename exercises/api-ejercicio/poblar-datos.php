<?php

declare(strict_types=1);

/**
 * Script para poblar la base de datos con datos de prueba
 *
 * Este script crea pacientes y citas de ejemplo para que los estudiantes
 * puedan verificar el funcionamiento de la API sin tener que crear datos manualmente.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use ApiEjercicio\Config\Database;
use ApiEjercicio\Repositorios\PacienteRepository;
use ApiEjercicio\Repositorios\CitaRepository;

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║     Poblando Base de Datos con Datos de Prueba           ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

try {
    // Inicializar repositorios
    $pacienteRepo = new PacienteRepository();
    $citaRepo = new CitaRepository();

    echo "📋 Creando pacientes de prueba...\n\n";

    // Crear pacientes de prueba
    $pacientes = [
        [
            'nombre' => 'María',
            'apellido' => 'García López',
            'email' => 'maria.garcia@email.com',
            'telefono' => '555-0101',
            'fecha_nacimiento' => '1985-03-15'
        ],
        [
            'nombre' => 'Juan',
            'apellido' => 'Martínez Rodríguez',
            'email' => 'juan.martinez@email.com',
            'telefono' => '555-0102',
            'fecha_nacimiento' => '1990-07-22'
        ],
        [
            'nombre' => 'Ana',
            'apellido' => 'Fernández Sánchez',
            'email' => 'ana.fernandez@email.com',
            'telefono' => '555-0103',
            'fecha_nacimiento' => '1978-11-08'
        ],
        [
            'nombre' => 'Carlos',
            'apellido' => 'López Pérez',
            'email' => 'carlos.lopez@email.com',
            'telefono' => '555-0104',
            'fecha_nacimiento' => '1995-02-14'
        ],
        [
            'nombre' => 'Isabel',
            'apellido' => 'Rodríguez Gómez',
            'email' => 'isabel.rodriguez@email.com',
            'telefono' => '555-0105',
            'fecha_nacimiento' => '1982-09-30'
        ],
        [
            'nombre' => 'Pedro',
            'apellido' => 'González Martín',
            'email' => 'pedro.gonzalez@email.com',
            'telefono' => '555-0106',
            'fecha_nacimiento' => '1988-06-18'
        ],
        [
            'nombre' => 'Laura',
            'apellido' => 'Díaz Hernández',
            'email' => 'laura.diaz@email.com',
            'telefono' => '555-0107',
            'fecha_nacimiento' => '1992-12-25'
        ],
        [
            'nombre' => 'Miguel',
            'apellido' => 'Ruiz Torres',
            'email' => 'miguel.ruiz@email.com',
            'telefono' => '555-0108',
            'fecha_nacimiento' => '1975-04-05'
        ]
    ];

    $pacienteIds = [];
    foreach ($pacientes as $index => $paciente) {
        $id = $pacienteRepo->crear($paciente);
        $pacienteIds[] = $id;
        echo "   ✅ Paciente #{$id}: {$paciente['nombre']} {$paciente['apellido']}\n";
    }

    echo "\n📅 Creando citas de prueba...\n\n";

    // Crear citas de prueba
    $citas = [
        [
            'paciente_id' => $pacienteIds[0],
            'fecha_hora' => '2025-11-25 09:00:00',
            'motivo' => 'Consulta general y revisión anual',
            'estado' => 'pendiente'
        ],
        [
            'paciente_id' => $pacienteIds[1],
            'fecha_hora' => '2025-11-25 10:30:00',
            'motivo' => 'Control de presión arterial',
            'estado' => 'pendiente'
        ],
        [
            'paciente_id' => $pacienteIds[2],
            'fecha_hora' => '2025-11-26 11:00:00',
            'motivo' => 'Revisión de análisis de sangre',
            'estado' => 'pendiente'
        ],
        [
            'paciente_id' => $pacienteIds[3],
            'fecha_hora' => '2025-11-26 15:00:00',
            'motivo' => 'Consulta por dolor de espalda',
            'estado' => 'confirmada'
        ],
        [
            'paciente_id' => $pacienteIds[4],
            'fecha_hora' => '2025-11-27 09:30:00',
            'motivo' => 'Seguimiento de tratamiento',
            'estado' => 'confirmada'
        ],
        [
            'paciente_id' => $pacienteIds[5],
            'fecha_hora' => '2025-11-27 14:00:00',
            'motivo' => 'Consulta por alergias estacionales',
            'estado' => 'pendiente'
        ],
        [
            'paciente_id' => $pacienteIds[0],
            'fecha_hora' => '2025-11-28 10:00:00',
            'motivo' => 'Revisión post-operatoria',
            'estado' => 'confirmada'
        ],
        [
            'paciente_id' => $pacienteIds[6],
            'fecha_hora' => '2025-11-28 16:30:00',
            'motivo' => 'Primera consulta - chequeo general',
            'estado' => 'pendiente'
        ],
        [
            'paciente_id' => $pacienteIds[7],
            'fecha_hora' => '2025-11-29 11:30:00',
            'motivo' => 'Control de diabetes',
            'estado' => 'confirmada'
        ],
        [
            'paciente_id' => $pacienteIds[2],
            'fecha_hora' => '2025-12-02 09:00:00',
            'motivo' => 'Renovación de receta médica',
            'estado' => 'pendiente'
        ],
        [
            'paciente_id' => $pacienteIds[4],
            'fecha_hora' => '2025-11-22 10:00:00',
            'motivo' => 'Consulta de urgencia',
            'estado' => 'completada'
        ],
        [
            'paciente_id' => $pacienteIds[1],
            'fecha_hora' => '2025-11-20 14:00:00',
            'motivo' => 'Vacunación anual',
            'estado' => 'completada'
        ]
    ];

    foreach ($citas as $index => $cita) {
        $id = $citaRepo->crear($cita);
        $paciente = $pacienteRepo->obtenerPorId($cita['paciente_id']);
        echo "   ✅ Cita #{$id}: {$paciente['nombre']} {$paciente['apellido']} - {$cita['fecha_hora']} ({$cita['estado']})\n";
    }

    echo "\n";
    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║              ✅ BASE DE DATOS POBLADA                     ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    echo "📊 Resumen:\n";
    echo "   • " . count($pacientes) . " pacientes creados\n";
    echo "   • " . count($citas) . " citas creadas\n";
    echo "\n";
    echo "🌐 Puedes verificar los datos en:\n";
    echo "   • Pacientes: http://localhost:8080/api.php?recurso=pacientes\n";
    echo "   • Citas:     http://localhost:8080/api.php?recurso=citas\n";
    echo "\n";

} catch (\Exception $e) {
    echo "\n❌ Error al poblar la base de datos:\n";
    echo "   " . $e->getMessage() . "\n\n";
    exit(1);
}
