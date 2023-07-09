<?php
require_once 'DetallesVentas.php';
require_once 'Empleados.php';
require_once 'Clientes.php';
require_once 'Productos.php';
//require_once 'models/config.php';
/**
 *  Permite crear los objetos, llamar métodos relacionados al modulo de ventas
 */
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
    /**
     * Método mas importante para agregar la conexión a la base de datos
     * @param object $bd Conexión a la base de datos.
     */
    public static function init($bd)
    {
        self::$bd = $bd;
        DetallesVentas::init($bd);
        Empleados::init($bd);
        Clientes::init($bd);
        Productos::init($bd);
    }
    /**
     * Para agregar una venta se necesita calcular el iva.
     * @param double $subtotal Se manda el subtotal de la venta, se obtiene llamando al método de la clase DetallesVentas
     */
    public static function generarIva($subtotal)
    {
        $iva = $subtotal * 0.16;
        return $iva;
    }

    /**
     * Se pasa los datos necesarios para crear un objeto de ventas
     * @param int $idVenta
     * @param double $subtotal 
     * @param double $iva
     * @param int $idEmpleado
     * @param int $idCliente
     * @param string $fechaVenta Con el **formato Y-m-d**
     * @param array $iProductos Id de los productos a vender
     * @param array $cantidades Cantidades por producto a vender, en el mismo orden correspondiente al $idVenta.
     * @param double $costo Total de la venta
     */
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
        $idProductos = $this->idProductos;
        $cantidades = $this->cantidades;
        $costo = $this->costo;

        try {
            Productos::actualizarProductos($idProductos, $cantidades);

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
            /**
             * Se obtiene la id de la nueva inserción  de la tabla ventas para el objeto de DetallesVentas
             */
            $idVenta = $consulta->insert_id;
            $consulta->close();
            $detalleVenta = new DetallesVentas($idVenta, $idProductos, $cantidades, $costo);
            $detalleVenta->agregarDetalles();
            $venta = self::consultarVenta($idVenta);
            self::agregarRegistroVenta($venta);
        } catch (Exception $e) {
            #echo "La cantidad solicitada del producto con ID {$e->getMessage()} excede la existencia actual";
            throw $e;
        }
    }

    private static function agregarRegistroVenta($venta)
    {
        $consulta = self::$bd->prepare("INSERT INTO registro_ventas VALUES(?,?,?,?,?,?,?,?,?)");
        $consulta->bind_param(
            'isssssddd',
            $venta->idVenta,
            $venta->idEmpleado,
            $venta->idCliente,
            $venta->fechaVenta,
            $venta->idProductos,
            $venta->cantidades,
            $venta->iva,
            $venta->subtotal,
            $venta->costo
        );
        $consulta->execute();
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

    public static function consultarRegistroVentas($pagina = null, $contenido = null)
    {
        $pagina -= 1;
        $ventas = [];
        $idVenta = 0;
        $empleado = '';
        $cliente = '';
        $fecha = '';
        $productos = '';
        $cantidad = '';
        $subtotal = 0.0;
        $iva = 0.0;
        $costo = 0.0;

        if (!is_null($pagina) && !is_null($contenido)) {
            $consulta = self::$bd->prepare('SELECT * FROM registro_ventas LIMIT ?,?');
            $consulta->bind_param('ii', $pagina, $contenido);
        } else {
            $consulta = self::$bd->prepare('SELECT * FROM registro_ventas');
        }

        #$consulta = self::$bd->prepare('SELECT * FROM registro_ventas');

        $consulta->execute();
        $consulta->bind_result($idVenta, $empleado, $cliente, $fecha, $productos, $cantidad, $iva, $subtotal, $costo);

        while ($consulta->fetch()) {

            array_push($ventas, new Ventas($idVenta, $subtotal, $iva, $empleado, $cliente, $fecha, $productos, $cantidad, $costo));
        }

        $consulta->close();

        return $ventas;
    }

    public static function consultarVenta($id)
    {

        $ventasArray = [];
        $venta = null;
        $idVenta = 0;
        $subtotal = 0;
        $iva = 0;
        $idEmpleado = 0;
        $idCliente = 0;
        $fechaVenta = '';


        $consulta = self::$bd->prepare("SELECT id_venta, subtotal, iva, id_empleado, id_cliente, fecha_venta FROM ventas WHERE id_venta = ?");
        $consulta->bind_param('i', $id);

        $consulta->execute();
        $consulta->bind_result($idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta);



        while ($consulta->fetch()) {

            array_push($ventasArray, [$idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta]);
        }
        $consulta->close();

        if (isset($ventasArray)) {
            foreach ($ventasArray as $ventaA) {
                $id = $ventaA[0];
                $detalle =  DetallesVentas::consultarDetallesVentas($id);
                $detalle = $detalle[0];
                $nombreEmpleado = Empleados::id_emple($ventaA[3]);
                $nombreCliente = Clientes::buscarnom($ventaA[4]);

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

                $venta = new Ventas($ventaA[0], $ventaA[1], $ventaA[2], $nombreEmpleado, $nombreCliente, $ventaA[5], $productos, $cantidades, $detalle->costo);
            }
        }

        return $venta;
    }

    public static function consultaFiltrada($filtro, $value)
    {
        $ventas = [];
        $idVenta = 0;
        $empleado = '';
        $cliente = '';
        $fecha = '';
        $productos = '';
        $cantidad = '';
        $subtotal = 0.0;
        $iva = 0.0;
        $costo = 0.0;


        switch ($filtro) {
            case 'id':
                $consulta = self::$bd->prepare("SELECT * FROM registro_ventas WHERE id_venta = ?");
                $consulta->bind_param('i', $value);
                break;

            case 'fecha':
                $value = '%' . $value . '%';
                $consulta = self::$bd->prepare("SELECT * FROM registro_ventas WHERE fecha like ?");
                $consulta->bind_param('s', $value);
                break;
            case 'empleados':
                $value = '%' . $value . '%';
                $consulta = self::$bd->prepare("SELECT * FROM registro_ventas WHERE empleado like ?");
                $consulta->bind_param('s', $value);
                break;
            case 'clientes':
                $value = '%' . $value . '%';
                $consulta = self::$bd->prepare("SELECT * FROM registro_ventas WHERE cliente like ?");
                $consulta->bind_param('s', $value);
                break;
            case 'productos':
                $value = '%' . $value . '%';
                $consulta = self::$bd->prepare("SELECT * FROM registro_ventas WHERE productos like ?");
                $consulta->bind_param('s', $value);
                break;
        }

        $consulta->execute();
        $consulta->bind_result($idVenta, $empleado, $cliente, $fecha, $productos, $cantidad, $iva, $subtotal, $costo);


        while ($consulta->fetch()) {

            array_push($ventas, new Ventas($idVenta, $subtotal, $iva, $empleado, $cliente, $fecha, $productos, $cantidad, $costo));
        }
        $consulta->close();


        return $ventas;
    }
}


