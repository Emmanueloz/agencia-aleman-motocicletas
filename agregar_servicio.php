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
require_once 'models/Servicios.php';

$mysqli = new mysqli($servidor, $usuario, $password, $bd);


Servicios::init($mysqli);
$target = 'agregar_servicio.php';

$html = new SpynTPL('views/');
$nav = navBar('ventas');



if ($_POST['accion']  == 'procesar') {
  $productos = [];
  $html->Fichero('frmTipoServicio.html');
  $html->Asigna('title', 'Agregar servicio');
  $html->Asigna('target', $target);
  $html->Asigna('msg', ' ');
  $html->Asigna('nav-bar', $nav);
  $html->Asigna('accionForm', 'agregar');
  $cliente = Clientes::buscarid($_POST['cliente']);
  $html->Asigna('id_cliente', $cliente->id_cliente);
  $html->Asigna('cliente', $cliente->nombre);
  $html->Asigna('fecha', $_POST['fecha']);
  $html->Asigna('btn', 'agregar');

  $idProductos = $_POST['productos'];

  foreach ($idProductos as $idProducto) {
    $producto = Productos::consultPrecioMarcaModelo($idProducto);
    array_push($productos, $producto);
  }

  foreach ($productos as $producto) {
    $html->AsignaBloque('productos', $producto);
    $data['tipoServicio'] = '';
    $html->AsignaBloque("servicios", $data);
  }
} elseif ($_POST['accion'] == 'agregar') {
  $servicios = new Servicios(0, $_POST['cliente'], $_POST['fecha'], $_POST['productos'], $_POST['servicios']);
  $id = $servicios->agregarServicio();
  header("Location: consultar_servicios.php?opcion=id&search=$id");
} else {
  $clientes = Clientes::consulta();

  $productos = Productos::consultaProductos();
  $html->Fichero('frmServicios.html');
  $html->Asigna('title', 'Agregar servicio');
  $html->Asigna('target', $target);
  $html->Asigna('msg', ' ');

  $html->Asigna('nav-bar', $nav);

  $html->Asigna('accionForm', 'procesar');

  if (count($clientes) > 0) {
    foreach ($clientes as $cliente) {
      $html->AsignaBloque("clientes", $cliente);
    }
  } else {
    $content = 'Agrega clientes para agregar un servicio';
    $html->AsignaBloque("clientes", null);
    $msgError = "<div class='alert alert-danger alert-dismissible fade show' role='alert' ><button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>$content</div>";
    $html->Asigna('msg', $msgError);
    $html->Asigna('isDisabled', 'disabled');
  }

  if (count($productos) > 0) {
    foreach ($productos as $producto) {
      $html->AsignaBloque("productos", $producto);
    }
  } else {
    $content = 'Agrega productos para agregar un servicio';
    $html->AsignaBloque("productos", null);
    $msgError = "<div class='alert alert-danger alert-dismissible fade show' role='alert' ><button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>$content</div>";
    $html->Asigna('msg', $msgError);
    $html->Asigna('isDisabled', 'disabled');
  }
}

echo $html->Muestra();
