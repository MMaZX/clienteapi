<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 0);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

date_default_timezone_set('America/Lima');

// Deshabilitar exposición de versión PHP
header_remove('X-Powered-By');


// Protección básica contra XSS
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Función para enviar respuesta JSON
function sendResponse($data, $statusCode = 200)
{
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Función para manejar errores
function sendError($message, $statusCode = 400)
{
    sendResponse([
        'statusCode' => $statusCode,
        'message' => $message
    ], $statusCode);
}

// Función para obtener parámetro de la URL o POST
function getParameter($key, $required = true, $default = null)
{
    $value = $_GET[$key] ?? $_POST[$key] ?? $default;

    if ($required && ($value === null || $value === '')) {
        sendError("Parámetro '$key' es requerido");
    }

    return $value;
}

// Función para validar DNI
function validateDni($dni)
{
    return preg_match('/^\d{8}$/', $dni);
}

// Función para validar RUC
function validateRuc($ruc)
{
    return preg_match('/^\d{11}$/', $ruc);
}
