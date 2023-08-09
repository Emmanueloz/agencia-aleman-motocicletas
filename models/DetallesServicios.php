<?php
require_once 'Productos.php';

class DetalleServicios
{
  public $idServicio;
  public $producto;
  public $tipoServicio;

  public function __construct($idServicio, $producto, $tipoServicio)
  {
    $this->idServicio = $idServicio;
    $this->producto = $producto;
    $this->tipoServicio = $tipoServicio;
  }

  /**
   * @var mysqli $bd objeto de conexión a la base de datos
   */
  private static $bd;

  public static function init($bd)
  {
    self::$bd = $bd;
    Productos::init($bd);
  }

  public function agregarDetalleServicio()
  {
    $consulta = self::$bd->prepare("INSERT INTO detalles_servicios VALUES(?,?,?)");
    $consulta->bind_param(
      'iis',
      $this->idServicio,
      $this->producto,
      $this->tipoServicio
    );

    $consulta->execute();
  }

  public static function consultarDetalleServicio($id)
  {
    $detallesArray = [];
    $nombreProductos = '';
    $servicios = '';

    $consulta = self::$bd->prepare('SELECT * FROM detalles_servicios WHERE id_servicio=?');
    $consulta->bind_param('i', $id);
    $consulta->execute();
    $consulta->bind_result($idServicio, $producto, $tipoServicio);

    while ($consulta->fetch()) {
      array_push($detallesArray, [$idServicio, $producto, $tipoServicio]);
    }

    $consulta->close();

    if (count($detallesArray) == 0) {
      return $detalleServicio = [];
    }

    foreach ($detallesArray as $key => $detalle) {
      $idProducto = $detalle[1];
      $servicios = $servicios . $detalle[2];
      $producto = Productos::consultPrecioMarcaModelo($idProducto);
      $nombreProductos = $nombreProductos . $producto[1] . ' ' . $producto[2];

      if ($key + 1 < count($detallesArray)) {
        $nombreProductos = $nombreProductos . '<br/>';
        $servicios = $servicios . '<br/>';
      }
    }

    return new DetalleServicios($id, $nombreProductos, $servicios);
  }
}
