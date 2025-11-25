-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-11-2025 a las 20:53:11
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
-- Base de datos: `micelanea`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `tipo_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `precio`, `stock`, `tipo_id`) VALUES
(1, 'Grapadora metálica', 8500.00, 20, 1),
(2, 'Cinta adhesiva 18mm', 2500.00, 30, 1),
(3, 'Corrector líquido', 3000.00, 25, 1),
(4, 'Resaltador amarillo', 2000.00, 35, 1),
(5, 'Marcador permanente negro', 2500.00, 40, 1),
(6, 'Tijeras oficina', 3500.00, 20, 1),
(7, 'Carpeta plastificada', 1800.00, 50, 1),
(8, 'Bloc de notas', 1500.00, 45, 1),
(9, 'Regla 30 cm', 1200.00, 30, 1),
(10, 'Bolígrafo azul', 1000.00, 60, 1),
(11, 'Cuaderno argollado', 3200.00, 20, 2),
(12, 'Lápiz HB', 800.00, 100, 2),
(13, 'Borrador blanco', 1000.00, 40, 2),
(14, 'Tajalápiz plástico', 1200.00, 25, 2),
(15, 'Colores x12 unidades', 4500.00, 15, 2),
(16, 'Temperas x6 colores', 3000.00, 20, 2),
(17, 'Pegante escolar', 1800.00, 50, 2),
(18, 'Cartulina blanca', 700.00, 80, 2),
(19, 'Plastilina x12 barras', 5000.00, 18, 2),
(20, 'Compás metálico', 4000.00, 22, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos_producto`
--

CREATE TABLE `tipos_producto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipos_producto`
--

INSERT INTO `tipos_producto` (`id`, `nombre`) VALUES
(1, 'Oficina'),
(2, 'Escolar');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipo_id` (`tipo_id`);

--
-- Indices de la tabla `tipos_producto`
--
ALTER TABLE `tipos_producto`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `tipos_producto`
--
ALTER TABLE `tipos_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`tipo_id`) REFERENCES `tipos_producto` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
