<?php

class Clientes
{
    public $id_cliente;
    public $rfc;
    public $nombre;
    public $direccion;
    public $telefono;
    public $correo;
    public $genero;
    private static $bd;

    public function __construct($id_cliente, $rfc, $nombre, $direccion, $telefono, $correo, $genero)
    {
        $this->id_cliente = $id_cliente;
        $this->rfc = $rfc;
        $this->nombre = $nombre;
        $this->direccion = $direccion;
        $this->telefono = $telefono;
        $this->correo = $correo;
        $this->genero = $genero;
    }

    public static function init($bd)
    {
        self::$bd = $bd;
    }

    public static function consulta()
    {
        $clientes = [];
        $consulta = self::$bd->prepare("select * from clientes");
        $consulta->execute();
        $consulta->bind_result($id_cliente, $rfc, $nombre, $direccion, $telefono, $correo, $genero);

        while ($consulta->fetch()) {
            array_push($clientes, new Clientes($id_cliente, $rfc, $nombre, $direccion, $telefono, $correo, $genero));
        }
        $consulta->close();
        return $clientes;
    }

    public static function buscarcli($nombre)
    {
        $id = [];
        $nombre = "%" . $nombre . "%";
        $consulta = self::$bd->prepare("select id_cliente from clientes where nombre like ?");
        $consulta->bind_param('s', $nombre);
        $consulta->execute();
        $consulta->bind_result($id_cliente);

        while ($consulta->fetch()) {
            array_push($id, $id_cliente);
        }
        $consulta->close();
        return $id;
    }
}

/* if (isset($argc) && $argc == 2) {
    $mysqli = new mysqli("localhost", "root", "", "agenciaBD");
    Clientes::init($mysqli);
    switch ($argv[1]) {
        case 'todos':
            $clientes = Clientes::consulta();
            print_r($clientes);
            break;
        case 'buscar':
            $id = Clientes::buscarcli('1');
            print_r($id);
            break;
    }
} */
