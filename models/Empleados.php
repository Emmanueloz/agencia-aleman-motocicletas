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
}
if (isset($argc) && $argc == 2) {
    $mysqli = new mysqli("localhost", "root", "", "agenciabd");
    Empleados::init($mysqli);
    switch ($argv[1]) {
        case 'consul_emple':
            $emplea = Empleados::consul();
            print_r($id);
            break;
        case 'por_nombre':
            $nombre = Empleados::nom('Robert');
            print_r($nombre);
            break;
        case 'emple_id':
            $id = Empleados::id_emple(3);
            print($id);
            break;
    }
}
