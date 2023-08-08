<?php

// Comprobar si hay una sesión iniciada
session_start();
require_once 'models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ./index.php');
}

require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/Productos.php';
$html = new SpynTPL('views/');
$html->Fichero('productos.html');
$html->Asigna('mens', '');
$html->Asigna('id_s', '');
$html->Asigna('modelo_s', '');
$html->Asigna('marca_s', '');
$html->Asigna('precio_s', '');
$html->Asigna('value', '');
$html->Asigna('title', 'Lista de productos');
$html->Asigna('limpiar_filtro', '');

$html->Asigna('mensaje', ' ');


$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Productos::init($mysqli);

$contenido = 5;
$totalPaginas = Productos::totalPaginas($contenido);

if (isset($_GET['value']) && !empty(trim($_GET['value']))) {
    $opcion = $_GET['opcion'];
    $value = $_GET['value'];

    $buttonFiltro = '<a href="./consulta_productos.php" type="button" class="btn btn-outline-secondary">
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
        case 'modelo':
            $html->Asigna('modelo_s', 'selected');
            break;
        case 'marca':
            $html->Asigna('marca_s', 'selected');
            break;
        case 'precio';
            $html->Asigna('precio_s', 'selected');
            break;
        case 'descripcion';
            $html->Asigna('descripcion_s', 'selected');
            break;
    }
    $productos = Productos::productoFiltrado($opcion, $value);
    if (count($productos) == 0) {
        $html->AsignaBloque('productos', null);
        $mensaje = "<h4 class='text-secondary text-center' >No se encontró ningún producto</h4>";
        $html->Asigna('mensaje', $mensaje);
    }
    $html->Asigna('link_report', "reportProductos.php?opcion=$opcion&value=$value");
    $html->Asigna('reporte', "Reporte de consulta");
    $html->Asigna('value', $value);
    $html->Asigna('limpiar_filtro', $buttonFiltro);
    $html->AsignaBloque('paginas', null);
} else {
    $paginaActual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

    $html->Asigna('link_report', "reportProductos.php");
    $html->Asigna('reporte', "Reporte general");
    $productos = Productos::consultaProductos($paginaActual, $contenido);
    if (count($productos) == 0) {
        $html->AsignaBloque('productos', null);
        $mensaje = "<h4 class='text-secondary text-center' >No se encontró ningún producto. Agregue un producto</h4>";
        $html->Asigna('mensaje', $mensaje);
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

foreach ($productos as $producto) {
    $html->AsignaBloque('productos', $producto);
}
echo $html->Muestra();
