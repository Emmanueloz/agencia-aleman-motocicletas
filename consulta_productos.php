<?php

// Comprobar si hay una sesión iniciada
session_start();
require_once 'models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ./index.php');
}

require_once './models/elements.php';
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

    $buttonFiltro = bntLimpiarFiltro();

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
