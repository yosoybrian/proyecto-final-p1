<?php

declare(strict_types=1);

use ApiEjercicio\Controladores\CitaController;
use ApiEjercicio\Controladores\PacienteController;

if (php_sapi_name() === 'cli-server' && !defined('API_EJERCICIO_FROM_API')) {
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $documentRoot = rtrim((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''), '/\\');
    $requestedFile = $documentRoot . $requestPath;

    if ($requestPath !== '/' && is_file($requestedFile)) {
        return false;
    }
}

require_once __DIR__ . '/../../vendor/autoload.php';

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$recurso = $_GET['recurso'] ?? null;
$id = $_GET['id'] ?? null;

if ($recurso === null || $recurso === '') {
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $segments = array_values(array_filter(explode('/', trim($requestPath, '/')), static fn ($segment): bool => $segment !== ''));

    if (isset($segments[0]) && $segments[0] === 'api') {
        $recurso = $segments[1] ?? null;
        $id = $segments[2] ?? ($id ?? null);
    }
}

if ($recurso === 'pacientes') {
    $controller = new PacienteController();

    switch ($requestMethod) {
        case 'GET':
            if ($id !== null && $id !== '') {
                $controller->show($id);
            }

            $controller->index();
            break;
        case 'POST':
            $controller->store();
            break;
        case 'PUT':
            if ($id !== null && $id !== '') {
                $controller->update($id);
                break;
            }

            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Falta el id'], JSON_UNESCAPED_UNICODE);
            break;
        case 'DELETE':
            if ($id !== null && $id !== '') {
                $controller->destroy($id);
                break;
            }

            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Falta el id'], JSON_UNESCAPED_UNICODE);
            break;
        default:
            http_response_code(405);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Metodo no permitido'], JSON_UNESCAPED_UNICODE);
            break;
    }

    exit;
}

if ($recurso === 'citas') {
    $controller = new CitaController();

    switch ($requestMethod) {
        case 'GET':
            if ($id !== null && $id !== '') {
                $controller->show($id);
            }

            $controller->index();
            break;
        case 'POST':
            $controller->store();
            break;
        case 'PUT':
            if ($id !== null && $id !== '') {
                $controller->update($id);
                break;
            }

            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Falta el id'], JSON_UNESCAPED_UNICODE);
            break;
        case 'DELETE':
            if ($id !== null && $id !== '') {
                $controller->destroy($id);
                break;
            }

            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Falta el id'], JSON_UNESCAPED_UNICODE);
            break;
        default:
            http_response_code(405);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Metodo no permitido'], JSON_UNESCAPED_UNICODE);
            break;
    }

    exit;
}

http_response_code(404);
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'success' => false,
    'message' => 'Ruta no encontrada',
], JSON_UNESCAPED_UNICODE);
