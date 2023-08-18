<?php

session_start();
require_once 'models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ./index.php');
}

require_once 'models/elementos.php';
require_once('SpynTPL.php');
require_once('models/config.php');
require_once('models/Clientes.php');

$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Clientes::init($mysqli);

$title = 'Modificar cliente';
$target = 'modificar_clientes.php';

$html = new SpynTPL('views/');
$html->Fichero('frmCliente.html');
$html->Asigna('title', $title);
$html->Asigna('target', $target);
$html->Asigna('btn-form', 'Modificar');

$nav = navBar('clientes');
$html->Asigna('nav-bar', $nav);

if (isset($_GET['id_cliente'])) {
    $id_cliente = $_GET['id_cliente'];

    $cliente = Clientes::buscarid($id_cliente);
    $html->Asigna('id_cliente', $cliente->id_cliente);
    $html->Asigna('rfc', $cliente->rfc);
    $html->Asigna('nombre', $cliente->nombre);
    $html->Asigna('direccion', $cliente->direccion);
    $html->Asigna('telefono', $cliente->telefono);
    $html->Asigna('correo', $cliente->correo);
    //$html->Asigna('genero', $cliente->genero);
    switch ($cliente->genero) {
        case 'M':
            $html->Asigna('masculino_c', 'checked');
            break;
        case 'F':
            $html->Asigna('femenino_c', 'checked');
            break;
    }
} else if (isset($_POST['id_cliente'])) {
    $id_cliente = $_POST['id_cliente'];
    $cliente = Clientes::buscarid($id_cliente);
    $cliente->id_cliente = $_POST['id_cliente'];
    $cliente->rfc = $_POST['rfc'];
    $cliente->nombre = $_POST['nombre'];
    $cliente->direccion = $_POST['direccion'];
    $cliente->telefono = $_POST['telefono'];
    $cliente->correo = $_POST['correo'];
    $cliente->genero = $_POST['genero'];

    $id_cliente = $cliente->modificar();
    unset($_POST);
    header("Location: vista_clientes.php?opcion=idcli&value=$id_cliente");
}

echo $html->Muestra();
