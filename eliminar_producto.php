<?php
require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/Productos.php';

$mensaje = "No se pudo eliminar el producto";

if(isset($_GET['id']))
{
    $mysqli = new mysqli($servidor, $usuario, $password, $bd);
    $id = $_GET['id'];
    Productos::init($mysqli);
    $producto = Productos::consultaProductoId($id);
    $producto->eliminarProducto();
    $mensaje = "Registro eliminado";
    header("Location: consulta_productos.php");

}
