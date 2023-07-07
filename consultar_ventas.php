<?php
require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/DetallesVentas.php';
require_once 'models/Ventas.php';

$titulo = "Lista de ventas";
$html = new SpynTPL('views/');
$html->Fichero('ventas.html');
$html->Asigna('title', $titulo);
$html->Asigna('mensaje', ' ');

## Opciones
$html->Asigna('op_id', '');
$html->Asigna('op_fecha', '');
$html->Asigna('op_empleados', "");
$html->Asigna('op_clientes', '');

$html->Asigna('value', '');
$html->Asigna('limpiar_filtro', '');

// Objeto de la base de datos
$mysqli = new mysqli($servidor, $usuario, $password, $bd);

// Iniciando parámetros
Ventas::init($mysqli);
Empleados::init($mysqli);
Clientes::init($mysqli);
Productos::init($mysqli);

$contenido = 5;
$totalPaginas = Ventas::totalPaginas($contenido);

if (isset($_GET['search'])  && !empty(trim($_GET['search']))) {
    $opcion = $_GET['opcion'];
    $search = $_GET['search'];

    $buttonFiltro = '<a href="./consultar_ventas.php" type="button" class="btn btn-outline-secondary">
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
            $html->Asigna('op_id', 'selected');
            break;
        case 'fecha':
            $html->Asigna('op_fecha', 'selected');
            break;
        case 'empleados':
            $html->Asigna('op_empleados', "selected");
            break;
        case 'clientes':
            $html->Asigna('op_clientes', 'selected');
            break;
        case 'productos':
            $html->Asigna('op_productos', 'selected');
            break;
    }

    if ($opcion == "id" || $opcion == "fecha") {
        $ventas = Ventas::consultaFiltrada($opcion, $search);
    } else {
        $ventas = Ventas::consultaFiltradaRelacionada($opcion, $search);
    }

    if (count($ventas) == 0) {
        $html->AsignaBloque('ventas', null);
        $mensaje = "<h4 class='text-secondary text-center' >No se encontró ninguna venta</h4>";
        $html->Asigna('mensaje', $mensaje);
    }

    $html->AsignaBloque('paginas', null);

    #$html->Asigna('link_report', 'reportVentas.php');
    $html->Asigna('link_report', "reportVentas.php?opcion=$opcion&search=$search");
    $html->Asigna('reporte', 'Reporte de consulta');
    $html->Asigna('value', $search);
    $html->Asigna('limpiar_filtro', $buttonFiltro);
} else {
    $paginaActual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;


    $html->Asigna('link_report', 'reportVentas.php');
    $html->Asigna('reporte', 'Reporte general');

    $ventas = Ventas::consultarVentas($paginaActual, $contenido);

    if (count($ventas) == 0) {
        $html->AsignaBloque('ventas', null);
        $html->AsignaBloque('paginas', null);
    }

    for ($pa = 1; $pa <= $totalPaginas; $pa++) {
        $pagina['active'] = '';
        $pagina['pagina'] = $pa;

        if ($pa == $paginaActual) {
            $pagina['active'] = 'active';
        }

        $html->AsignaBloque('paginas', $pagina);
    }
}


foreach ($ventas as $venta) {
    $html->AsignaBloque('ventas', $venta);
}

echo $html->Muestra();
