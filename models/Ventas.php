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
    public $cantidades;
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

    public function __construct($idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta, $idProductos, $cantidades, $costo)
    {
        $this->idVenta = $idVenta;
        $this->subtotal = $subtotal;
        $this->iva = $iva;
        $this->idEmpleado = $idEmpleado;
        $this->idCliente = $idCliente;
        $this->fechaVenta = $fechaVenta;
        $this->idProductos = $idProductos;
        $this->cantidades = $cantidades;
        $this->costo = $costo;
    }

    public function agregarVenta()
    {
        $idVenta = $this->idVenta;
        $idProductos = $this->idProductos;
        $cantidades = $this->cantidades;
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
        $detalleVenta = new DetallesVentas($idVenta, $idProductos, $cantidades, $costo);
        $detalleVenta->agregarDetalles();
    }

    public static function totalPaginas($contenido)
    {
        $totalFilas = 0;
        $consulta = self::$bd->prepare("SELECT COUNT(id_venta) FROM ventas");
        $consulta->execute();
        $consulta->bind_result($totalFilas);
        $consulta->fetch();
        $consulta->close();

        $totalPaginas = ceil($totalFilas / $contenido);

        return $totalPaginas;
    }

    public static function consultarVentas($pagina, $contenido)
    {
        $pagina = ($pagina - 1) * $contenido;
        $ventasArray = [];
        $ventas = [];
        $idVenta = 0;
        $subtotal = 0;
        $iva = 0;
        $idEmpleado = 0;
        $idCliente = 0;
        $fechaVenta = '';

        $consulta = self::$bd->prepare("SELECT id_venta, subtotal, iva, id_empleado, id_cliente, fecha_venta FROM ventas LIMIT ?,?");
        $consulta->bind_param('ii', $pagina, $contenido);
        $consulta->execute();
        $consulta->bind_result($idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta);



        while ($consulta->fetch()) {

            array_push($ventasArray, [$idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta]);
        }
        $consulta->close();

        if (isset($ventasArray)) {
            foreach ($ventasArray as $venta) {
                $id = $venta[0];
                $detalle =  DetallesVentas::consultarDetallesVentas($id);
                $detalle = $detalle[0];
                $nombreEmpleado = Empleados::id_emple($venta[3]);
                $nombreCliente = Clientes::buscarnom($venta[4]);

                $productos = '';
                $cantidades = '';

                $productosNombre = $detalle->idProductos;
                $cantidadesPorProducto = $detalle->cantidades;


                foreach ($productosNombre as $productoNombre) {
                    $productos = $productos . "$productoNombre[1] <br/>";
                }

                foreach ($cantidadesPorProducto as $cantidad) {
                    $cantidades = $cantidades . "$cantidad <br/>";
                }


                array_push($ventas, new Ventas($venta[0], $venta[1], $venta[2], $nombreEmpleado, $nombreCliente, $venta[5], $productos, $cantidades, $detalle->costo));
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
                $value = '%' . $value . '%';
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

        if (isset($ventasArray)) {
            foreach ($ventasArray as $venta) {
                $id = $venta[0];
                $detalle =  DetallesVentas::consultarDetallesVentas($id);
                $detalle = $detalle[0];
                $nombreEmpleado = Empleados::id_emple($venta[3]);
                $nombreCliente = Clientes::buscarnom($venta[4]);

                $productos = '';
                $cantidades = '';

                $productosNombre = $detalle->idProductos;
                $cantidadesPorProducto = $detalle->cantidades;

                foreach ($productosNombre as $productoNombre) {
                    $productos = $productos . "$productoNombre[1] <br/>";
                }

                foreach ($cantidadesPorProducto as $cantidad) {
                    $cantidades = $cantidades . "$cantidad <br/>";
                }

                array_push($ventas, new Ventas($venta[0], $venta[1], $venta[2], $nombreEmpleado, $nombreCliente, $venta[5], $productos, $cantidades, $detalle->costo));
            }
        } else {
            $ventas = null;
        }

        return $ventas;
    }

    public static function consultaFiltradaRelacionada($filtro, $value)
    {
        $idVentas = [];
        $ventas = [];
        $idVenta = 0;
        $idEmpleado = 0;
        $idCliente = 0;


        switch ($filtro) {
            case 'empleados':
                $idEmpleados = Empleados::nom($value);

                if (count($idEmpleados) != 0) {
                    foreach ($idEmpleados as $idEmpleado) {
                        $consulta = self::$bd->prepare("SELECT id_venta FROM ventas WHERE id_empleado = ?");
                        $consulta->bind_param("i", $idEmpleado);
                        $consulta->execute();
                        $consulta->bind_result($idVenta);
                        while ($consulta->fetch()) {
                            array_push($idVentas, $idVenta);
                        }
                    }
                    $consulta->close();
                }

                break;
            case 'clientes':
                $idClientes = Clientes::buscarcli($value);

                if (count($idClientes) != 0) {
                    foreach ($idClientes as $idCliente) {
                        $consulta = self::$bd->prepare("SELECT id_venta FROM ventas WHERE id_cliente = ?");
                        $consulta->bind_param("i", $idCliente);
                        $consulta->execute();
                        $consulta->bind_result($idVenta);

                        while ($consulta->fetch()) {
                            array_push($idVentas, $idVenta);
                        }
                    }
                    $consulta->close();
                }
                break;
            case 'productos':
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
                    $consulta->close();
                }
                break;
        }

        function compararPorIdVenta($a, $b)
        {
            if ($a->idVenta == $b->idVenta) {
                return 0;
            }
            return ($a->idVenta > $b->idVenta) ? 1 : -1;
        }

        if (isset($idVentas) || count($idVentas) != 0) {
            $idVentas = array_unique($idVentas);
            foreach ($idVentas as $idVenta) {
                $venta = self::consultaFiltrada("id", $idVenta);
                array_push($ventas, $venta[0]);
            }
            usort($ventas, 'compararPorIdVenta');
        } else {
            $ventas = null;
        }

        return $ventas;
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
            $ventas = Ventas::consultaFiltradaRelacionada("empleados", "robe");
            print_r($ventas);
            break;
        case 'clientes':
            $ventas = Ventas::consultaFiltradaRelacionada("clientes", "ju");
            print_r($ventas);
            break;
        case "id":
            $ventas = Ventas::consultaFiltrada("id", 3);
            print_r($ventas);
            break;
        case 'ventas':
            $pagina = 1;
            $ventas = Ventas::consultarVentas($pagina, 5);
            print_r($ventas);
            break;
        case 'paginasTotal':
            $totalPaginas = Ventas::totalPaginas(5);
            print_r($totalPaginas);
            break;
        case 'productos':
            $ventas = Ventas::consultaFiltradaRelacionada("productos", "Modelo");
            print_r($ventas);
            break;
        case 'subtotal':
            $subtotal = DetallesVentas::obtenerSubtotal([3, 1], [1, 1]);
            print_r($subtotal . "\n");
            break;
        case 'detalles':
            $detalles = DetallesVentas::consultarDetallesVentas(5);
            print_r($detalles);
            break;
    }
}
