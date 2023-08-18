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
require_once 'models/Empleados.php';

$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Empleados::init($mysqli);

if (isset($_POST['rfc'])) {
    $rfc = $_POST['rfc'];
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $puesto = $_POST['puesto'];
    $salario = $_POST['salario'];
    $estudios = $_POST['estudios'];

    try {
        $empleado = new Empleados(0, $rfc, $nombre, $direccion, $telefono, $correo, $puesto, $salario, $estudios);
        $id_empleados = $empleado->nuev($mysqli);
        unset($_POST);
        header("Location: vista_empleados.php?opcion=id&value=$id_empleados");
    } catch (Exception $e) {
        $msgerror = $e->getMessage();
        header("Location: agregar_empleados.php?error=$msgerror");
    }
}

$title = 'Agregar nuevo empleado';
$target = 'agregar_empleados.php';


$html = new SpynTPL('views/');
$html->Fichero('frmEmpleados.html');
$html->Asigna('title', $title);
$html->Asigna('target', $target);
$html->Asigna('btn-form', 'Agregar');

$html->Asigna('rfc', '');
$html->Asigna('nombre', '');
$html->Asigna('direccion', '');
$html->Asigna('telefono', '');
$html->Asigna('correo', '');
$html->Asigna('puesto', '');
$html->Asigna('salario', '');
$html->Asigna('estudio', '');
$nav = navBar('empleados');
$html->Asigna('nav-bar', $nav);
$html->Asigna('error', '');
if (isset($_GET['error'])) {
    $msgerror = $_GET['error'];
    $msgeror = "<div class='alert alert-danger' role='alert'>$msgerror</div>";
    $html->Asigna('error', $msgeror);
}

echo $html->Muestra();
