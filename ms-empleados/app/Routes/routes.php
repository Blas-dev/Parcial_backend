<?php

use Slim\App;
use App\Controllers\EmpleadoController;
use App\Middleware\AuthMiddleware;

return function (App $app) {
    $app->get('/api/empleados',             [EmpleadoController::class, 'listar'])->add(AuthMiddleware::class);
    $app->get('/api/empleados/{id}',        [EmpleadoController::class, 'obtener'])->add(AuthMiddleware::class);
    $app->post('/api/empleados',            [EmpleadoController::class, 'crear'])->add(AuthMiddleware::class);
    $app->put('/api/empleados/{id}',        [EmpleadoController::class, 'editar'])->add(AuthMiddleware::class);
    $app->patch('/api/empleados/{id}/estado', [EmpleadoController::class, 'cambiarEstado'])->add(AuthMiddleware::class);
};
