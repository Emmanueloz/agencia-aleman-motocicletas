<?php

// Comprobar si hay una sesión iniciada
session_start();
require_once 'models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ./index.php');
}

require_once './models/elementos.php';

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
$html->Asigna('title', 'Lista de empleados');
$html->Asigna('id_s', '');
$html->Asigna('rfc_s', '');
$html->Asigna('nombre_s', '');
$html->Asigna('salario_s', '');
$html->Asigna('estudios_s', '');
$html->Asigna('value', '');
$html->Asigna('limpiar_filtro', '');
$html->Asigna('mensaje', '');



$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Empleados::init($mysqli);

$totalPaginas = Empleados::totalPaginas(5);

if (isset($_GET['value']) && !empty(trim($_GET['value']))) {
    $opcion = $_GET['opcion'];
    $value = $_GET['value'];
    $html->Asigna('link_report', "reportEmpleados.php?opcion=$opcion&value=$value");
    $html->Asigna('reporte', 'Reporte de consulta');
    $buttonFiltro = bntLimpiarFiltro('vista_empleados.php');

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
            $html->Asigna('salarios_s', 'selected');
            break;
        case 'estudios':
            $html->Asigna('estudios_s', 'selected');
    }
    $emples = Empleados::filtro($opcion, $value);
    if (count($emples) == 0) {
        $html->AsignaBloque('emplea2', null);
        $mensaje = "<h4 class='text-secondary text-center' >No se encontró ningún empleado.</h4>";
        $html->Asigna('mensaje', $mensaje);
    }
    $html->AsignaBloque('paginas', null);

    $html->Asigna('value', $value);
    $html->Asigna('limpiar_filtro', $buttonFiltro);
} else {

    $paginaActual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
    $emples = Empleados::consul($paginaActual, 5);

    if (count($emples) == 0) {

        $html->AsignaBloque('paginas', null);
        $html->AsignaBloque('emplea2', null);
        $mensaje = "<h4 class='text-secondary text-center' >No se encontró ningún empleado. Agrega una empleado</h4>";
        $html->Asigna('mensaje', $mensaje);
    }

    $html->Asigna('link_report', "reportEmpleados.php");
    $html->Asigna('reporte', 'Reporte General');

    for ($pa = 1; $pa <= $totalPaginas; $pa++) {
        $pagina['active'] = '';
        $pagina['pagina'] = $pa;

        if ($pa == $paginaActual) {
            $pagina['active'] = 'active';
        }

        $html->AsignaBloque('paginas', $pagina);
    }
}




foreach ($emples as $empleado) {
    $html->AsignaBloque('emplea2', $empleado);
}
echo $html->Muestra();
