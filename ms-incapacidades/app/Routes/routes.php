<?php

use Slim\App;
use App\Controllers\IncapacidadController;

return function (App $app) {
    // Endpoint para radicar solicitudes de incapacidad
    $app->post('/api/incapacidades', [IncapacidadController::class, 'registrar']);
};