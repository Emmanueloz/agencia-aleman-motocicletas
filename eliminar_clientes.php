<?php

session_start();
require_once 'models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ./index.html');
}

require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/Clientes.php';

$msg = 'No se pudo eliminar el registro';

if(isset($_GET['id_cliente']))
{
    $mysqli = new mysqli($servidor, $usuario, $password, $bd);
    $id_client = $_GET['id_cliente'];
    Clientes::init($mysqli);
    $client = Clientes::buscarid($id_client);
    $client->eliminar($mysqli);
    $msg = 'Registro eliminado';
}

header("Location: vista_clientes.php?msg=$msg");