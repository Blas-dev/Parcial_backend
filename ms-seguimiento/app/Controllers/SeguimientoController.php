<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Seguimiento;
use Illuminate\Database\Capsule\Manager as Capsule;

class SeguimientoController {
    
    public function registrar(Request $request, Response $response) {
        $data = $request->getParsedBody();
        $campos = ['incapacidad_id', 'fecha', 'comentario', 'estado', 'usuario_responsable'];

        foreach ($campos as $campo) {
            if (empty($data[$campo])) {
                $response->getBody()->write(json_encode(['error' => "El campo $campo es obligatorio"]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
            }
        }

        $incapacidadExists = Capsule::connection('incapacidades')->table('incapacidades')->where('id', $data['incapacidad_id'])->exists();
        if (!$incapacidadExists) {
            $response->getBody()->write(json_encode(['error' => 'La incapacidad especificada no existe']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $usuarioExists = Capsule::connection('auth')->table('usuarios')->where('usuario', $data['usuario_responsable'])->exists();
        if (!$usuarioExists) {
            $response->getBody()->write(json_encode(['error' => 'El usuario responsable no existe']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        Capsule::connection('incapacidades')->table('incapacidades')->where('id', $data['incapacidad_id'])->update(['estado' => $data['estado']]);

        $seguimiento = Seguimiento::create([
            'incapacidad_id'      => $data['incapacidad_id'],
            'fecha'               => $data['fecha'],
            'comentario'          => $data['comentario'],
            'estado'              => $data['estado'],
            'usuario_responsable' => $data['usuario_responsable']
        ]);

        $response->getBody()->write(json_encode([
            'mensaje' => 'Seguimiento registrado exitosamente',
            'seguimiento' => $seguimiento
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function consultarHistorial(Request $request, Response $response, $args) {
        $incapacidad_id = $args['incapacidad_id'];
        $historial = Seguimiento::where('incapacidad_id', $incapacidad_id)->get();
        
        $response->getBody()->write(json_encode($historial));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}