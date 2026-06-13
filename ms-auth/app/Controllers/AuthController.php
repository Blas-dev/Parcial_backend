<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Usuario;

class AuthController {

    public function login(Request $request, Response $response) {
        $data = $request->getParsedBody();

        if (empty($data['usuario']) || empty($data['contrasena'])) {
            $response->getBody()->write(json_encode(['error' => 'Usuario y contrasena son obligatorios']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $user = Usuario::where('usuario', $data['usuario'])
                    ->orWhere('correo', $data['usuario'])
                    ->first();

        if (!$user || ($user->contrasena !== $data['contrasena'] && !password_verify($data['contrasena'], $user->contrasena))) {
            $response->getBody()->write(json_encode(['error' => 'Credenciales invalidas']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        if ($user->estado === 'inactivo') {
            $response->getBody()->write(json_encode(['error' => 'Usuario inactivo']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
        }

        $token = bin2hex(random_bytes(32));
        $user->token = $token;
        $user->sesion_activa = true;
        $user->save();

        $response->getBody()->write(json_encode([
            'token'   => $token,
            'usuario' => [
                'id'      => $user->id,
                'usuario' => $user->usuario,
                'nombre'  => $user->nombre,
                'rol'     => $user->rol
            ]
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function logout(Request $request, Response $response) {
        $data = $request->getParsedBody();
        $token = $data['token'] ?? null;

        if (empty($token)) {
            $response->getBody()->write(json_encode(['error' => 'Token no proporcionado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $user = Usuario::where('token', $token)->first();

        if (!$user) {
            $response->getBody()->write(json_encode(['error' => 'Token invalido']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $user->token = null;
        $user->sesion_activa = false;
        $user->save();

        $response->getBody()->write(json_encode(['mensaje' => 'Sesion cerrada correctamente']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
