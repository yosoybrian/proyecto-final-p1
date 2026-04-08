#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Config\Database;

echo "🔄 Inicializando base de datos SQLite...\n\n";

try {
    // Eliminar base de datos existente si existe
    $dbPath = __DIR__ . '/database.sqlite';
    if (file_exists($dbPath)) {
        unlink($dbPath);
        echo "✅ Base de datos anterior eliminada\n";
    }

    // Crear nueva base de datos e inicializar tablas
    $db = Database::getInstance();
    echo "✅ Tablas creadas exitosamente\n\n";

    // Insertar datos de prueba
    echo "🔄 Insertando datos de prueba...\n";
    $db->insertarDatosPrueba();
    echo "✅ Datos de prueba insertados\n\n";

    // Mostrar estadísticas
    $conexion = $db->getConexion();

    $stmt = $conexion->query("SELECT COUNT(*) as count FROM pacientes");
    $pacientes = $stmt->fetch()['count'];

    $stmt = $conexion->query("SELECT COUNT(*) as count FROM medicos");
    $medicos = $stmt->fetch()['count'];

    $stmt = $conexion->query("SELECT COUNT(*) as count FROM citas");
    $citas = $stmt->fetch()['count'];

    echo "📊 Estadísticas:\n";
    echo "   - Pacientes: $pacientes\n";
    echo "   - Médicos: $medicos\n";
    echo "   - Citas: $citas\n\n";

    echo "✨ Base de datos inicializada correctamente!\n";
    echo "📍 Ubicación: $dbPath\n\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
