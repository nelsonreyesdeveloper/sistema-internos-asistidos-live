<?php
$db = mysqli_connect($_ENV['HOST_DB'], $_ENV['USER_DB'], $_ENV['PASS_DB'], $_ENV['DB_DB']);

$db->set_charset("utf8");

if (!$db) {
    echo "Error: No se pudo conectar a MySQL.";
    echo "errno de depuración: " . mysqli_connect_errno();
    echo "error de depuración: " . mysqli_connect_error();
    exit;
}


