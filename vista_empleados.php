<?php
require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/Empleados.php';

$msg = '';
if (isset($_GET['msg'])) {
    $content = $_GET['msg'];
    $msg = "<div class='alert alert-success alert-dismissible fade show'
    role='alert'><button type='button' class='btn-close'
    data-bs-dismiss='alert' aria-label='Close'></button>$content</div>";
}

$html = new SpynTPL('views/');
$html->Fichero('empleados.html');
$html->Asigna('msg', $msg);
$html->Asigna('title', 'lista de empleados');
$html->Asigna('id_empleado_s', '');
$html->Asigna('rfc_s', '');
$html->Asigna('nombre_s', '');
$html->Asigna('salario_s', '');
$html->Asigna('estudios_s', '');
$html->Asigna('value', '');


$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Empleados::init($mysqli);

if (isset($_GET['value']) && !empty(trim($_GET['value']))) {
    $opcion = $_GET['opcion'];
    $value = $_GET['value'];
    $html->Asigna('link_report', "reportEmpleados.php?opcion=$opcion&value=$value");
    $html->Asigna('reporte', 'Reporte de consulta');




    switch ($opcion) {
        case 'id':
            $html->Asigna('id_s', 'selected');
            break;
        case 'rfc':
            $html->Asigna('rfc_s', 'selected');
            break;
        case 'nombre':
            $html->Asigna('nombre_s', 'selected');
            break;
        case 'salario':
            $html->Asigna('salario_s', 'selected');
            break;
        case 'estudios':
            $html->Asigna('estudios_s', 'selected');
    }
    $emples = Empleados::filtro($opcion, $value);
    if (count($emples) == 0) {
        $html->AsignaBloque('emplea2', null);
    }
    $html->Asigna('value', $value);
} else {
    $emples = Empleados::consul();
    $html->Asigna('link_report', "reportEmpleados.php");
    $html->Asigna('reporte', 'Reporte General');
}


foreach ($emples as $empleado) {
    $html->AsignaBloque('emplea2', $empleado);
}
echo $html->Muestra();
