<?php

use Slim\App;
use App\Controllers\IncapacidadController;
use App\Middleware\AuthMiddleware;

return function (App $app) {
    $app->get('/api/incapacidades',                    [IncapacidadController::class, 'listar'])->add(AuthMiddleware::class);
    $app->get('/api/incapacidades/{id}',               [IncapacidadController::class, 'obtener'])->add(AuthMiddleware::class);
    $app->post('/api/incapacidades',                   [IncapacidadController::class, 'registrar'])->add(AuthMiddleware::class);
    $app->put('/api/incapacidades/{id}',               [IncapacidadController::class, 'editar'])->add(AuthMiddleware::class);
    $app->patch('/api/incapacidades/{id}/finalizar',   [IncapacidadController::class, 'finalizar'])->add(AuthMiddleware::class);
};
