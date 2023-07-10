<?php

// Comprobar si hay una sesión iniciada
session_start();
require_once 'models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ./index.html');
}
require_once ('SpynTPL.php');
require_once ('models/config.php');
require_once ('models/Empleados.php');

$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Empleados::init($mysqli);

$title = "modificar empleado";
$target = "modificar_empleado.php";

$html =  new SpynTPL('views/');
$html->Fichero('frmEmpleados.html');
$html->Asigna('title', $title);
$html->Asigna('target', $target);

if (isset($_GET['id_empleado']))
{
    $id_empleado = $_GET['id_empleado'];
    $empledos = Empleados::consultaEmpleadoId($id_empleado);
    $html->Asigna('id_empleado', $empledos->id_empleado);
    $html->Asigna('rfc', $empledos->rfc);
    $html->Asigna('nombre', $empledos->nombre);
    $html->Asigna('direccion', $empledos->direccion);
    $html->Asigna('telefono', $empledos->telefono);
    $html->Asigna('correo', $empledos->correo);
    $html->Asigna('puesto', $empledos->puesto);
    $html->Asigna('salario', $empledos->salario);
    $html->Asigna('estudio', $empledos->estudios);
}
else if (isset($_POST['id_empleado']))
{
    $id_empleado = $_POST['id_empleado'];
    $empledos = Empleados::consultaEmpleadoId($id_empleado);
    $empledos->rfc = $_POST['rfc'];
    $empledos->nombre = $_POST['nombre'];
    $empledos->direccion = $_POST['direccion'];
    $empledos->telefono = $_POST['telefono'];
    $empledos->correo = $_POST['correo'];
    $empledos->puesto = $_POST['puesto'];
    $empledos->salario = $_POST['salario'];
    $empledos->estudios = $_POST['estudios'];

    $empledos->update($mysqli);
    unset($_POST);
header('Location: vista_empleados.php');
}



echo $html->Muestra();
