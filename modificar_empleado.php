<?php

// Comprobar si hay una sesión iniciada
session_start();
require_once 'models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ./index.php');
}

require_once 'models/elementos.php';
require_once('SpynTPL.php');
require_once('models/config.php');
require_once('models/Empleados.php');

$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Empleados::init($mysqli);

$title = "Modificar empleado";
$target = "modificar_empleado.php";

$html =  new SpynTPL('views/');
$html->Fichero('frmEmpleados.html');
$html->Asigna('title', $title);
$html->Asigna('target', $target);
$html->Asigna('btn-form', 'Modificar');
$nav = navBar('empleados');
$html->Asigna('nav-bar', $nav);

if (isset($_GET['id_empleado'])) {
    $id_empleado = $_GET['id_empleado'];
    $empledos = Empleados::consultaEmpleadoId($id_empleado);
    $html->Asigna('id_empleado', $empledos->id_empleado);
    $html->Asigna('rfc', $empledos->rfc);
    $html->Asigna('nombre', $empledos->nombre);
    $html->Asigna('direccion', $empledos->direccion);
    $html->Asigna('telefono', $empledos->telefono);
    $html->Asigna('correo', $empledos->correo);
    $html->Asigna('salario', $empledos->salario);
    switch ($empledos->puesto) {
        case 'analista':
            $html->Asigna('analista_s', 'selected');
            break;
        case 'gerente':
            $html->Asigna('gerente_s', 'selected');
            break;
        case 'mecanico':
            $html->Asigna('mecanico_s', 'selected');
            break;
        case 'vendedor':
            $html->Asigna('vendedor_s', 'selected');
            break;
    }
    //$html->Asigna('estudio', $empledos->estudios);
    switch ($empledos->estudios) {
        case 'bachillerato':
            $html->Asigna('bachillerato_s', 'selected');
            break;
        case 'técnico':
            $html->Asigna('tecnico_s', 'selected');
            break;
        case 'licenciatura':
            $html->Asigna('licenciatura_s', 'selected');
            break;
        case 'maestria':
            $html->Asigna('maestria_s', 'selected');
            break;
    }
} else if (isset($_POST['id_empleado'])) {
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
