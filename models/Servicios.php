<?php
require_once 'Clientes.php';
require_once 'DetallesServicios.php';

class Servicios
{
  public $idServicio;
  public $idCliente;
  public $fechaServicio;
  public $productos;
  public $tipoServicios;

  public function __construct($idServicio, $idCliente, $fechaServicio, $productos, $tipoServicios)
  {
    $this->idServicio = $idServicio;
    $this->idCliente = $idCliente;
    $this->fechaServicio = $fechaServicio;
    $this->productos = $productos;
    $this->tipoServicios = $tipoServicios;
  }

  /**
   * @var mysqli $bd objeto de conexión a la base de datos
   */
  private static $bd;

  public static function init($bd)
  {
    self::$bd = $bd;
    Clientes::init($bd);
    Productos::init($bd);
    DetalleServicios::init($bd);
  }

  public function agregarServicio()
  {
    $productos = $this->productos;
    $tipoServicios = $this->tipoServicios;
    $consulta = self::$bd->prepare("INSERT INTO cliente_servicio VALUES(null,?,?)");
    $consulta->bind_param(
      'is',
      $this->idCliente,
      $this->fechaServicio
    );

    $consulta->execute();

    $idServicio = $consulta->insert_id;
    foreach ($productos as $key => $producto) {
      $detalle = new DetalleServicios($idServicio, $producto, $tipoServicios[$key]);
      $detalle->agregarDetalleServicio();
    }
  }
}

if (isset($argc) && $argc == 2) {
  $mysqli = new mysqli("localhost", "root", "", "agenciaBD");
  Servicios::init($mysqli);

  switch ($argv[1]) {
    case "agregar":
      date_default_timezone_set('America/Mexico_City'); # Zona horaria para Mexico
      $fecha = date("Y-m-d");
      $servicio = new Servicios(null, 4, $fecha, [1, 4], ["garantía", "refacción"]);
      $servicio->agregarServicio();
      break;
  }
}
