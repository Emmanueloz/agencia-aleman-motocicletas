<?php

session_start();
require_once 'models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ./index.php');
}

require_once 'models/elementos.php';
require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/Clientes.php';

$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Clientes::init($mysqli);
if (isset($_POST['rfc'])) {
    $rfc = $_POST['rfc'];
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $genero = $_POST['genero'];

    try {
        $clientes = new Clientes(null, $rfc, $nombre, $direccion, $telefono, $correo, $genero);
        $idClient = $clientes->agregar();
        unset($_POST);
        header("Location: vista_clientes.php?opcion=idcli&value=$idClient");
    } catch (Exception $e) {
        $msgerror = $e->getMessage();
        header("Location: agregar_clientes.php?error=$msgerror");
    }
}
$title = 'Agregar nuevo cliente';
$target = 'agregar_clientes.php';

$html = new SpynTPL('views/');
$html->Fichero('frmCliente.html');
$html->Asigna('title', $title);
$html->Asigna('target', $target);
$html->Asigna('btn-form', 'Agregar');

$html->Asigna('rfc', '');
$html->Asigna('nombre', '');
$html->Asigna('direccion', '');
$html->Asigna('telefono', '');
$html->Asigna('correo', '');
$html->Asigna('genero', '');
$nav = navBar('clientes');
$html->Asigna('nav-bar', $nav);
$html->Asigna('error', '');
if (isset($_GET['error'])) {
    $msgerror = $_GET['error'];
    $errormsg = "<div class='alert alert-danger' role='alert'>$msgerror</div>";
    $html->Asigna('error', $errormsg);
}
echo $html->Muestra();
