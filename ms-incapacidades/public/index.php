<?php

require __DIR__ . '/../vendor/autoload.php';

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Cargar la configuración de la base de datos
require __DIR__ . '/../app/Config/database.php';

echo "Conexión a la base de datos configurada correctamente.";