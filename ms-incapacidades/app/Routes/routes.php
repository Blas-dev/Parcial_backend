<?php

use Slim\App;
use App\Controllers\IncapacidadController;

return function (App $app) {
    $app->get('/api/incapacidades', [IncapacidadController::class, 'listar']);
    $app->get('/api/incapacidades/{id}', [IncapacidadController::class, 'obtenerPorId']);
    $app->post('/api/incapacidades', [IncapacidadController::class, 'registrar']);
    $app->put('/api/incapacidades/{id}', [IncapacidadController::class, 'editar']);
    $app->patch('/api/incapacidades/{id}/finalizar', [IncapacidadController::class, 'finalizar']);
};