-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-09-2025 a las 04:17:52
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
-- Base de datos: `mascotas_ms_zr1`
--
CREATE DATABASE IF NOT EXISTS `mascotas_ms_zr1` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `mascotas_ms_zr1`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adoptantes`
--

CREATE TABLE `adoptantes` (
  `id_adoptante` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `id_mascota` int(11) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `adoptantes`
--

INSERT INTO `adoptantes` (`id_adoptante`, `nombre`, `telefono`, `id_mascota`, `estado`) VALUES
(1, 'Carlos Ramírez', '7777-0001', 1, 'Activo'),
(2, 'María López', '7777-0002', 2, 'Activo'),
(3, 'José Martínez', '7777-0003', 3, 'Activo'),
(4, 'Ana Gómez', '7777-0004', 4, 'Activo'),
(5, 'Pedro Torres', '7777-0005', 5, 'Activo'),
(6, 'Lucía Hernández', '7777-0006', 6, 'Activo'),
(7, 'Jorge Castillo', '7777-0007', 7, 'Activo'),
(8, 'Laura Morales', '7777-0008', 8, 'Activo'),
(9, 'Sofía Vásquez', '7777-0009', 9, 'Activo'),
(10, 'Miguel Estrada', '7777-0010', 10, 'Activo'),
(11, 'Andrea Ramírez', '7777-0011', 11, 'Activo'),
(12, 'Raúl Jiménez', '7777-0012', 12, 'Activo'),
(13, 'Gabriela Díaz', '7777-0013', 13, 'Activo'),
(14, 'Héctor Navarro', '7777-0014', 14, 'Activo'),
(15, 'Patricia López', '7777-0015', 15, 'Activo'),
(16, 'Rodrigo Castillo', '7777-0016', 16, 'Activo'),
(17, 'Verónica Soto', '7777-0017', 17, 'Activo'),
(18, 'Daniel Herrera', '7777-0018', 18, 'Activo'),
(19, 'Paola Méndez', '7777-0019', 19, 'Activo'),
(20, 'Felipe Rojas', '7777-0020', 20, 'Activo'),
(21, 'Luis Figueroa', '7777-0021', 21, 'Activo'),
(22, 'Karen Salazar', '7777-0022', 22, 'Activo'),
(23, 'Ricardo Molina', '7777-0023', 23, 'Activo'),
(24, 'Adriana Campos', '7777-0024', 24, 'Activo'),
(25, 'César Flores', '7777-0025', 25, 'Activo'),
(26, 'Isabel Cabrera', '7777-0026', 26, 'Activo'),
(27, 'Samuel Rivera', '7777-0027', 27, 'Activo'),
(28, 'Valeria Guzmán', '7777-0028', 28, 'Activo'),
(29, 'Esteban Duarte', '7777-0029', 29, 'Activo'),
(30, 'Montserrat Pineda', '7777-0030', 30, 'Activo'),
(31, 'Diego Morales', '7777-0031', 31, 'Activo'),
(32, 'Camila Ramos', '7777-0032', 32, 'Activo'),
(33, 'Mauricio Torres', '7777-0033', 33, 'Activo'),
(34, 'Alejandra Pérez', '7777-0034', 34, 'Activo'),
(35, 'Fernando Silva', '7777-0035', 35, 'Activo'),
(36, 'Natalia Cañas', '7777-0036', 36, 'Activo'),
(37, 'Javier Varela', '7777-0037', 37, 'Activo'),
(38, 'Paula Estrada', '7777-0038', 38, 'Activo'),
(39, 'Cristian Jiménez', '7777-0039', 39, 'Activo'),
(40, 'Rosa Aguilar', '7777-0040', 40, 'Activo'),
(41, 'Pablo Romero', '7777-0041', 41, 'Activo'),
(42, 'Mónica García', '7777-0042', 42, 'Activo'),
(43, 'Ernesto Cruz', '7777-0043', 43, 'Activo'),
(44, 'Carolina Vega', '7777-0044', 44, 'Activo'),
(45, 'Roberto Chávez', '7777-0045', 45, 'Activo'),
(46, 'Marisol Méndez', '7777-0046', 46, 'Activo'),
(47, 'Hugo Herrera', '7777-0047', 47, 'Activo'),
(48, 'Daniela Castillo', '7777-0048', 48, 'Activo'),
(49, 'Gustavo Ruiz', '7777-0049', 49, 'Activo'),
(50, 'Patricia Molina', '7777-0050', 50, 'Activo'),
(51, 'Santiago Torres', '7777-0051', 51, 'Activo'),
(52, 'Andrea Castañeda', '7777-0052', 52, 'Activo'),
(53, 'Oscar Ramírez', '7777-0053', 53, 'Activo'),
(54, 'Melissa Ortiz', '7777-0054', 54, 'Activo'),
(55, 'Víctor Pineda', '7777-0055', 55, 'Activo'),
(56, 'Paula Fernández', '7777-0056', 56, 'Activo'),
(57, 'Álvaro Gutiérrez', '7777-0057', 57, 'Activo'),
(58, 'Claudia Solís', '7777-0058', 58, 'Activo'),
(59, 'Bruno Salgado', '7777-0059', 59, 'Activo'),
(60, 'Jessica Miranda', '7777-0060', 60, 'Activo'),
(61, 'Francisco Castro', '7777-0061', 61, 'Activo'),
(62, 'Marina López', '7777-0062', 62, 'Activo'),
(63, 'Andrés Cornejo', '7777-0063', 63, 'Activo'),
(64, 'Beatriz Cabrera', '7777-0064', 64, 'Activo'),
(65, 'Eduardo Guzmán', '7777-0065', 65, 'Activo'),
(66, 'Liliana Vargas', '7777-0066', 66, 'Activo'),
(67, 'Cristóbal Sánchez', '7777-0067', 67, 'Activo'),
(68, 'Ruth Morales', '7777-0068', 68, 'Activo'),
(69, 'Felipe Mejía', '7777-0069', 69, 'Activo'),
(70, 'Elsa Ramírez', '7777-0070', 70, 'Activo'),
(71, 'Raúl Castillo', '7777-0071', 71, 'Activo'),
(72, 'Patricia Rojas', '7777-0072', 72, 'Activo'),
(73, 'Ignacio Herrera', '7777-0073', 73, 'Activo'),
(74, 'Daniel Castro', '7777-0074', 74, 'Activo'),
(75, 'Carmen Díaz', '7777-0075', 75, 'Activo'),
(76, 'Martín García', '7777-0076', 76, 'Activo'),
(77, 'María Torres', '7777-0077', 77, 'Activo'),
(78, 'Julio Pérez', '7777-0078', 78, 'Activo'),
(79, 'Victoria Ponce', '7777-0079', 79, 'Activo'),
(80, 'David Reyes', '7777-0080', 80, 'Activo'),
(81, 'Fabiola Estrada', '7777-0081', 81, 'Activo'),
(82, 'Hernán López', '7777-0082', 82, 'Activo'),
(83, 'Sofía Morales', '7777-0083', 83, 'Activo'),
(84, 'Mauricio Rivas', '7777-0084', 84, 'Activo'),
(85, 'Karla Varela', '7777-0085', 85, 'Activo'),
(86, 'Manuel Salgado', '7777-0086', 86, 'Activo'),
(87, 'Adriana Ruiz', '7777-0087', 87, 'Activo'),
(88, 'Jorge Pineda', '7777-0088', 88, 'Activo'),
(89, 'Sara Campos', '7777-0089', 89, 'Activo'),
(90, 'Esteban Morales', '7777-0090', 90, 'Activo'),
(91, 'Carlos Ramírez', '7777-0091', 91, 'Activo'),
(92, 'María López', '7777-0092', 92, 'Activo'),
(93, 'José Martínez', '7777-0093', 93, 'Activo'),
(94, 'Ana Gómez', '7777-0094', 94, 'Activo'),
(95, 'Pedro Torres', '7777-0095', 95, 'Activo'),
(96, 'Lucía Hernández', '7777-0096', 96, 'Activo'),
(97, 'Jorge Castillo', '7777-0097', 97, 'Activo'),
(98, 'Laura Morales', '7777-0098', 98, 'Activo'),
(99, 'Sofía Vásquez', '7777-0099', 99, 'Activo'),
(100, 'Miguel Estrada', '7777-0100', 100, 'Activo'),
(101, 'Ever', '78196462', 101, 'Inactivo'),
(102, 'Ever', '78196462', 101, 'Inactivo'),
(103, 'Ever', '78196462', 101, 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mascotas`
--

