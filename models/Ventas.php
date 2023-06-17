<?php
require_once 'DetallesVentas.php';

class Ventas
{
    public $idVenta;
    public $subtotal;
    public $iva;
    public $idEmpleado;
    public $idCliente;
    public $fechaVenta;

    private static $bd;


    public static function generarIva($subtotal)
    {
        $iva = $subtotal * 0.16;
        return $iva;
    }

    public function __construct($idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta,)
    {
        $this->idVenta = $idVenta;
        $this->subtotal = $subtotal;
        $this->iva = $iva;
        $this->idEmpleado = $idEmpleado;
        $this->idCliente = $idCliente;
        $this->fechaVenta = $fechaVenta;
    }

    public function agregarVenta()
    {
        $consulta = self::$bd;
        $consulta->prepare("INSERT INTO ventas(subtotal,iva,empleados_id_empleado,clientes_id_cliente,fecha_venta) VALUES(?,?,?,?,?)");
        $consulta->bind_param(
            "f,f,i,i,s",
            $this->subtotal,
            $this->iva,
            $this->idEmpleado,
            $this->idCliente,
            $this->fechaVenta
        );
        $consulta->execute();
        $consulta->close();
    }

    public static function init($bd)
    {
        self::$bd = $bd;
    }
}

$mysqli = new mysqli("localhost", "root", "", "agenciaBD");

Ventas::init($mysqli);
DetallesVentas::init($mysqli);
$subtotal = DetallesVentas::obtenerSubtotal([1, 2]);
$iva = Ventas::generarIva($subtotal);
$costo = DetallesVentas::calcularCosto($subtotal, $iva);

if (isset($argc) && $argc == 2) {
    switch ($argv[1]) {
        case 'nuevo':

            break;
        case 'consulta':
            echo "Subtotal: $subtotal \n";
            echo "Iva: $iva \n";
            echo "Costo Total: $costo \n";
            break;
    }
}
