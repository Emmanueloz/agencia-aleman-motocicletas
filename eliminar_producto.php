<?php
require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/Productos.php';

$mensaje = "No se pudo eliminar el producto";

if(isset($_GET['id']))
{
    $mysqli = new mysqli($servidor, $usuario, $password, $bd);
    $id = $_GET['id'];
    print_r($id);

}