<?php
class Empleados
{
    public $id_empleado;
    public $rfc;
    public $nombre;
    public $direccion;
    public $telefono;
    public $correo;
    public $puesto;
    public $salario;
    public $estudios;
    private static $bd;

    public function __construct(
        $id_empleado,
        $rfc,
        $nombre,
        $direccion,
        $telefono,
        $correo,
        $puesto,
        $salario,
        $estudios
    ) {
        $this->id_empleado = $id_empleado;
        $this->rfc = $rfc;
        $this->nombre = $nombre;
        $this->direccion = $direccion;
        $this->telefono = $telefono;
        $this->correo = $correo;
        $this->puesto = $puesto;
        $this->salario = $salario;
        $this->estudios = $estudios;
    }
    public function nuev()
    {
        if ($consult = self::$bd->prepare("insert into empleados values(null, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $consult->bind_param(
                "ssssssds",
                $this->rfc,
                $this->nombre,
                $this->direccion,
                $this->telefono,
                $this->correo,
                $this->puesto,
                $this->salario,
                $this->estudios
            );

            $consult->execute();
            $consult->close();
        }
    }

    public static function init($bd)
    {
        self::$bd = $bd;
    }
    public static function consul()
    {
        $emplea = [];
        $consult = self::$bd->prepare("select * from empleados");
        $consult->execute();
        $consult->bind_result($id_empleado, $rfc, $nombre, $direccion, $telefono, $correo, $puesto, $salario, $estudios);
        while ($consult->fetch()) {
            array_push($emplea, new Empleados($id_empleado, $rfc, $nombre, $direccion, $telefono, $correo, $puesto, $salario, $estudios));
        }
        $consult->close();
        return $emplea;
    }
    public static function nom($nombre)
    {
        $id = [];
        $nombre = "%" . $nombre . "%";
        $consult = self::$bd->prepare("select id_empleado from empleados where nombre like ?");
        $consult->bind_param('s', $nombre);
        $consult->execute();
        $consult->bind_result($id_empleado);
        while ($consult->fetch()) {
            array_push($id, $id_empleado);
        }
        $consult->close();
        return $id;
    }
    public static function id_emple($id)
    {
        $nom = "";
        $consult = self::$bd->prepare("select nombre from empleados where id_empleado = ?");
        $consult->bind_param('i', $id);
        $consult->execute();
        $consult->bind_result($nom);
        $consult->fetch();
        $consult->close();
        return $nom;
    }
    public static function filtro($opcion, $value)
    {
        switch ($opcion) {
            case 'id':
                $consult = self::$bd->prepare("select * from empleados where id_empleado = ?");
                $consult->bind_param('i', $value);
                break;
            case 'rfc':
                $consult = self::$bd->prepare("select * from empleados where rfc like ?");
                $value = $value . '%';
                $consult->bind_param("s", $value);
                break;
            case 'nombre':
                $consult = self::$bd->prepare("select * from empleados where nombre like ?");
                $value = $value . '%';
                $consult->bind_param("s", $value);
                break;
            case 'salario':
                $consult = self::$bd->prepare("select * from empleados where salario = ?");
                $value = $value . '%';
                $consult->bind_param("i", $value);
                break;
            case 'estudios':
                $consult = self::$bd->prepare("select * from empleados where estudios like ?");
                $value = $value . '%';
                $consult->bind_param("s", $value);
                break;
        }
        $opc = [];
        $consult->execute();
        $consult->bind_result($id_empleado, $rfc, $nombre, $direccion, $telefono, $correo, $puesto, $salario, $estudios);
        while ($consult->fetch()) {
            array_push($opc, new Empleados($id_empleado, $rfc, $nombre, $direccion, $telefono, $correo, $puesto, $salario, $estudios));
        }
        $consult->close();
        return $opc;
    }
}


if (isset($argc) && $argc == 2) {
    $mysqli = new mysqli("localhost", "root", "", "agenciabd");
    Empleados::init($mysqli);
    switch ($argv[1]) {
        case 'consul_emple':
            $emplea = Empleados::consul();
            print_r($emplea);
            break;
        case 'por_nombre':
            $nombre = Empleados::nom('Robert');
            print_r($nombre);
            break;
        case 'emple_id':
            $id = Empleados::id_emple(3);
            print($id);
            break;
        case 'filtro':
            Empleados::init($mysqli);
            $opc = Empleados::filtro('id', 3);
            print_r($opc);
            break;
        case 'nuevo':
            Empleados::init($mysqli);
            $empledos = new Empleados(0, 'RFC992', 'Roberto Carlos', '20noviembre', 9191200000, 'robe2@gmail.com', 'Desarrolador Java', 6000, 'maestria');
            $empledos->nuev();
            break;
    }
}
