-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-02-2024 a las 20:34:36
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
-- Base de datos: `bd_fasganz`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `beneficiarios`
--

CREATE TABLE `beneficiarios` (
  `id` int(11) NOT NULL,
  `cedula_empleado` varchar(10) DEFAULT NULL,
  `nombre` varchar(20) DEFAULT NULL,
  `apellido` varchar(20) DEFAULT NULL,
  `cedula_beneficiario` varchar(10) DEFAULT NULL,
  `parentesco` varchar(10) DEFAULT NULL,
  `fecha_nac` date DEFAULT NULL,
  `edad` int(11) GENERATED ALWAYS AS (year(curdate()) - year(`fecha_nac`)) VIRTUAL,
  `genero` varchar(20) NOT NULL,
  `fecha_registro` date NOT NULL,
  `encargado_registro` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `beneficiarios`
--

INSERT INTO `beneficiarios` (`id`, `cedula_empleado`, `nombre`, `apellido`, `cedula_beneficiario`, `parentesco`, `fecha_nac`, `genero`, `fecha_registro`, `encargado_registro`) VALUES
(1, '30383453', 'Bomba de Agua', 'Guiche', '78900', 'HIJO(A)', '2010-10-10', 'M', '2024-02-16', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `citas`
--

CREATE TABLE `citas` (
  `id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `cedula_paciente` varchar(14) DEFAULT NULL,
  `nombre` varchar(250) NOT NULL,
  `apellido` varchar(250) NOT NULL,
  `institucion` varchar(50) NOT NULL,
  `tipo_paciente` varchar(20) NOT NULL,
  `fecha_nac` varchar(50) NOT NULL,
  `genero` varchar(50) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `num_historia` varchar(10) NOT NULL,
  `especialidad` varchar(50) NOT NULL,
  `medico` varchar(50) NOT NULL,
  `num_telefono` varchar(11) NOT NULL,
  `estado` varchar(255) DEFAULT NULL,
  `numero_turno` int(11) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL,
  `encargado_registro` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `citas`
--

INSERT INTO `citas` (`id`, `fecha`, `cedula_paciente`, `nombre`, `apellido`, `institucion`, `tipo_paciente`, `fecha_nac`, `genero`, `categoria`, `num_historia`, `especialidad`, `medico`, `num_telefono`, `estado`, `numero_turno`, `fecha_registro`, `encargado_registro`) VALUES
(1, '2024-01-23', '30383453', 'Frank', 'Guiche', 'FASGANZ', 'Empleado', '2002-04-04', 'M', 'Sucesivo', '33', 'Medicina Familiar', 'Frank Guiche', '020202020', 'Pendiente', NULL, '2024-01-22 14:11:10', 'Frank Guiche'),
(2, '2024-02-16', '30383453', 'Frank', 'Guiche', 'FASGANZ', 'Empleado', '2002-04-04', 'M', 'Sucesivo', '33', 'Medicina Familiar', 'Frank Guiche', '1234', 'Cancelado', NULL, '2024-02-16 12:54:11', 'Frank Guiche'),
(3, '2024-02-16', '30383453', 'Frank', 'Guiche', 'FASGANZ', 'Empleado', '2002-04-04', 'M', 'Sucesivo', '33', 'Medicina Familiar', 'Otro prueba', '23456', 'Atendido', NULL, '2024-02-16 12:54:24', 'Frank Guiche'),
(4, '2024-02-16', '123', 'Moises', 'wq', 'asas', 'Empleado', '1940-01-01', 'M', 'Primario', '', 'Medicina Familiar', 'Frank Guiche', '1234', 'Pendiente', NULL, '2024-02-16 23:52:26', 'Frank Guiche'),
(5, '2024-02-23', '78900', 'Bomba de Agua', 'Guiche', '', 'Beneficiario', '2010-10-10', '', 'Primario', '', 'Medicina Familiar', 'Otro prueba', '12345', 'Pendiente', NULL, '2024-02-17 00:05:56', 'Frank Guiche');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `especialidades`
--

CREATE TABLE `especialidades` (
  `id` int(11) NOT NULL,
  `especialidad` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `especialidades`
--

INSERT INTO `especialidades` (`id`, `especialidad`) VALUES
(1, 'Medicina Familiar'),
(2, 'Otra');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historias_medicas`
--

CREATE TABLE `historias_medicas` (
  `id` int(11) NOT NULL,
  `cedula_titular` varchar(10) NOT NULL,
  `apellido_familia` varchar(50) NOT NULL,
  `num_historia` int(11) NOT NULL,
  `cedula_paciente` varchar(10) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `fecha_nac` date NOT NULL,
  `genero` varchar(20) NOT NULL,
  `estado_civil` varchar(20) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `ocupacion` varchar(50) NOT NULL,
  `estudios` varchar(50) NOT NULL,
  `años_aprobados` varchar(4) NOT NULL,
  `analfabeta` varchar(4) NOT NULL,
  `lugar_nac` varchar(100) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `pais` varchar(20) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `num_telefono` varchar(12) NOT NULL,
  `religion` varchar(20) NOT NULL,
  `establecimiento` varchar(100) NOT NULL,
  `municipio` varchar(50) NOT NULL,
  `parroquia` varchar(50) NOT NULL,
  `comunidad` varchar(20) NOT NULL,
  `etnias` varchar(20) NOT NULL,
  `nom_madre` varchar(20) NOT NULL,
  `madre_ocupacion` varchar(20) NOT NULL,
  `nom_padre` varchar(20) NOT NULL,
  `padre_ocupacion` varchar(20) NOT NULL,
  `representante` varchar(20) NOT NULL,
  `nom_representante` varchar(20) NOT NULL,
  `cedula_representante` varchar(12) NOT NULL,
  `telefono_representante` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historias_medicas`
--

INSERT INTO `historias_medicas` (`id`, `cedula_titular`, `apellido_familia`, `num_historia`, `cedula_paciente`, `nombre`, `apellido`, `fecha_nac`, `genero`, `estado_civil`, `telefono`, `ocupacion`, `estudios`, `años_aprobados`, `analfabeta`, `lugar_nac`, `estado`, `pais`, `direccion`, `num_telefono`, `religion`, `establecimiento`, `municipio`, `parroquia`, `comunidad`, `etnias`, `nom_madre`, `madre_ocupacion`, `nom_padre`, `padre_ocupacion`, `representante`, `nom_representante`, `cedula_representante`, `telefono_representante`) VALUES
(1, '30383453', 'Guiche', 33, '30383453', 'Frank', 'Guiche', '2002-04-04', 'M', 'Soltero', '0002222', 'Equis', 'Secundarios', '5', 'No', 'Equis2', 'Anzoategui', 'Venezuela', 'Equis 3', '04125263589', 'Equis Religion', 'Equis 4', 'ss', 'ss', 'otros', 'otros', 'Nombre Madre', 'Ocupacion MAdre', 'Nombre Padre', 'Ocupacion Padre', 'Representante', 'Nombre Representante', '121212', '020202022');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `medicos`
--

CREATE TABLE `medicos` (
  `id` int(11) NOT NULL,
  `cedula` varchar(12) DEFAULT NULL,
  `nombre` varchar(20) DEFAULT NULL,
  `apellido` varchar(20) DEFAULT NULL,
  `especialidad` varchar(30) DEFAULT NULL,
  `horario` varchar(10) DEFAULT NULL,
  `telefono` varchar(14) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `direccion` varchar(50) DEFAULT NULL,
  `fecha_nac` date DEFAULT NULL,
  `edad` int(11) GENERATED ALWAYS AS (year(curdate()) - year(`fecha_nac`)) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `medicos`
--

INSERT INTO `medicos` (`id`, `cedula`, `nombre`, `apellido`, `especialidad`, `horario`, `telefono`, `email`, `direccion`, `fecha_nac`) VALUES
(1, '30383453', 'Frank', 'Guiche', 'Medicina Familiar', '5', '0002222', 'fra@gmail.com', 'equis', '1999-02-09'),
(2, '12980765', 'Otro', 'prueba', 'Medicina Familiar', '5', '0202022', 'kegipif101@elixirsd.com', 'equis', '1992-03-19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pacientes`
--

CREATE TABLE `pacientes` (
  `id` int(11) NOT NULL,
  `cedula` varchar(10) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `cargo` varchar(50) DEFAULT NULL,
  `institucion` varchar(20) DEFAULT NULL,
  `fecha_nac` date DEFAULT NULL,
  `edad` int(11) GENERATED ALWAYS AS (year(curdate()) - year(`fecha_nac`)) VIRTUAL,
  `genero` varchar(20) NOT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `años_servicio` int(11) GENERATED ALWAYS AS (year(curdate()) - year(`fecha_ingreso`)) VIRTUAL,
  `fecha_registro` date NOT NULL,
  `encargado_registro` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pacientes`
--

INSERT INTO `pacientes` (`id`, `cedula`, `nombre`, `apellido`, `cargo`, `institucion`, `fecha_nac`, `genero`, `fecha_ingreso`, `fecha_registro`, `encargado_registro`) VALUES
(1, '30383453', 'Frank', 'Guiche', 'Asistente de Informatica', 'FASGANZ', '2002-04-04', 'M', '2023-02-01', '2024-02-16', ''),
(2, '123', 'Moises', 'wq', 'asasa', 'FASGANZ', '1940-01-01', 'M', '2002-01-01', '0000-00-00', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `rol` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `rol`) VALUES
(1, 'Administrador'),
(2, 'Recepción'),
(3, 'Historias Medicas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `username`, `password`, `id_rol`) VALUES
(1, 'Frank', 'Guiche', 'administrador', 'admin', 1),
(6, 'Frank', 'Moises', 'recepcion', '123', 2),
(7, 'Moises', 'wq', 'historias', '123', 3);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `beneficiarios`
--
ALTER TABLE `beneficiarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cedula_beneficiario` (`cedula_beneficiario`),
  ADD KEY `cedula_empleado` (`cedula_empleado`);

--
-- Indices de la tabla `citas`
--
ALTER TABLE `citas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `especialidades`
--
ALTER TABLE `especialidades`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `historias_medicas`
--
ALTER TABLE `historias_medicas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cedula_titular` (`cedula_titular`),
  ADD KEY `cedula_paciente` (`cedula_paciente`);

--
-- Indices de la tabla `medicos`
--
ALTER TABLE `medicos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD UNIQUE KEY `id_2` (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `beneficiarios`
--
ALTER TABLE `beneficiarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `citas`
--
ALTER TABLE `citas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `especialidades`
--
ALTER TABLE `especialidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `historias_medicas`
--
ALTER TABLE `historias_medicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `medicos`
--
ALTER TABLE `medicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pacientes`
--
ALTER TABLE `pacientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `beneficiarios`
--
ALTER TABLE `beneficiarios`
  ADD CONSTRAINT `fk_beneficiarios_pacientes` FOREIGN KEY (`cedula_empleado`) REFERENCES `pacientes` (`cedula`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
