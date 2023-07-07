<?php

require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/Clientes.php';

$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Clientes::init($mysqli);

if(isset($_POST['rfc']))
{
    $rfc = $_POST['rfc'];
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $genero = $_POST['genero'];

    $clientes = new Clientes(null, $rfc, $nombre, $direccion, $telefono, $correo, $genero);
    $clientes->agregar();
    unset($_POST);
    header('Location: vista_clientes.php');
}
$title = 'Agregar nuevo cliente';
$target = 'agregar_clientes.php';

$html = new SpynTPL('views/');
$html->Fichero('frmCliente.html');
$html->Asigna('title', $title);
$html->Asigna('target', $target);

$html->Asigna('rfc', '');
$html->Asigna('nombre', '');
$html->Asigna('direccion', '');
$html->Asigna('telefono', '');
$html->Asigna('correo', '');
$html->Asigna('genero', '');

echo $html->Muestra();