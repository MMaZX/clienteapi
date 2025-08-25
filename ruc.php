<?php
require_once 'src/config.php';
require_once 'src/database.php';
require_once 'src/migration.php';
require_once 'src/connection-api.php';

try {
    $migration = new MigrationDatabase();
    $migration->createDocumento();
    $ruc = $_REQUEST['documento'] ?? null;
    if (!$ruc) {
        throw new Exception("El parámetro 'documento' es requerido", 400);
    }

    $respuesta = [];

    $db = new Database();
    $result = $db->prepare("SELECT * FROM documento WHERE numero = ? AND tipo_documento = 'RUC'", [$ruc]);
    if ($result->isEmpty()) {
        // VAMOS A HACER LA CONSULTA A SUNAT
        $api = new ConnectionApiPeru();
        $data = $api->fetchRuc($ruc);
        // var_export($data);
        try {
            $db->prepare("INSERT INTO documento (tipo_documento, numero, denominacion, direccion, ubigeo) VALUES (?, ?, ?, ?, ?)", [
                'RUC',
                $data['ruc'],
                $data['razonSocial'],
                $data['direccion'],
                $data['ubigeo']
            ]);
        } catch (Exception $e) {
            // var_export($e->getMessage());
            // Si hay un error al insertar, simplemente retornamos la data obtenida
        } finally {
            $respuesta = [
                "ruc" => $data['ruc'],
                "razon_social" => $data['razonSocial'],
                "direccion" => $data['direccion'],
            ];
        }
    } else {
        //IMPRIMIMOS LA RESPUESTA
        $juridico = $result->first();
        $respuesta = [
            "ruc" => $juridico['numero'],
            "razon_social" => $juridico['denominacion'],
            "direccion" => $juridico['direccion'],
        ];
    }
    return sendResponse($respuesta);
} catch (Exception $e) {
    sendError($e->getMessage(), 500);
}
