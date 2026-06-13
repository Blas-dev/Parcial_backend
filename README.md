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



# Proyecto Microservicios - Gestión de Incapacidades

Instrucciones completas para configurar y ejecutar la aplicación en un equipo nuevo.

## Requisitos Previos

- XAMPP (PHP 8.0+ y MySQL)
- Composer
- Git
- Visual Studio Code + extensión Live Server

---

## 1. Preparar las Bases de Datos

1. Abre **XAMPP Control Panel** e inicia **Apache** y **MySQL**.
2. Ingresa a `http://localhost/phpmyadmin`.
3. Crea las siguientes **4 bases de datos** (una por una, clic en "Nueva"):

| Base de datos      | Microservicio     |
|--------------------|-------------------|
| `db_auth`          | ms-auth           |
| `db_empleados`     | ms-empleados      |
| `db_incapacidades` | ms-incapacidades  |
| `db_seguimiento`   | ms-seguimiento    |

4. Por cada base de datos, seleccionala, ve a la pestaña **Importar** y sube el archivo `.sql` correspondiente de la carpeta `Docs/`.

---

## 2. Instalar Dependencias del Backend

Abre **4 terminales de PowerShell** (o pestañas) dentro de la carpeta del proyecto y ejecuta en cada una:

**Terminal 1 — ms-auth**
```powershell
cd ms-auth
composer install
php -S localhost:8081 -t public
```

**Terminal 2 — ms-empleados**
```powershell
cd ms-empleados
composer install
php -S localhost:8082 -t public
```

**Terminal 3 — ms-incapacidades**
```powershell
cd ms-incapacidades
composer install
php -S localhost:8083 -t public
```

**Terminal 4 — ms-seguimiento**
```powershell
cd ms-seguimiento
composer install
php -S localhost:8084 -t public
```

> Si `vendor/` ya existe en la carpeta, puedes omitir `composer install` y ejecutar directamente `php -S ...`

---

## 3. Abrir el Frontend

1. Abre la carpeta **Parcial_Frontend** en Visual Studio Code.
2. Haz clic derecho sobre `index.html` → **Open with Live Server**.
3. La aplicación abre en `http://127.0.0.1:5500`.

---

## Credenciales por defecto

| Usuario | Contraseña | Rol           |
|---------|------------|---------------|
| admin   | admin123   | administrador |

---

## Puertos del sistema

| Microservicio     | Puerto |
|-------------------|--------|
| ms-auth           | 8081   |
| ms-empleados      | 8082   |
| ms-incapacidades  | 8083   |
| ms-seguimiento    | 8084   |
| Frontend          | 5500   |

ARCHIVOS .env

ms-auth

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_auth
DB_USERNAME=root
DB_PASSWORD=

ms-empleados

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_empleados
DB_USERNAME=root
DB_PASSWORD=
DB_AUTH_DATABASE=db_auth

ms-incapacidades

DB_HOST=127.0.0.1
DB_DATABASE=db_incapacidades
DB_USERNAME=root
DB_PASSWORD=
DB_EMPLEADOS_DATABASE=db_empleados
DB_AUTH_DATABASE=db_auth

ms-seguimiento

DB_HOST=127.0.0.1
DB_DATABASE=db_seguimiento
DB_USERNAME=root
DB_PASSWORD=
DB_INCAPACIDADES_DATABASE=db_incapacidades
DB_AUTH_DATABASE=db_auth