<?php
require_once 'DetallesVentas.php';
require_once 'Empleados.php';
require_once 'Clientes.php';
require_once 'Productos.php';
require_once 'Servicios.php';

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
        Servicios::init($bd);
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

    /**
     * Agrega una venta a la base de datos.
     *
     * @throws Exception Si se produce un error cuando la cantidad a vender excede la cantidad de existencias
     */
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
            $servicio = new Servicios(null, $this->idCliente, $this->fechaVenta, $idProductos, '');
            $servicio->agregarServicio();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Calcula el total de páginas necesarias para mostrar todas las ventas, dada la cantidad de contenido por página.
     *
     * @param int $contenido La cantidad de contenido por página.
     * @return int El total de páginas necesarias.
     */
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

    /**
     * Consulta las ventas en la base de datos.
     *
     * @param int $pagina La página actual.
     * @param int $contenido La cantidad de contenido por página.
     * @return array Un array con las ventas consultadas.
     */
    public static function consultarVentas($pagina = null, $contenido = null)
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

        if (!is_null($pagina) && !is_null($contenido)) {
            $consulta = self::$bd->prepare("SELECT id_venta, subtotal, iva, id_empleado, id_cliente, fecha_venta FROM ventas LIMIT ?,?");
            $consulta->bind_param('ii', $pagina, $contenido);
        } else {
            $consulta = self::$bd->prepare("SELECT id_venta, subtotal, iva, id_empleado, id_cliente, fecha_venta FROM ventas");
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
        }

        return $ventas;
    }

    /**
     * Realiza una consulta filtrada de las ventas.
     *
     * @param string $filtro El filtro a utilizar en la consulta.
     * @param string $value El valor del filtro.
     * @return array Un array con las ventas que cumplen el filtro.
     */
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
        }

        return $ventas;
    }

    /**
     * Realiza una consulta filtrada de las ventas basada en relaciones.
     *
     * @param string $filtro El filtro a utilizar en la consulta.
     * @param string $value El valor del filtro.
     * @return array Un array con las ventas que cumplen el filtro.
     */
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

        if (count($idVentas) != 0) {
            $idVentas = array_unique($idVentas);
            foreach ($idVentas as $idVenta) {
                $venta = self::consultaFiltrada("id", $idVenta);
                array_push($ventas, $venta[0]);
            }
            usort($ventas, 'compararPorIdVenta');
        }

        return $ventas;
    }
}


if (isset($argc) && $argc == 2) {
    $mysqli = new mysqli("localhost", "root", "", "agenciaBD");
    Ventas::init($mysqli);

    switch ($argv[1]) {
        case "id":
            $ventas = Ventas::consultaFiltrada("id", 3);
            #$ventas = Ventas::consultarVenta(7);
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
        case 'subtotal':
            $subtotal = DetallesVentas::obtenerSubtotal([3, 1], [1, 1]);
            print_r($subtotal . "\n");
            break;
        case 'detalles':
            $detalles = DetallesVentas::consultarDetallesVentas(2);
            print_r($detalles);
            break;
        case 'agrega':
            $idProductos = [3, 2, 4];
            $cantidades = [4, 1, 2];
            $subtotal = DetallesVentas::obtenerSubtotal($idProductos, $cantidades);
            $iva = Ventas::generarIva($subtotal);
            $costo = DetallesVentas::calcularCosto($subtotal, $iva);
            date_default_timezone_set('America/Mexico_City'); # Zona horaria para Mexico
            $fecha = date("Y-m-d");
            $venta = new Ventas('0', $subtotal, $iva, 3, 3, $fecha, $idProductos, $cantidades, $costo);
            print_r($venta->agregarVenta());
            break;
        case 'filtro':
            $ventas = Ventas::consultaFiltrada('id', '1');
            print_r($ventas);
            break;
    }
}
