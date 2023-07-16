<?php

// Comprobar si hay una sesión iniciada
session_start();
require_once 'models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ./index.php');
}


require_once 'SpynTPL.php';
require_once 'models/config.php';

$mysqli = new mysqli($servidor, $usuario, $password, $bd);
if (isset($_POST['numero_serie'])) {

    require_once 'models/Productos.php';

    Productos::init($mysqli);


    $numero_serie = $_POST['numero_serie'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $existencias = $_POST['existencias'];

    $producto = new Productos(0, $numero_serie, $marca, $descripcion, $modelo, $precio, $existencias);
    $producto->save();
    unset($_POST);
    header("Location: consulta_productos.php");
    #print_r($_POST);
}
$title = 'Agregar nuevo producto';
$target = 'agregar_productos.php';
#$products = Productos::consultaProductos();
$html = new SpynTPL('views/');
$html->Fichero('frmProducto.html');
$html->Asigna('title', $title);
$html->Asigna('target', $target);

$html->Asigna('numero_serie', '');
$html->Asigna('marca', '');
$html->Asigna('modelo', '');
$html->Asigna('descripcion', '');
$html->Asigna('precio', '');
$html->Asigna('existencias', '');

echo $html->Muestra();
