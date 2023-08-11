
<?php
// Comprobar si hay una sesión iniciada
session_start();
require_once 'models/Login.php';
if (!isset($_SESSION['user'])) {
  header('Location: ./index.php');
}

require_once 'models/elementos.php';
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

# Usando una función para obtener la barra de navegación
$nav = navBar('servicios');
$html->Asigna('nav-bar', $nav);

// Objeto de la base de datos
$mysqli = new mysqli($servidor, $usuario, $password, $bd);

// Iniciando parámetros
Servicios::init($mysqli);

$contenido = 5;
$totalPaginas = Servicios::totalPaginas($contenido);

if (isset($_GET['search'])  && !empty(trim($_GET['search']))) {
  $opcion = $_GET['opcion'];
  $search = $_GET['search'];

  $buttonFiltro = bntLimpiarFiltro('consultar_servicios.php');

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
    $servicios = Servicios::consultaFiltradaRelacionada($opcion, $search);
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
    $html->AsignaBloque('servicios', null);
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
