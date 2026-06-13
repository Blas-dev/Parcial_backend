<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Empleado;

class EmpleadoController {

    // Listar empleados con filtros opcionales
    public function listar(Request $request, Response $response) {
        $params = $request->getQueryParams();
        $query  = Empleado::query();

        if (!empty($params['documento'])) $query->where('documento', 'like', '%' . $params['documento'] . '%');
        if (!empty($params['area']))      $query->where('area', $params['area']);
        if (!empty($params['estado']))    $query->where('estado', $params['estado']);

        $empleados = $query->orderBy('id', 'desc')->get();
        $response->getBody()->write(json_encode($empleados));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    // Obtener un empleado por ID
    public function obtener(Request $request, Response $response, $args) {
        $empleado = Empleado::find($args['id']);
        if (!$empleado) {
            $response->getBody()->write(json_encode(['error' => 'Empleado no encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        $response->getBody()->write(json_encode($empleado));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    // Crear empleado
    public function crear(Request $request, Response $response) {
        $data = $request->getParsedBody();

        $obligatorios = ['nombres', 'apellidos', 'documento', 'correo', 'telefono', 'cargo', 'area', 'fecha_ingreso'];
        foreach ($obligatorios as $campo) {
            if (empty($data[$campo])) {
                $response->getBody()->write(json_encode(['error' => "El campo $campo es obligatorio"]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        }

        // Validar documento duplicado
        if (Empleado::where('documento', $data['documento'])->exists()) {
            $response->getBody()->write(json_encode(['error' => 'Ya existe un empleado con ese documento']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
        }

        // Validar correo duplicado
        if (Empleado::where('correo', $data['correo'])->exists()) {
            $response->getBody()->write(json_encode(['error' => 'Ya existe un empleado con ese correo']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
        }

        // Validar fecha de ingreso
        if (!strtotime($data['fecha_ingreso'])) {
            $response->getBody()->write(json_encode(['error' => 'La fecha de ingreso no es valida']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $empleado = Empleado::create([
            'nombres'       => $data['nombres'],
            'apellidos'     => $data['apellidos'],
            'documento'     => $data['documento'],
            'correo'        => $data['correo'],
            'telefono'      => $data['telefono'],
            'cargo'         => $data['cargo'],
            'area'          => $data['area'],
            'fecha_ingreso' => $data['fecha_ingreso'],
            'estado'        => 'activo'
        ]);

        $response->getBody()->write(json_encode(['mensaje' => 'Empleado creado exitosamente', 'empleado' => $empleado]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    // Editar empleado
    public function editar(Request $request, Response $response, $args) {
        $empleado = Empleado::find($args['id']);
        if (!$empleado) {
            $response->getBody()->write(json_encode(['error' => 'Empleado no encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $data = $request->getParsedBody();

        // Validar documento duplicado (excluyendo al propio empleado)
        if (!empty($data['documento']) && $data['documento'] !== $empleado->documento) {
            if (Empleado::where('documento', $data['documento'])->where('id', '!=', $args['id'])->exists()) {
                $response->getBody()->write(json_encode(['error' => 'Ya existe un empleado con ese documento']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
            }
        }

        // Validar correo duplicado (excluyendo al propio empleado)
        if (!empty($data['correo']) && $data['correo'] !== $empleado->correo) {
            if (Empleado::where('correo', $data['correo'])->where('id', '!=', $args['id'])->exists()) {
                $response->getBody()->write(json_encode(['error' => 'Ya existe un empleado con ese correo']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
            }
        }

        $campos = ['nombres', 'apellidos', 'documento', 'correo', 'telefono', 'cargo', 'area', 'fecha_ingreso', 'estado'];
        foreach ($campos as $campo) {
            if (isset($data[$campo])) {
                $empleado->$campo = $data[$campo];
            }
        }
        $empleado->save();

        $response->getBody()->write(json_encode(['mensaje' => 'Empleado actualizado exitosamente', 'empleado' => $empleado]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    // Cambiar estado del empleado
    public function cambiarEstado(Request $request, Response $response, $args) {
        $empleado = Empleado::find($args['id']);
        if (!$empleado) {
            $response->getBody()->write(json_encode(['error' => 'Empleado no encontrado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $data = $request->getParsedBody();
        if (empty($data['estado']) || !in_array($data['estado'], ['activo', 'inactivo'])) {
            $response->getBody()->write(json_encode(['error' => 'Estado no valido. Use: activo o inactivo']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $empleado->estado = $data['estado'];
        $empleado->save();

        $response->getBody()->write(json_encode(['mensaje' => 'Estado actualizado', 'estado' => $empleado->estado]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
