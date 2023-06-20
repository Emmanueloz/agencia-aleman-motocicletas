<?php

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
    /**
     * Consulta los detalles de las ventas
     * @param mixed $id Con valor por defecto es null, si se le pasa como parámetro la consulta es por el id
     * @return  $detallesVentaArray Es un array que cuenta con objetos de cada elemento de la consulta completa de todos los detalles
     * @return DetallesVentas Es el objeto de una consulta por el `$id`;
     */
    public static function consultarDetallesVentas($id = null)
    {
        $detallesVentaArray = [];
        $idVenta = 0;
        $productos = '';
        $cantidad = 0;
        $costo = 0;
        /**
         * TODO: Mejorar el formato de la consulta
         */
        if ($id == null) {
            $consulta = self::$bd->prepare("SELECT detalles_venta.id_venta, GROUP_CONCAT(',',productos.numero_serie,': ',productos.marca,'<br/>'),
            detalles_venta.cantidad,detalles_venta.costo
            FROM ventas, productos, detalles_venta
            WHERE ventas.id_venta = detalles_venta.id_venta 
            AND productos.id_producto = detalles_venta.id_producto
            GROUP BY detalles_venta.id_venta");
            $consulta->execute();
            $consulta->bind_result($idVenta, $productos, $cantidad, $costo);

            while ($consulta->fetch()) {

                array_push($detallesVentaArray, new DetallesVentas($idVenta, $productos, $cantidad, $costo));
            }

            $consulta->close();
            return  $detallesVentaArray;
        } else {
            $consulta = self::$bd->prepare("SELECT detalles_venta.id_venta, GROUP_CONCAT(productos.numero_serie,': ',productos.marca,'<br/>'),
            detalles_venta.cantidad,detalles_venta.costo
            FROM ventas, productos, detalles_venta
            WHERE ventas.id_venta = detalles_venta.id_venta 
            AND productos.id_producto = detalles_venta.id_producto
            AND detalles_venta.id_venta = ?
            GROUP BY detalles_venta.id_venta");
            $consulta->bind_param('i', $id);
            $consulta->execute();
            $consulta->store_result();
            $consulta->bind_result($idVenta, $productos, $cantidad, $costo);
            $consulta->fetch();
            $consulta->close();

            return new DetallesVentas($idVenta, $productos, $cantidad, $costo);
        }
    }
}
