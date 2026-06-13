# Proyecto Microservicios - Gestión de Incapacidades (App3)

Este documento contiene las instrucciones paso a paso para configurar, instalar y ejecutar la aplicación web de Gestión de Incapacidades en un entorno local nuevo.

## 1. Requisitos Previos

Antes de iniciar, asegúrate de tener instalados los siguientes programas en el equipo:

* XAMPP (con PHP 8.0 o superior y MySQL)
* Composer (Gestor de dependencias de PHP)
* Git (Para clonar los repositorios)
* Visual Studio Code (Con la extensión Live Server instalada)

## 2. Preparación de la Base de Datos

1. Abre XAMPP Control Panel e inicia los módulos de **Apache** y **MySQL**.
2. Ingresa a `http://localhost/phpmyadmin` desde el navegador.
3. Crea las bases de datos necesarias para cada microservicio.
4. Importa los archivos `.sql` proporcionados en la carpeta del proyecto dentro de sus respectivas bases de datos.

## 3. Instalación de Dependencias del Backend

Como el proyecto se ejecutará en un equipo nuevo, es obligatorio instalar las librerías de Slim Framework y Eloquent. Abre una terminal de PowerShell y ejecuta los siguientes comandos para cada microservicio:

```powershell
cd ms-auth
composer install

cd ../ms-empleados
composer install

cd ../ms-incapacidades
composer install

cd ../ms-seguimiento
composer install

cd ms-auth
php -S localhost:8081 -t public

cd ms-empleados
php -S localhost:8082 -t public

cd ms-incapacidades
php -S localhost:8083 -t public

cd ms-seguimiento
php -S localhost:8084 -t public