<?php

use Slim\App;
use App\Controllers\AuthController;

return function (App $app) {
    // Endpoints públicos
    $app->post('/api/login', [AuthController::class, 'login']);
    
    // Endpoint protegido (requiere enviar el token)
    $app->post('/api/logout', [AuthController::class, 'logout']);
};