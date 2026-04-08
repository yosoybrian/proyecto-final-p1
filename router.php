<?php

/**
 * Router para servidor de desarrollo PHP
 * Uso: php -S localhost:8000 router.php
 */

// Si la petición es para un archivo estático que existe, servirlo directamente
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Si es una petición a /api/*, redirigir a api.php
if (strpos($path, '/api') === 0) {
    $_SERVER['SCRIPT_NAME'] = '/api.php';
    require __DIR__ . '/public/api.php';
    exit;
}

// Si el archivo existe, servirlo
if ($path !== '/' && file_exists(__DIR__ . '/public' . $path)) {
    return false;
}

// En caso contrario, servir index.php
require __DIR__ . '/public/index.php';
