<?php


/**
 * Permite crear los objetos
 */
class Clientes
{
    public $id_cliente;
    public $rfc;
    public $nombre;
    public $direccion;
    public $telefono;
    public $correo;
    public $genero;
    public $estado;
    /**
     * @var mysqli $bd objeto de conexión a la base de datos
     */
    private static $bd;

    /**
     * Se pasan los datos necesarios para crear un objeto de clientes
     * @param int $id_cliente
     * @param string $rfc del cliente
     * @param string $nombre del cliente
     * @param string $direccion del cliente
     * @param string $telefono del cliente
     * @param string $correo del cliente
     * @param string $genero
     * @param int $estado
     */
    public function __construct($id_cliente, $rfc, $nombre, $direccion, $telefono, $correo, $genero, $estado = 1)
    {
        $this->id_cliente = $id_cliente;
        $this->rfc = $rfc;
        $this->nombre = $nombre;
        $this->direccion = $direccion;
        $this->telefono = $telefono;
        $this->correo = $correo;
        $this->genero = $genero;
        $this->estado = $estado;
    }

    /**
     * Método más importante para agregar la conexión a la base de datos
     * @param object $bd Es la conexión a la base de datos
     */
    public static function init($bd)
    {
        self::$bd = $bd;
    }

    /**
     * Consulta los clientes que hay en la base de datos
     * @param int $pagina La página actual en la que nos encontramos
     * @param int $contenido La cantidad de contenido que se muestra por página
     * @return array Un array con los clientes consultados
     */
    public static function consulta($pagina = null, $contenido = null)
    {
        $pagina = ($pagina - 1) * $contenido;
        $clientes = [];

        if (!is_null($pagina) && !is_null($contenido)) {
            $consulta = self::$bd->prepare('SELECT * FROM clientes WHERE estado = 1 LIMIT ?,?');
            $consulta->bind_param('ii', $pagina, $contenido);
        } else {
            $consulta = self::$bd->prepare("SELECT * FROM clientes WHERE estado = 1");
        }

        $consulta->execute();
        $consulta->bind_result($id_cliente, $rfc, $nombre, $direccion, $telefono, $correo, $genero, $estado);

        while ($consulta->fetch()) {
            array_push($clientes, new Clientes($id_cliente, $rfc, $nombre, $direccion, $telefono, $correo, $genero));
        }
        $consulta->close();
        return $clientes;
    }

    /**
     * Realiza una busqueda de clientes por el nombre y nos devuelve el id
     * @param string $nombre El nombre a buscar
     * @return array Un array con el id de los clientes que clumple con la condición
     */
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

    /**
     * Realiza una busqueda de clientes por el id y nos devuelve el nombre
     * @param int $id_cliente El id a buscar
     * @return array Un array con el nombre de los clientes que clumple con la condición
     */
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

    /**
     * Realiza una busqueda filtrada de los clientes
     * @param string $opciones Las opciones por las que vamos a buscar al cliente, o el filtro
     * @param string $value El valor de las opciones o del filtro
     * @return array Un array con los clientes que cumplen con los valores pasados en el filtro
     */
    public static function busquedafil($opciones, $value)
    {
        switch ($opciones) {
            case 'idcli':
                $consulta = self::$bd->prepare("select * from clientes where id_cliente = ? AND estado = 1");
                $consulta->bind_param('i', $value);
                break;
            case 'nomcli':
                $consulta = self::$bd->prepare("select * from clientes where nombre like ? AND estado = 1");
                $value = '%' . $value . '%';
                $consulta->bind_param("s", $value);
                break;
            case 'rfccli':
                $consulta = self::$bd->prepare("select * from clientes where rfc like ? AND estado = 1");
                $value = '%' . $value . '%';
                $consulta->bind_param("s", $value);
                break;
        }
        $consclientes = [];
        $consulta->execute();
        $consulta->bind_result($id_cliente, $rfc, $nombre, $direccion, $telefono, $correo, $genero, $estado);
        while ($consulta->fetch()) {
            array_push($consclientes, new Clientes($id_cliente, $rfc, $nombre, $direccion, $telefono, $correo, $genero));
        }
        $consulta->close();
        return $consclientes;
    }

    /**
     * Esta funcion es la que se usa para poder agregar a un nuevo cliente a nuestra BD
     */
    public function agregar()
    {
        try {
            $consulta = self::$bd->prepare('select id_cliente from clientes where rfc = ?');
            $consulta->bind_param('s', $this->rfc);
            $consulta->execute();
            if($consulta->fetch()){
                throw new Exception('El RFC ya existe');
            }
            $consulta->close();
            $consulta = self::$bd->prepare("insert into clientes values(null, ?, ?, ?, ?, ?, ?,1)");
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
            $idClient = $consulta->insert_id;
            $consulta->close();
            return $idClient;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Esta funcion sirve para el paginado y el contenido que se mostrara en cada pagina
     */
    public static function totalPaginas($contenido)
    {
        $totalFilas = 0;
        $consulta = self::$bd->prepare("SELECT COUNT(id_cliente) FROM clientes WHERE estado = 1");
        $consulta->execute();
        $consulta->bind_result($totalFilas);
        $consulta->fetch();
        $consulta->close();

        $totalPaginas = ceil($totalFilas / $contenido);

        return $totalPaginas;
    }

    /**
     * Esta funcion sirve para poder modificar un registro de un cliente ya agregado
     */
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
            return $this->id_cliente;
        }
    }

    /**
     * Realiza una busqueda por el id del cliente y nos devuelve todos los datos de dicho cliente
     */
    public static function buscarid($id)
    {
        $cliente = null;
        $consulta = self::$bd->prepare("select * from clientes where id_cliente = ? AND estado = 1");
        $consulta->bind_param('i', $id);
        $consulta->execute();
        $consulta->bind_result($id_cliente, $rfc, $nombre, $direccion, $telefono, $correo, $genero, $estado);
        if ($consulta->fetch()) {
            $cliente = new Clientes($id_cliente, $rfc, $nombre, $direccion, $telefono, $correo, $genero);
        }
        return $cliente;
    }

    /**
     * Esta funcion sirve para poder eliminar un registro de un cliente de la vista, pero no se elimina de la BD
     */
    public function eliminar($bd)
    {
        if ($consulta = $bd->prepare("update clientes set estado = 0 where id_cliente = ?")) {
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
            print("Registro estado");
            break;
    }
} */
