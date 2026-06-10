<?php

use Slim\App;
use App\Controllers\SeguimientoController;

return function (App $app) {
    $app->post('/api/seguimientos', [SeguimientoController::class, 'registrar']);
    $app->get('/api/seguimientos/incapacidad/{incapacidad_id}', [SeguimientoController::class, 'consultarHistorial']);
};