if (isset($argc) && $argc == 2) {
    $mysqli = new mysqli("localhost", "root", "", "agenciaBD");
    Ventas::init($mysqli);

    switch ($argv[1]) {
        case "id":
            #$ventas = Ventas::consultaFiltrada("id", 3);
            $ventas = Ventas::consultarVenta(1);
            print_r($ventas);
            break;
        case 'ventas':
            $pagina = 1;
            $ventas = Ventas::consultarRegistroVentas($pagina, 5);
            print_r($ventas);
            break;
        case 'paginasTotal':
            $totalPaginas = Ventas::totalPaginas(5);
            print_r($totalPaginas);
            break;
        case 'subtotal':
            $subtotal = DetallesVentas::obtenerSubtotal([3, 1], [1, 1]);
            print_r($subtotal . "\n");
            break;
        case 'detalles':
            $detalles = DetallesVentas::consultarDetallesVentas(5);
            print_r($detalles);
            break;
        case 'agrega':
            $idProductos = [3, 5, 4];
            $cantidades = [1, 1, 2];
            $subtotal = DetallesVentas::obtenerSubtotal($idProductos, $cantidades);
            $iva = Ventas::generarIva($subtotal);
            $costo = DetallesVentas::calcularCosto($subtotal, $iva);
            date_default_timezone_set('America/Mexico_City'); # Zona horaria para Mexico
            $fecha = date("Y-m-d");
            $venta = new Ventas('0', $subtotal, $iva, 4, 1, $fecha, $idProductos, $cantidades, $costo);
            $venta->agregarVenta();
            break;
    }
}
