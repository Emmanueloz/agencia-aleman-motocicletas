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

  public static function consultarServicios($pagina = null, $contenido = null)
  {
    $pagina = ($pagina - 1) * $contenido;
    $serviciosArray = [];
    $servicios = [];

    if (!is_null($pagina) && !is_null($contenido)) {
      $consulta = self::$bd->prepare("SELECT * FROM cliente_servicio LIMIT ?,?");
      $consulta->bind_param('ii', $pagina, $contenido);
    } else {
      $consulta = self::$bd->prepare("SELECT * FROM cliente_servicio");
    }

    $consulta->execute();
    $consulta->bind_result($idServicio, $idCliente, $fechaServicio);

    while ($consulta->fetch()) {
      array_push($serviciosArray, [$idServicio, $idCliente, $fechaServicio]);
    }
    $consulta->close();

    if (count($serviciosArray) == 0) {
      return $serviciosArray;
    }

    foreach ($serviciosArray as $key => $servicio) {
      $id = $servicio[0];
      $idCliente = $servicio[1];
      $fecha = $servicio[2];

      $nombreCliente = Clientes::buscarnom($idCliente);
      $detalleServicio = DetalleServicios::consultarDetalleServicio($id);

      array_push($servicios, new Servicios($id, $nombreCliente, $fecha, $detalleServicio->producto, $detalleServicio->tipoServicio));
    }

    return $servicios;
  }
}

if (isset($argc) && $argc == 2) {
  $mysqli = new mysqli("localhost", "root", "", "agenciaBD");
  Servicios::init($mysqli);

  switch ($argv[1]) {
    case "agregar":
      date_default_timezone_set('America/Mexico_City'); # Zona horaria para Mexico
      $fecha = date("Y-m-d");
      $servicio = new Servicios(null, 2, $fecha, [3, 2], ["mantenimiento", "mantenimiento"]);
      $servicio->agregarServicio();
      break;
    case 'consultar':
      $servicios = Servicios::consultarServicios(2, 1);
      print_r($servicios);
      break;
  }
}