CREATE TABLE `mascotas` (
  `id_mascota` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `estado_adopcion` enum('Disponible','Adoptado') DEFAULT 'Disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mascotas`
--

INSERT INTO `mascotas` (`id_mascota`, `id_tipo`, `nombre`, `foto`, `estado`, `estado_adopcion`) VALUES
(1, 1, 'Buddy', 'https://images.pexels.com/photos/1805164/pexels-photo-1805164.jpeg', 'Activo', 'Adoptado'),
(2, 2, 'Luna', 'https://images.pexels.com/photos/45201/kitty-cat-kitten-pet-45201.jpeg', 'Activo', 'Adoptado'),
(3, 3, 'Max', 'https://images.pexels.com/photos/3763266/pexels-photo-3763266.jpeg', 'Activo', 'Adoptado'),
(4, 4, 'Milo', 'https://images.pexels.com/photos/1056247/pexels-photo-1056247.jpeg', 'Activo', 'Adoptado'),
(5, 5, 'Bella', 'https://images.pexels.com/photos/117098/pexels-photo-117098.jpeg', 'Activo', 'Adoptado'),
(6, 6, 'Rocky', 'https://images.pexels.com/photos/3305379/pexels-photo-3305379.jpeg', 'Activo', 'Adoptado'),
(7, 7, 'Charlie', 'https://images.pexels.com/photos/14856376/pexels-photo-14856376.jpeg', 'Activo', 'Adoptado'),
(8, 8, 'Lucy', 'https://images.pexels.com/photos/1570773/pexels-photo-1570773.jpeg', 'Activo', 'Adoptado'),
(9, 9, 'Daisy', 'https://images.pexels.com/photos/2071873/pexels-photo-2071873.jpeg', 'Activo', 'Adoptado'),
(10, 10, 'Cooper', 'https://images.pexels.com/photos/2208007/pexels-photo-2208007.jpeg', 'Activo', 'Adoptado'),
(11, 11, 'Simba', 'https://images.pexels.com/photos/2199616/pexels-photo-2199616.jpeg', 'Activo', 'Adoptado'),
(12, 12, 'Nala', 'https://images.pexels.com/photos/16601445/pexels-photo-16601445.jpeg', 'Activo', 'Adoptado'),
(13, 13, 'Tiger', 'https://images.pexels.com/photos/15234567/pexels-photo-15234567/free-photo-of-un-gato-joven-atigrado-y-blanco-en-un-bosque.jpeg', 'Activo', 'Adoptado'),
(14, 14, 'Oscar', 'https://images.pexels.com/photos/1741206/pexels-photo-1741206.jpeg', 'Activo', 'Adoptado'),
(15, 15, 'Lily', 'https://images.pexels.com/photos/1474240/pexels-photo-1474240.jpeg', 'Activo', 'Adoptado'),
(16, 16, 'Zoe', 'https://images.pexels.com/photos/15436324/pexels-photo-15436324.jpeg', 'Activo', 'Adoptado'),
(17, 17, 'Toby', 'https://images.pexels.com/photos/1429774/pexels-photo-1429774.jpeg', 'Activo', 'Adoptado'),
(18, 18, 'Chloe', 'https://images.pexels.com/photos/15720743/pexels-photo-15720743.jpeg', 'Activo', 'Adoptado'),
(19, 19, 'Leo', 'https://images.pexels.com/photos/1314550/pexels-photo-1314550.jpeg', 'Activo', 'Adoptado'),
(20, 20, 'Sasha', 'https://images.pexels.com/photos/2089408/pexels-photo-2089408.jpeg', 'Activo', 'Adoptado'),
(21, 21, 'Jack', 'https://images.pexels.com/photos/1039867/pexels-photo-1039867.jpeg', 'Activo', 'Adoptado'),
(22, 22, 'Maggie', 'https://images.pexels.com/photos/45201/kitty-cat-kitten-pet-45201.jpeg', 'Activo', 'Adoptado'),
(23, 23, 'Bentley', 'https://images.pexels.com/photos/1805164/pexels-photo-1805164.jpeg', 'Activo', 'Adoptado'),
(24, 24, 'Ruby', 'https://images.pexels.com/photos/3763266/pexels-photo-3763266.jpeg', 'Activo', 'Adoptado'),
(25, 25, 'Coco', 'https://images.pexels.com/photos/1056247/pexels-photo-1056247.jpeg', 'Activo', 'Adoptado'),
(26, 26, 'Loki', 'https://images.pexels.com/photos/45201/kitty-cat-kitten-pet-45201.jpeg', 'Activo', 'Adoptado'),
(27, 27, 'Mia', 'https://images.pexels.com/photos/1805164/pexels-photo-1805164.jpeg', 'Activo', 'Adoptado'),
(28, 28, 'Riley', 'https://images.pexels.com/photos/3763266/pexels-photo-3763266.jpeg', 'Activo', 'Adoptado'),
(29, 29, 'Sophie', 'https://images.pexels.com/photos/1056247/pexels-photo-1056247.jpeg', 'Activo', 'Adoptado'),
(30, 30, 'Bailey', 'https://images.pexels.com/photos/117098/pexels-photo-117098.jpeg', 'Activo', 'Adoptado'),
(31, 31, 'Leo', 'https://images.pexels.com/photos/1314550/pexels-photo-1314550.jpeg', 'Activo', 'Adoptado'),
(32, 32, 'Molly', 'https://images.pexels.com/photos/2199616/pexels-photo-2199616.jpeg', 'Activo', 'Adoptado'),
(33, 33, 'Ziggy', 'https://images.pexels.com/photos/15234567/pexels-photo-15234567/free-photo-of-un-gato-joven-atigrado-y-blanco-en-un-bosque.jpeg', 'Activo', 'Adoptado'),
(34, 34, 'Gracie', 'https://images.pexels.com/photos/1474240/pexels-photo-1474240.jpeg', 'Activo', 'Adoptado'),
(35, 35, 'Buster', 'https://images.pexels.com/photos/1741206/pexels-photo-1741206.jpeg', 'Activo', 'Adoptado'),
(36, 36, 'Lola', 'https://images.pexels.com/photos/1474240/pexels-photo-1474240.jpeg', 'Activo', 'Adoptado'),
(37, 37, 'Ruby', 'https://images.pexels.com/photos/2071873/pexels-photo-2071873.jpeg', 'Activo', 'Adoptado'),
(38, 38, 'Hunter', 'https://images.pexels.com/photos/2208007/pexels-photo-2208007.jpeg', 'Activo', 'Adoptado'),
(39, 39, 'Cleo', 'https://images.pexels.com/photos/15720743/pexels-photo-15720743.jpeg', 'Activo', 'Adoptado'),
(40, 40, 'Duke', 'https://images.pexels.com/photos/1314550/pexels-photo-1314550.jpeg', 'Activo', 'Adoptado'),
(41, 41, 'Shadow', 'https://images.pexels.com/photos/2089408/pexels-photo-2089408.jpeg', 'Activo', 'Adoptado'),
(42, 42, 'Angel', 'https://images.pexels.com/photos/1039867/pexels-photo-1039867.jpeg', 'Activo', 'Adoptado'),
(43, 43, 'Ginger', 'https://images.pexels.com/photos/3305379/pexels-photo-3305379.jpeg', 'Activo', 'Adoptado'),
(44, 44, 'Pepper', 'https://images.pexels.com/photos/14856376/pexels-photo-14856376.jpeg', 'Activo', 'Adoptado'),
(45, 45, 'Sandy', 'https://images.pexels.com/photos/1805164/pexels-photo-1805164.jpeg', 'Activo', 'Adoptado'),
(46, 46, 'Teddy', 'https://images.pexels.com/photos/1570773/pexels-photo-1570773.jpeg', 'Activo', 'Adoptado'),
(47, 47, 'Sammy', 'https://images.pexels.com/photos/14856376/pexels-photo-14856376.jpeg', 'Activo', 'Adoptado'),
(48, 48, 'Kiki', 'https://images.pexels.com/photos/1056247/pexels-photo-1056247.jpeg', 'Activo', 'Adoptado'),
(49, 49, 'Marley', 'https://images.pexels.com/photos/117098/pexels-photo-117098.jpeg', 'Activo', 'Adoptado'),
(50, 50, 'Missy', 'https://images.pexels.com/photos/45201/kitty-cat-kitten-pet-45201.jpeg', 'Activo', 'Adoptado'),
(51, 51, 'Rex', 'https://images.pexels.com/photos/3305379/pexels-photo-3305379.jpeg', 'Activo', 'Adoptado'),
(52, 52, 'Fiona', 'https://images.pexels.com/photos/1570773/pexels-photo-1570773.jpeg', 'Activo', 'Adoptado'),
(53, 53, 'Rusty', 'https://images.pexels.com/photos/14856376/pexels-photo-14856376.jpeg', 'Activo', 'Adoptado'),
(54, 54, 'Belle', 'https://images.pexels.com/photos/1056247/pexels-photo-1056247.jpeg', 'Activo', 'Adoptado'),
(55, 55, 'Ace', 'https://images.pexels.com/photos/117098/pexels-photo-117098.jpeg', 'Activo', 'Adoptado'),
(56, 56, 'Misty', 'https://images.pexels.com/photos/45201/kitty-cat-kitten-pet-45201.jpeg', 'Activo', 'Adoptado'),
(57, 57, 'Thor', 'https://images.pexels.com/photos/3305379/pexels-photo-3305379.jpeg', 'Activo', 'Adoptado'),
(58, 58, 'Duke', 'https://images.pexels.com/photos/1805164/pexels-photo-1805164.jpeg', 'Activo', 'Adoptado'),
(59, 59, 'Luna', 'https://images.pexels.com/photos/45201/kitty-cat-kitten-pet-45201.jpeg', 'Activo', 'Adoptado'),
(60, 60, 'Simba', 'https://images.pexels.com/photos/2199616/pexels-photo-2199616.jpeg', 'Activo', 'Adoptado'),
(61, 61, 'Bella', 'https://images.pexels.com/photos/117098/pexels-photo-117098.jpeg', 'Activo', 'Adoptado'),
(62, 62, 'Rocky', 'https://images.pexels.com/photos/3305379/pexels-photo-3305379.jpeg', 'Activo', 'Adoptado'),
(63, 63, 'Milo', 'https://images.pexels.com/photos/1056247/pexels-photo-1056247.jpeg', 'Activo', 'Adoptado'),
(64, 64, 'Lucy', 'https://images.pexels.com/photos/1570773/pexels-photo-1570773.jpeg', 'Activo', 'Adoptado'),
(65, 65, 'Charlie', 'https://images.pexels.com/photos/14856376/pexels-photo-14856376.jpeg', 'Activo', 'Adoptado'),
(66, 66, 'Lily', 'https://images.pexels.com/photos/1474240/pexels-photo-1474240.jpeg', 'Activo', 'Adoptado'),
(67, 67, 'Oscar', 'https://images.pexels.com/photos/1741206/pexels-photo-1741206.jpeg', 'Activo', 'Adoptado'),
(68, 68, 'Chloe', 'https://images.pexels.com/photos/15720743/pexels-photo-15720743.jpeg', 'Activo', 'Adoptado'),
(69, 69, 'Max', 'https://images.pexels.com/photos/1314550/pexels-photo-1314550.jpeg', 'Activo', 'Adoptado'),
(70, 70, 'Zoe', 'https://images.pexels.com/photos/15436324/pexels-photo-15436324.jpeg', 'Activo', 'Adoptado'),
(71, 71, 'Toby', 'https://images.pexels.com/photos/1429774/pexels-photo-1429774.jpeg', 'Activo', 'Adoptado'),
(72, 72, 'Daisy', 'https://images.pexels.com/photos/2071873/pexels-photo-2071873.jpeg', 'Activo', 'Adoptado'),
(73, 73, 'Leo', 'https://images.pexels.com/photos/1314550/pexels-photo-1314550.jpeg', 'Activo', 'Adoptado'),
(74, 74, 'Sasha', 'https://images.pexels.com/photos/2089408/pexels-photo-2089408.jpeg', 'Activo', 'Adoptado'),
(75, 75, 'Jack', 'https://images.pexels.com/photos/1039867/pexels-photo-1039867.jpeg', 'Activo', 'Adoptado'),
(76, 76, 'Maggie', 'https://images.pexels.com/photos/45201/kitty-cat-kitten-pet-45201.jpeg', 'Activo', 'Adoptado'),
(77, 77, 'Bentley', 'https://images.pexels.com/photos/1805164/pexels-photo-1805164.jpeg', 'Activo', 'Adoptado'),
(78, 78, 'Ruby', 'https://images.pexels.com/photos/3763266/pexels-photo-3763266.jpeg', 'Activo', 'Adoptado'),
(79, 79, 'Coco', 'https://images.pexels.com/photos/1056247/pexels-photo-1056247.jpeg', 'Activo', 'Adoptado'),
(80, 80, 'Loki', 'https://images.pexels.com/photos/45201/kitty-cat-kitten-pet-45201.jpeg', 'Activo', 'Adoptado'),
(81, 81, 'Mia', 'https://images.pexels.com/photos/1805164/pexels-photo-1805164.jpeg', 'Activo', 'Adoptado'),
(82, 82, 'Riley', 'https://images.pexels.com/photos/3763266/pexels-photo-3763266.jpeg', 'Activo', 'Adoptado'),
(83, 83, 'Sophie', 'https://images.pexels.com/photos/1056247/pexels-photo-1056247.jpeg', 'Activo', 'Adoptado'),
(84, 84, 'Bailey', 'https://images.pexels.com/photos/117098/pexels-photo-117098.jpeg', 'Activo', 'Adoptado'),
(85, 85, 'Leo', 'https://images.pexels.com/photos/1314550/pexels-photo-1314550.jpeg', 'Activo', 'Adoptado'),
(86, 86, 'Molly', 'https://images.pexels.com/photos/2199616/pexels-photo-2199616.jpeg', 'Activo', 'Adoptado'),
(87, 87, 'Ziggy', 'https://images.pexels.com/photos/15234567/pexels-photo-15234567/free-photo-of-un-gato-joven-atigrado-y-blanco-en-un-bosque.jpeg', 'Activo', 'Adoptado'),
(88, 88, 'Gracie', 'https://images.pexels.com/photos/1474240/pexels-photo-1474240.jpeg', 'Activo', 'Adoptado'),
(89, 89, 'Buster', 'https://images.pexels.com/photos/1741206/pexels-photo-1741206.jpeg', 'Activo', 'Adoptado'),
(90, 90, 'Lola', 'https://images.pexels.com/photos/1474240/pexels-photo-1474240.jpeg', 'Activo', 'Adoptado'),
(91, 91, 'Rocko', 'https://images.pexels.com/photos/1805164/pexels-photo-1805164.jpeg', 'Activo', 'Adoptado'),
(92, 92, 'Felix', 'https://images.pexels.com/photos/45201/kitty-cat-kitten-pet-45201.jpeg', 'Activo', 'Adoptado'),
(93, 93, 'Snow', 'https://images.pexels.com/photos/1056247/pexels-photo-1056247.jpeg', 'Activo', 'Adoptado'),
(94, 94, 'Mochi', 'https://images.pexels.com/photos/117098/pexels-photo-117098.jpeg', 'Activo', 'Adoptado'),
(95, 95, 'Kiki', 'https://images.pexels.com/photos/45201/kitty-cat-kitten-pet-45201.jpeg', 'Activo', 'Adoptado'),
(96, 96, 'Baxter', 'https://images.pexels.com/photos/1805164/pexels-photo-1805164.jpeg', 'Activo', 'Adoptado'),
(97, 97, 'Tiger', 'https://images.pexels.com/photos/14856376/pexels-photo-14856376.jpeg', 'Activo', 'Adoptado'),
(98, 98, 'Neon', 'https://images.pexels.com/photos/1570773/pexels-photo-1570773.jpeg', 'Activo', 'Adoptado'),
(99, 99, 'Fluffy', 'https://images.pexels.com/photos/1056247/pexels-photo-1056247.jpeg', 'Activo', 'Adoptado'),
(100, 100, 'Albi', 'https://images.pexels.com/photos/117098/pexels-photo-117098.jpeg', 'Activo', 'Adoptado'),
(101, 101, 'Sharky', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQQZuIHtCa-M_9_FKcMECuuQOcfd5HkQyF7Yg&s', 'Activo', 'Adoptado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposmascota`
--

CREATE TABLE `tiposmascota` (
  `id_tipo` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tiposmascota`
--

INSERT INTO `tiposmascota` (`id_tipo`, `nombre`, `descripcion`, `estado`) VALUES
(1, 'Labrador Retriever', 'Perro amigable y activo', 'Activo'),
(2, 'Golden Retriever', 'Perro amigable y sociable', 'Activo'),
(3, 'Pastor Alemán', 'Perro inteligente y protector', 'Activo'),
(4, 'Beagle', 'Perro de caza y familia', 'Activo'),
(5, 'Bulldog Inglés', 'Perro fuerte y tranquilo', 'Activo'),
(6, 'Bulldog Francés', 'Perro pequeño y juguetón', 'Activo'),
(7, 'Poodle', 'Perro elegante y entrenable', 'Activo'),
(8, 'Chihuahua', 'Perro pequeño y valiente', 'Activo'),
(9, 'Dachshund', 'Perro salchicha amigable', 'Activo'),
(10, 'Husky Siberiano', 'Perro fuerte y enérgico', 'Activo'),
(11, 'Corgi', 'Perro pequeño y trabajador', 'Activo'),
(12, 'Boxer', 'Perro atlético y protector', 'Activo'),
(13, 'Rottweiler', 'Perro fuerte y guardián', 'Activo'),
(14, 'Shih Tzu', 'Perro pequeño y cariñoso', 'Activo'),
(15, 'Schnauzer', 'Perro activo y leal', 'Activo'),
(16, 'Pomerania', 'Perro pequeño y vivaz', 'Activo'),
(17, 'Akita', 'Perro grande y noble', 'Activo'),
(18, 'Bóxer', 'Perro protector y amigable', 'Activo'),
(19, 'Dálmata', 'Perro enérgico y sociable', 'Activo'),
(20, 'Maltés', 'Perro pequeño y cariñoso', 'Activo'),
(21, 'Siamés', 'Gato elegante y vocal', 'Activo'),
(22, 'Persa', 'Gato tranquilo y peludo', 'Activo'),
(23, 'Maine Coon', 'Gato grande y amistoso', 'Activo'),
(24, 'Ragdoll', 'Gato dócil y cariñoso', 'Activo'),
(25, 'Bengala', 'Gato activo y exótico', 'Activo'),
(26, 'Azul Ruso', 'Gato elegante y reservado', 'Activo'),
(27, 'Scottish Fold', 'Gato con orejas dobladas', 'Activo'),
(28, 'Sphynx', 'Gato sin pelo, curioso', 'Activo'),
(29, 'British Shorthair', 'Gato robusto y tranquilo', 'Activo'),
(30, 'Exótico de pelo corto', 'Gato suave y amigable', 'Activo'),
(31, 'Oriental', 'Gato delgado y elegante', 'Activo'),
(32, 'Savannah', 'Gato exótico y alto', 'Activo'),
(33, 'Kurilian Bobtail', 'Gato con cola corta', 'Activo'),
(34, 'Norwegian Forest', 'Gato grande y peludo', 'Activo'),
(35, 'Balinés', 'Gato elegante y sociable', 'Activo'),
(36, 'Periquito', 'Ave pequeña y social', 'Activo'),
(37, 'Loro Arcoíris', 'Ave parlante', 'Activo'),
(38, 'Loro Eclectus', 'Ave colorida', 'Activo'),
(39, 'Canario', 'Ave cantadora', 'Activo'),
(40, 'Cacatúa', 'Ave amigable y ruidosa', 'Activo'),
(41, 'Agapornis', 'Ave pequeña y social', 'Activo'),
(42, 'Gallo', 'Ave de granja', 'Activo'),
(43, 'Pato Pekín', 'Ave acuática', 'Activo'),
(44, 'Pato Real', 'Ave acuática', 'Activo'),
(45, 'Ganso Canadiense', 'Ave grande y territorial', 'Activo'),
(46, 'Conejo Holandés', 'Roedor amistoso', 'Activo'),
(47, 'Conejo Enano', 'Roedor pequeño y sociable', 'Activo'),
(48, 'Cobaya', 'Roedor social', 'Activo'),
(49, 'Hamster Dorado', 'Roedor pequeño', 'Activo'),
(50, 'Hamster Ruso', 'Roedor activo', 'Activo'),
(51, 'Hamster Roborovski', 'Roedor pequeño y rápido', 'Activo'),
(52, 'Jerbo', 'Roedor desertico', 'Activo'),
(53, 'Ratón Albino', 'Roedor pequeño', 'Activo'),
(54, 'Chinchilla', 'Roedor nocturno y suave', 'Activo'),
(55, 'Hurón', 'Mamífero curioso', 'Activo'),
(56, 'Iguana Verde', 'Reptil herbívoro', 'Activo'),
(57, 'Camaleón', 'Reptil cambiante', 'Activo'),
(58, 'Gecko Leopardo', 'Reptil nocturno', 'Activo'),
(59, 'Serpiente Rey', 'Reptil venenoso', 'Activo'),
(60, 'Boa Constrictor', 'Reptil grande', 'Activo'),
(61, 'Tortuga de Florida', 'Reptil acuático', 'Activo'),
(62, 'Tortuga Sulcata', 'Reptil terrestre', 'Activo'),
(63, 'Rana Dardo Azul', 'Anfibio venenoso', 'Activo'),
(64, 'Salamandra Tigre', 'Anfibio terrestre', 'Activo'),
(65, 'Tortuga Oriental', 'Reptil terrestre', 'Activo'),
(66, 'Betta', 'Pez colorido', 'Activo'),
(67, 'Goldfish', 'Pez dorado', 'Activo'),
(68, 'Guppy', 'Pez pequeño', 'Activo'),
(69, 'Neon Tetra', 'Pez de acuario', 'Activo'),
(70, 'Molly', 'Pez de agua dulce', 'Activo'),
(71, 'Platys', 'Pez de agua dulce', 'Activo'),
(72, 'Discus', 'Pez exótico', 'Activo'),
(73, 'Koi', 'Pez ornamental', 'Activo'),
(74, 'Cíclido Africano', 'Pez agresivo', 'Activo'),
(75, 'Gourami', 'Pez pacífico', 'Activo'),
(76, 'Caballo Árabe', 'Caballo elegante', 'Activo'),
(77, 'Caballo Percherón', 'Caballo fuerte', 'Activo'),
(78, 'Vaca Holstein', 'Animal de granja', 'Activo'),
(79, 'Oveja Suffolk', 'Animal de granja', 'Activo'),
(80, 'Cabra Alpina', 'Animal de granja', 'Activo'),
(81, 'Cerdo Miniatura', 'Animal doméstico', 'Activo'),
(82, 'Alpaca', 'Animal doméstico', 'Activo'),
(83, 'Llama', 'Animal doméstico', 'Activo'),
(84, 'Camello', 'Animal de transporte', 'Activo'),
(85, 'Rana Africana', 'Anfibio', 'Activo'),
(86, 'Erizo Africano', 'Roedor pequeño', 'Activo'),
(87, 'Conejo Rex', 'Roedor grande', 'Activo'),
(88, 'Tortuga Sulcata', 'Reptil terrestre', 'Activo'),
(89, 'Peces Ángel', 'Pez de acuario', 'Activo'),
(90, 'Pato Mandarín', 'Ave colorida', 'Activo'),
(91, 'Perro Shar Pei', 'Perro con arrugas características', 'Activo'),
(92, 'Gato Peterbald', 'Gato elegante sin pelo', 'Activo'),
(93, 'Perro Samoyedo', 'Perro blanco y esponjoso', 'Activo'),
(94, 'Gato Munchkin', 'Gato de patas cortas', 'Activo'),
(95, 'Loro Kakapo', 'Ave nocturna y pesada', 'Activo'),
(96, 'Perro Basset Hound', 'Perro de orejas largas', 'Activo'),
(97, 'Gato Toyger', 'Gato con rayas de tigre', 'Activo'),
(98, 'Pez Guppy Endler', 'Pez colorido pequeño', 'Activo'),
(99, 'Conejo Angora', 'Roedor de pelo largo y suave', 'Activo'),
(100, 'Hurón Albino', 'Mamífero pequeño albino', 'Activo'),
(101, 'Tiburon', 'De mar', 'Activo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `adoptantes`
--
ALTER TABLE `adoptantes`
  ADD PRIMARY KEY (`id_adoptante`),
  ADD KEY `fk_adoptante_mascota` (`id_mascota`);

--
-- Indices de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD PRIMARY KEY (`id_mascota`),
  ADD KEY `fk_mascota_tipo` (`id_tipo`);

--
-- Indices de la tabla `tiposmascota`
--
ALTER TABLE `tiposmascota`
  ADD PRIMARY KEY (`id_tipo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `adoptantes`
--
ALTER TABLE `adoptantes`
  MODIFY `id_adoptante` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  MODIFY `id_mascota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT de la tabla `tiposmascota`
--
ALTER TABLE `tiposmascota`
  MODIFY `id_tipo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `adoptantes`
--
ALTER TABLE `adoptantes`
  ADD CONSTRAINT `fk_adoptante_mascota` FOREIGN KEY (`id_mascota`) REFERENCES `mascotas` (`id_mascota`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD CONSTRAINT `fk_mascota_tipo` FOREIGN KEY (`id_tipo`) REFERENCES `tiposmascota` (`id_tipo`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
