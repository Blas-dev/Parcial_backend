<?php

use Slim\App;
use App\Controllers\AuthController;

return function (App $app) {
    $app->post('/api/login', [AuthController::class, 'login']);
    $app->post('/api/logout', [AuthController::class, 'logout']);
};