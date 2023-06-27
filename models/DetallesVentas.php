<?php
require_once 'Productos.php';
/**
 * Clase con todos los métodos relacionado a los detalles
 */

class DetallesVentas
{
    public $idVenta;
    public $idProductos;
    public $cantidad;
    public $costo;

    private static $bd;

    public function __construct($idVenta, $idProductos, $cantidad, $costo)
    {
        $this->idVenta = $idVenta;
        $this->idProductos = $idProductos;
        $this->cantidad = $cantidad;
        $this->costo = $costo;
    }

    public static function init($bd)
    {
        self::$bd = $bd;
    }

    public static function obtenerSubtotal($idProductos)
    {
        $subtotal = 0.0;

        $precio = 0.0;

        foreach ($idProductos as $idProducto) {
            $consulta = self::$bd->prepare("SELECT productos.precio 
            FROM productos
            WHERE productos.id_producto = ?");
            $consulta->bind_param("i", $idProducto);
            $consulta->execute();
            $consulta->bind_result($precio);
            $consulta->fetch();
            $consulta->close();
            $subtotal = $subtotal + $precio;
        }

        return $subtotal;
    }

    public static function calcularCosto($subtotal, $iva)
    {
        return $subtotal + $iva;
    }

    /*  public function mostrarDetalles($subtotal, $iva)
    {
        $detalleVenta = [];
        array_push($detalleVenta, $this->idVenta, $this->idProductos);
    } */


    public function agregarDetalles()
    {
        $idProductos = $this->idProductos;

        foreach ($idProductos as $idProducto) {
            $consulta = self::$bd->prepare("INSERT INTO detalles_venta VALUES(?,?,?,?)");
            $consulta->bind_param(
                "iiid",
                $this->idVenta,
                $idProducto,
                $this->cantidad,
                $this->costo
            );
            $consulta->execute();
            $consulta->close();
        }
    }

    public static function productosVendidos($idVenta)
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
     * Consulta los detalles de las ventas
     * @param mixed $id Con valor por defecto es null, si se le pasa como parámetro la consulta es por el id
     * @return  $detallesVentaArray Es un array que cuenta con objetos de cada elemento de la consulta completa de todos los detalles
     * @return DetallesVentas Es el objeto de una consulta por el `$id`;
     */

    public static function consultarDetallesVentas($id = null)
    {
        $detallesVentaArray = [];
        $detalles = [];
        $idVenta = 0;

        $productos = [];
        $productosArray = [];
        $cantidad = 0;
        $costo = 0;
        /**
         * TODO: Mejorar el formato de la consulta
         */
        if ($id == null) {
            $consulta = self::$bd->prepare("SELECT id_venta, cantidad, costo
            FROM  detalles_venta
            GROUP BY id_venta");
            $consulta->execute();
            $consulta->bind_result($idVenta, $cantidad, $costo);

            while ($consulta->fetch()) {

                array_push($detallesVentaArray, [$idVenta, $cantidad, $costo]);
            }
            $consulta->close();

            if (count($detallesVentaArray) == 0 || isset($detallesVentaArray)) {
                foreach ($detallesVentaArray as $detalle) {
                    $id = $detalle[0];
                    $idProductos = self::productosVendidos($id);
                    foreach ($idProductos as $idProducto) {
                        $producto = Productos::consultPrecioMarcaModelo($idProducto);
                        $producto = "$producto[0] $producto[1] $producto[2]";
                        array_push($productos, $producto);
                    }
                    #array_push($productosArray, $productos);
                    $productosDatos = $productos[0];
                    array_push($detalles, new DetallesVentas($id, $productosDatos, $detalle[1], $detalle[2]));
                    $productos = [];
                }
            }
            return $detalles;
        } else {

            $consulta = self::$bd->prepare("SELECT detalles_venta.id_venta, GROUP_CONCAT(productos.marca,': ',productos.modelo,'<br/>'),
            detalles_venta.cantidad,detalles_venta.costo
            FROM ventas, productos, detalles_venta
            WHERE ventas.id_venta = detalles_venta.id_venta 
            AND productos.id_producto = detalles_venta.id_producto
            AND detalles_venta.id_venta = ?
            GROUP BY detalles_venta.id_venta");
            $consulta->bind_param('i', $id);
            $consulta->execute();
            $consulta->bind_result($idVenta, $productos, $cantidad, $costo);
            $consulta->fetch();
            $detalle = new DetallesVentas($idVenta, $productos, $cantidad, $costo);
            $consulta->close();

            return $detalle;
        }
    }
}


# Pruebas
if (isset($argc) && $argc == 2) {
    $mysqli = new mysqli("localhost", "root", "", "agenciaBD");

    DetallesVentas::init($mysqli);
    Productos::init($mysqli);
    switch ($argv[1]) {

        case 'productos':
            $idVentas = DetallesVentas::productosVendidos(2);
            print_r($idVentas);
            break;
        case 'detalles':
            $detalle = DetallesVentas::consultarDetallesVentas(1);
            print_r($detalle);
            break;
    }
}
