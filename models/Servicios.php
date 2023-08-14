<?php
require_once 'Clientes.php';
require_once 'DetallesServicios.php';
require_once 'Productos.php';

class Servicios
{
  public $idServicio;
  public $idCliente;
  public $fechaServicio;
  public $productos;
  public $tipoServicios;
  /**
   * @param int $idServicio debe ser null o 0 cuando se crea el objeto para agregarlo
   */
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
  /**
   * Agrega el servicio al la tabla cliente servicio, después se agrega a la tabla detalles_servicios
   */
  public function agregarServicio()
  {
    $productos = $this->productos;

    $consulta = self::$bd->prepare("INSERT INTO cliente_servicio VALUES(null,?,?)");
    $consulta->bind_param(
      'is',
      $this->idCliente,
      $this->fechaServicio
    );

    $consulta->execute();
    $idServicio = $consulta->insert_id;

    $consulta->close();
    /**
     * Se recorre el array de productos para agregar el la tabla detalles_servicios
     */
    foreach ($productos as $key => $producto) {
      $detalle = new DetalleServicios($idServicio, $producto, 'garantía');
      $detalle->agregarDetalleServicio();
    }
    return $idServicio;
  }

  /**
   * Se calcula el total de paginas para la página de consulta
   * @param int $contenido es la cantidad que se mostrara como limite en la página de consulta
   */
  public static function totalPaginas($contenido)
  {
    $totalFilas = 0;
    $consulta = self::$bd->prepare("SELECT COUNT(id_servicio) FROM cliente_servicio");
    $consulta->execute();
    $consulta->bind_result($totalFilas);
    $consulta->fetch();
    $consulta->close();

    $totalPaginas = ceil($totalFilas / $contenido);

    return $totalPaginas;
  }
  /**
   * Consultar todos los servicios si IDs y mostrando los datos
   * @param int $pagina es la pagina en donde inicia la consulta
   * @param int $contenido cuanto se mostrara por pagina
   */
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
  /**
   * Consultar solo un servicio, con id y no con los datos
   */
  public static function consultarServicio($id)
  {
    $consulta = self::$bd->prepare("SELECT * FROM cliente_servicio WHERE id_servicio =?");
    $consulta->bind_param('i', $id);
    $consulta->execute();
    $consulta->bind_result($idServicio, $idCliente, $fechaServicio);
    $consulta->fetch();
    $consulta->close();
    $detalleServicio = DetalleServicios::consultarDetalleServicio($id, true);

    return new Servicios($idServicio, $idCliente, $fechaServicio, $detalleServicio->producto, $detalleServicio->tipoServicio);
  }

  public function actualizarServicio()
  {
    $consulta = self::$bd->prepare('UPDATE cliente_servicio SET fecha_servicio = ? WHERE id_servicio = ?');
    $consulta->bind_param('si', $this->fechaServicio, $this->idServicio);
    $consulta->execute();
    $consulta->close();
    $detalleServicio = new DetalleServicios($this->idServicio, $this->productos, $this->tipoServicios);
    $detalleServicio->actualizaDetalleServicios();
  }

  public static function consultaFiltrada($filtro, $value)
  {
    $serviciosArray = [];
    $servicios = [];

    switch ($filtro) {
      case 'id':
        $consulta = self::$bd->prepare('SELECT * FROM cliente_servicio WHERE id_servicio = ?');
        $consulta->bind_param('i', $value);
        break;
      case 'fecha':
        $value = '%' . $value . '%';
        $consulta = self::$bd->prepare('SELECT * FROM cliente_servicio WHERE fecha_servicio LIKE ?');
        $consulta->bind_param('s', $value);
        break;
      default:
        return $servicios;
        break;
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

    foreach ($serviciosArray as $servicio) {
      $id = $servicio[0];
      $idCliente = $servicio[1];
      $fecha = $servicio[2];

      $nombreCliente = Clientes::buscarnom($idCliente);
      $detalleServicio = DetalleServicios::consultarDetalleServicio($id);

      array_push($servicios, new Servicios($id, $nombreCliente, $fecha, $detalleServicio->producto, $detalleServicio->tipoServicio));
    }

    return $servicios;
  }
  /**
   * Consulta filtrada para los campos de la tabla cliente_servicio
   * @param string $filtro que filtro se usara para la búsqueda
   * @param string $value el valor que se agregara el filtro
   */
  public static function consultaFiltradaRelacionada($filtro, $value)
  {
    $idServicios = [];
    $servicios = [];

    switch ($filtro) {
      case 'clientes':
        $idClientes = Clientes::buscarcli($value);

        if (count($idClientes) != 0) {
          foreach ($idClientes as $idCliente) {
            $consulta = self::$bd->prepare("SELECT id_servicio FROM cliente_servicio WHERE id_cliente = ?");
            $consulta->bind_param("i", $idCliente);
            $consulta->execute();
            $consulta->bind_result($idServicio);

            while ($consulta->fetch()) {
              array_push($idServicios, $idServicio);
            }
          }
          $consulta->close();
        }
        break;
      case 'productos':
        $idProductos = Productos::cosultMarcaModelo($value);

        if (count($idProductos) != 0) {
          foreach ($idProductos as $idProducto) {
            $consulta = self::$bd->prepare("SELECT id_servicio FROM detalles_servicios WHERE id_producto = ?");
            $consulta->bind_param("i", $idProducto);
            $consulta->execute();
            $consulta->bind_result($idServicio);
            while ($consulta->fetch()) {
              array_push($idServicios, $idServicio);
            }
          }
          $consulta->close();
        }
        break;
      case 'servicio':
        $value = '%' . $value . '%';
        $consulta = self::$bd->prepare('SELECT id_servicio FROM detalles_servicios WHERE tipo_servicio LIKE ?');
        $consulta->bind_param("i", $value);
        $consulta->execute();
        $consulta->bind_result($idServicio);
        while ($consulta->fetch()) {
          array_push($idServicios, $idServicio);
        }
        $consulta->close();

        break;
    }
    /**
     * Ordenar el resultada de la búsqueda por el id
     */
    function compararPorIdVenta($a, $b)
    {
      if ($a->idServicio == $b->idServicio) {
        return 0;
      }
      return ($a->idServicio > $b->idServicio) ? 1 : -1;
    }

    if (count($idServicios) != 0) {
      $idServicios = array_unique($idServicios);
      foreach ($idServicios as $idServicios) {
        $servicio = self::consultaFiltrada("id", $idServicios);
        array_push($servicios, $servicio[0]);
      }
      usort($servicios, 'compararPorIdVenta');
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
    case 'id':
      $servicio = Servicios::consultarServicio(1);
      print_r($servicio);
      break;
    case 'actualizar':
      $servicio = Servicios::consultarServicio(1);
      print_r($servicio);
      $servicio->fechaServicio = '2023-08-10';
      $servicio->tipoServicios = ["mono", "mono"];
      $servicio->actualizarServicio();
      print_r($servicio);
      break;
    case 'filtro':
      $servicios = Servicios::consultaFiltrada('cliente', 'a');
      print_r($servicios);
      break;
  }
}
