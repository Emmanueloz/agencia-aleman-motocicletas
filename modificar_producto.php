<?php

// Comprobar si hay una sesión iniciada
session_start();
require_once 'models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ./index.html');
}


require_once('SpynTPL.php');
require_once('models/config.php');
require_once('models/Productos.php');
$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Productos::init($mysqli);

$title = 'Editar película';
$target = 'modificar_producto.php';
$productos = Productos::findAll();
$html = new SpynTPL('views/');
$html->Fichero('frmProducto.html');
$html->Asigna('title', $title);
$html->Asigna('target', $target);
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $productos = Productos::consultaProductoId($id);
    $html->Asigna('id_producto', $productos->id_producto);
    $html->Asigna('numero_serie', $productos->numero_serie);
    $html->Asigna('marca', $productos->marca);
    $html->Asigna('descripcion', $productos->descripcion);
    $html->Asigna('modelo', $productos->modelo);
    $html->Asigna('precio', $productos->precio);
    $html->Asigna('existencias', $productos->existencias);
}
elseif(isset($_POST['id_producto']))
{
    $id_producto = $_POST['id_producto'];
    $productos = Productos::consultaProductoId($id_producto);
    $productos->numero_serie = $_POST['numero_serie'];
    $productos->marca = $_POST['marca'];
    $productos->modelo = $_POST['modelo'];
    $productos->descripcion = $_POST['descripcion'];
    $productos->precio = $_POST['precio'];
    $productos->existencias = $_POST['existencias'];
    
    $productos->modificar();
    unset($_POST);
    header("Location: consulta_productos.php");
}



echo $html->Muestra();
