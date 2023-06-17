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

    public static function generarIva($subtotal)
    {
        $iva = $subtotal * 0.16;
        return $iva;
    }

    public function __construct($idVenta, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta,)
    {
        $this->idVenta = $idVenta;
        $this->subtotal = $subtotal;
        $this->iva = $iva;
        $this->idEmpleado = $idEmpleado;
        $this->idCliente = $idCliente;
        $this->fechaVenta = $fechaVenta;
    }

    public static function init($bd)
    {
        self::$bd = $bd;
    }
}
