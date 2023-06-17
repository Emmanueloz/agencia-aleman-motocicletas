<?php
require_once 'DetallesVentas.php';
require_once 'models/config.php';

class Ventas
{
    public $idVenta;
    public $subtotal;
    public $iva;
    public $idEmpleado;
    public $idCliente;
    public $fechaVenta;
    public $detalleVenta;

    private static $bd;

    public static function init($bd)
    {
        self::$bd = $bd;
    }

    public static function generarIva($subtotal)
    {
        $iva = $subtotal * 0.16;
        return $iva;
    }

    public static function obtenerIdVenta()
    {
        $idVenta = 1;
        $consulta = self::$bd->prepare("SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'agenciaBD' AND TABLE_NAME = 'ventas'");
        $consulta->execute();
        $consulta->bind_result($idVenta);
        $consulta->fetch();
        return  $idVenta;
    }

    public function __construct($idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta, $detalleVenta)
    {
        $this->idVenta = $idVenta;
        $this->subtotal = $subtotal;
        $this->iva = $iva;
        $this->idEmpleado = $idEmpleado;
        $this->idCliente = $idCliente;
        $this->fechaVenta = $fechaVenta;
        $this->detalleVenta = $detalleVenta;
    }

    public function agregarVenta($idProductos, $cantidad, $costo)
    {
        $idVenta = $this->idVenta;

        $consulta = self::$bd->prepare("INSERT INTO ventas VALUES(null,?,?,?,?,?)");
        $consulta->bind_param(
            'ddiis',
            $this->subtotal,
            $this->iva,
            $this->idEmpleado,
            $this->idCliente,
            $this->fechaVenta
        );
        $consulta->execute();
        $consulta->close();
        $detalleVenta = new DetallesVentas($idVenta, $idProductos, $cantidad, $costo);
        $detalleVenta->agregarDetalles();
    }

    public static function consultarVentas($id = null)
    {
        $ventasArray = [];
        $idVenta = 0;
        $subtotal = 0;
        $iva = 0;
        $idEmpleado = 0;
        $idCliente = 0;
        $fechaVenta = '';

        if ($id == null) {
            $detallesVenta = DetallesVentas::consultarDetallesVentas();

            $consulta = self::$bd->prepare("SELECT ventas.id_venta, ventas.subtotal,ventas.iva,ventas.empleados_id_empleado,ventas.clientes_id_cliente,
            ventas.fecha_venta
            FROM ventas,detalles_venta
            WHERE ventas.id_venta = detalles_venta.id_venta
            GROUP BY ventas.id_venta");
            $consulta->execute();
            $consulta->bind_result($idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta);

            $contador = 0;

            while ($consulta->fetch()) {
                array_push($ventasArray, new Ventas($idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta, $detallesVenta[$contador]));
                $contador++;
            }

            $consulta->close();
            return  $ventasArray;
        } else {

            $consulta = self::$bd->prepare("SELECT ventas.id_venta, ventas.subtotal, ventas.iva, ventas.empleados_id_empleado, 
            ventas.clientes_id_cliente, ventas.fecha_venta
            FROM ventas
            WHERE ventas.id_venta = ?");

            $consulta->bind_param('i', $id);
            $consulta->execute();
            $consulta->store_result();
            $consulta->bind_result($idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta);
            $consulta->fetch();
            $consulta->close();
            $detalleVenta = DetallesVentas::consultarDetallesVentas($idVenta);

            return new Ventas($idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta, $detalleVenta);
        }
    }
}

$mysqli = new mysqli("localhost", "root", "", "agenciaBD");

Ventas::init($mysqli);
DetallesVentas::init($mysqli);

$idProductos = [3, 1, 5];
$cantidad = count($idProductos);

$subtotal = DetallesVentas::obtenerSubtotal($idProductos);
$iva = Ventas::generarIva($subtotal);
$costo = DetallesVentas::calcularCosto($subtotal, $iva);

$idVenta = Ventas::obtenerIdVenta();
$fechaActual = date("Y-m-d");


if (isset($argc) && $argc == 2) {
    switch ($argv[1]) {
        case 'nuevo':
            $venta = new Ventas($idVenta, $subtotal, $iva, 5, 2, $fechaActual, 0, 0, 0);
            $venta->agregarVenta($idProductos, $cantidad, $costo);
            break;
        case 'consulta':
            $ventas = Ventas::consultarVentas();
            print_r($ventas);
            break;
        case 'detalles':
            $detallesVenta = DetallesVentas::consultarDetallesVentas();
            print_r($detallesVenta);
            break;
    }
}
