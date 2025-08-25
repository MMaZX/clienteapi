<?php
require_once 'src/config.php';
require_once 'src/database.php';
require_once 'src/migration.php';
require_once 'src/connection-api.php';

try {
    $migration = new MigrationDatabase();
    $migration->createDocumento();
    $dni = $_REQUEST['documento'] ?? null;
    if (!$dni) {
        throw new Exception("El parámetro 'documento' es requerido", 400);
    }

    $respuesta = [];

    $db = new Database();
    $result = $db->prepare("SELECT * FROM documento WHERE numero = ? AND tipo_documento = 'DNI'", [$dni]);
    if ($result->isEmpty()) {
        // Consultar API para DNI
        $api = new ConnectionApiPeru();
        $data = $api->fetchDni($dni);
        try {
            $db->prepare(
                "INSERT INTO documento (tipo_documento, numero, denominacion, direccion, nombres, apellidos, ubigeo) VALUES (?, ?, ?, ?, ?, ?, ?)",
                [
                    'DNI',
                    $data['dni'],
                    $data['nombres'] . ' ' . $data['apellidoPaterno'] . ' ' . $data['apellidoMaterno'],
                    '', // dirección no disponible para DNI
                    $data['nombres'],
                    $data['apellidoPaterno'] . ' ' . $data['apellidoMaterno'],
                    ($data['dni'] . '-' . $data['codVerifica']) ?? '' // Ubigeo no disponible para DNI
                ]
            );
        } catch (Exception $e) {
            // var_export($e->getMessage());
            // Si hay un error al insertar, simplemente retornamos la data obtenida
        } finally {
            $respuesta = [
                "dni" => $data['dni'] ?? '',
                "nombres" => $data['nombres'] ?? '',
                "apellidos" => ($data['apellidoPaterno'] . ' ' . $data['apellidoMaterno']) ?? '',
                "direccion" => $data['direccion'] ?? '',
                "email" => $data['email'] ?? ''
            ];
        }
    } else {
        //IMPRIMIMOS LA RESPUESTA
        $persona = $result->first();
        $respuesta = [
            "dni" => $persona['numero'] ?? '',
            "nombres" => $persona['nombres'] ?? '',
            "apellidos" => $persona['apellidos'] ?? '',
            "direccion" => $persona['direccion'] ?? '',
            "email" => $persona['email'] ?? ''
        ];
    }
    return sendResponse($respuesta);
} catch (Exception $e) {
    sendError($e->getMessage(), 500);
}
