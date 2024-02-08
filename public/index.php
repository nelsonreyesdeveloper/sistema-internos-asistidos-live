<?php


require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\IndexController;
use Controllers\BackupController;
use Controllers\ExportarController;
use Controllers\ExpedienteController;
use Controllers\LoginController;
use Controllers\UserController;

$router = new Router();


$router->get('/expedientes', [ExpedienteController::class, 'index']);

$router->post('/expedientes-delete', [ExpedienteController::class, 'destroy']);

$router->post('/expedientes-update', [ExpedienteController::class, 'update']);

$router->post('/expedientes-create', [ExpedienteController::class, 'create']);

$router->post('/expedientes/archivar', [ExpedienteController::class, 'status']);

$router->post('/exportarpdf', [ExportarController::class, 'pdf']);

$router->get('/users', [IndexController::class, 'busqueda']);

$router->get('/users-table', [UserController::class, 'index']);

$router->post('/users-create', [UserController::class, 'store']);

$router->post('/users-update', [UserController::class, 'update']);

$router->get('/backup', [BackupController::class, 'backup']);

$router->get('/dashboard', [IndexController::class, 'index']);

$router->get('/', [LoginController::class, 'login']);
$router->post('/', [LoginController::class, 'login']);

$router->post('/logout', [LoginController::class, 'logout']);



$router->comprobarRutas();
