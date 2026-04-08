<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Configurar headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

use App\Controladores\PacienteController;
use App\Controladores\CitaController;
use App\Controladores\MedicoController;
use App\Config\Database;

// Inicializar base de datos y datos de prueba
try {
    $db = Database::getInstance();
    $db->insertarDatosPrueba();
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al inicializar la base de datos: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Parsear la URI
$requestUri = $_SERVER['REQUEST_URI'];

// Remover api.php del path si existe
$requestUri = str_replace('/api.php', '', $requestUri);

$uri = parse_url($requestUri, PHP_URL_PATH);
$uri = explode('/', trim($uri, '/'));

$requestMethod = $_SERVER['REQUEST_METHOD'];

// Router simple
try {
    // Endpoints de Pacientes
    if (isset($uri[0]) && $uri[0] === 'api' && isset($uri[1]) && $uri[1] === 'pacientes') {
        $controller = new PacienteController();

        if ($requestMethod === 'GET' && !isset($uri[2])) {
            $controller->index();
        } elseif ($requestMethod === 'GET' && isset($uri[2])) {
            $controller->show($uri[2]);
        } elseif ($requestMethod === 'POST') {
            $controller->store();
        } elseif ($requestMethod === 'PUT' && isset($uri[2])) {
            $controller->update($uri[2]);
        } elseif ($requestMethod === 'DELETE' && isset($uri[2])) {
            $controller->destroy($uri[2]);
        } else {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido'
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    // Endpoints de Citas
    if (isset($uri[0]) && $uri[0] === 'api' && isset($uri[1]) && $uri[1] === 'citas') {
        $controller = new CitaController();

        if ($requestMethod === 'GET' && !isset($uri[2])) {
            $controller->index();
        } elseif ($requestMethod === 'GET' && isset($uri[2])) {
            $controller->show($uri[2]);
        } elseif ($requestMethod === 'POST') {
            $controller->store();
        } elseif ($requestMethod === 'PUT' && isset($uri[2])) {
            $controller->update($uri[2]);
        } elseif ($requestMethod === 'DELETE' && isset($uri[2])) {
            $controller->destroy($uri[2]);
        } else {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido'
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    // Endpoints de Médicos
    if (isset($uri[0]) && $uri[0] === 'api' && isset($uri[1]) && $uri[1] === 'medicos') {
        $controller = new MedicoController();

        if ($requestMethod === 'GET' && !isset($uri[2])) {
            $controller->index();
        } elseif ($requestMethod === 'GET' && isset($uri[2])) {
            $controller->show($uri[2]);
        } elseif ($requestMethod === 'POST') {
            $controller->store();
        } elseif ($requestMethod === 'PUT' && isset($uri[2])) {
            $controller->update($uri[2]);
        } elseif ($requestMethod === 'DELETE' && isset($uri[2])) {
            $controller->destroy($uri[2]);
        } else {
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Método no permitido'
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

    // Endpoint raíz - Documentación de la API
    if ($uri[0] === 'api' && count($uri) === 1) {
        echo json_encode([
            'success' => true,
            'message' => 'API de Gestión de Citas Médicas',
            'version' => '1.0.0',
            'endpoints' => [
                'pacientes' => [
                    'GET /api/pacientes' => 'Obtener todos los pacientes',
                    'GET /api/pacientes/{id}' => 'Obtener un paciente por ID',
                    'POST /api/pacientes' => 'Crear un nuevo paciente',
                    'PUT /api/pacientes/{id}' => 'Actualizar un paciente',
                    'DELETE /api/pacientes/{id}' => 'Eliminar un paciente'
                ],
                'citas' => [
                    'GET /api/citas' => 'Obtener todas las citas',
                    'GET /api/citas?estado={estado}' => 'Filtrar citas por estado',
                    'GET /api/citas?paciente_id={id}' => 'Filtrar citas por paciente',
                    'GET /api/citas?medico_id={id}' => 'Filtrar citas por médico',
                    'GET /api/citas/{id}' => 'Obtener una cita por ID',
                    'POST /api/citas' => 'Crear una nueva cita',
                    'PUT /api/citas/{id}' => 'Actualizar una cita',
                    'DELETE /api/citas/{id}' => 'Eliminar una cita'
                ],
                'medicos' => [
                    'GET /api/medicos' => 'Obtener todos los médicos',
                    'GET /api/medicos?especialidad={nombre}' => 'Filtrar médicos por especialidad',
                    'GET /api/medicos/{id}' => 'Obtener un médico por ID',
                    'POST /api/medicos' => 'Crear un nuevo médico',
                    'PUT /api/medicos/{id}' => 'Actualizar un médico',
                    'DELETE /api/medicos/{id}' => 'Eliminar un médico'
                ]
            ]
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    // Ruta no encontrada
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'message' => 'Endpoint no encontrado'
    ], JSON_UNESCAPED_UNICODE);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
