<?php
// Comprobar si hay una sesión iniciada
session_start();
require_once './models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ./index.php');
}

require_once './models/elementos.php';
require_once './SpynTPL.php';
require_once './models/config.php';
require_once './models/Ventas.php';

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

# Usando una función para obtener la barra de navegación
$nav = navBar('ventas');
$html->Asigna('nav-bar', $nav);
// Objeto de la base de datos
$mysqli = new mysqli($servidor, $usuario, $password, $bd);

// Iniciando parámetros
Ventas::init($mysqli);

$contenido = 5;
$totalPaginas = Ventas::totalPaginas($contenido);

if (isset($_GET['search'])  && !empty(trim($_GET['search']))) {
    $opcion = $_GET['opcion'];
    $search = $_GET['search'];

    $buttonFiltro = bntLimpiarFiltro('consultar_ventas.php');

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

    if ($opcion == 'id' || $opcion == 'fecha') {
        $ventas = Ventas::consultaFiltrada($opcion, $search);
    } else {
        $ventas = Ventas::consultaFiltradaRelacionada($opcion, $search);
    }

    if (count($ventas) == 0) {
        $html->AsignaBloque('ventas', null);
        $mensaje = "<h4 class='text-secondary text-center' >No se encontró ninguna venta</h4>";
        $html->Asigna('mensaje', $mensaje);
        $html->Asigna('disabled-a', 'disabled-a');
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
        $mensaje = "<h4 class='text-secondary text-center' >No se encontró ninguna venta. Agrega una venta</h4>";
        $html->Asigna('mensaje', $mensaje);
        $html->Asigna('disabled-a', 'disabled-a');
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
