<?php

// Comprobar si hay una sesión iniciada
session_start();
require_once 'models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ./index.html');
}
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
$html->Asigna('id_empleado_s', '');
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
    $buttonFiltro = '<a href="./vista_empleados.php" type="button" class="btn btn-outline-secondary rounded-0">
<svg
  xmlns="http://www.w3.org/2000/svg"
  width="16"
  height="16"
  fill="currentColor"
  class="bi bi-x-circle"
  viewBox="0 0 16 16"
>
  <path
    d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"
  ></path>
  <path
    d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"
  ></path>
</svg>
</a>';


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
        #$html->AsignaBloque('empleado', null);
        $html->AsignaBloque('paginas', null);
        $mensaje = "<h4 class='text-secondary text-center' >No se encontró ningún empleado.</h4>";
        $html->Asigna('mensaje', $mensaje);
    }
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
