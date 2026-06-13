-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-06-2026 a las 13:13:50
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `db_incapacidades`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `incapacidades`
--

CREATE TABLE `incapacidades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `empleado_id` bigint(20) UNSIGNED NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `tipo` enum('enfermedad_general','accidente_laboral','licencia_medica','incapacidad_temporal') NOT NULL,
  `diagnostico_general` text NOT NULL,
  `entidad_medica` varchar(150) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `dias_incapacidad` int(11) NOT NULL,
  `estado` enum('registrada','en_revision','aprobada','rechazada','finalizada') DEFAULT 'registrada',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `incapacidades`
--

INSERT INTO `incapacidades` (`id`, `empleado_id`, `fecha_inicio`, `fecha_fin`, `tipo`, `diagnostico_general`, `entidad_medica`, `observaciones`, `dias_incapacidad`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-06-01', '2026-06-05', 'enfermedad_general', 'Infeccion respiratoria', 'Clinica Central', 'Reposo medico durante cinco dias', 5, 'finalizada', '2026-06-02 23:53:37', '2026-06-02 23:53:37'),
(2, 2, '2026-06-08', '2026-06-10', 'licencia_medica', 'Control medico general', 'Hospital Regional', 'Seguimiento medico preventivo', 3, 'finalizada', '2026-06-02 23:53:37', '2026-06-13 11:51:40'),
(3, 3, '2026-06-18', '2026-06-23', 'accidente_laboral', 'Derrame Cerebral', 'SANITAS', 'khb', 6, 'finalizada', '2026-06-13 10:54:51', '2026-06-13 11:53:25'),
(4, 3, '2026-06-08', '2026-06-10', 'enfermedad_general', 'uoh', 'ml', 'jbkjk', 3, 'registrada', '2026-06-13 11:56:48', '2026-06-13 11:56:48');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `incapacidades`
--
ALTER TABLE `incapacidades`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `incapacidades`
--
ALTER TABLE `incapacidades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
