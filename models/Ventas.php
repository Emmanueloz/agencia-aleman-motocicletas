<?php

class Ventas
{
    public $idVenta;
    public $rfc;
    public $nombre;
    public $apellidos;
    public $calle;
    public $numero;
    public $barrio;
    public $telefono;
    public $email;

    private static $bd;

    public function __construct($idVenta, $rfc, $nombre, $apellidos, $calle, $numero, $barrio, $telefono, $email)
    {
        $this->idVenta = $idVenta;
        $this->rfc = $rfc;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->calle = $calle;
        $this->numero = $numero;
        $this->barrio = $barrio;
        $this->telefono = $telefono;
        $this->email = $email;
    }

    public static function init($bd)
    {
        self::$bd = $bd;
    }

    public function agregarVentas()
    {
        $bd = self::$bd;
    }
}

//$ventas = new Ventas(1, "DOT", "David", "Ozuna", "Tulipanes", 12, "Bugambilia", "91919191", "correo@gmail.con");
