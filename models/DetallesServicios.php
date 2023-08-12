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
  /**
   * Se agrega el detalle de un servicio
   */
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
    $consulta->close();
  }
  /**
   * Consulta el detalle de un servicio, con los datos de la vista o solo ID's
   * @param int $id ID del servicio para consultar su detalle
   * @param bool $conIDs si es falso la consulta es con los nombres de productos, si es verdadero la consulta es con el IDs
   * @return DetalleServicios un objeto con el resultado de la consulta
   */
  public static function consultarDetalleServicio($id, $conIDs = false)
  {
    $detallesArray = [];
    $nombreProductos = '';
    $servicios = '';

    $idProductos = [];
    $serviciosArray = [];

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

    if ($conIDs == true) {
      foreach ($detallesArray as $key => $detalle) {
        $idProducto = $detalle[1];
        $servicio = $servicios . $detalle[2];

        array_push($idProductos, $idProducto);
        array_push($serviciosArray, $servicio);
      }
      return new DetalleServicios($id, $idProductos, $serviciosArray);
    } else {
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
  /**
   * Actualiza el tipo de servicio
   */
  public function actualizaDetalleServicios()
  {
    foreach ($this->producto as $key => $idProducto) {
      $consulta = self::$bd->prepare('UPDATE detalles_servicios SET tipo_servicio  = ? WHERE id_servicio = ? AND id_producto = ?');
      $consulta->bind_param('sii', $this->tipoServicio[$key], $this->idServicio, $idProducto);
      $consulta->execute();
      $consulta->close();
    }
  }
}
