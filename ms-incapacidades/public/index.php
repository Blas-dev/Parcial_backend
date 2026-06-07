<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Inicializar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Inicializar base de datos con Eloquent
require __DIR__ . '/../app/Config/database.php';

// Instanciar Slim Framework
$app = AppFactory::create();

// Middlewares requeridos para el procesamiento JSON y ruteo
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Inyectar listado de endpoints
$routes = require __DIR__ . '/../app/Routes/routes.php';
$routes($app);

// Desplegar servicio
$app->run();