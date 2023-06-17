<?php

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

    public function obtenerSubtotal()
    {
        $consulta = self::$bd;
        $idProductos = $this->idProductos;

        $subtotal = 0;

        $precio = 0;
        foreach ($idProductos as $idProducto) {
            $consulta->prepare("SELECT productos.precio 
            FROM productos,detalles_venta 
            WHERE productos.id_producto = detalles_venta.id_producto AND detalles_venta.id_producto = ?");
            $consulta->bind_param("i", $idProducto);
            $consulta->execute();
            $consulta->bind_result($precio);
            $consulta->fetch();
            $subtotal = $subtotal + $precio;
        }
    }
}
