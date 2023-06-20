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
    public $idProductos;
    public $cantidad;
    public $costo;

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

    public function __construct($idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta, $idProductos, $cantidad, $costo)
    {
        $this->idVenta = $idVenta;
        $this->subtotal = $subtotal;
        $this->iva = $iva;
        $this->idEmpleado = $idEmpleado;
        $this->idCliente = $idCliente;
        $this->fechaVenta = $fechaVenta;
        $this->idProductos = $idProductos;
        $this->cantidad = $cantidad;
        $this->costo = $costo;
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

    public static function consultarVentas()
    {
        $ventasArray = [];
        $idVenta = 0;
        $subtotal = 0;
        $iva = 0;
        $idEmpleado = 0;
        $idCliente = 0;
        $fechaVenta = '';

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
            $detalle = $detallesVenta[$contador];
            array_push($ventasArray, new Ventas($idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta, $detalle->idProductos, $detalle->cantidad, $detalle->costo));
            $contador++;
        }

        $consulta->close();
        return  $ventasArray;
    }

    public static function consultaFiltrada($filtro, $value)
    {
        $idVenta = 0;
        $subtotal = 0;
        $iva = 0;
        $idEmpleado = 0;
        $idCliente = 0;
        $fechaVenta = '';

        //$detallesVenta = DetallesVentas::consultarDetallesVentas();

        switch ($filtro) {
            case 'id':
                $consulta = self::$bd->prepare("SELECT ventas.id_venta, ventas.subtotal, 
                ventas.iva, ventas.empleados_id_empleado, 
                ventas.clientes_id_cliente, ventas.fecha_venta
                FROM ventas
                WHERE ventas.id_venta = ?");
                $consulta->bind_param('i', $value);
                break;
            case 'fecha':
                $value = $value . "%";
                $consulta = self::$bd->prepare("SELECT ventas.id_venta, ventas.subtotal, 
                ventas.iva, ventas.empleados_id_empleado, 
                ventas.clientes_id_cliente, ventas.fecha_venta
                FROM ventas
                WHERE ventas.fecha_venta like ?");
                $consulta->bind_param('s', $value);
                break;
        }

        $ventasArray = [];
        $consulta->execute();
        $consulta->store_result();
        $consulta->bind_result($idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta);
        //$consulta->fetch();
        //$consulta->close();
        $detallesVenta = DetallesVentas::consultarDetallesVentas($idVenta);

        $contador = 0;

        while ($consulta->fetch()) {
            $detalle = $detallesVenta[$contador];
            array_push($ventasArray, new Ventas($idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta, $detalle->idProductos, $detalle->cantidad, $detalle->costo));
            $contador++;
        }

        //$consulta->close();
        return  $ventasArray;
    }
}

/* $mysqli = new mysqli("localhost", "root", "", "agenciaBD");

Ventas::init($mysqli);
DetallesVentas::init($mysqli);

$idProductos = [3, 1, 5];
$cantidad = count($idProductos);

$subtotal = DetallesVentas::obtenerSubtotal($idProductos);
$iva = Ventas::generarIva($subtotal);
$costo = DetallesVentas::calcularCosto($subtotal, $iva);

$idVenta = Ventas::obtenerIdVenta();
$fechaActual = date("Y-m-d");
*/
