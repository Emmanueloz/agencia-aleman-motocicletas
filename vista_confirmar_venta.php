<?php

#print_r($_POST);
require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/DetallesVentas.php';
require_once 'models/Ventas.php';

$mysqli = new mysqli($servidor, $usuario, $password, $bd);


Ventas::init($mysqli);
DetallesVentas::init($mysqli);
Empleados::init($mysqli);
Clientes::init($mysqli);
Productos::init($mysqli);

$idEmpleado = $_POST["empleado"];
$nombreEmpleado =  Empleados::id_emple($idEmpleado);
$idCliente = $_POST["cliente"];;
$nombreCliente = Clientes::buscarnom($idCliente);

$idProductos = $_POST["productos"];

$html = new SpynTPL('views/');
$html->Fichero('confirmar_ventas.html');

$fechaVenta = $_POST["fecha-venta"];

$html->Asigna("id_empleado", $idEmpleado);
$html->Asigna("nombre_empleado", $nombreEmpleado);
$html->Asigna("id_cliente", $idCliente);
$html->Asigna("nombre_cliente", $nombreCliente);

$html->Asigna("fecha-venta", $fechaVenta);

print_r($idProductos);
echo $html->Muestra();
