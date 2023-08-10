
<?php
// Comprobar si hay una sesión iniciada
session_start();
require_once 'models/Login.php';
if (!isset($_SESSION['user'])) {
  header('Location: ./index.php');
}

require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/Servicios.php';

$titulo = "Lista de servicios";
$html = new SpynTPL('views/');
$html->Fichero('servicios.html');
$html->Asigna('title', $titulo);
$html->Asigna('mensaje', ' ');

## Opciones
$html->Asigna('op_id', '');
$html->Asigna('op_fecha', '');
$html->Asigna('op_clientes', '');
$html->Asigna('op_servicio', '');

$html->Asigna('value', '');
$html->Asigna('limpiar_filtro', '');

// Objeto de la base de datos
$mysqli = new mysqli($servidor, $usuario, $password, $bd);

// Iniciando parámetros
Servicios::init($mysqli);

$contenido = 5;
$totalPaginas = Servicios::totalPaginas($contenido);

if (isset($_GET['search'])  && !empty(trim($_GET['search']))) {
  $opcion = $_GET['opcion'];
  $search = $_GET['search'];

  $buttonFiltro = '<a href="./consultar_servicios.php" type="button" class="btn btn-outline-secondary">
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
    case 'clientes':
      $html->Asigna('op_clientes', 'selected');
      break;
    case 'productos':
      $html->Asigna('op_productos', 'selected');
      break;
    case 'servicio':
      $html->Asigna('op_servicio', "selected");
      break;
  }

  if ($opcion == 'id' || $opcion == 'fecha') {
    $servicios = Servicios::consultaFiltrada($opcion, $search);
  } else {
    //$ventas = Ventas::consultaFiltradaRelacionada($opcion, $search);
  }

  if (count($servicios) == 0) {
    $html->AsignaBloque('servicios', null);
    $mensaje = "<h4 class='text-secondary text-center' >No se encontró ningún servicio</h4>";
    $html->Asigna('mensaje', $mensaje);
  }

  $html->AsignaBloque('paginas', null);

  #$html->Asigna('link_report', "reportVentas.php?opcion=$opcion&search=$search");
  $html->Asigna('reporte', 'Reporte de consulta');
  $html->Asigna('value', $search);
  $html->Asigna('limpiar_filtro', $buttonFiltro);
} else {
  $paginaActual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;


  $html->Asigna('link_report', 'reportVentas.php');
  $html->Asigna('reporte', 'Reporte general');

  $servicios = Servicios::consultarServicios($paginaActual, $contenido);

  if (count($servicios) == 0) {
    $html->AsignaBloque('ventas', null);
    $html->AsignaBloque('paginas', null);
    $mensaje = "<h4 class='text-secondary text-center' >No se encontró ningún servicio.</h4>";
    $html->Asigna('mensaje', $mensaje);
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


foreach ($servicios as $servicio) {
  $html->AsignaBloque('servicios', $servicio);
}

echo $html->Muestra();
