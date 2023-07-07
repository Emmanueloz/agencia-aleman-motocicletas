<?php
require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/Empleados.php';

$msg = 'No se pudo eliminar el registro';

if (isset($_GET['id_empleado']))
{
$mysqli = new mysqli($servidor, $usuario, $password, $bd);
$id_empleado = $_GET['id_empleado'];
Empleados::init($mysqli);
$empledos = Empleados::consultaEmpleadoId($id_empleado);
$empledos->Eliminar($mysqli);
$msg = 'Registro Eliminado';
}
header('Location: vista_empleados.php');
