<?php

use Slim\App;
use App\Controllers\SeguimientoController;
use App\Middleware\AuthMiddleware;

return function (App $app) {
    $app->post('/api/seguimientos',                                  [SeguimientoController::class, 'registrar'])->add(AuthMiddleware::class);
    $app->get('/api/seguimientos/incapacidad/{incapacidad_id}',      [SeguimientoController::class, 'consultarHistorial'])->add(AuthMiddleware::class);
};
