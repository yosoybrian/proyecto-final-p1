#!/usr/bin/env php
<?php

echo "🧪 Probando API REST de Citas Médicas\n";
echo str_repeat("=", 60) . "\n\n";

$baseUrl = "http://localhost:8000/api";

// Función helper para hacer peticiones
function testEndpoint($method, $url, $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    if ($data !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'code' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

echo "📋 TEST 1: Listar todos los pacientes\n";
echo "GET /api/pacientes\n";
$result = testEndpoint('GET', $baseUrl . '/pacientes');
echo "Estado: " . $result['code'] . "\n";
if ($result['body']['success']) {
    echo "✅ Total de pacientes: " . $result['body']['total'] . "\n";
    echo "Primer paciente: " . $result['body']['data'][0]['nombre'] . " " . $result['body']['data'][0]['apellido'] . "\n";
}
echo "\n";

echo "📋 TEST 2: Obtener un paciente específico\n";
echo "GET /api/pacientes/1\n";
$result = testEndpoint('GET', $baseUrl . '/pacientes/1');
echo "Estado: " . $result['code'] . "\n";
if ($result['body']['success']) {
    $p = $result['body']['data'];
    echo "✅ Paciente encontrado: {$p['nombre']} {$p['apellido']}\n";
    echo "   Email: {$p['email']}\n";
    echo "   Teléfono: {$p['telefono']}\n";
}
echo "\n";

echo "📋 TEST 3: Listar todos los médicos\n";
echo "GET /api/medicos\n";
$result = testEndpoint('GET', $baseUrl . '/medicos');
echo "Estado: " . $result['code'] . "\n";
if ($result['body']['success']) {
    echo "✅ Total de médicos: " . $result['body']['total'] . "\n";
    foreach ($result['body']['data'] as $medico) {
        echo "   - {$medico['nombre']} {$medico['apellido']} - {$medico['especialidad']}\n";
    }
}
echo "\n";

echo "📋 TEST 4: Listar todas las citas\n";
echo "GET /api/citas\n";
$result = testEndpoint('GET', $baseUrl . '/citas');
echo "Estado: " . $result['code'] . "\n";
if ($result['body']['success']) {
    echo "✅ Total de citas: " . $result['body']['total'] . "\n";
    $cita = $result['body']['data'][0];
    echo "Primera cita:\n";
    echo "   - Paciente: {$cita['paciente_nombre']} {$cita['paciente_apellido']}\n";
    echo "   - Médico: {$cita['medico_nombre']} {$cita['medico_apellido']}\n";
    echo "   - Fecha: {$cita['fecha_hora']}\n";
    echo "   - Estado: {$cita['estado']}\n";
}
echo "\n";

echo "📋 TEST 5: Filtrar citas por estado\n";
echo "GET /api/citas?estado=confirmada\n";
$result = testEndpoint('GET', $baseUrl . '/citas?estado=confirmada');
echo "Estado: " . $result['code'] . "\n";
if ($result['body']['success']) {
    echo "✅ Citas confirmadas: " . $result['body']['total'] . "\n";
}
echo "\n";

echo "📋 TEST 6: Crear un nuevo paciente\n";
echo "POST /api/pacientes\n";
$nuevoPaciente = [
    'nombre' => 'Roberto',
    'apellido' => 'Fernández Silva',
    'email' => 'roberto.fernandez@email.com',
    'telefono' => '555-9999',
    'fecha_nacimiento' => '1987-08-12',
    'direccion' => 'Calle Nueva 999, Madrid'
];
$result = testEndpoint('POST', $baseUrl . '/pacientes', $nuevoPaciente);
echo "Estado: " . $result['code'] . "\n";
if ($result['body']['success']) {
    echo "✅ Paciente creado con ID: " . $result['body']['data']['id'] . "\n";
    echo "   Nombre: {$result['body']['data']['nombre']} {$result['body']['data']['apellido']}\n";
}
echo "\n";

echo "📋 TEST 7: Crear una nueva cita\n";
echo "POST /api/citas\n";
$nuevaCita = [
    'paciente_id' => 1,
    'medico_id' => 1,
    'fecha_hora' => '2025-12-01 09:00:00',
    'motivo' => 'Consulta de prueba desde API',
    'estado' => 'pendiente',
    'notas' => 'Cita de prueba creada automáticamente'
];
$result = testEndpoint('POST', $baseUrl . '/citas', $nuevaCita);
echo "Estado: " . $result['code'] . "\n";
if ($result['body']['success']) {
    echo "✅ Cita creada con ID: " . $result['body']['data']['id'] . "\n";
    echo "   Paciente: {$result['body']['data']['paciente_nombre']}\n";
    echo "   Médico: {$result['body']['data']['medico_nombre']}\n";
    echo "   Fecha: {$result['body']['data']['fecha_hora']}\n";
}
echo "\n";

echo "✨ Pruebas completadas!\n";
