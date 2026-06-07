<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Incapacidad;
use Illuminate\Database\Capsule\Manager as Capsule;
use DateTime;

class IncapacidadController {
    
    // Registrar una nueva incapacidad médica
    public function registrar(Request $request, Response $response) {
        $data = $request->getParsedBody();

        // 1. Validar campos obligatorios
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

        // 2. Validar coherencia cronológica de las fechas
        $dateInicio = new DateTime($fecha_inicio);
        $dateFin = new DateTime($fecha_fin);

        if ($dateInicio > $dateFin) {
            $response->getBody()->write(json_encode(['error' => 'La fecha de inicio no puede ser posterior a la fecha de fin']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // 3. Verificar la existencia del empleado en db_empleados
        $empleadoExists = Capsule::connection('empleados')->table('empleados')->where('id', $empleado_id)->exists();
        if (!$empleadoExists) {
            $response->getBody()->write(json_encode(['error' => 'El empleado especificado no esta registrado en el sistema']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // 4. Control de superposición de fechas para el mismo empleado
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

        // 5. Cálculo automático de los días de incapacidad (incluyendo el día inicial)
        $intervalo = $dateInicio->diff($dateFin);
        $diasCalculados = $intervalo->days + 1;

        // 6. Almacenar el registro en el sistema
        $incapacidad = Incapacidad::create([
            'empleado_id'         => $empleado_id,
            'fecha_inicio'        => $fecha_inicio,
            'fecha_fin'           => $fecha_fin,
            'tipo'                => $data['tipo'],
            'diagnostico_general' => $data['diagnostico_general'],
            'entidad_medica'      => $data['entidad_medica'],
            'observaciones'       => $data['observaciones'] ?? null,
            'dias_incapacidad'    => $diasCalculados,
            'estado'              => 'en_revision' // Estado base inicial según requerimientos
        ]);

        $response->getBody()->write(json_encode([
            'mensaje'     => 'Incapacidad registrada exitosamente',
            'incapacidad' => $incapacidad
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}