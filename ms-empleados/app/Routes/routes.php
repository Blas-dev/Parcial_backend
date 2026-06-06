<?php

use Slim\App;
use App\Controllers\EmpleadoController;
use App\Middleware\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    
    // Agrupamos todas las rutas de la API bajo el middleware de autenticación
    $app->group('/api', function (RouteCollectorProxy $group) {
        $group->get('/empleados', [EmpleadoController::class, 'listar']);
        $group->post('/empleados', [EmpleadoController::class, 'crear']);
        $group->put('/empleados/{id}', [EmpleadoController::class, 'editar']);
        $group->patch('/empleados/{id}/estado', [EmpleadoController::class, 'cambiarEstado']);
    })->add(new AuthMiddleware());

};