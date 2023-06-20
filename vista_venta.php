<?php
require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/DetallesVentas.php';
require_once 'models/Ventas.php';

$titulo = "Lista de ventas";
$html = new SpynTPL('views/');
$html->Fichero('ventas.html');
$html->Asigna('title', $titulo);

## Opciones
$html->Asigna('op_id', '');
$html->Asigna('op_fecha', '');
$html->Asigna('value', '');

// Objeto de la base de datos
$mysqli = new mysqli($servidor, $usuario, $password, $bd);

// Iniciando parámetros
Ventas::init($mysqli);
DetallesVentas::init($mysqli);

/* $idProductos = [3, 1, 5];
$cantidad = count($idProductos);

$subtotal = DetallesVentas::obtenerSubtotal($idProductos);
$iva = Ventas::generarIva($subtotal);
$costo = DetallesVentas::calcularCosto($subtotal, $iva);

$idVenta = Ventas::obtenerIdVenta();
$fechaActual = date("Y-m-d");*/

if (isset($_POST['search'])  && !empty(trim($_POST['search']))) {
    $opcion = $_POST['opcion'];
    $search = $_POST['search'];

    switch ($opcion) {
        case 'id':
            $html->Asigna('op_id', 'selected');
            break;
        case 'fecha':
            $html->Asigna('op_fecha', 'selected');
            break;
    }

    $ventas = Ventas::consultaFiltrada($opcion, $search);
    if (count($ventas) == 0) {
        $html->AsignaBloque('ventas', null);
    }

    $html->Asigna('value', $search);
} else {
    $ventas = Ventas::consultarVentas();
}

foreach ($ventas as $venta) {
    $html->AsignaBloque('ventas', $venta);
}

echo $html->Muestra();
