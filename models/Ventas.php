<?php

class Ventas
{
    public $idVenta;
    public $subtotal;
    public $iva;
    public $idEmpleado;
    public $idCliente;
    public $fechaVenta;


    private static $bd;

    public function generarIva($subtotal)
    {
        $this->iva = $subtotal;
    }

    public function __construct($idVenta, $subtotal, $idEmpleado, $idCliente, $fechaVenta,)
    {
        $this->idVenta = $idVenta;
        $this->subtotal = $subtotal;
        $this->idEmpleado = $idEmpleado;
        $this->idCliente = $idCliente;
        $this->fechaVenta = $fechaVenta;
    }

    public static function init($bd)
    {
        self::$bd = $bd;
    }
}
