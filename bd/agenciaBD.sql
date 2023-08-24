-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 24-08-2023 a las 07:23:27
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `agenciaBD`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL COMMENT 'Numero de identificacion para el cliente',
  `rfc` varchar(14) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Registro Federal del Contribuyente, del cliente',
  `nombre` varchar(45) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre completo del cliente',
  `direccion` varchar(100) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Direccion del cliente donde vive.',
  `telefono` varchar(10) COLLATE utf8_spanish_ci NOT NULL COMMENT 'numero de telefono',
  `correo` varchar(45) COLLATE utf8_spanish_ci NOT NULL COMMENT 'correro electronico personal',
  `genero` varchar(1) COLLATE utf8_spanish_ci NOT NULL COMMENT 'genero del cliente',
  `estado` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `rfc`, `nombre`, `direccion`, `telefono`, `correo`, `genero`, `estado`) VALUES
(1, 'hgffghfghfghh', 'marcus', 'dasdsads', '3535353535', 'example@sads.com', 'M', 1),
(2, 'awdaasdasdasw', 'David', 'dwadasdsadasd', '1231231231', 'da@sads.com', 'M', 1),
(3, 'ssssssssssss', 'David Emmanuel Ozuna Navarro', 'Barrio Linda Vista', '1431543654', 'myEmai13@hotmail.com', 'M', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_servicio`
--

CREATE TABLE `cliente_servicio` (
  `id_servicio` int(11) NOT NULL COMMENT 'Id del servicio',
  `id_cliente` int(11) NOT NULL COMMENT 'Llave foranea del cliente',
  `fecha_servicio` date NOT NULL COMMENT 'fecha del servicio'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_servicios`
--

CREATE TABLE `detalles_servicios` (
  `id_servicio` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `tipo_servicio` varchar(45) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalles_venta`
--

CREATE TABLE `detalles_venta` (
  `id_venta` int(11) NOT NULL COMMENT 'Lave foranea de la venta',
  `id_producto` int(11) NOT NULL COMMENT 'Lave foranea del producto',
  `cantidad` int(11) NOT NULL COMMENT 'Cantidad a comprar',
  `costo` double NOT NULL COMMENT 'costo total de la vena'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `detalles_venta`
--

INSERT INTO `detalles_venta` (`id_venta`, `id_producto`, `cantidad`, `costo`) VALUES
(1, 1, 2, 304875.84),
(2, 1, 1, 152437.92),
(3, 2, 3, 6960),
(4, 1, 2, 309515.84),
(4, 2, 2, 309515.84),
(5, 2, 3, 6960),
(6, 2, 1, 2320),
(7, 1, 1, 152437.92),
(8, 1, 1, 152437.92),
(9, 1, 1, 152437.92),
(10, 1, 2, 304875.84),
(11, 1, 2, 332973.36),
(11, 4, 2, 332973.36),
(12, 2, 4, 9280),
(13, 1, 4, 1038247.9776),
(13, 3, 3, 1038247.9776);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id_empleado` int(11) NOT NULL COMMENT 'Numero de identificacion para el empleado\n',
  `rfc` varchar(45) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Registro Federal del Contribuyente, del empleado',
  `nombre` varchar(45) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Nombre propio del empleado',
  `direccion` varchar(100) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Direccion del empleado donde vive.',
  `telefono` varchar(10) COLLATE utf8_spanish_ci NOT NULL COMMENT 'numero de telefono',
  `correo` varchar(45) COLLATE utf8_spanish_ci NOT NULL COMMENT 'correro electronico personal',
  `puesto` varchar(45) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Puesto que ocupa el empleado',
  `salario` double NOT NULL COMMENT 'Salario que tiene el empleado',
  `estudios` varchar(45) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Estudios con los que cuenta el empleado',
  `estado` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id_empleado`, `rfc`, `nombre`, `direccion`, `telefono`, `correo`, `puesto`, `salario`, `estudios`, `estado`) VALUES
(1, 'asdsa1sdsadsa', 'fernando', 'tonina', '1213131313', 'da@sads.com', 'tecnico', 1341, 'técnico', 1),
(2, 'RFC1234561314', 'Roberto', 'mi casa', '1333333333', 'example@sads.com', 'dsadas', 3131313, 'bachillerato', 1),
(3, 'RFC1234561SDA', 'David', 'Centro', '3143413131', 'da@sads.com1', 'Analista', 1000, 'bachillerato', 1),
(4, 'weqewqeqweqwe', 'David', 'Tonina', '9191919191', 'dsd@assd.com', 'tecnico', 1000, 'bachillerato', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL COMMENT 'numero de identificacion del producto',
  `numero_serie` varchar(20) COLLATE utf8_spanish_ci NOT NULL COMMENT 'numero de serie de cada producto',
  `marca` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'marca de los productos ',
  `descripcion` varchar(100) COLLATE utf8_spanish_ci NOT NULL COMMENT 'descripcion breve del producto',
  `modelo` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'modelo del producto en existencia',
  `precio` double NOT NULL COMMENT 'precio de venta de cada producto sin el IVA',
  `existencias` int(11) NOT NULL COMMENT 'cantidad de producto que hay en existencia',
  `estado` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `numero_serie`, `marca`, `descripcion`, `modelo`, `precio`, `existencias`, `estado`) VALUES
(1, 'fdsf4werwerwdsf1saas', 'yamaha', 'mi motocicletas', 'y-10', 131412, 26, 1),
(2, '21312321', 'ktm', 'moto deportiva\r\n', '200', 2000, 9, 1),
(3, 'adasda', 'suzuki', 'asdasdasd', 'assad', 123131.12, 7, 1),
(4, 'sdasddqwv313', 'yamaha', 'cuatrimoto', '2000', 12111, 10, 1),
(5, 'qwrqrqwrq', 'kawasaki', 'Mi descripción\r\n', 'sad', 1441, 14, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `usuario` varchar(30) COLLATE utf8_spanish_ci NOT NULL COMMENT 'Usuario unico',
  `nombre` varchar(45) COLLATE utf8_spanish_ci NOT NULL COMMENT 'nombre del que le pertenece la cuenta',
  `password` varchar(100) COLLATE utf8_spanish_ci NOT NULL COMMENT 'contraseña del usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`usuario`, `nombre`, `password`) VALUES
('admin', 'David', 'b1b3773a05c0ed0176787a4f1574ff0075f7521e'),
('admin2', 'david2', 'd6a9450dc08555d6ecfaf7162e5267f401e6dd9a');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL COMMENT 'numero de la identificacion',
  `subtotal` float NOT NULL COMMENT 'total de las compras sin IVA',
  `iva` float NOT NULL COMMENT 'el IVA de todas las compras',
  `id_empleado` int(11) NOT NULL COMMENT 'id del empleado que realiza la venta',
  `id_cliente` int(11) NOT NULL COMMENT 'id del cliente que realiza su compra',
  `fecha_venta` date NOT NULL COMMENT 'fecha en que se realizo la venta'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_venta`, `subtotal`, `iva`, `id_empleado`, `id_cliente`, `fecha_venta`) VALUES
(1, 262824, 42051.8, 1, 1, '2023-08-10'),
(2, 131412, 21025.9, 1, 1, '2023-08-14'),
(3, 6000, 960, 1, 1, '2023-08-14'),
(4, 266824, 42691.8, 1, 1, '2023-08-14'),
(5, 6000, 960, 1, 1, '2023-08-14'),
(6, 2000, 320, 1, 1, '2023-08-14'),
(7, 131412, 21025.9, 1, 1, '2023-08-14'),
(8, 131412, 21025.9, 1, 2, '2023-08-14'),
(9, 131412, 21025.9, 1, 2, '2023-08-14'),
(10, 262824, 42051.8, 1, 1, '2023-08-14'),
(11, 287046, 45927.4, 2, 3, '2023-08-15'),
(12, 8000, 1280, 1, 1, '2023-08-17'),
(13, 895041, 143207, 3, 1, '2023-08-17');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `id_cliente_UNIQUE` (`id_cliente`);

--
-- Indices de la tabla `cliente_servicio`
--
ALTER TABLE `cliente_servicio`
  ADD PRIMARY KEY (`id_servicio`),
  ADD UNIQUE KEY `id_cliente_servicio_UNIQUE` (`id_servicio`),
  ADD KEY `fk_clientes_has_tipo_servicios_clientes1_idx` (`id_cliente`);

--
-- Indices de la tabla `detalles_servicios`
--
ALTER TABLE `detalles_servicios`
  ADD PRIMARY KEY (`id_servicio`,`id_producto`),
  ADD KEY `fk_productos_has_cliente_servicio_cliente_servicio1_idx` (`id_servicio`),
  ADD KEY `fk_productos_has_cliente_servicio_productos1_idx` (`id_producto`);

--
-- Indices de la tabla `detalles_venta`
--
ALTER TABLE `detalles_venta`
  ADD KEY `fk_productos_has_ventas_ventas1_idx` (`id_venta`),
  ADD KEY `fk_productos_has_ventas_productos1_idx` (`id_producto`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id_empleado`),
  ADD UNIQUE KEY `id_empleado_UNIQUE` (`id_empleado`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD UNIQUE KEY `id_producto_UNIQUE` (`id_producto`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usuario`),
  ADD UNIQUE KEY `usuario_UNIQUE` (`usuario`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`),
  ADD UNIQUE KEY `id_venta_UNIQUE` (`id_venta`),
  ADD KEY `fk_compras_empleados1_idx` (`id_empleado`),
  ADD KEY `fk_compras_clientes1_idx` (`id_cliente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Numero de identificacion para el cliente', AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `cliente_servicio`
--
ALTER TABLE `cliente_servicio`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id del servicio';

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id_empleado` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Numero de identificacion para el empleado\n', AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT COMMENT 'numero de identificacion del producto', AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT COMMENT 'numero de la identificacion', AUTO_INCREMENT=14;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cliente_servicio`
--
ALTER TABLE `cliente_servicio`
  ADD CONSTRAINT `fk_clientes_has_tipo_servicios_clientes1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `detalles_servicios`
--
ALTER TABLE `detalles_servicios`
  ADD CONSTRAINT `fk_productos_has_cliente_servicio_cliente_servicio1` FOREIGN KEY (`id_servicio`) REFERENCES `cliente_servicio` (`id_servicio`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_productos_has_cliente_servicio_productos1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `detalles_venta`
--
ALTER TABLE `detalles_venta`
  ADD CONSTRAINT `fk_ventas_productos_productos1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ventas_productos_ventas1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `fk_compras_clientes1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_compras_empleados1` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id_empleado`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
