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

    public function __construct($idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta)
    {
        $this->idVenta = $idVenta;
        $this->subtotal = $subtotal;
        $this->iva = $iva;
        $this->idEmpleado = $idEmpleado;
        $this->idCliente = $idCliente;
        $this->fechaVenta = $fechaVenta;
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
}

$mysqli = new mysqli("localhost", "root", "", "agenciaBD");

Ventas::init($mysqli);
DetallesVentas::init($mysqli);

$idProductos = [3, 1, 5];
$subtotal = DetallesVentas::obtenerSubtotal($idProductos);
$iva = Ventas::generarIva($subtotal);
$costo = DetallesVentas::calcularCosto($subtotal, $iva);

$idVenta = Ventas::obtenerIdVenta();

$fechaActual = date("Y-m-d");

if (isset($argc) && $argc == 2) {
    switch ($argv[1]) {
        case 'nuevo':
            $venta = new Ventas($idVenta, $subtotal, $iva, 5, 2, $fechaActual);
            $venta->agregarVenta($idProductos, 1, $costo);
            break;
        case 'consulta':
            echo "Subtotal: $subtotal \n";
            echo "Iva: $iva \n";
            echo "Costo Total: $costo \n";
            echo "Id venta: $idVenta \n";
            echo  $fechaActual = date("Y-m-d") . "\n";
            break;
    }
}
