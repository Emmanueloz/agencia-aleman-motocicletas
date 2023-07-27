<?php
require_once 'Productos.php';
/**
 * Clase con todos los métodos relacionado a los detalles
 */

class DetallesVentas
{
    public $idVenta;
    public $idProductos;
    public $cantidades;
    public $costo;
    /**
     * @var object Referencia a la base de datos.
     */
    private static $bd;

    public function __construct($idVenta, $idProductos, $cantidades, $costo)
    {
        $this->idVenta = $idVenta;
        $this->idProductos = $idProductos;
        $this->cantidades = $cantidades;
        $this->costo = $costo;
    }
    /**
     * Inicializa la conexión a la base de datos.
     *
     * @param object $bd Base de datos.
     */
    public static function init($bd)
    {
        self::$bd = $bd;
    }
    /**
     * Obtiene el subtotal de la venta basado en la lista de productos y sus cantidades.
     *
     * @param array $idProductos Arreglo con los identificadores de los productos.
     * @param array $cantidades Arreglo con las cantidades de los productos.
     * @return float Subtotal de la venta.
     */
    public static function obtenerSubtotal($idProductos, $cantidades)
    {
        $subtotal = 0.0;

        $precio = 0.0;
        for ($i = 0; $i < count($idProductos); $i++) {

            $consulta = self::$bd->prepare("SELECT precio FROM productos WHERE id_producto = ?");
            $consulta->bind_param("i", $idProductos[$i]);
            $consulta->execute();
            $consulta->bind_result($precio);
            $consulta->fetch();
            $consulta->close();
            $subtotal = $subtotal + ($precio * $cantidades[$i]);
        }

        return $subtotal;
    }

    public static function calcularCosto($subtotal, $iva)
    {
        return $subtotal + $iva;
    }

    public function agregarDetalles()
    {
        $idProductos = $this->idProductos;
        $cantidades = $this->cantidades;


        for ($i = 0; $i < count($idProductos); $i++) {
            $consulta = self::$bd->prepare("INSERT INTO detalles_venta VALUES(?,?,?,?)");
            $consulta->bind_param(
                "iiid",
                $this->idVenta,
                $idProductos[$i],
                $cantidades[$i],
                $this->costo
            );
            $consulta->execute();
            $consulta->close();
        }
    }
    /**
     * Obtiene la lista de productos vendidos en una venta.
     *
     * @param int $idVenta Identificador de la venta.
     * @return array Arreglo con los identificadores de los productos vendidos.
     */
    private static function productosVendidos($idVenta)
    {
        $detallesIdVenta = [];
        $idProducto = 0;

        $consulta = self::$bd->prepare("SELECT id_producto FROM detalles_venta WHERE id_venta = ?");
        $consulta->bind_param('i', $idVenta);
        $consulta->execute();
        $consulta->bind_result($idProducto);

        while ($consulta->fetch()) {

            array_push($detallesIdVenta, $idProducto);
        }

        $consulta->close();
        return  $detallesIdVenta;
    }

    /**
     * Obtiene las cantidades vendidas por producto en una venta.
     *
     * @param int $id Identificador de la venta.
     * @return array Arreglo con las cantidades vendidas por producto.
     */
    private static function consultarCantidadesPorProducto($id)
    {
        $cantidad = 0;
        $cantidades = [];
        $consulta = self::$bd->prepare('SELECT cantidad FROM `detalles_venta` WHERE id_venta = ?');
        $consulta->bind_param('i', $id);
        $consulta->execute();
        $consulta->bind_result($cantidad);
        while ($consulta->fetch()) {

            array_push($cantidades, $cantidad);
        }
        $consulta->close();
        return $cantidades;
    }

    /**
     * Consulta los detalles de una venta.
     *
     * @param int $id Identificador de la venta.
     * @return array|DetallesVentas Arreglo con objetos de los detalles de la venta, 
     * o un objeto DetallesVentas si se especifica un id.
     */
    public static function consultarDetallesVentas($id)
    {
        $detallesVentaArray = [];
        $detalles = [];
        $idVenta = 0;

        $productos = [];

        $costo = 0;

        $consulta = self::$bd->prepare("SELECT id_venta, costo
        FROM  detalles_venta
        WHERE id_venta = ?
        GROUP BY id_venta");
        $consulta->bind_param('i', $id);

        $consulta->execute();
        $consulta->bind_result($idVenta, $costo);

        if ($consulta->fetch()) {
            array_push($detallesVentaArray, [$idVenta, $costo]);
        }

        $consulta->close();

        if (count($detallesVentaArray) == 0 || isset($detallesVentaArray)) {
            for ($i = 0; $i < count($detallesVentaArray); $i++) {
                $detalle = $detallesVentaArray[$i];
                $id = $detalle[0];
                $idProductos = self::productosVendidos($id);
                $cantidades = self::consultarCantidadesPorProducto($id);


                foreach ($idProductos as $idProducto) {
                    $producto = Productos::consultPrecioMarcaModelo($idProducto);
                    $productoNom = "$producto[1] $producto[2] \$$producto[3]";
                    array_push($productos, [$producto[0], $productoNom]);
                }

                array_push($detalles, new DetallesVentas($id, $productos, $cantidades, $detalle[1]));
                $productos = [];
            }
        }
        return $detalles;
    }
}
