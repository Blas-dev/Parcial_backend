<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Usuario;

class AuthController {
    
    public function login(Request $request, Response $response) {
        $data = $request->getParsedBody();
        $identificador = $data['identificador'] ?? ''; // Puede ser usuario o correo
        $contrasena = $data['contrasena'] ?? '';

        // Buscar el usuario por nombre de usuario o correo electrónico
        $usuario = Usuario::where('usuario', $identificador)
                          ->orWhere('correo', $identificador)
                          ->first();

        // Nota: Según el script SQL proporcionado, las contraseñas están en texto plano (ej: 'admin123').
        // En un entorno real se usaría password_verify(). Aquí validamos texto plano.
        if ($usuario && $usuario->contrasena === $contrasena) {
            
            if ($usuario->estado !== 'activo') {
                $response->getBody()->write(json_encode(['error' => 'El usuario esta inactivo']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(403);
            }

            // Generar un token simple
            $token = bin2hex(random_bytes(32));
            
            // Actualizar estado de sesión
            $usuario->token = $token;
            $usuario->sesion_activa = true;
            $usuario->save();

            $response->getBody()->write(json_encode([
                'mensaje' => 'Login exitoso',
                'token' => $token,
                'usuario' => [
                    'nombre' => $usuario->nombre,
                    'rol' => $usuario->rol
                ]
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

        $response->getBody()->write(json_encode(['error' => 'Credenciales incorrectas']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }

    public function logout(Request $request, Response $response) {
        // Extraer el token de las cabeceras (Authorization: Bearer <token>)
        $header = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $header);

        if (empty($token)) {
            $response->getBody()->write(json_encode(['error' => 'Token no proporcionado']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Buscar al usuario dueño del token
        $usuario = Usuario::where('token', $token)->first();

        if ($usuario) {
            // Invalidar sesión
            $usuario->token = null;
            $usuario->sesion_activa = false;
            $usuario->save();

            $response->getBody()->write(json_encode(['mensaje' => 'Sesion cerrada exitosamente']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

        $response->getBody()->write(json_encode(['error' => 'Token invalido o sesion ya cerrada']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
    }
}