<?php

session_start();
require_once 'models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ./index.php');
}

require_once './public/elements.php';
require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/Clientes.php';

$msg = '';
if (isset($_GET['msg'])) {
    $contenido = $_GET['msg'];
    $msg = "<div class='alert alert-success alert-dismissible fade show'
    role='alert'><button type='button' class='btn-close'
    data-bs-dismiss='alert' aria-label='Close'></button>$contenido</div>";
}

$html = new SpynTPL('views/');
$html->Fichero('clientes.html');
$html->Asigna('msg', $msg);
$html->Asigna('titulo', 'Lista de Clientes');
$html->Asigna('idcli_s', '');
$html->Asigna('nomcli_s', '');
$html->Asigna('rfccli_s', '');
$html->Asigna('value', '');
$html->Asigna('limpiar_filtro', '');
$html->Asigna('mensaje', '');

$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Clientes::init($mysqli);

if (isset($_GET['value']) && !empty(trim($_GET['value']))) {
    $opcion = $_GET['opcion'];
    $value = $_GET['value'];

    $buttonFiltro = bntLimpiarFiltro();

    $html->AsignaBloque('paginas', null);
    $html->Asigna('link_report', "reportClientes.php?opcion=$opcion&value=$value");
    $html->Asigna('reporte', 'Reporte de consulta');
    $html->Asigna('limpiar_filtro', $buttonFiltro);

    switch ($opcion) {
        case 'idcli':
            $html->Asigna('idcli_s', 'selected');
            break;
        case 'nomcli':
            $html->Asigna('nomcli_s', 'selected');
            break;
        case 'rfccli':
            $html->Asigna('rfccli_s', 'selected');
            break;
    }
    $clientes = Clientes::busquedafil($opcion, $value);
    if (count($clientes) == 0) {
        $html->AsignaBloque('clientes', null);
        $mensaje = "<h4 class='text-secondary text-center' >No se encontró ningún cliente.";
        $html->Asigna('mensaje', $mensaje);
    }
    $html->Asigna('value', $value);
} else {
    $paginaActual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;

    $html->Asigna('link_report', "reportClientes.php");
    $html->Asigna('reporte', 'Reporte general');

    $clientes = Clientes::consulta($paginaActual, 5);

    if (count($clientes) == 0) {
        $html->AsignaBloque('clientes', null);
        $html->AsignaBloque('paginas', null);
        $mensaje = "<h4 class='text-secondary text-center' >No se encontró ningún cliente. Agrega a un cliente</h4>";
        $html->Asigna('mensaje', $mensaje);
    }

    $totalPaginas = Clientes::totalPaginas(5);

    for ($pa = 1; $pa <= $totalPaginas; $pa++) {
        $pagina['active'] = '';
        $pagina['pagina'] = $pa;

        if ($pa == $paginaActual) {
            $pagina['active'] = 'active';
        }
        $html->AsignaBloque('paginas', $pagina);
    }
}

foreach ($clientes as $cliente) {
    $html->AsignaBloque('clientes', $cliente);
}
echo $html->Muestra();
