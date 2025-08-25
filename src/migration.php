<?php
require_once 'src/database.php';

class MigrationDatabase
{
    public function createDocumento()
    {
        $db = new Database();
        $db->prepare("CREATE TABLE IF NOT EXISTS documento (
            id BIGINT AUTO_INCREMENT PRIMARY KEY,
            tipo_documento ENUM('DNI', 'RUC', 'PASAPORTE', 'CE') NOT NULL,
            numero VARCHAR(20) NOT NULL,
            denominacion VARCHAR(255) NOT NULL,
            nombres VARCHAR(255) NULL,
            apellidos VARCHAR(255) NULL,
            direccion VARCHAR(200),
            telefono VARCHAR(20),
            email VARCHAR(100),
            ubigeo VARCHAR(100),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            CONSTRAINT uq_tipo_numero UNIQUE (tipo_documento, numero),
            INDEX idx_tipo_documento (tipo_documento),
            INDEX idx_numero (numero),
            FULLTEXT idx_denominacion_full (denominacion)
        )")->close();
    }
}