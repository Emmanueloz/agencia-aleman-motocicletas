<?php
require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/DetallesVentas.php';
require_once 'models/Ventas.php';

// Objeto de la base de datos
$mysqli = new mysqli($servidor, $usuario, $password, $bd);

// Iniciando parámetros
Ventas::init($mysqli);
DetallesVentas::init($mysqli);

$idProductos = [3, 1, 5];
$cantidad = count($idProductos);

$subtotal = DetallesVentas::obtenerSubtotal($idProductos);
$iva = Ventas::generarIva($subtotal);
$costo = DetallesVentas::calcularCosto($subtotal, $iva);

$idVenta = Ventas::obtenerIdVenta();
$fechaActual = date("Y-m-d");

$html = new SpynTPL('views/');
$html->Fichero('ventas.html');


$html->Muestra();
