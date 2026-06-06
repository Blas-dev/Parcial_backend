<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Cargar la configuración de la base de datos
require __DIR__ . '/../app/Config/database.php';

// Crear la instancia de la aplicación Slim
$app = AppFactory::create();

// Middleware para parsear el body de peticiones JSON
$app->addBodyParsingMiddleware();

// Middleware de enrutamiento y errores
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Registrar las rutas
$routes = require __DIR__ . '/../app/Routes/routes.php';
$routes($app);

// Ejecutar la aplicación
$app->run();