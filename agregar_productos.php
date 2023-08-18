<?php

// Comprobar si hay una sesión iniciada
session_start();
require_once 'models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ./index.php');
}

require_once 'models/elementos.php';
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

    try {
        $producto = new Productos(0, $numero_serie, $marca, $descripcion, $modelo, $precio, $existencias);
        $idproducto = $producto->agregarProducto();
        unset($_POST);
        header("Location: consulta_productos.php?opcion=id&value=$idproducto");
    } catch (Exception $e) {
        $errorMsg = $e->getMessage();
        header("Location: agregar_productos.php?error=$errorMsg");
    }
    #print_r($_POST);
}
$title = 'Agregar nuevo producto';
$target = 'agregar_productos.php';
#$products = Productos::consultaProductos();
$html = new SpynTPL('views/');
$html->Fichero('frmProducto.html');
$html->Asigna('title', $title);
$html->Asigna('target', $target);
$html->Asigna('btn-form', 'Agregar');

$html->Asigna('numero_serie', '');
$html->Asigna('marca', '');
$html->Asigna('modelo', '');
$html->Asigna('descripcion', '');
$html->Asigna('precio', '');
$html->Asigna('existencias', '');
$nav = navBar('productos');
$html->Asigna('nav-bar', $nav);
$html->Asigna('errormsg', '');
if (isset($_GET['error'])) {
    $msgerror = $_GET['error'];
    $errormsg = "<div class='alert alert-danger' role='alert'>$msgerror</div>";
    $html->Asigna('errormsg', $errormsg);
}

echo $html->Muestra();
