<?php

// Comprobar si hay una sesión iniciada
session_start();
require_once 'models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ./index.php');
} else {
    require_once 'SpynTPL.php';
    require_once 'models/config.php';
    require_once 'models/Empleados.php';

    $msg = 'No se pudo eliminar el registro';

    if (isset($_GET['id_empleado'])) {
        $mysqli = new mysqli($servidor, $usuario, $password, $bd);
        $id_empleado = $_GET['id_empleado'];
        Empleados::init($mysqli);
        $empleados = Empleados::consultaEmpleadoId($id_empleado);
        $empleados->Eliminar();
        $msg = 'Registro Eliminado';
    }
    header("Location: vista_empleados.php?msg=$msg");
}
