<?php

use Slim\App;
use App\Controllers\IncapacidadController;

return function (App $app) {
    $app->get('/api/incapacidades', [IncapacidadController::class, 'listar']);
    $app->get('/api/incapacidades/{id}', [IncapacidadController::class, 'obtener']);
    $app->post('/api/incapacidades', [IncapacidadController::class, 'crear']);
    $app->get('/api/incapacidades/empleado/{empleado_id}', [IncapacidadController::class, 'listarPorEmpleado']);
};