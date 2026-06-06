<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Empleado;

class EmpleadoController {
    
    // Listar empleados con soporte para filtros de búsqueda
    public function listar(Request $request, Response $response) {
        $params = $request->getQueryParams();
        $query = Empleado::query();

        // Aplicar filtros si vienen en la URL (ej: ?documento=123 o ?area=Sistemas)
        if (!empty($params['documento'])) $query->where('documento', $params['documento']);
        if (!empty($params['area'])) $query->where('area', $params['area']);
        if (!empty($params['estado'])) $query->where('estado', $params['estado']);

        $empleados = $query->get();
        $response->getBody()->write(json_encode($empleados));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    // Crear un nuevo empleado (Se mantiene igual al commit 4)
    public function crear(Request $request, Response $response) {
        $data = $request->getParsedBody();
        // ... (Tu código actual de crear, omitido por brevedad, asume que está intacto. 
        // Pega aquí la función "crear" exacta del paso anterior para no perderla).
    }

    // Editar un empleado
    public function editar(Request $request, Response $response, $args) {
        $id = $args['id'];
        $data = $request->getParsedBody();
        
        $empleado = Empleado::find($id);
        if (!$empleado) {
            $response->getBody()->write(json_encode(['error' => 'Empleado no encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        
        // Actualizar datos
        $empleado->update($data);

        $response->getBody()->write(json_encode(['mensaje' => 'Empleado actualizado', 'empleado' => $empleado]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    // Cambiar estado del empleado
    public function cambiarEstado(Request $request, Response $response, $args) {
        $id = $args['id'];
        $data = $request->getParsedBody();
        
        $empleado = Empleado::find($id);
        if (!$empleado) {
            $response->getBody()->write(json_encode(['error' => 'Empleado no encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        
        if (isset($data['estado']) && in_array($data['estado'], ['activo', 'inactivo'])) {
            $empleado->estado = $data['estado'];
            $empleado->save();
            $response->getBody()->write(json_encode(['mensaje' => 'Estado actualizado', 'estado' => $empleado->estado]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

        $response->getBody()->write(json_encode(['error' => 'Estado no valido']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
}