<?php
require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/DetallesVentas.php';
require_once 'models/Ventas.php';

$titulo = "Lista de ventas";
$html = new SpynTPL('views/');
$html->Fichero('ventas.html');
$html->Asigna('title', $titulo);
$html->Asigna('mensaje', ' ');

## Opciones
$html->Asigna('op_id', '');
$html->Asigna('op_fecha', '');
$html->Asigna('op_empleados', "");
$html->Asigna('op_clientes', '');

$html->Asigna('value', '');

// Objeto de la base de datos
$mysqli = new mysqli($servidor, $usuario, $password, $bd);

// Iniciando parámetros
Ventas::init($mysqli);
DetallesVentas::init($mysqli);
Empleados::init($mysqli);
Clientes::init($mysqli);
Productos::init($mysqli);


if (isset($_GET['search'])  && !empty(trim($_GET['search']))) {
    $opcion = $_GET['opcion'];
    $search = $_GET['search'];

    switch ($opcion) {
        case 'id':
            $html->Asigna('op_id', 'selected');
            break;
        case 'fecha':
            $html->Asigna('op_fecha', 'selected');
            break;
        case 'empleados':
            $html->Asigna('op_empleados', "selected");
            break;
        case 'clientes':
            $html->Asigna('op_clientes', 'selected');
            break;
        case 'productos':
            $html->Asigna('op_productos', 'selected');
            break;
    }

    if ($opcion == "id" || $opcion == "fecha") {
        $ventas = Ventas::consultaFiltrada($opcion, $search);
    } else {
        $ventas = Ventas::consultaFiltradaRelacionada($opcion, $search);
    }

    if (count($ventas) == 0) {
        $html->AsignaBloque('ventas', null);
        $mensaje = "<h4 class='text-secondary text-center' >No se encontró ninguna venta</h4>";
        $html->Asigna('mensaje', $mensaje);
    }
    $html->Asigna('reporte', 'Reporte de consulta');
    $html->Asigna('value', $search);
} else {
    $html->Asigna('reporte', 'Reporte general');
    $ventas = Ventas::consultarVentas();
}


foreach ($ventas as $venta) {
    $html->AsignaBloque('ventas', $venta);
}

echo $html->Muestra();
