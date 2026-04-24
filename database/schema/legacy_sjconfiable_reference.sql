-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: sjconfiable
-- ------------------------------------------------------
-- Server version	8.0.42

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */; -- Ajustado para pleno soporte Unicode
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `documentos`
--

DROP TABLE IF EXISTS `documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `solicitud_id` int NOT NULL,
  `nombre_documento` varchar(255) NOT NULL,
  `ruta_documento` varchar(255) NOT NULL,
  `fecha_subida` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `solicitud_id` (`solicitud_id`),
  CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `documentos_respuesta`
--

DROP TABLE IF EXISTS `documentos_respuesta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documentos_respuesta` (
  `id` int NOT NULL AUTO_INCREMENT,
  `respuesta_madre_id` int DEFAULT NULL,
  `nombre_documentoResp` varchar(255) NOT NULL,
  `ruta_documentoResp` varchar(255) NOT NULL,
  `fecha_subidaResp` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_madre` (`respuesta_madre_id`),
  CONSTRAINT `fk_doc_madre` FOREIGN KEY (`respuesta_madre_id`) REFERENCES `respuesta_madre` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo` varchar(30) NOT NULL,
  `cliente_nombre` varchar(100) NOT NULL,
  `id_solicitud` int NOT NULL,
  `mensaje` varchar(255) NOT NULL,
  `rol_destino` int NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT '0',
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notif_rol_leido` (`rol_destino`,`leido`),
  KEY `idx_notif_fecha` (`fecha`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notificaciones_cliente`
--

DROP TABLE IF EXISTS `notificaciones_cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notificaciones_cliente` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tipo` varchar(30) NOT NULL,
  `cliente_nombre` varchar(100) NOT NULL,
  `id_solicitud` int NOT NULL,
  `mensaje` varchar(255) NOT NULL,
  `id_usuario_destino` int NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT '0',
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notif_cli_usuario_leido` (`id_usuario_destino`,`leido`),
  KEY `idx_notif_cli_fecha` (`fecha`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `respuesta_madre`
--

DROP TABLE IF EXISTS `respuesta_madre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `respuesta_madre` (
  `id` int NOT NULL AUTO_INCREMENT,
  `solicitud_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `respuesta` text NOT NULL,
  `estado_actual` varchar(50) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `solicitud_id` (`solicitud_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `respuesta_madre_ibfk_1` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`),
  CONSTRAINT `respuesta_madre_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `t_usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `respuesta_solicitudes`
--

DROP TABLE IF EXISTS `respuesta_solicitudes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `respuesta_solicitudes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `solicitud_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `respuesta` text NOT NULL,
  `documento_respuesta` varchar(255) DEFAULT NULL,
  `fecha_respuesta` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado_anterior` varchar(50) DEFAULT NULL,
  `estado_actual` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `solicitud_id` (`solicitud_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `idx_resp_fecha` (`fecha_respuesta`),
  CONSTRAINT `respuesta_solicitudes_ibfk_1` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`),
  CONSTRAINT `respuesta_solicitudes_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `t_usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sesiones_persistentes`
--

DROP TABLE IF EXISTS `sesiones_persistentes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sesiones_persistentes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `selector` varchar(12) NOT NULL,
  `hasher` varchar(64) NOT NULL,
  `expiracion` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `selector` (`selector`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `sesiones_persistentes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `t_usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `solicitud_servicios`
--

DROP TABLE IF EXISTS `solicitud_servicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitud_servicios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `solicitud_id` int NOT NULL,
  `servicio_id` int NOT NULL,
  `creado_en` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_solicitud_servicio` (`solicitud_id`,`servicio_id`),
  KEY `idx_solicitud` (`solicitud_id`),
  KEY `idx_servicio` (`servicio_id`),
  CONSTRAINT `fk_ss_servicio` FOREIGN KEY (`servicio_id`) REFERENCES `t_cat_servicio` (`id_servicio`),
  CONSTRAINT `fk_ss_solicitud` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `solicitudes`
--

DROP TABLE IF EXISTS `solicitudes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `solicitudes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `empresa_solicitante` varchar(255) NOT NULL,
  `nit_empresa_solicitante` varchar(50) NOT NULL,
  `servicio_id` int DEFAULT NULL,
  `paquete_id` int DEFAULT NULL,
  `ciudad_prestacion_servicio` varchar(255) NOT NULL,
  `ciudad_solicitud_servicio` varchar(255) NOT NULL,
  `nombres` varchar(255) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `tipo_identificacion` varchar(50) NOT NULL,
  `numero_documento` varchar(50) NOT NULL,
  `fecha_expedicion` date NOT NULL,
  `lugar_expedicion` varchar(255) NOT NULL,
  `telefono_fijo` varchar(50) DEFAULT NULL,
  `celular` varchar(50) NOT NULL,
  `ciudad_residencia_evaluado` varchar(255) NOT NULL,
  `direccion_residencia` varchar(255) NOT NULL,
  `comentarios` text,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_id` int NOT NULL,
  `estado` varchar(50) DEFAULT 'Registrado',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `id_proveedor` int DEFAULT NULL,
  `fecha_asignacion_proveedor` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `solicitudes_ibfk_1` (`servicio_id`),
  KEY `solicitudes_jbh_2` (`usuario_id`),
  KEY `idx_id_proveedor` (`id_proveedor`),
  KEY `fk_solicitudes_paquete` (`paquete_id`),
  CONSTRAINT `fk_solicitudes_paquete` FOREIGN KEY (`paquete_id`) REFERENCES `t_paquetes_servicio` (`id`),
  CONSTRAINT `fk_solicitudes_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `t_proveedores` (`id_proveedor`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `solicitudes_ibfk_1` FOREIGN KEY (`servicio_id`) REFERENCES `t_cat_servicio` (`id_servicio`),
  CONSTRAINT `solicitudes_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `t_usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_cat_roles`
--

DROP TABLE IF EXISTS `t_cat_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_cat_roles` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(245) NOT NULL,
  `descripcion` varchar(245) DEFAULT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_cat_servicio`
--

DROP TABLE IF EXISTS `t_cat_servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_cat_servicio` (
  `id_servicio` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_servicio`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_clientes`
--

DROP TABLE IF EXISTS `t_clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_clientes` (
  `id_cliente` int NOT NULL AUTO_INCREMENT,
  `NIT` int NOT NULL,
  `razon_social` varchar(255) NOT NULL,
  `direccion_cliente` varchar(255) DEFAULT NULL,
  `ciudad_cliente` varchar(20) DEFAULT NULL,
  `telefono_cliente` varchar(20) DEFAULT NULL,
  `correo_cliente` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `nombre` varchar(100) DEFAULT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `tipo_cliente` varchar(100) NOT NULL,
  PRIMARY KEY (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_paquetes_servicio`
--

DROP TABLE IF EXISTS `t_paquetes_servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_paquetes_servicio` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_persona`
--

DROP TABLE IF EXISTS `t_persona`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_persona` (
  `id_persona` int NOT NULL AUTO_INCREMENT,
  `paterno` varchar(245) NOT NULL,
  `materno` varchar(245) DEFAULT NULL,
  `nombre` varchar(245) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `correo` varchar(245) NOT NULL,
  `fechaInsert` datetime DEFAULT CURRENT_TIMESTAMP,
  `celular` varchar(15) NOT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_persona`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_proveedores`
--

DROP TABLE IF EXISTS `t_proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_proveedores` (
  `id_proveedor` int NOT NULL AUTO_INCREMENT,
  `NIT_proveedor` int NOT NULL,
  `razon_social_proveedor` varchar(50) NOT NULL,
  `nombre_comercial` varchar(50) NOT NULL,
  `correo_proveedor` varchar(50) NOT NULL,
  `telefono_proveedor` varchar(50) DEFAULT NULL,
  `celular_proveedor` varchar(50) NOT NULL,
  `direccion_proveedor` varchar(50) NOT NULL,
  `ciudad_proveedor` varchar(50) NOT NULL,
  `nombre_contacto_proveedor` varchar(50) NOT NULL,
  `cargo_contacto_proveedor` varchar(50) NOT NULL,
  PRIMARY KEY (`id_proveedor`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_solicitudes_usuario`
--

DROP TABLE IF EXISTS `t_solicitudes_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_solicitudes_usuario` (
  `id_solicitud` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int NOT NULL,
  `id_usuario_solicitante` int NOT NULL,
  `tipo` enum('Crear','Modificar','Inactivar') NOT NULL,
  `datos_usuario` json NOT NULL,
  `estado` enum('Pendiente','Aprobada','Rechazada') DEFAULT 'Pendiente',
  `fecha_solicitud` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_respuesta` timestamp NULL DEFAULT NULL,
  `id_usuario_responde` int DEFAULT NULL,
  `comentario_respuesta` text,
  PRIMARY KEY (`id_solicitud`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_usuario_solicitante` (`id_usuario_solicitante`),
  KEY `id_usuario_responde` (`id_usuario_responde`),
  CONSTRAINT `t_solicitudes_usuario_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `t_clientes` (`id_cliente`),
  CONSTRAINT `t_solicitudes_usuario_ibfk_2` FOREIGN KEY (`id_usuario_solicitante`) REFERENCES `t_usuarios` (`id_usuario`),
  CONSTRAINT `t_solicitudes_usuario_ibfk_3` FOREIGN KEY (`id_usuario_responde`) REFERENCES `t_usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_usuarios`
--

DROP TABLE IF EXISTS `t_usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `t_usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `id_rol` int NOT NULL,
  `id_persona` int NOT NULL,
  `usuario` varchar(245) NOT NULL,
  `password` varchar(245) NOT NULL,
  `activo` int NOT NULL DEFAULT '1',
  `ciudad` varchar(255) NOT NULL,
  `fecha_insert` date NOT NULL,
  `estado_conexion` varchar(50) DEFAULT 'Desconectado',
  `id_cliente` int DEFAULT NULL,
  `creado_por` int NOT NULL COMMENT 'ID del usuario que creó este registro',
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `id_proveedor` int DEFAULT NULL,
  `permiso_ver_documentos` tinyint(1) DEFAULT '0',
  `permiso_subir_documentos` tinyint(1) DEFAULT '0',
  `permiso_crear_solicitudes` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_usuario`),
  KEY `fkPersona_idx` (`id_persona`),
  KEY `fkRoles_idx` (`id_rol`),
  KEY `id_cliente` (`id_cliente`),
  KEY `fk_usuario_proveedor` (`id_proveedor`),
  CONSTRAINT `fk_usuario_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `t_proveedores` (`id_proveedor`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fkPersona` FOREIGN KEY (`id_persona`) REFERENCES `t_persona` (`id_persona`),
  CONSTRAINT `fkRoles` FOREIGN KEY (`id_rol`) REFERENCES `t_cat_roles` (`id_rol`),
  CONSTRAINT `t_usuarios_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `t_clientes` (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `v_solicitudes`
--

DROP TABLE IF EXISTS `v_solicitudes`;
/*!50001 DROP VIEW IF EXISTS `v_solicitudes`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_solicitudes` AS SELECT 
 1 AS `id`,
 1 AS `empresa_solicitante`,
 1 AS `nit_empresa_solicitante`,
 1 AS `servicio_id`,
 1 AS `ciudad_prestacion_servicio`,
 1 AS `ciudad_solicitud_servicio`,
 1 AS `nombres`,
 1 AS `apellidos`,
 1 AS `tipo_identificacion`,
 1 AS `numero_documento`,
 1 AS `fecha_expedicion`,
 1 AS `lugar_expedicion`,
 1 AS `telefono_fijo`,
 1 AS `celular`,
 1 AS `ciudad_residencia_evaluado`,
 1 AS `direccion_residencia`,
 1 AS `comentarios`,
 1 AS `fecha_creacion`,
 1 AS `usuario_id`,
 1 AS `estado`,
 1 AS `activo`,
 1 AS `id_proveedor`,
 1 AS `fecha_asignacion_proveedor`,
 1 AS `servicios_lista`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `v_solicitudes`
--

/*!50001 DROP VIEW IF EXISTS `v_solicitudes`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE VIEW `v_solicitudes` AS select 
    `s`.`id` AS `id`,
    `s`.`empresa_solicitante` AS `empresa_solicitante`,
    `s`.`nit_empresa_solicitante` AS `nit_empresa_solicitante`,
    `s`.`servicio_id` AS `servicio_id`,
    `s`.`ciudad_prestacion_servicio` AS `ciudad_prestacion_servicio`,
    `s`.`ciudad_solicitud_servicio` AS `ciudad_solicitud_servicio`,
    `s`.`nombres` AS `nombres`,
    `s`.`apellidos` AS `apellidos`,
    `s`.`tipo_identificacion` AS `tipo_identificacion`,
    `s`.`numero_documento` AS `numero_documento`,
    `s`.`fecha_expedicion` AS `fecha_expedicion`,
    `s`.`lugar_expedicion` AS `lugar_expedicion`,
    `s`.`telefono_fijo` AS `telefono_fijo`,
    `s`.`celular` AS `celular`,
    `s`.`ciudad_residencia_evaluado` AS `ciudad_residencia_evaluado`,
    `s`.`direccion_residencia` AS `direccion_residencia`,
    `s`.`comentarios` AS `comentarios`,
    `s`.`fecha_creacion` AS `fecha_creacion`,
    `s`.`usuario_id` AS `usuario_id`,
    `s`.`estado` AS `estado`,
    `s`.`activo` AS `activo`,
    `s`.`id_proveedor` AS `id_proveedor`,
    `s`.`fecha_asignacion_proveedor` AS `fecha_asignacion_proveedor`,
    (SELECT GROUP_CONCAT(`cs`.`nombre` ORDER BY `cs`.`nombre` ASC SEPARATOR ', ') 
     FROM (`solicitud_servicios` `ss` JOIN `t_cat_servicio` `cs` ON((`cs`.`id_servicio` = `ss`.`servicio_id`))) 
     WHERE (`ss`.`solicitud_id` = `s`.`id`)) AS `servicios_lista` 
FROM `solicitudes` `s` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-14 17:29:15
