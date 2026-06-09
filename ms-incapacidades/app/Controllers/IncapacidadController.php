<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Incapacidad;
use Illuminate\Database\Capsule\Manager as Capsule;
use DateTime;

class IncapacidadController {
    
    public function listar(Request $request, Response $response) {
        $params = $request->getQueryParams();
        $query = Incapacidad::query();

        if (!empty($params['empleado_id'])) {
            $query->where('empleado_id', $params['empleado_id']);
        }
        
        if (!empty($params['estado'])) {
            $query->where('estado', $params['estado']);
        }
        
        if (!empty($params['tipo'])) {
            $query->where('tipo', $params['tipo']);
        }

        $incapacidades = $query->get();
        
        $response->getBody()->write(json_encode($incapacidades));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function obtenerPorId(Request $request, Response $response, $args) {
        $id = $args['id'];
        $incapacidad = Incapacidad::find($id);

        if (!$incapacidad) {
            $response->getBody()->write(json_encode(['error' => 'Incapacidad no encontrada']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode($incapacidad));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function registrar(Request $request, Response $response) {
        $data = $request->getParsedBody();

        $camposObligatorios = ['empleado_id', 'fecha_inicio', 'fecha_fin', 'tipo', 'diagnostico_general', 'entidad_medica'];
        foreach ($camposObligatorios as $campo) {
            if (empty($data[$campo])) {
                $response->getBody()->write(json_encode(['error' => "El campo $campo es obligatorio"]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        }

        $empleado_id = $data['empleado_id'];
        $fecha_inicio = $data['fecha_inicio'];
        $fecha_fin = $data['fecha_fin'];

        $dateInicio = new DateTime($fecha_inicio);
        $dateFin = new DateTime($fecha_fin);

        if ($dateInicio > $dateFin) {
            $response->getBody()->write(json_encode(['error' => 'La fecha de inicio no puede ser posterior a la fecha de fin']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $empleadoExists = Capsule::connection('empleados')->table('empleados')->where('id', $empleado_id)->exists();
        if (!$empleadoExists) {
            $response->getBody()->write(json_encode(['error' => 'El empleado especificado no esta registrado en el sistema']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $cruce = Incapacidad::where('empleado_id', $empleado_id)
            ->where(function($query) use ($fecha_inicio, $fecha_fin) {
                $query->whereBetween('fecha_inicio', [$fecha_inicio, $fecha_fin])
                      ->orWhereBetween('fecha_fin', [$fecha_inicio, $fecha_fin])
                      ->orWhere(function($q) use ($fecha_inicio, $fecha_fin) {
                          $q->where('fecha_inicio', '<=', $fecha_inicio)
                            ->where('fecha_fin', '>=', $fecha_fin);
                      });
            })->first();

        if ($cruce) {
            $response->getBody()->write(json_encode(['error' => 'El empleado ya cuenta con una incapacidad activa o registrada que se cruza con este rango de fechas']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $intervalo = $dateInicio->diff($dateFin);
        $diasCalculados = $intervalo->days + 1;

        $incapacidad = Incapacidad::create([
            'empleado_id'         => $empleado_id,
            'fecha_inicio'        => $fecha_inicio,
            'fecha_fin'           => $fecha_fin,
            'tipo'                => $data['tipo'],
            'diagnostico_general' => $data['diagnostico_general'],
            'entidad_medica'      => $data['entidad_medica'],
            'observaciones'       => $data['observaciones'] ?? null,
            'dias_incapacidad'    => $diasCalculados,
            'estado'              => 'en_revision'
        ]);

        $response->getBody()->write(json_encode([
            'mensaje'     => 'Incapacidad registrada exitosamente',
            'incapacidad' => $incapacidad
        ]));
        
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function editar(Request $request, Response $response, $args) {
        $id = $args['id'];
        $incapacidad = Incapacidad::find($id);

        if (!$incapacidad) {
            $response->getBody()->write(json_encode(['error' => 'Incapacidad no encontrada']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $data = $request->getParsedBody();

        if (!empty($data['fecha_inicio']) || !empty($data['fecha_fin'])) {
            $fecha_inicio = $data['fecha_inicio'] ?? $incapacidad->fecha_inicio;
            $fecha_fin = $data['fecha_fin'] ?? $incapacidad->fecha_fin;

            $dateInicio = new DateTime($fecha_inicio);
            $dateFin = new DateTime($fecha_fin);

            if ($dateInicio > $dateFin) {
                $response->getBody()->write(json_encode(['error' => 'La fecha de inicio no puede ser posterior a la fecha de fin']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $cruce = Incapacidad::where('empleado_id', $incapacidad->empleado_id)
                ->where('id', '!=', $id)
                ->where(function($query) use ($fecha_inicio, $fecha_fin) {
                    $query->whereBetween('fecha_inicio', [$fecha_inicio, $fecha_fin])
                          ->orWhereBetween('fecha_fin', [$fecha_inicio, $fecha_fin])
                          ->orWhere(function($q) use ($fecha_inicio, $fecha_fin) {
                              $q->where('fecha_inicio', '<=', $fecha_inicio)
                                ->where('fecha_fin', '>=', $fecha_fin);
                          });
                })->first();

            if ($cruce) {
                $response->getBody()->write(json_encode(['error' => 'El empleado ya cuenta con una incapacidad activa o registrada que se cruza con este rango de fechas']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }

            $intervalo = $dateInicio->diff($dateFin);
            $incapacidad->dias_incapacidad = $intervalo->days + 1;
            $incapacidad->fecha_inicio = $fecha_inicio;
            $incapacidad->fecha_fin = $fecha_fin;
        }

        if (isset($data['observaciones'])) {
            $incapacidad->observaciones = $data['observaciones'];
        }

        if (!empty($data['estado'])) {
            $incapacidad->estado = $data['estado'];
        }

        $incapacidad->save();

        $response->getBody()->write(json_encode([
            'mensaje' => 'Incapacidad actualizada exitosamente',
            'incapacidad' => $incapacidad
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function finalizar(Request $request, Response $response, $args) {
        $id = $args['id'];
        $incapacidad = Incapacidad::find($id);

        if (!$incapacidad) {
            $response->getBody()->write(json_encode(['error' => 'Incapacidad no encontrada']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $incapacidad->estado = 'finalizada';
        $incapacidad->save();

        $response->getBody()->write(json_encode([
            'mensaje' => 'Incapacidad finalizada exitosamente',
            'incapacidad' => $incapacidad
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}