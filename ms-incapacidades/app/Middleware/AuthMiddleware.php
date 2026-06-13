<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Illuminate\Database\Capsule\Manager as Capsule;

class AuthMiddleware {
    public function __invoke(Request $request, RequestHandler $handler): Response {
        $header = $request->getHeaderLine('Authorization');
        $token  = str_replace('Bearer ', '', $header);

        if (empty($token)) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Acceso denegado. Token no proporcionado.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $usuario = Capsule::connection('auth')->table('usuarios')
            ->where('token', $token)
            ->where('sesion_activa', 1)
            ->where('estado', 'activo')
            ->first();

        if (!$usuario) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Token invalido o sesion expirada.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        return $handler->handle($request);
    }
}
