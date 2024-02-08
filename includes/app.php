<?php

// Conectarnos a la base de datos
use Model\ActiveRecord;
require __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = Dotenv::createMutable(__DIR__);
$dotenv->safeLoad();


require 'funciones.php';
require 'database.php';




ActiveRecord::setDB($db);
