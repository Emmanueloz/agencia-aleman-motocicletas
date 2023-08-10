<?php
// Comprobar si hay una sesión iniciada
session_start();
require_once 'models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ./index.php');
}

require_once './models/elements.php';
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
$html->Asigna('msg', '');
$html->Asigna('isDisabled', '');

$nav = navBar('ventas');
$html->Asigna('nav-bar', $nav);

$empleados = Empleados::consul();


$clientes = Clientes::consulta();

$productos = Productos::consultaProductos();

if (isset($_GET['error'])) {
    $content = $_GET['error'];
    $msgError = "<div class='alert alert-danger' role='alert'>$content</div>";
    $html->Asigna('msg', $msgError);
}

if (count($empleados) > 0) {

    foreach ($empleados as $empleado) {
        $html->AsignaBloque("empleados", $empleado);
    }
} else {
    $html->AsignaBloque("empleados", null);
    $msgError = "<div class='alert alert-danger' role='alert'>Agrega empleados para realizar una venta</div>";
    $html->Asigna('msg', $msgError);
    $html->Asigna('isDisabled', 'disabled');
}

if (count($clientes) > 0) {
    foreach ($clientes as $cliente) {
        $html->AsignaBloque("clientes", $cliente);
    }
} else {
    $html->AsignaBloque("clientes", null);
    $msgError = "<div class='alert alert-danger' role='alert'>Agrega clientes para realizar una venta</div>";
    $html->Asigna('msg', $msgError);
    $html->Asigna('isDisabled', 'disabled');
}

if (count($productos) > 0) {
    foreach ($productos as $producto) {
        $html->AsignaBloque("productos", $producto);
    }
} else {
    $html->AsignaBloque("productos", null);
    $msgError = "<div class='alert alert-danger' role='alert'>Agrega productos para realizar una venta</div>";
    $html->Asigna('msg', $msgError);
    $html->Asigna('isDisabled', 'disabled');
}


#print_r($clientes);
echo $html->Muestra();
