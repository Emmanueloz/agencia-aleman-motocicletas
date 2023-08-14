<?php

// Comprobar si hay una sesión iniciada
session_start();
require_once 'models/Login.php';
if (!isset($_SESSION['user'])) {
  header('Location: ./index.php');
}

require_once 'models/elementos.php';
require_once('SpynTPL.php');
require_once('models/config.php');
require_once('models/Servicios.php');
$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Servicios::init($mysqli);

$title = 'Editar Servicios';
$target = 'modificar_servicios.php';
$html = new SpynTPL('views/');
$html->Fichero('frmTipoServicio.html');
$html->Asigna('title', 'Modificar servicio');
$html->Asigna('target', $target);
$html->Asigna('btn', 'modificar');

$nav = navBar('servicios');
$html->Asigna('nav-bar', $nav);

if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $servicio = Servicios::consultarServicio($id);
  $nomCliente = Clientes::buscarnom($servicio->idCliente);
  $productos = [];
  foreach ($servicio->productos as $idProducto) {
    $producto = Productos::consultPrecioMarcaModelo($idProducto);
    array_push($productos, $producto);
  }

  $html->Asigna('idServicio', $id);
  $html->Asigna('cliente', $nomCliente);
  foreach ($productos as $producto) {
    $html->AsignaBloque('productos', $producto);
  }

  $html->Asigna('fecha', $servicio->fechaServicio);

  foreach ($servicio->tipoServicios as $key => $tipo) {
    $tipoServicio['tipoServicio'] = $tipo;
    $html->AsignaBloque('servicios', $tipoServicio);
  }
  echo $html->Muestra();
} elseif (isset($_POST['id'])) {
  $id = $_POST['id'];
  $servicio = Servicios::consultarServicio($id);
  $servicio->fechaServicio = $_POST['fecha'];
  $servicio->tipoServicios = $_POST['servicios'];

  $servicio->actualizarServicio();

  unset($_POST);
  header("Location: consultar_servicios.php?opcion=id&search=$id");
}
