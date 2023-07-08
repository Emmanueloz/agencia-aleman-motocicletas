<?php
require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/Ventas.php';

$mysqli = new mysqli($servidor, $usuario, $password, $bd);


Ventas::init($mysqli);

$titulo = "Agregar una nueva venta";
date_default_timezone_set('America/Mexico_City'); # Zona horaria para Mexico
$fecha = date("Y-m-d"); # colocar la fecha actual para el formulario de venta
$html = new SpynTPL('views/');
$html->Fichero('frmVentas.html');
$html->Asigna('title', $titulo);
$html->Asigna('mensaje', ' ');
$html->Asigna('fecha', $fecha);

$empleados = Empleados::consul();

$clientes = Clientes::consulta();

$productos = Productos::findAll();

foreach ($empleados as $empleado) {
    $html->AsignaBloque("empleados", $empleado);
}

foreach ($clientes as $cliente) {
    $html->AsignaBloque("clientes", $cliente);
}

foreach ($productos as $producto) {
    $html->AsignaBloque("productos", $producto);
}

#print_r($clientes);
echo $html->Muestra();
