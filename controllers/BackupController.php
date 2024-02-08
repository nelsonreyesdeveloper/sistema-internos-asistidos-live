<?php

namespace Controllers;

use Ifsnop\Mysqldump\Mysqldump;
use Ifsnop\Mysqldump as IMysqldump;

class BackupController
{
    public static function backup()
    {

        try {
            $dbHost = $_ENV['DB_HOST'] ?? null;
            $dbUser = $_ENV['DB_USER'] ?? null;
            $dbPass = $_ENV['DB_PASS'] ?? null;
            $dbName = $_ENV['DB_NAME'] ?? null;

            // Verificar que las variables de entorno estén definidas
            if (empty($dbHost) || empty($dbUser) || is_null($dbName)) {
                throw new \Exception('Variables de entorno no definidas correctamente.');
            }

            $dsn = "mysql:host=$dbHost;dbname=$dbName";
            $dump = new Mysqldump($dsn, $dbUser, $dbPass);

            // Configurar las cabeceras para indicar que el contenido es SQL

            /* fecha actual */

            $date = date('Y-m-d');
            header('Content-Type: application/sql');
            header('Content-Disposition: attachment; filename="sistema-internos-asistidos-' . $date . '.sql"'); // Opcional: para descargar el archivo

            // Iniciar el proceso de volcado e imprimir directamente en el navegador
            $dump->start('php://output');
        } catch (\Exception $e) {
            echo 'mysqldump-php error: ' . $e->getMessage();
        }
    }
}
