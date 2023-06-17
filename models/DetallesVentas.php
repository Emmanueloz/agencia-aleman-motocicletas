<?php

class DetallesVentas
{
    public $idVenta;
    public $idProducto;
    public $cantidad;
    public $costo;

    public function __construct($idVenta, $idProducto, $cantidad, $costo)
    {
        $this->idVenta = $idVenta;
        $this->idProducto = $idProducto;
        $this->cantidad = $cantidad;
        $this->costo = $costo;
    }
}
