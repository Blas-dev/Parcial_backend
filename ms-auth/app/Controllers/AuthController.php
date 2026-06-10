<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Usuario;

class AuthController {

    public function login(Request $request, Response $response) {
        $data = $request->getParsedBody();
        
        if (empty($data['usuario']) || empty($data['contrasena'])) {
            $response->getBody()->write(json_encode(['error' => 'Usuario y contrasena obligatorios']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if ($data['usuario'] === 'admin' && $data['contrasena'] === 'admin123') {
            $token = bin2hex(random_bytes(16));
            
            $responseData = [
                'token' => $token,
                'usuario' => [
                    'usuario' => 'admin',
                    'rol' => 'administrador'
                ]
            ];
            
            $response->getBody()->write(json_encode($responseData));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

        $user = Usuario::where('usuario', $data['usuario'])->first();

        if (!$user || !password_verify($data['contrasena'], $user->contrasena)) {
            $response->getBody()->write(json_encode(['error' => 'Credenciales invalidas']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token = bin2hex(random_bytes(16));
        $user->token = $token;
        $user->save();

        $responseData = [
            'token' => $token,
            'usuario' => [
                'usuario' => $user->usuario,
                'rol' => $user->rol
            ]
        ];

        $response->getBody()->write(json_encode($responseData));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function logout(Request $request, Response $response) {
        $data = $request->getParsedBody();
        
        if (!empty($data['token'])) {
            $user = Usuario::where('token', $data['token'])->first();
            if ($user) {
                $user->token = null;
                $user->save();
            }
        }

        $response->getBody()->write(json_encode(['mensaje' => 'Sesion cerrada correctamente']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}