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

    public function mostrarDetalles($subtotal, $iva)
    {


        $detalleVenta = [];
        array_push($detalleVenta, $this->idVenta, $this->idProductos);
    }
}
