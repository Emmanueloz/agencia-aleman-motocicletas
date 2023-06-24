<?php
require_once 'DetallesVentas.php';
require_once 'Empleados.php';
require_once 'Clientes.php';
require_once 'Productos.php';
//require_once 'models/config.php';

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

    public function agregarVenta()
    {
        $idVenta = $this->idVenta;
        $idProductos = $this->idProductos;
        $cantidad = $this->cantidad;
        $costo = $this->costo;

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
        $ventas = [];
        $idVenta = 0;
        $subtotal = 0;
        $iva = 0;
        $idEmpleado = 0;
        $idCliente = 0;
        $fechaVenta = '';

        $consulta = self::$bd->prepare("SELECT ventas.id_venta, ventas.subtotal,ventas.iva,ventas.id_empleado,ventas.id_cliente,
            ventas.fecha_venta
            FROM ventas,detalles_venta
            WHERE ventas.id_venta = detalles_venta.id_venta
            GROUP BY ventas.id_venta");
        $consulta->execute();
        $consulta->bind_result($idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta);

        //$contador = 0;

        while ($consulta->fetch()) {

            array_push($ventasArray, [$idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta]);
            //contador++;
        }
        $consulta->close();

        if (isset($ventasArray)) {
            foreach ($ventasArray as $venta) {
                $id = $venta[0];
                $detalle =  DetallesVentas::consultarDetallesVentas($id);
                $nombreEmpleado = Empleados::id_emple($venta[3]);
                $nombreCliente = Clientes::buscarnom($venta[4]);

                $productos = $detalle->idProductos;
                $productos = str_replace(',', '', $productos);
                array_push($ventas, new Ventas($venta[0], $venta[1], $venta[2], $nombreEmpleado, $nombreCliente, $venta[5], $productos, $detalle->cantidad, $detalle->costo));
            }
        } else {
            $ventas = null;
        }

        return $ventas;
    }

    public static function consultaFiltrada($filtro, $value)
    {
        $ventasArray = [];
        $ventas = [];

        $idVenta = 0;
        $subtotal = 0;
        $iva = 0;
        $idEmpleado = 0;
        $idCliente = 0;
        $fechaVenta = '';


        switch ($filtro) {
            case 'id':
                $consulta = self::$bd->prepare("SELECT ventas.id_venta, ventas.subtotal, 
                ventas.iva, ventas.id_empleado, 
                ventas.id_cliente, ventas.fecha_venta
                FROM ventas
                WHERE ventas.id_venta = ?");
                $consulta->bind_param('i', $value);
                break;

            case 'fecha':
                $value = $value . "%";
                $consulta = self::$bd->prepare("SELECT ventas.id_venta, ventas.subtotal, 
                ventas.iva, ventas.id_empleado, 
                ventas.id_cliente, ventas.fecha_venta
                FROM ventas
                WHERE ventas.fecha_venta like ?");
                $consulta->bind_param('s', $value);
                break;
        }

        $consulta->execute();
        $consulta->bind_result($idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta);

        while ($consulta->fetch()) {

            array_push($ventasArray, [$idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta]);
        }
        $consulta->close();

        foreach ($ventasArray as $venta) {
            $id = $venta[0];
            $detalle =  DetallesVentas::consultarDetallesVentas($id);
            $nombreEmpleado = Empleados::id_emple($venta[3]);
            $nombreCliente = Clientes::buscarnom($venta[4]);

            $productos = $detalle->idProductos;
            $productos = str_replace(',', '', $productos);
            array_push($ventas, new Ventas($venta[0], $venta[1], $venta[2], $nombreEmpleado, $nombreCliente, $venta[5], $productos, $detalle->cantidad, $detalle->costo));
        }

        return $ventas;
    }

    public static function consultaFiltradaRelacionada($filtro, $value)
    {
        $idVenta = 0;
        $subtotal = 0;
        $iva = 0;
        $idEmpleado = 0;
        $idCliente = 0;
        $fechaVenta = '';

        switch ($filtro) {
            case 'empleados':
                $idEmpleados = Empleados::nom($value);
                $ventasArray = [];
                $ventas = [];

                if (count($idEmpleados) != 0) {
                    foreach ($idEmpleados as $idEmpleado) {
                        $consulta = self::$bd->prepare("SELECT * FROM ventas WHERE id_empleado = ?");
                        $consulta->bind_param("i", $idEmpleado);
                        $consulta->execute();
                        $consulta->bind_result($idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta);
                        $consulta->store_result();
                        if ($consulta->fetch()) {
                            array_push($ventasArray, [$idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta]);
                        }
                    }
                    $consulta->close();
                }


                function compararPorIdVenta($a, $b)
                {
                    if ($a->idVenta == $b->idVenta) {
                        return 0;
                    }
                    return ($a->idVenta > $b->idVenta) ? 1 : -1;
                }


                if (isset($ventasArray) || count($ventasArray) != 0) {
                    foreach ($ventasArray as $venta) {
                        $id = $venta[0];
                        $detalle =  DetallesVentas::consultarDetallesVentas($id);
                        $nombreEmpleado = Empleados::id_emple($venta[3]);
                        $nombreCliente = Clientes::buscarnom($venta[4]);

                        $productos = $detalle->idProductos;
                        $productos = str_replace(',', '', $productos);
                        array_push($ventas, new Ventas($venta[0], $venta[1], $venta[2], $nombreEmpleado, $nombreCliente, $venta[5], $productos, $detalle->cantidad, $detalle->costo));
                    }
                    usort($ventas, 'compararPorIdVenta');
                } else {
                    $ventas = null;
                }

                return $ventas;
                break;
            case 'clientes':

                $ventasArray = [];
                $ventas = [];
                $idClientes = Clientes::buscarcli($value);

                if (count($idClientes) != 0) {
                    foreach ($idClientes as $idCliente) {
                        $consulta = self::$bd->prepare("SELECT * FROM ventas WHERE id_cliente = ?");
                        $consulta->bind_param("i", $idCliente);
                        $consulta->execute();
                        $consulta->bind_result($idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta);
                        $consulta->store_result();
                        if ($consulta->fetch()) {
                            array_push($ventasArray, [$idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta]);
                        }
                    }
                    $consulta->close();
                }


                function compararPorIdVenta($a, $b)
                {
                    if ($a->idVenta == $b->idVenta) {
                        return 0;
                    }
                    return ($a->idVenta > $b->idVenta) ? 1 : -1;
                }

                if (isset($ventasArray) || count($ventasArray) != 0) {
                    foreach ($ventasArray as $venta) {
                        $id = $venta[0];
                        $detalle =  DetallesVentas::consultarDetallesVentas($id);
                        $nombreEmpleado = Empleados::id_emple($venta[3]);
                        $nombreCliente = Clientes::buscarnom($venta[4]);

                        $productos = $detalle->idProductos;
                        $productos = str_replace(',', '', $productos);
                        array_push($ventas, new Ventas($venta[0], $venta[1], $venta[2], $nombreEmpleado, $nombreCliente, $venta[5], $productos, $detalle->cantidad, $detalle->costo));
                    }
                    usort($ventas, 'compararPorIdVenta');
                } else {
                    $ventas = null;
                }

                return $ventas;

                break;
            case 'productos':
                $idVentas = [];
                $ventasArray = [];
                $ventas = [];
                $idProductos = Productos::cosultMarcaModelo($value);

                if (count($idProductos) != 0) {
                    foreach ($idProductos as $idProducto) {
                        $consulta = self::$bd->prepare("SELECT id_venta FROM detalles_venta WHERE id_producto = ?");
                        $consulta->bind_param("i", $idProducto);
                        $consulta->execute();
                        $consulta->bind_result($idVenta);
                        while ($consulta->fetch()) {
                            array_push($idVentas, $idVenta);
                        }
                    }
                }

                if (isset($idVentas) || count($idVentas) != 0) {
                    foreach ($idVentas as $idVenta) {
                        $venta = self::consultaFiltrada("id", $idVenta);
                        array_push($ventas, $venta[0]);
                    }
                }

                function compararPorIdVenta($a, $b)
                {
                    if ($a->idVenta == $b->idVenta) {
                        return 0;
                    }
                    return ($a->idVenta > $b->idVenta) ? 1 : -1;
                }


                return $ventas;

                break;
        }
    }
}


if (isset($argc) && $argc == 2) {
    $mysqli = new mysqli("localhost", "root", "", "agenciaBD");

    Ventas::init($mysqli);
    DetallesVentas::init($mysqli);
    Empleados::init($mysqli);
    Clientes::init($mysqli);
    Productos::init($mysqli);

    switch ($argv[1]) {
        case 'empleados':
            $ventas = Ventas::consultaFiltradaRelacionada("empleados", "rasdas");
            print_r($ventas);
            break;
        case 'clientes':
            $ventas = Ventas::consultaFiltradaRelacionada("clientes", "paco");
            print_r($ventas);
            break;
        case "id":
            $ventas = Ventas::consultaFiltrada("id", 5);
            print_r($ventas);
            break;
        case 'ventas':
            $ventas = Ventas::consultarVentas();
            print_r($ventas);
            break;
        case 'productos':
            $ventas = Ventas::consultaFiltradaRelacionada("productos", "Mas");
            print_r($ventas);
            break;
    }
}
