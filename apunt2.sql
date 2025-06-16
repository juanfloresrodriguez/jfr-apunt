-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 04-06-2025 a las 15:23:20
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `apunt2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno`
--

DROP TABLE IF EXISTS `alumno`;
CREATE TABLE IF NOT EXISTS `alumno` (
  `IDuser` int NOT NULL,
  PRIMARY KEY (`IDuser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;

--
-- Volcado de datos para la tabla `alumno`
--

INSERT INTO `alumno` (`IDuser`) VALUES
(3),
(4),
(6),
(7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignatura`
--

DROP TABLE IF EXISTS `asignatura`;
CREATE TABLE IF NOT EXISTS `asignatura` (
  `idAsignatura` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `IDTutor` int DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `descripcion` text,
  PRIMARY KEY (`idAsignatura`),
  KEY `IDTutor` (`IDTutor`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 ;

--
-- Volcado de datos para la tabla `asignatura`
--

INSERT INTO `asignatura` (`idAsignatura`, `nombre`, `IDTutor`, `imagen`, `descripcion`) VALUES
(1, 'Matemáticas', 2, 'Li4vdXBsb2Fkcy9hc2lnbmF0dXJhLmpwZw==', 'Esto es una descripción estandar.'),
(2, 'Física', 2, 'Li4vdXBsb2Fkcy9hc2lnbmF0dXJhLmpwZw==', 'Esto es una descripción estandar.'),
(3, 'Informática', 2, 'Li4vdXBsb2Fkcy9hc2lnbmF0dXJhLmpwZw==', 'Esto es una descripción estandar.'),
(4, 'Historia', 2, 'Li4vdXBsb2Fkcy9hc2lnbmF0dXJhLmpwZw==', 'Esto es una descripción estandar.'),
(5, 'Química', 2, 'Li4vdXBsb2Fkcy9hc2lnbmF0dXJhLmpwZw==', 'Esto es una descripción estandar.'),
(6, 'Lengua', 2, 'Li4vdXBsb2Fkcy9hc2lnbmF0dXJhLmpwZw==', 'Esto es una descripción estandar.'),
(7, 'Biología', 2, 'Li4vdXBsb2Fkcy9hc2lnbmF0dXJhLmpwZw==', 'Esto es una descripción estandar.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contenido`
--

DROP TABLE IF EXISTS `contenido`;
CREATE TABLE IF NOT EXISTS `contenido` (
  `IDcontenido` int NOT NULL AUTO_INCREMENT,
  `IDAsignatura` int DEFAULT NULL,
  `NomDocumento` varchar(255) DEFAULT NULL,
  `RutaDocumento` text,
  `Descripción` text,
  PRIMARY KEY (`IDcontenido`),
  KEY `IDAsignatura` (`IDAsignatura`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 ;

--
-- Volcado de datos para la tabla `contenido`
--

INSERT INTO `contenido` (`IDcontenido`, `IDAsignatura`, `NomDocumento`, `RutaDocumento`, `Descripción`) VALUES
(10, 1, 'Ejemplo Matemáticas 2', 'Li4vdXBsb2Fkcy9tYXRlbWF0aWNhczIucGRm', 'Documento de ejemplo 2 de matemáticas'),
(9, 1, 'Ejemplo Matemáticas 1', 'Li4vdXBsb2Fkcy9tYXRlbWF0aWNhczEucGRm', 'Documento de ejemplo 1 de matemáticas'),
(11, 1, 'Ejemplo Matemáticas 3', 'Li4vdXBsb2Fkcy9tYXRlbWF0aWNhczMucGRm', 'Documento de ejemplo 3 de matemáticas'),
(12, 1, 'Ejemplo Matemáticas 4', 'Li4vdXBsb2Fkcy9tYXRlbWF0aWNhczQucGRm', 'Documento de ejemplo 4 de matemáticas'),
(13, 1, 'Ejemplo Matemáticas 5', 'Li4vdXBsb2Fkcy9tYXRlbWF0aWNhczUucGRm', 'Documento de ejemplo 5 de matemáticas'),
(14, 1, 'Ejemplo Matemáticas 6', 'Li4vdXBsb2Fkcy9tYXRlbWF0aWNhczYucGRm', 'Documento de ejemplo 6 de matemáticas'),
(15, 1, 'Ejemplo Matemáticas 7', 'Li4vdXBsb2Fkcy9tYXRlbWF0aWNhczcucGRm', 'Documento de ejemplo 7 de matemáticas'),
(16, 1, 'Ejemplo Matemáticas 8', 'Li4vdXBsb2Fkcy9tYXRlbWF0aWNhczgucGRm', 'Documento de ejemplo 8 de matemáticas'),
(17, 1, 'Ejemplo Matemáticas 9', 'Li4vdXBsb2Fkcy9tYXRlbWF0aWNhczkucGRm', 'Documento de ejemplo 9 de matemáticas'),
(18, 1, 'Ejemplo Matemáticas 10', 'Li4vdXBsb2Fkcy9tYXRlbWF0aWNhczEwLnBkZg==', 'Documento de ejemplo 10 de matemáticas'),
(19, 2, 'Ejemplo Física 1', 'Li4vdXBsb2Fkcy9maXNpY2ExLnBkZg==', 'Documento de ejemplo 1 de física'),
(20, 2, 'Ejemplo Física 2', 'Li4vdXBsb2Fkcy9maXNpY2EyLnBkZg==', 'Documento de ejemplo 2 de física'),
(21, 2, 'Ejemplo Física 3', 'Li4vdXBsb2Fkcy9maXNpY2EzLnBkZg==', 'Documento de ejemplo 3 de física'),
(22, 2, 'Ejemplo Física 4', 'Li4vdXBsb2Fkcy9maXNpY2E0LnBkZg==', 'Documento de ejemplo 4 de física'),
(23, 2, 'Ejemplo Física 5', 'Li4vdXBsb2Fkcy9maXNpY2E1LnBkZg==', 'Documento de ejemplo 5 de física'),
(24, 2, 'Ejemplo Física 6', 'Li4vdXBsb2Fkcy9maXNpY2E2LnBkZg==', 'Documento de ejemplo 6 de física'),
(25, 2, 'Ejemplo Física 7', 'Li4vdXBsb2Fkcy9maXNpY2E3LnBkZg==', 'Documento de ejemplo 7 de física'),
(26, 2, 'Ejemplo Física 8', 'Li4vdXBsb2Fkcy9maXNpY2E4LnBkZg==', 'Documento de ejemplo 8 de física'),
(27, 2, 'Ejemplo Física 9', 'Li4vdXBsb2Fkcy9maXNpY2E5LnBkZg==', 'Documento de ejemplo 9 de física'),
(28, 2, 'Ejemplo Física 10', 'Li4vdXBsb2Fkcy9maXNpY2ExMC5wZGY=', 'Documento de ejemplo 10 de física'),
(29, 3, 'Ejemplo Informática 1', 'Li4vdXBsb2Fkcy9pbmZvcm1hdGljYTEucGRm', 'Documento de ejemplo 1 de informática'),
(30, 3, 'Ejemplo Informática 2', 'Li4vdXBsb2Fkcy9pbmZvcm1hdGljYTIucGRm', 'Documento de ejemplo 2 de informática'),
(31, 3, 'Ejemplo Informática 3', 'Li4vdXBsb2Fkcy9pbmZvcm1hdGljYTMucGRm', 'Documento de ejemplo 3 de informática'),
(32, 3, 'Ejemplo Informática 4', 'Li4vdXBsb2Fkcy9pbmZvcm1hdGljYTQucGRm', 'Documento de ejemplo 4 de informática'),
(33, 3, 'Ejemplo Informática 5', 'Li4vdXBsb2Fkcy9pbmZvcm1hdGljYTUucGRm', 'Documento de ejemplo 5 de informática'),
(34, 3, 'Ejemplo Informática 6', 'Li4vdXBsb2Fkcy9pbmZvcm1hdGljYTYucGRm', 'Documento de ejemplo 6 de informática'),
(35, 3, 'Ejemplo Informática 7', 'Li4vdXBsb2Fkcy9pbmZvcm1hdGljYTcucGRm', 'Documento de ejemplo 7 de informática'),
(36, 3, 'Ejemplo Informática 8', 'Li4vdXBsb2Fkcy9pbmZvcm1hdGljYTgucGRm', 'Documento de ejemplo 8 de informática'),
(37, 3, 'Ejemplo Informática 9', 'Li4vdXBsb2Fkcy9pbmZvcm1hdGljYTkucGRm', 'Documento de ejemplo 9 de informática'),
(38, 3, 'Ejemplo Informática 10', 'Li4vdXBsb2Fkcy9pbmZvcm1hdGljYTEwLnBkZg==', 'Documento de ejemplo 10 de informática'),
(39, 1, 'Hola', 'Li4vdXBsb2Fkcy9URkctMy00LnBkZg==', 'sajkdsjakd'),
(40, 1, 'Prueba Daniel', 'Li4vdXBsb2Fkcy9URkctMy00LTEucGRm', 'asdasd'),
(41, 1, 'foto', 'Li4vdXBsb2Fkcy9hc2lnbmF0dXJhLmpwZw==', 'asd');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `crea`
--

DROP TABLE IF EXISTS `crea`;
CREATE TABLE IF NOT EXISTS `crea` (
  `IDuser` int NOT NULL,
  `IDgroup` int NOT NULL,
  PRIMARY KEY (`IDuser`,`IDgroup`),
  KEY `IDgroup` (`IDgroup`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;

--
-- Volcado de datos para la tabla `crea`
--

INSERT INTO `crea` (`IDuser`, `IDgroup`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo_directivo`
--

DROP TABLE IF EXISTS `equipo_directivo`;
CREATE TABLE IF NOT EXISTS `equipo_directivo` (
  `IDuser` int NOT NULL,
  PRIMARY KEY (`IDuser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;

--
-- Volcado de datos para la tabla `equipo_directivo`
--

INSERT INTO `equipo_directivo` (`IDuser`) VALUES
(1),
(5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo`
--

DROP TABLE IF EXISTS `grupo`;
CREATE TABLE IF NOT EXISTS `grupo` (
  `IDgroup` int NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `IDTutor` int DEFAULT NULL,
  PRIMARY KEY (`IDgroup`),
  KEY `IDTutor` (`IDTutor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;

--
-- Volcado de datos para la tabla `grupo`
--

INSERT INTO `grupo` (`IDgroup`, `nombre`, `IDTutor`) VALUES
(1, '1º Bachillerato A', 2),
(2, '2º Bachillerato B', 2),
(3, '3º ESO C', 8),
(0, '2º ESO A', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo_alumno`
--

DROP TABLE IF EXISTS `grupo_alumno`;
CREATE TABLE IF NOT EXISTS `grupo_alumno` (
  `IDgroup` int NOT NULL,
  `IDuser` int NOT NULL,
  PRIMARY KEY (`IDgroup`,`IDuser`),
  KEY `IDuser` (`IDuser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;

--
-- Volcado de datos para la tabla `grupo_alumno`
--

INSERT INTO `grupo_alumno` (`IDgroup`, `IDuser`) VALUES
(1, 0),
(1, 3),
(1, 4),
(2, 6),
(3, 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo_asignatura`
--

DROP TABLE IF EXISTS `grupo_asignatura`;
CREATE TABLE IF NOT EXISTS `grupo_asignatura` (
  `IDgroup` int NOT NULL,
  `idAsignatura` int NOT NULL,
  PRIMARY KEY (`IDgroup`,`idAsignatura`),
  KEY `idAsignatura` (`idAsignatura`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;

--
-- Volcado de datos para la tabla `grupo_asignatura`
--

INSERT INTO `grupo_asignatura` (`IDgroup`, `idAsignatura`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 13),
(2, 4),
(2, 5),
(2, 10),
(2, 11),
(3, 6),
(3, 7),
(3, 12);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo_profesor`
--

DROP TABLE IF EXISTS `grupo_profesor`;
CREATE TABLE IF NOT EXISTS `grupo_profesor` (
  `IDgroup` int NOT NULL,
  `IDuser` int NOT NULL,
  PRIMARY KEY (`IDgroup`,`IDuser`),
  KEY `IDuser` (`IDuser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;

--
-- Volcado de datos para la tabla `grupo_profesor`
--

INSERT INTO `grupo_profesor` (`IDgroup`, `IDuser`) VALUES
(2, 8),
(3, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor`
--

DROP TABLE IF EXISTS `profesor`;
CREATE TABLE IF NOT EXISTS `profesor` (
  `IDuser` int NOT NULL,
  PRIMARY KEY (`IDuser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 ;

--
-- Volcado de datos para la tabla `profesor`
--

INSERT INTO `profesor` (`IDuser`) VALUES
(2),
(8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `IDuser` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`IDuser`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 ;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`IDuser`, `username`, `password`, `email`) VALUES
(1, 'directivo1', '$2y$10$wjwO6nuNuGV/aEbg4dAoQ.sMGH6xTlz9LZRG7OAZO6sZQFVRwljOS', 'directivo1@centro.com'),
(2, 'profe1', '$2y$10$dZ7Rj.6fUPzC.vkeaMOstelNxTTAgbznyrX0UPaqZUZ2s0SI3azGK', 'profe1@centro.com'),
(3, 'alumno1', '$2y$10$dZ7Rj.6fUPzC.vkeaMOstelNxTTAgbznyrX0UPaqZUZ2s0SI3azGK', 'alumno1@centro.com'),
(4, 'germanpalomares', '$2y$10$dZ7Rj.6fUPzC.vkeaMOstelNxTTAgbznyrX0UPaqZUZ2s0SI3azGK', 'germanpalomares861@gmail.com'),
(5, 'pacoperez', '$2y$10$dZ7Rj.6fUPzC.vkeaMOstelNxTTAgbznyrX0UPaqZUZ2s0SI3azGK', 'pacoperez@gmail.com'),
(6, 'alumno2', '$2y$10$dZ7Rj.6fUPzC.vkeaMOstelNxTTAgbznyrX0UPaqZUZ2s0SI3azGK', 'alumno2@centro.com'),
(7, 'alumno3', '$2y$10$dZ7Rj.6fUPzC.vkeaMOstelNxTTAgbznyrX0UPaqZUZ2s0SI3azGK', 'alumno3@centro.com'),
(8, 'profe2', '$2y$10$9DZG1qSg49hI3PKoamtl0.N1Dn7fdt.Zj9UTeaFUqAX0I/ZXIcrXi', 'profe2@gmail.com');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
