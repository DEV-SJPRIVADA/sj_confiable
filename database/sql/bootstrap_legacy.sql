-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 14-10-2025 a las 14:18:07
-- Versión del servidor: 11.8.3-MariaDB-log
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Base de datos: se usa la conexión de Laravel (p. ej. sj_confiable). No CREATE/USE aquí.

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos`
--

CREATE TABLE `documentos` (
  `id` int(11) NOT NULL,
  `solicitud_id` int(11) NOT NULL,
  `nombre_documento` varchar(255) NOT NULL,
  `ruta_documento` varchar(255) NOT NULL,
  `fecha_subida` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos_respuesta`
--

CREATE TABLE `documentos_respuesta` (
  `id` int(11) NOT NULL,
  `respuesta_madre_id` int(11) DEFAULT NULL,
  `nombre_documentoResp` varchar(255) NOT NULL,
  `ruta_documentoResp` varchar(255) NOT NULL,
  `fecha_subidaResp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `documentos_respuesta`
--

INSERT INTO `documentos_respuesta` (`id`, `respuesta_madre_id`, `nombre_documentoResp`, `ruta_documentoResp`, `fecha_subidaResp`) VALUES
(2, 2, 'SEGURIDAD SJ - 06102025 - DIEGO ANDRES ROSAS RAMÍREZ - PRE.pdf', 'resp_68e50e45e3fa42.13658242.pdf', '2025-10-07 12:57:41'),
(3, 3, 'OSCAR IVAN NIÑO CARDENAS.docx.pdf', 'resp_68e57b6c7b5872.92804712.pdf', '2025-10-07 20:43:24'),
(4, 4, 'HEYERIS RAFAEL ANCHILA DELUQUE.docx.pdf', 'resp_68e57b9edcb054.36245959.pdf', '2025-10-07 20:44:14'),
(5, 2, 'CIERRE ESTUDIO DE CONFIABILIDAD - DIEGO ANDRES ROSAS RAMIREZ.pdf', 'resp_68e58aa2863a97.66028626.pdf', '2025-10-07 21:48:18'),
(6, 3, 'CIERRE ESTUDIO DE CONFIABILIDAD - OSCAR IVAN NIÑO CARDENAS..pdf', 'resp_68e6682d592895.36334117.pdf', '2025-10-08 13:33:33'),
(7, 4, 'CIERRE ESTUDIO DE CONFIABILIDAD - HEYERIS RAFAEL ANCHILA DE LUQUE.pdf', 'resp_68e668548689b6.46475758.pdf', '2025-10-08 13:34:12'),
(8, 6, 'DAVID ANDRES OREJUELA PITO.pdf', 'resp_68e7c91d1b9ba9.71146725.pdf', '2025-10-09 14:39:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluados`
--

CREATE TABLE `evaluados` (
  `id` int(11) NOT NULL,
  `solicitud_id` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `tipo_identificacion` varchar(5) NOT NULL,
  `numero_documento` varchar(20) NOT NULL,
  `fecha_expedicion` date NOT NULL,
  `lugar_expedicion` varchar(100) NOT NULL,
  `telefono_fijo` varchar(20) DEFAULT NULL,
  `celular` varchar(20) NOT NULL,
  `ciudad_residencia_evaluado` varchar(100) NOT NULL,
  `direccion_residencia` varchar(255) NOT NULL,
  `cargo_candidato` varchar(100) NOT NULL DEFAULT '',
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `tipo` varchar(30) NOT NULL,
  `cliente_nombre` varchar(100) NOT NULL,
  `id_solicitud` int(11) NOT NULL,
  `mensaje` varchar(255) NOT NULL,
  `rol_destino` int(11) NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `tipo`, `cliente_nombre`, `id_solicitud`, `mensaje`, `rol_destino`, `leido`, `fecha`) VALUES
(1, 'confiabilidad', 'Sj Seguridad Privada', 1, 'El cliente Sj Seguridad Privada ha subido la solicitud #1 de confiabilidad. Dar clic para más detalle.', 2, 1, '2025-09-26 14:46:32'),
(2, 'confiabilidad', 'Sj Seguridad Privada', 1, 'El cliente Sj Seguridad Privada ha subido la solicitud #1 de confiabilidad. Dar clic para más detalle.', 3, 1, '2025-09-26 14:46:36'),
(3, 'usuarios', 'Sj Seguridad Privada', 1, 'El cliente Sj Seguridad Privada ha subido la solicitud #1 de usuarios. Dar clic para más detalle.', 2, 1, '2025-09-30 21:47:59'),
(4, 'usuarios', 'Sj Seguridad Privada', 1, 'El cliente Sj Seguridad Privada ha subido la solicitud #1 de usuarios. Dar clic para más detalle.', 3, 1, '2025-09-30 21:48:02'),
(5, 'confiabilidad', 'Sj Seguridad Privada', 2, 'El cliente Sj Seguridad Privada ha subido la solicitud #2 de confiabilidad. Dar clic para más detalle.', 2, 1, '2025-10-01 20:28:34'),
(6, 'confiabilidad', 'Sj Seguridad Privada', 2, 'El cliente Sj Seguridad Privada ha subido la solicitud #2 de confiabilidad. Dar clic para más detalle.', 3, 1, '2025-10-01 20:28:37'),
(7, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 3, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #3 de confiabilidad. Dar clic para más detalle.', 2, 1, '2025-10-01 20:51:13'),
(8, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 3, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #3 de confiabilidad. Dar clic para más detalle.', 3, 1, '2025-10-01 20:51:16'),
(9, 'confiabilidad', 'Sj Seguridad Privada', 4, 'El cliente Sj Seguridad Privada ha subido la solicitud #4 de confiabilidad. Dar clic para más detalle.', 2, 1, '2025-10-02 14:21:26'),
(10, 'confiabilidad', 'Sj Seguridad Privada', 4, 'El cliente Sj Seguridad Privada ha subido la solicitud #4 de confiabilidad. Dar clic para más detalle.', 3, 1, '2025-10-02 14:21:29'),
(11, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 1, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #1 de confiabilidad. Dar clic para más detalle.', 2, 1, '2025-10-02 19:57:51'),
(12, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 1, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #1 de confiabilidad. Dar clic para más detalle.', 3, 1, '2025-10-02 19:57:54'),
(13, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 2, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #2 de confiabilidad. Dar clic para más detalle.', 2, 1, '2025-10-02 20:02:27'),
(14, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 2, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #2 de confiabilidad. Dar clic para más detalle.', 3, 1, '2025-10-02 20:02:31'),
(15, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 3, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #3 de confiabilidad. Dar clic para más detalle.', 2, 1, '2025-10-02 20:16:59'),
(16, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 3, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #3 de confiabilidad. Dar clic para más detalle.', 3, 1, '2025-10-02 20:17:02'),
(17, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 4, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #4 de confiabilidad. Dar clic para más detalle.', 2, 0, '2025-10-06 21:47:04'),
(18, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 4, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #4 de confiabilidad. Dar clic para más detalle.', 3, 1, '2025-10-06 21:47:07'),
(19, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 4, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #4 de confiabilidad. Dar clic para más detalle.', 2, 0, '2025-10-08 15:29:17'),
(20, 'confiabilidad', 'SJ SEGURIDAD PRIVADA LTDA', 4, 'El cliente SJ SEGURIDAD PRIVADA LTDA ha subido la solicitud #4 de confiabilidad. Dar clic para más detalle.', 3, 1, '2025-10-08 15:29:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones_cliente`
--

CREATE TABLE `notificaciones_cliente` (
  `id` int(11) NOT NULL,
  `tipo` varchar(30) NOT NULL,
  `cliente_nombre` varchar(100) NOT NULL,
  `id_solicitud` int(11) NOT NULL,
  `mensaje` varchar(255) NOT NULL,
  `id_usuario_destino` int(11) NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notificaciones_cliente`
--

INSERT INTO `notificaciones_cliente` (`id`, `tipo`, `cliente_nombre`, `id_solicitud`, `mensaje`, `id_usuario_destino`, `leido`, `fecha`) VALUES
(1, 'Poligrafia de pre-empleo', 'Sj Seguridad Privada', 2, 'Su solicitud #2 de Poligrafia de pre-empleo ha recibido una nueva respuesta. Nuevo estado: En proceso.', 7, 1, '2025-10-01 20:34:59'),
(2, 'solicitud', 'SJ SEGURIDAD PRIVADA LTDA', 3, 'Su solicitud #3 de solicitud ha recibido una nueva respuesta. Estado: Completado.', 4, 1, '2025-10-01 20:54:13'),
(3, 'solicitud', 'SJ SEGURIDAD PRIVADA LTDA', 1, 'Su solicitud #1 de solicitud ha recibido una nueva respuesta. Estado: En proceso.', 4, 0, '2025-10-07 12:57:39'),
(4, 'solicitud', 'SJ SEGURIDAD PRIVADA LTDA', 2, 'Su solicitud #2 de solicitud ha recibido una nueva respuesta. Estado: En proceso.', 4, 0, '2025-10-07 20:43:23'),
(5, 'solicitud', 'SJ SEGURIDAD PRIVADA LTDA', 3, 'Su solicitud #3 de solicitud ha recibido una nueva respuesta. Estado: En proceso.', 4, 0, '2025-10-07 20:44:13'),
(6, 'solicitud', 'SJ SEGURIDAD PRIVADA LTDA', 1, 'Su solicitud #1 de solicitud ha recibido una nueva respuesta. Estado: Completado.', 4, 0, '2025-10-07 21:48:16'),
(7, 'Poligrafia de pre-empleo', 'Sj Seguridad Privada', 2, 'Su solicitud #2 de Poligrafia de pre-empleo ha recibido una nueva respuesta. Estado: Completado.', 7, 1, '2025-10-07 21:48:44'),
(8, 'solicitud', 'SJ SEGURIDAD PRIVADA LTDA', 2, 'Su solicitud #2 de solicitud ha recibido una nueva respuesta. Estado: Completado.', 4, 0, '2025-10-08 13:33:31'),
(9, 'solicitud', 'SJ SEGURIDAD PRIVADA LTDA', 3, 'Su solicitud #3 de solicitud ha recibido una nueva respuesta. Estado: Completado.', 4, 0, '2025-10-08 13:34:10'),
(10, 'solicitud', 'SJ SEGURIDAD PRIVADA LTDA', 4, 'Su solicitud #4 de solicitud ha recibido una nueva respuesta. Estado: En proceso.', 4, 0, '2025-10-09 14:39:23'),
(11, 'Referencias personales', 'Sj Seguridad Privada', 1, 'Su solicitud #1 de Referencias personales ha recibido una nueva respuesta. Estado: En proceso.', 7, 0, '2025-10-09 20:38:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones_proveedor`
--

CREATE TABLE `notificaciones_proveedor` (
  `id` int(11) NOT NULL,
  `tipo` varchar(30) NOT NULL,
  `proveedor_nombre` varchar(100) NOT NULL,
  `id_solicitud` int(11) NOT NULL,
  `mensaje` varchar(255) NOT NULL,
  `id_proveedor_destino` int(11) NOT NULL,
  `id_usuario_destino` int(11) DEFAULT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuesta_madre`
--

CREATE TABLE `respuesta_madre` (
  `id` int(11) NOT NULL,
  `solicitud_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `respuesta` text NOT NULL,
  `estado_actual` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `respuesta_madre`
--

INSERT INTO `respuesta_madre` (`id`, `solicitud_id`, `usuario_id`, `respuesta`, `estado_actual`, `fecha_creacion`) VALUES
(2, 1, 6, 'Se adjunta informe de poligrafía, esta pendiente el informe de estudio de confiabilidad (07-10-25). \r\nSe adjuntar informe de estudio de confiabilidad', 'Completado', '2025-10-07 21:48:18'),
(3, 2, 6, 'Remito informe de poligrafia, esta pendiente el informe del estudio de confiabilidad  (07-10-25)\r\nRemito informe de estudio de confiabilidad del candidato (08-10-25)', 'Completado', '2025-10-08 13:33:33'),
(4, 3, 6, 'Remito informe de poligrafía, queda pendiente el informe de estudio de confiabilidad (07-10-25)\r\nRemito informe de estudio de confiabilidad del candidato (08-10-25)', 'Completado', '2025-10-08 13:34:12'),
(6, 4, 6, 'Remito informe de poligrafía, esta pendiente el informe del estudio de confiabilidad (9-10-25)', 'En proceso', '2025-10-09 14:39:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuesta_solicitudes`
--

CREATE TABLE `respuesta_solicitudes` (
  `id` int(11) NOT NULL,
  `solicitud_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `respuesta` text NOT NULL,
  `documento_respuesta` varchar(255) DEFAULT NULL,
  `fecha_respuesta` datetime DEFAULT current_timestamp(),
  `estado_anterior` varchar(50) DEFAULT NULL,
  `estado_actual` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `respuesta_solicitudes`
--

INSERT INTO `respuesta_solicitudes` (`id`, `solicitud_id`, `usuario_id`, `respuesta`, `documento_respuesta`, `fecha_respuesta`, `estado_anterior`, `estado_actual`) VALUES
(3, 1, 6, 'Se adjunta informe de poligrafía, esta pendiente el informe de estudio de confiabilidad (07-10-25)', NULL, '2025-10-07 12:57:39', 'Nuevo', 'En proceso'),
(4, 2, 6, 'Remito informe de poligrafia, esta pendiente el informe del estudio de confiabilidad  (07-10-25)', NULL, '2025-10-07 20:43:23', 'Nuevo', 'En proceso'),
(5, 3, 6, 'Remito informe de poligrafía, queda pendiente el informe de estudio de confiabilidad (07-10-25)', NULL, '2025-10-07 20:44:13', 'Nuevo', 'En proceso'),
(6, 1, 6, 'Se adjunta informe de poligrafía, esta pendiente el informe de estudio de confiabilidad (07-10-25). \r\nSe adjuntar informe de estudio de confiabilidad', NULL, '2025-10-07 21:48:16', 'En proceso', 'Completado'),
(8, 2, 6, 'Remito informe de poligrafia, esta pendiente el informe del estudio de confiabilidad  (07-10-25)\r\nRemito informe de estudio de confiabilidad del candidato (08-10-25)', NULL, '2025-10-08 13:33:31', 'En proceso', 'Completado'),
(9, 3, 6, 'Remito informe de poligrafía, queda pendiente el informe de estudio de confiabilidad (07-10-25)\r\nRemito informe de estudio de confiabilidad del candidato (08-10-25)', NULL, '2025-10-08 13:34:10', 'En proceso', 'Completado'),
(10, 4, 6, 'Remito informe de poligrafía, esta pendiente el informe del estudio de confiabilidad (9-10-25)', NULL, '2025-10-09 14:39:23', 'Nuevo', 'En proceso');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesiones_persistentes`
--

CREATE TABLE `sesiones_persistentes` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `selector` varchar(12) NOT NULL,
  `hasher` varchar(64) NOT NULL,
  `expiracion` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `id` int(11) NOT NULL,
  `empresa_solicitante` varchar(255) NOT NULL,
  `nit_empresa_solicitante` varchar(50) NOT NULL,
  `cliente_final` varchar(150) DEFAULT NULL,
  `tipo_cliente` enum('Interno','Externo') DEFAULT NULL,
  `servicio_id` int(11) DEFAULT NULL,
  `paquete_id` int(11) DEFAULT NULL,
  `ciudad_prestacion_servicio` varchar(255) NOT NULL,
  `ciudad_solicitud_servicio` varchar(255) NOT NULL,
  `nombres` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `tipo_identificacion` varchar(50) NOT NULL,
  `numero_documento` varchar(50) NOT NULL,
  `fecha_expedicion` date DEFAULT NULL,
  `lugar_expedicion` varchar(255) DEFAULT NULL,
  `telefono_fijo` varchar(50) DEFAULT NULL,
  `celular` varchar(50) NOT NULL,
  `ciudad_residencia_evaluado` varchar(255) NOT NULL,
  `direccion_residencia` varchar(255) NOT NULL,
  `cargo_candidato` varchar(100) NOT NULL DEFAULT '',
  `comentarios` text DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL,
  `estado` varchar(50) DEFAULT 'Registrado',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `id_proveedor` int(11) DEFAULT NULL,
  `fecha_asignacion_proveedor` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `solicitudes`
--

INSERT INTO `solicitudes` (`id`, `empresa_solicitante`, `nit_empresa_solicitante`, `cliente_final`, `tipo_cliente`, `servicio_id`, `paquete_id`, `ciudad_prestacion_servicio`, `ciudad_solicitud_servicio`, `nombres`, `apellidos`, `tipo_identificacion`, `numero_documento`, `fecha_expedicion`, `lugar_expedicion`, `telefono_fijo`, `celular`, `ciudad_residencia_evaluado`, `direccion_residencia`, `cargo_candidato`, `comentarios`, `fecha_creacion`, `usuario_id`, `estado`, `activo`, `id_proveedor`, `fecha_asignacion_proveedor`) VALUES
(1, '', '', NULL, NULL, NULL, 22, 'Cali', 'Cali', 'DIEGO ANDRES ', 'ROSAS RAMIREZ ', 'CC', '80091085', '2013-08-28', 'Cali', '3248653453', '3248653453', 'Cali', 'Carrera 17 a # 26-31', 'JEFE DE SEGURIDAD', '', '2025-10-02 14:57:51', 4, 'Completado', 1, NULL, NULL),
(2, '', '', NULL, NULL, NULL, 22, 'Sincelejo', 'Sincelejo', 'OSCAR IVAN ', 'NIÑO CARDENAS ', 'CC', '1099212066', '2012-05-15', 'Sincelejo', '3218106580', '3218106580', 'Sincelejo', 'Calle 31b # 14b-16', 'JEFE DE SEGURIDAD', '', '2025-10-02 15:02:27', 4, 'Completado', 1, NULL, NULL),
(3, '', '', NULL, NULL, NULL, 22, 'Valledupar', 'Valledupar', 'HEYERIS RAFAEL', 'ANCHILA DELUQUE', 'CC', '72268282', '2000-07-18', 'Barranquilla', '3206650190', '3206650190', 'Valledupar', 'Calle 9 # 29-37', 'JEFE DE SEGURIDAD', '', '2025-10-02 15:16:59', 4, 'Completado', 1, NULL, NULL),
(4, 'red de servicios del cauca', '', 'Red de servicios del Cauca', NULL, NULL, 22, 'Popayán', 'Popayán', 'DAVID ANDRES ', 'OREJUELA PITO', 'CC', '1061696776', '2005-04-25', 'Popayán', '58585858', '3108330461', 'Popayán', 'carrera 21c # 15-35', 'ESCOLTA', '', '2025-10-06 16:47:04', 4, 'En proceso', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud_servicios`
--

CREATE TABLE `solicitud_servicios` (
  `id` int(11) NOT NULL,
  `solicitud_id` int(11) NOT NULL,
  `servicio_id` int(11) NOT NULL,
  `creado_en` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_cat_roles`
--

CREATE TABLE `t_cat_roles` (
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(245) NOT NULL,
  `descripcion` varchar(245) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `t_cat_roles`
--

INSERT INTO `t_cat_roles` (`id_rol`, `nombre`, `descripcion`) VALUES
(1, 'cliente', 'Es un cliente con permisos'),
(2, 'admin', 'Es Admin'),
(3, 'SuperAdmin', 'Administrador del sistema'),
(4, 'admin_cliente', 'Administrador Cliente'),
(5, 'cliente_sin_p', 'Es cliente sin permisos'),
(6, 'Proveedores', 'Proveedores');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_cat_servicio`
--

CREATE TABLE `t_cat_servicio` (
  `id_servicio` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `t_cat_servicio`
--

INSERT INTO `t_cat_servicio` (`id_servicio`, `nombre`, `descripcion`) VALUES
(1, 'Verificacion en base de datos especiales', NULL),
(2, 'Verificacion laboral', NULL),
(3, 'Verificacion academica', NULL),
(4, 'Verificacion personal', NULL),
(5, 'Visita domiciliaria presencial', NULL),
(6, 'Visita domiciliaria virtual', NULL),
(7, 'Poligrafia de pre-empleo', NULL),
(8, 'Poligrafia de rutina', NULL),
(9, 'Poligrafia especifica', NULL),
(10, 'Referencias personales', NULL),
(11, 'CIFIN', NULL),
(12, 'Prueba VSA', NULL),
(13, 'Visita empresarial', NULL),
(14, 'Verificacion documental asociado a negocio', NULL),
(15, 'Informe socioeconomico', NULL),
(16, 'Analisis de riesgos a instalaciones y seguridad fisica', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_clientes`
--

CREATE TABLE `t_clientes` (
  `id_cliente` int(11) NOT NULL,
  `NIT` int(11) NOT NULL,
  `razon_social` varchar(255) NOT NULL,
  `direccion_cliente` varchar(255) DEFAULT NULL,
  `ciudad_cliente` varchar(20) DEFAULT NULL,
  `telefono_cliente` varchar(20) DEFAULT NULL,
  `correo_cliente` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `nombre` varchar(100) DEFAULT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `tipo_cliente` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `t_clientes`
--

INSERT INTO `t_clientes` (`id_cliente`, `NIT`, `razon_social`, `direccion_cliente`, `ciudad_cliente`, `telefono_cliente`, `correo_cliente`, `activo`, `nombre`, `cargo`, `tipo_cliente`) VALUES
(1, 900576718, 'SJ SEGURIDAD PRIVADA LTDA', 'AV 4N #26N - 39', 'Cali', '3186324112', 'contacto@sjsp.com.co', 1, 'Wilfredo Velez', 'Gerente General', 'Grupo'),
(2, 900576718, 'Sj Seguridad Privada', 'AV 4N #26N - 39', 'Cali', '3156703771', 'contacto@sjsp.com.co', 1, 'Luisa Ferrerosa', 'Directora comercial', 'Grupo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_login_attempts`
--

CREATE TABLE `t_login_attempts` (
  `id_usuario` int(11) NOT NULL,
  `intentos` int(11) NOT NULL DEFAULT 0,
  `last_attempt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_paquetes_servicio`
--

CREATE TABLE `t_paquetes_servicio` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `t_paquetes_servicio`
--

INSERT INTO `t_paquetes_servicio` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Estudio de confiabilidad hoja de vida completo con cifin - visita domiciliaria presencial', 'Bases de datos, verificación laboral, verificación académica, referencias personales, CIFIN, Visita domiciliaria presencial. Tiempo de respuesta: Cinco (5) días hábiles'),
(2, 'Estudio de confiabilidad hoja de vida completo con cifin - visita domiciliaria virtual', 'Bases de datos, verificación laboral, verificación académica, referencias personales, CIFIN, Visita domiciliaria virtual. Tiempo de respuesta: Cinco (5) días hábiles'),
(3, 'Estudio de confiabilidad hoja de vida completo sin cifin con visita domiciliaria presencial', 'Bases de datos, verificación laboral, verificación académica, referencias personales, Visita domiciliaria presencial. Tiempo de respuesta: Cinco (5) días hábiles'),
(4, 'Estudio de confiabilidad hoja de vida completo sin cifin con visita domiciliaria virtual', 'Bases de datos, verificación laboral, verificación académica, referencias personales, Visita domiciliaria virtual. Tiempo de respuesta: Cinco (5) días hábiles'),
(5, 'Estudio socioeconomico', 'Visita Domiciliaria, informe socioeconómico, informe ejecutivo. Tiempo de respuesta: Tres (3) días hábiles después de haberse realizado la Visita'),
(6, 'Estudio de seguridad asociado de negocio con visita', 'Visita a instalaciones insitu, Verificación documental, Verificación en bases de datos especiales: NIT, Representante legal y suplente, Comportamiento financiero'),
(7, 'Estudio de seguridad asociado de negocio sin visita', 'Verificación documental, Verificación en bases de datos especiales: NIT, Representante legal y suplente, Comportamiento financiero.'),
(8, 'Sarlaft resolucion 2328 del 2025 superintendencia de transporte', 'La Resolución 2328 del 6 de marzo de 2025, tiene como principal objetivo prevenir el uso del sector de transporte como medio para el lavado de activos y la financiación del terrorismo. Por ello, impone la obligación de implementar el SARLAFT'),
(9, 'Estudio de confiabilidad hoja de vida completo con visita presencial y poligrafia pre-empleo', 'Verificacion laboral, academica, personal, verificacion en base de datos especiales, Visita domiciliaria presencial, poligrafia de pre-empleo\n'),
(10, 'Estudio de confiabilidad hoja de vida completo con visita virtual y poligrafia pre-empleo', 'Verificacion laboral, academica, personal, verificacion en base de datos especiales, Visita domiciliaria virtual, poligrafia de pre-empleo\r\n'),
(11, 'Estudio de confiabilidad hoja de vida completo con visita online y poligrafia VSA', 'Verificacion laboral, academica, personal, verificacion en base de datos especiales, visita domiciliaria virtual, poligrafia VSA'),
(12, 'Estudio de confiabilidad hoja de vida completo con visita presencial y poligrafia VSA', 'Verificacion laboral, academica, personal, verificacion en base de datos especiales, visita domiciliaria presencial, poligrafia VSA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_persona`
--

CREATE TABLE `t_persona` (
  `id_persona` int(11) NOT NULL,
  `paterno` varchar(245) NOT NULL,
  `materno` varchar(245) DEFAULT NULL,
  `nombre` varchar(245) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `correo` varchar(245) NOT NULL,
  `identificacion` varchar(50) DEFAULT NULL,
  `fechaInsert` datetime DEFAULT current_timestamp(),
  `celular` varchar(15) NOT NULL,
  `direccion` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `t_persona`
--

INSERT INTO `t_persona` (`id_persona`, `paterno`, `materno`, `nombre`, `telefono`, `correo`, `identificacion`, `fechaInsert`, `celular`, `direccion`) VALUES
(1, 'Mendez', NULL, 'Duvan', '3103618679', 'programador.tic@sjsp.com.co', '1005936099', '2025-09-11 21:33:14', '3103618679', 'AV 4N #26N-39'),
(2, 'Garrido', '', 'Lorena', '3174029444', 'coordinacion.personal@sjsp.com.co', '66916986', '2025-09-12 14:59:48', '3174029444', 'Av 4N #26N - 39'),
(3, 'Vidal', '', 'Jesus', '3156204196', 'ejecutivocomercial5@sjsp.com.co', '18463965', '2025-09-12 15:10:43', '3156204196', 'Av 4N #26N-39'),
(4, 'Aristizabal', '', 'David', '3155938631', 'analistaseleccion@sjsp.com.co', '1143861992', '2025-09-12 15:29:04', '3155938631', 'Av 4N #26N - 39'),
(5, 'Ferrerosa', NULL, 'Luisa', '3186128406', 'comercial@sjsp.com.co', NULL, '2025-09-12 15:37:48', '3186128406', 'Av 4N #26N - 39'),
(6, 'Alzate', '', 'Nataly', '3156496859', 'asistentecomercial@sjsp.com.co', '1151963257', '2025-09-12 16:07:52', '3156496859', 'Av 4N #26N - 39'),
(7, 'Prueba', '', 'Prueba', '3103618679', 'duvanmenddez2001@gmail.com', '99999999999', '2025-09-26 14:03:10', '3174029444', 'Av 4N #26N - 39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_proveedores`
--

CREATE TABLE `t_proveedores` (
  `id_proveedor` int(11) NOT NULL,
  `NIT_proveedor` int(11) NOT NULL,
  `razon_social_proveedor` varchar(50) NOT NULL,
  `nombre_comercial` varchar(50) NOT NULL,
  `correo_proveedor` varchar(50) NOT NULL,
  `telefono_proveedor` varchar(50) DEFAULT NULL,
  `celular_proveedor` varchar(50) NOT NULL,
  `direccion_proveedor` varchar(50) NOT NULL,
  `ciudad_proveedor` varchar(50) NOT NULL,
  `nombre_contacto_proveedor` varchar(50) NOT NULL,
  `cargo_contacto_proveedor` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `t_proveedores`
--

INSERT INTO `t_proveedores` (`id_proveedor`, `NIT_proveedor`, `razon_social_proveedor`, `nombre_comercial`, `correo_proveedor`, `telefono_proveedor`, `celular_proveedor`, `direccion_proveedor`, `ciudad_proveedor`, `nombre_contacto_proveedor`, `cargo_contacto_proveedor`) VALUES
(7, 900518300, 'CENTRAL TRUTH SAS', 'CENTRAL TRUTH SAS', 'duvanmendez.2001@hotmail.es', '3113002294', '3336025200', 'AV 6A BIS # 35 N 100 OFIC 410', 'Bogotá', 'RUBEN DARIO PERLAZA', 'Gerente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_solicitudes_usuario`
--

CREATE TABLE `t_solicitudes_usuario` (
  `id_solicitud` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_usuario_solicitante` int(11) NOT NULL,
  `tipo` enum('Crear','Modificar','Inactivar') NOT NULL,
  `datos_usuario` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`datos_usuario`)),
  `estado` enum('Pendiente','Aprobada','Rechazada') DEFAULT 'Pendiente',
  `fecha_solicitud` timestamp NULL DEFAULT current_timestamp(),
  `fecha_respuesta` timestamp NULL DEFAULT NULL,
  `id_usuario_responde` int(11) DEFAULT NULL,
  `comentario_respuesta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `t_solicitudes_usuario`
--

INSERT INTO `t_solicitudes_usuario` (`id_solicitud`, `id_cliente`, `id_usuario_solicitante`, `tipo`, `datos_usuario`, `estado`, `fecha_solicitud`, `fecha_respuesta`, `id_usuario_responde`, `comentario_respuesta`) VALUES
(1, 2, 7, 'Modificar', '{\"idUsuario\":\"7\",\"motivo\":\"Modificacion\"}', 'Pendiente', '2025-09-30 21:47:59', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `t_usuarios`
--

CREATE TABLE `t_usuarios` (
  `id_usuario` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `id_persona` int(11) NOT NULL,
  `usuario` varchar(245) NOT NULL,
  `password` varchar(245) NOT NULL,
  `activo` int(11) NOT NULL DEFAULT 1,
  `ciudad` varchar(255) NOT NULL,
  `fecha_insert` date NOT NULL,
  `estado_conexion` varchar(50) DEFAULT 'Desconectado',
  `id_cliente` int(11) DEFAULT NULL,
  `creado_por` int(11) NOT NULL COMMENT 'ID del usuario que creó este registro',
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `id_proveedor` int(11) DEFAULT NULL,
  `permiso_ver_documentos` tinyint(1) DEFAULT 0,
  `permiso_subir_documentos` tinyint(1) DEFAULT 0,
  `permiso_crear_solicitudes` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `t_usuarios`
--

INSERT INTO `t_usuarios` (`id_usuario`, `id_rol`, `id_persona`, `usuario`, `password`, `activo`, `ciudad`, `fecha_insert`, `estado_conexion`, `id_cliente`, `creado_por`, `reset_token`, `reset_token_expiry`, `id_proveedor`, `permiso_ver_documentos`, `permiso_subir_documentos`, `permiso_crear_solicitudes`) VALUES
(1, 3, 1, 'Administrador', '$2y$10$XwAu7doZO6CKpEJOh.NG0O827OFsMYGPfi8jcsEi5hCJrpJsuIzTe', 1, 'Cali', '2025-09-11', 'Activo', 2, 1, NULL, NULL, NULL, 0, 0, 0),
(2, 4, 2, 'Lorena Garrido', '$2y$10$HmcN95gPiq7f/nBGPRuSROQQtvo1wiasqDfcyWla.dAICeYRotsX.', 1, 'Cali', '2025-09-12', 'Desconectado', 1, 1, NULL, NULL, NULL, 0, 0, 0),
(3, 2, 3, 'Consultor 1', '$2y$10$4oHuZ./8NcGUMtTwYE82q.81EvLGpxksX73itGCDqcihKKJKwNcQm', 1, 'Cali', '2025-09-12', 'Desconectado', 2, 1, NULL, NULL, NULL, 0, 0, 0),
(4, 1, 4, 'analistaseleccion@sjsp.com.co', '$2y$10$3sGG8drFB3HbevULjDbDO.geRaRIFa6BXe7ywEqbAgQPNzuV.vXNy', 1, 'Cali', '2025-09-12', 'Desconectado', 1, 1, NULL, NULL, NULL, 1, 1, 1),
(5, 3, 5, 'Luisa Ferrerosa', '$2y$10$LhQY9bvPiUru91Iahs/Dnu3FXuuWj4WvIsKltneLpQwJq1e4V5pj2', 1, 'Cali', '2025-09-12', 'Activo', 2, 1, NULL, NULL, NULL, 0, 0, 0),
(6, 2, 6, 'Consultor 2', '$2y$10$3QfLK1S/B959HmgYrtxmvex5aLaaXZ7zjNVo4rnLCrLAAluB7i/I.', 1, 'Cali', '2025-09-12', 'Desconectado', 2, 1, NULL, NULL, NULL, 0, 0, 0),
(7, 4, 7, 'Prueba', '$2y$10$ewWq4y27kCbHzJuRuI2woeqpt6EUOiv/cbn0/y4MnKDaestmiYPLa', 1, 'Cali', '2025-09-26', 'Desconectado', 2, 1, NULL, NULL, NULL, 0, 0, 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `solicitud_id` (`solicitud_id`);

--
-- Indices de la tabla `documentos_respuesta`
--
ALTER TABLE `documentos_respuesta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_madre` (`respuesta_madre_id`);

--
-- Indices de la tabla `evaluados`
--
ALTER TABLE `evaluados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `solicitud_id` (`solicitud_id`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notif_rol_leido` (`rol_destino`,`leido`),
  ADD KEY `idx_notif_fecha` (`fecha`);

--
-- Indices de la tabla `notificaciones_cliente`
--
ALTER TABLE `notificaciones_cliente`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notif_cli_usuario_leido` (`id_usuario_destino`,`leido`),
  ADD KEY `idx_notif_cli_fecha` (`fecha`);

--
-- Indices de la tabla `notificaciones_proveedor`
--
ALTER TABLE `notificaciones_proveedor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notif_prov_leido` (`id_proveedor_destino`,`leido`),
  ADD KEY `idx_notif_prov_usuario_leido` (`id_usuario_destino`,`leido`),
  ADD KEY `idx_notif_prov_fecha` (`fecha`);

--
-- Indices de la tabla `respuesta_madre`
--
ALTER TABLE `respuesta_madre`
  ADD PRIMARY KEY (`id`),
  ADD KEY `solicitud_id` (`solicitud_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `respuesta_solicitudes`
--
ALTER TABLE `respuesta_solicitudes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `solicitud_id` (`solicitud_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `idx_resp_fecha` (`fecha_respuesta`);

--
-- Indices de la tabla `sesiones_persistentes`
--
ALTER TABLE `sesiones_persistentes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `selector` (`selector`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `solicitudes_ibfk_1` (`servicio_id`),
  ADD KEY `solicitudes_jbh_2` (`usuario_id`),
  ADD KEY `idx_id_proveedor` (`id_proveedor`),
  ADD KEY `fk_solicitudes_paquete` (`paquete_id`),
  ADD KEY `idx_solicitudes_cargo_candidato` (`cargo_candidato`);

--
-- Indices de la tabla `solicitud_servicios`
--
ALTER TABLE `solicitud_servicios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_solicitud_servicio` (`solicitud_id`,`servicio_id`),
  ADD KEY `idx_solicitud` (`solicitud_id`),
  ADD KEY `idx_servicio` (`servicio_id`);

--
-- Indices de la tabla `t_cat_roles`
--
ALTER TABLE `t_cat_roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `t_cat_servicio`
--
ALTER TABLE `t_cat_servicio`
  ADD PRIMARY KEY (`id_servicio`);

--
-- Indices de la tabla `t_clientes`
--
ALTER TABLE `t_clientes`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `t_login_attempts`
--
ALTER TABLE `t_login_attempts`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `t_paquetes_servicio`
--
ALTER TABLE `t_paquetes_servicio`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `t_persona`
--
ALTER TABLE `t_persona`
  ADD PRIMARY KEY (`id_persona`);

--
-- Indices de la tabla `t_proveedores`
--
ALTER TABLE `t_proveedores`
  ADD PRIMARY KEY (`id_proveedor`);

--
-- Indices de la tabla `t_solicitudes_usuario`
--
ALTER TABLE `t_solicitudes_usuario`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_usuario_solicitante` (`id_usuario_solicitante`),
  ADD KEY `id_usuario_responde` (`id_usuario_responde`);

--
-- Indices de la tabla `t_usuarios`
--
ALTER TABLE `t_usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `fkPersona_idx` (`id_persona`),
  ADD KEY `fkRoles_idx` (`id_rol`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `fk_usuario_proveedor` (`id_proveedor`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `documentos_respuesta`
--
ALTER TABLE `documentos_respuesta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `evaluados`
--
ALTER TABLE `evaluados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `notificaciones_cliente`
--
ALTER TABLE `notificaciones_cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `notificaciones_proveedor`
--
ALTER TABLE `notificaciones_proveedor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `respuesta_madre`
--
ALTER TABLE `respuesta_madre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `respuesta_solicitudes`
--
ALTER TABLE `respuesta_solicitudes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `sesiones_persistentes`
--
ALTER TABLE `sesiones_persistentes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `solicitud_servicios`
--
ALTER TABLE `solicitud_servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `t_cat_roles`
--
ALTER TABLE `t_cat_roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `t_cat_servicio`
--
ALTER TABLE `t_cat_servicio`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de la tabla `t_clientes`
--
ALTER TABLE `t_clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `t_paquetes_servicio`
--
ALTER TABLE `t_paquetes_servicio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `t_persona`
--
ALTER TABLE `t_persona`
  MODIFY `id_persona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `t_proveedores`
--
ALTER TABLE `t_proveedores`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `t_solicitudes_usuario`
--
ALTER TABLE `t_solicitudes_usuario`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `t_usuarios`
--
ALTER TABLE `t_usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`);

--
-- Filtros para la tabla `documentos_respuesta`
--
ALTER TABLE `documentos_respuesta`
  ADD CONSTRAINT `fk_doc_madre` FOREIGN KEY (`respuesta_madre_id`) REFERENCES `respuesta_madre` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `evaluados`
--
ALTER TABLE `evaluados`
  ADD CONSTRAINT `evaluados_ibfk_1` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notificaciones_proveedor`
--
ALTER TABLE `notificaciones_proveedor`
  ADD CONSTRAINT `fk_notif_prov_proveedor` FOREIGN KEY (`id_proveedor_destino`) REFERENCES `t_proveedores` (`id_proveedor`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notif_prov_usuario` FOREIGN KEY (`id_usuario_destino`) REFERENCES `t_usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `respuesta_madre`
--
ALTER TABLE `respuesta_madre`
  ADD CONSTRAINT `respuesta_madre_ibfk_1` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`),
  ADD CONSTRAINT `respuesta_madre_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `t_usuarios` (`id_usuario`);

--
-- Filtros para la tabla `respuesta_solicitudes`
--
ALTER TABLE `respuesta_solicitudes`
  ADD CONSTRAINT `respuesta_solicitudes_ibfk_1` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`),
  ADD CONSTRAINT `respuesta_solicitudes_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `t_usuarios` (`id_usuario`);

--
-- Filtros para la tabla `sesiones_persistentes`
--
ALTER TABLE `sesiones_persistentes`
  ADD CONSTRAINT `sesiones_persistentes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `t_usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `fk_solicitudes_paquete` FOREIGN KEY (`paquete_id`) REFERENCES `t_paquetes_servicio` (`id`),
  ADD CONSTRAINT `fk_solicitudes_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `t_proveedores` (`id_proveedor`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `solicitudes_ibfk_1` FOREIGN KEY (`servicio_id`) REFERENCES `t_cat_servicio` (`id_servicio`),
  ADD CONSTRAINT `solicitudes_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `t_usuarios` (`id_usuario`);

--
-- Filtros para la tabla `solicitud_servicios`
--
ALTER TABLE `solicitud_servicios`
  ADD CONSTRAINT `fk_ss_servicio` FOREIGN KEY (`servicio_id`) REFERENCES `t_cat_servicio` (`id_servicio`),
  ADD CONSTRAINT `fk_ss_solicitud` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `t_login_attempts`
--
ALTER TABLE `t_login_attempts`
  ADD CONSTRAINT `fk_login_attempts_user` FOREIGN KEY (`id_usuario`) REFERENCES `t_usuarios` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `t_solicitudes_usuario`
--
ALTER TABLE `t_solicitudes_usuario`
  ADD CONSTRAINT `t_solicitudes_usuario_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `t_clientes` (`id_cliente`),
  ADD CONSTRAINT `t_solicitudes_usuario_ibfk_2` FOREIGN KEY (`id_usuario_solicitante`) REFERENCES `t_usuarios` (`id_usuario`),
  ADD CONSTRAINT `t_solicitudes_usuario_ibfk_3` FOREIGN KEY (`id_usuario_responde`) REFERENCES `t_usuarios` (`id_usuario`);

--
-- Filtros para la tabla `t_usuarios`
--
ALTER TABLE `t_usuarios`
  ADD CONSTRAINT `fkPersona` FOREIGN KEY (`id_persona`) REFERENCES `t_persona` (`id_persona`),
  ADD CONSTRAINT `fkRoles` FOREIGN KEY (`id_rol`) REFERENCES `t_cat_roles` (`id_rol`),
  ADD CONSTRAINT `fk_usuario_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `t_proveedores` (`id_proveedor`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `t_usuarios_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `t_clientes` (`id_cliente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
