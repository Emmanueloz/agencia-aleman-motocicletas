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
    public $eliminado;
    private static $bd;

    public function __construct($id_cliente, $rfc, $nombre, $direccion, $telefono, $correo, $genero, $eliminado = 1)
    {
        $this->id_cliente = $id_cliente;
        $this->rfc = $rfc;
        $this->nombre = $nombre;
        $this->direccion = $direccion;
        $this->telefono = $telefono;
        $this->correo = $correo;
        $this->genero = $genero;
        $this->eliminado = $eliminado;
    }

    public static function init($bd)
    {
        self::$bd = $bd;
    }

    public static function consulta($pagina = null, $contenido = null)
    {
        $pagina = ($pagina - 1) * $contenido;
        $clientes = [];

        if (!is_null($pagina) && !is_null($contenido)) {
            $consulta = self::$bd->prepare('SELECT * FROM clientes WHERE eliminado = 1 LIMIT ?,?');
            $consulta->bind_param('ii', $pagina, $contenido);
        } else {
            $consulta = self::$bd->prepare("SELECT * FROM clientes WHERE eliminado = 1");
        }

        $consulta->execute();
        $consulta->bind_result($id_cliente, $rfc, $nombre, $direccion, $telefono, $correo, $genero, $eliminado);

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

    public static function buscarnom($id_cliente)
    {
        $consulta = self::$bd->prepare("select nombre from clientes where id_cliente = ?");
        $consulta->bind_param('i', $id_cliente);
        $consulta->execute();
        $consulta->bind_result($nombre);
        $consulta->fetch();
        $consulta->close();
        return $nombre;
    }

    public static function busquedafil($opciones, $value)
    {
        switch ($opciones) {
            case 'idcli':
                $consulta = self::$bd->prepare("select * from clientes where id_cliente = ? AND eliminado = 1");
                $consulta->bind_param('i', $value);
                break;
            case 'nomcli':
                $consulta = self::$bd->prepare("select * from clientes where nombre like ? AND eliminado = 1");
                $value = $value . '%';
                $consulta->bind_param("s", $value);
                break;
            case 'rfccli':
                $consulta = self::$bd->prepare("select * from clientes where rfc like ? AND eliminado = 1");
                $value = $value . '%';
                $consulta->bind_param("s", $value);
                break;
        }
        $consclientes = [];
        $consulta->execute();
        $consulta->bind_result($id_cliente, $rfc, $nombre, $direccion, $telefono, $correo, $genero, $eliminado);
        while ($consulta->fetch()) {
            array_push($consclientes, new Clientes($id_cliente, $rfc, $nombre, $direccion, $telefono, $correo, $genero));
        }
        $consulta->close();
        return $consclientes;
    }

    public function agregar()
    {
        if ($consulta = self::$bd->prepare("insert into clientes values(null, ?, ?, ?, ?, ?, ?,1)")); {
            $consulta->bind_param(
                'ssssss',
                $this->rfc,
                $this->nombre,
                $this->direccion,
                $this->telefono,
                $this->correo,
                $this->genero
            );

            $consulta->execute();
            $consulta->close();
        }
    }

    public static function totalPaginas($contenido)
    {
        $totalFilas = 0;
        $consulta = self::$bd->prepare("SELECT COUNT(id_cliente) FROM clientes WHERE eliminado = 1");
        $consulta->execute();
        $consulta->bind_result($totalFilas);
        $consulta->fetch();
        $consulta->close();

        $totalPaginas = ceil($totalFilas / $contenido);

        return $totalPaginas;
    }

    public function modificar()
    {
        if ($consulta = self::$bd->prepare("update clientes set rfc = ?, nombre = ?, direccion = ?, telefono = ?, correo = ?, genero = ? where id_cliente = ?")) {
            $consulta->bind_param(
                'ssssssi',
                $this->rfc,
                $this->nombre,
                $this->direccion,
                $this->telefono,
                $this->correo,
                $this->genero,
                $this->id_cliente
            );

            $consulta->execute();
            $consulta->close();
        }
    }

    public static function buscarid($id)
    {
        $cliente = null;
        $consulta = self::$bd->prepare("select * from clientes where id_cliente = ? AND eliminado = 1");
        $consulta->bind_param('i', $id);
        $consulta->execute();
        $consulta->bind_result($id_cliente, $rfc, $nombre, $direccion, $telefono, $correo, $genero, $eliminado);
        if ($consulta->fetch()) {
            $cliente = new Clientes($id_cliente, $rfc, $nombre, $direccion, $telefono, $correo, $genero);
        }
        return $cliente;
    }

    public function eliminar($bd)
    {
        if ($consulta = $bd->prepare("update clientes set eliminado = 0 where id_cliente = ?")) {
            $consulta->bind_param('i', $this->id_cliente);
            $consulta->execute();
            $consulta->close();
        }
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
        case 'nom':
            $nombre = Clientes::buscarnom(4);
            print($nombre);
            break;
        case 'filtro':
            #Clientes::init($mysqli);
            $consclientes = Clientes::busquedafil('nomcli', 'Cliente 3');
            print_r($consclientes);
            break;
        case 'nuevo':
            Clientes::init($mysqli);
            $clientes = new Clientes(0, 'RFC7', 'Cliente 7', 'Direccion 7', '0987654321', 'cliente7@ejemplo.com', 'F');
            $clientes->agregar();
            break;
        case 'actualizar':
            $cliente = Clientes::buscarid(1);
            print_r($cliente);
            $cliente->rfc = "RFC1";
            $cliente->telefono = "0123456789";
            $cliente->modificar($mysqli);
            print_r($cliente);
            break;
        case 'eliminarcli':
            $client = Clientes::buscarid(1);
            print_r($client);
            $client->eliminar($mysqli);
            print("Registro eliminado");
            break;
    }
} */
