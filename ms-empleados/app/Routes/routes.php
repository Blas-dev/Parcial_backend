<?php

use Slim\App;
use App\Controllers\EmpleadoController;

return function (App $app) {
    $app->get('/api/empleados', [EmpleadoController::class, 'listar']);
    $app->get('/api/empleados/{id}', [EmpleadoController::class, 'obtener']);
    $app->post('/api/empleados', [EmpleadoController::class, 'crear']);
    $app->put('/api/empleados/{id}', [EmpleadoController::class, 'actualizar']);
    $app->delete('/api/empleados/{id}', [EmpleadoController::class, 'eliminar']);
    $app->get('/api/empleados/buscar/cedula/{cedula}', [EmpleadoController::class, 'buscarPorCedula']);
};