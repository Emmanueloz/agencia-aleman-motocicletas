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
}
