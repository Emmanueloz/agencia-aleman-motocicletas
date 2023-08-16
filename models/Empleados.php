<?php

/**
 *  Permite crear los objetos y llamar métodos relacionados al modulo de Empleados
 */
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
    public $estado;
    /**
     * @var mysqli $bd objeto de conexión a la base de datos
     */
    private static $bd;

    /**Se pasan los datos necesarios para crear los objetos de empleados
     * @param int $id_empleado
     * @param string $rfc
     * @param string $nombre
     * @param string $direccion
     * @param string $telefono
     * @param string $correo
     * @param string $puesto
     * @param float $salario
     * @param string $estudios
     * @param string $estado Se utiliza para que no aparezca en la vista, como si fuera eliminar pero  en el modulo venta se guardan los datos de ese cliente. 
     */
    public function __construct(
        $id_empleado,
        $rfc,
        $nombre,
        $direccion,
        $telefono,
        $correo,
        $puesto,
        $salario,
        $estudios,
        $estado = 1

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
        $this->estado = $estado;
    }

    /**Para insertar un nuevo empleado, que se agrega a la base de datos tiene que evaluar los datos que aparecen y llenada
     */
    public function nuev()
    {
        if ($consult = self::$bd->prepare("insert into empleados values(null, ?, ?, ?, ?, ?, ?, ?, ?,1)")) {
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
    /**
     * Consulta las ventas en la base de datos.
     *
     * @param int $pagina La página actual.
     * @param int $contenido La cantidad de contenido por página.
     * @return array Un array con las empleados consultadas.
     */
    public static function consul($pagina = null, $contenido = null)
    {
        $pagina = ($pagina - 1) * $contenido;
        $emplea = [];
        if (!is_null($pagina) && !is_null($contenido)) {
            $consult = self::$bd->prepare('SELECT * FROM empleados WHERE estado = 1 LIMIT ?,? ');
            $consult->bind_param('ii', $pagina, $contenido);
        } else {
            $consult = self::$bd->prepare("select * from empleados WHERE estado = 1");
        }

        $consult->execute();
        $consult->bind_result($id_empleado, $rfc, $nombre, $direccion, $telefono, $correo, $puesto, $salario, $estudios, $estado);

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
    /**
     * Realiza una consulta filtrada en Empleados.
     *
     * @param string $opcion El filtro a utilizar en la consulta.
     * @param string $value El valor del filtro.
     * @return array Un array con las ventas que cumplen el filtro.
     */
    public static function filtro($opcion, $value)
    {
        $opc = [];

        switch ($opcion) {
            case 'id':
                $consult = self::$bd->prepare("select * from empleados where id_empleado = ? AND estado = 1");
                $consult->bind_param('i', $value);
                break;
            case 'rfc':
                $consult = self::$bd->prepare("select * from empleados where rfc like ? AND estado = 1");
                $value = $value . '%';
                $consult->bind_param("s", $value);
                break;
            case 'nombre':
                $consult = self::$bd->prepare("select * from empleados where nombre like ? AND estado = 1");
                $value = $value . '%';
                $consult->bind_param("s", $value);
                break;
            case 'salario':
                $consult = self::$bd->prepare("select * from empleados where salario = ? AND estado = 1");
                $value = $value . '%';
                $consult->bind_param("i", $value);
                break;
            case 'estudios':
                $consult = self::$bd->prepare("select * from empleados where estudios like ? AND estado = 1");
                $value = $value . '%';
                $consult->bind_param("s", $value);
                break;
            default:
                return $opc;
                break;
        }
        $consult->execute();
        $consult->bind_result($id_empleado, $rfc, $nombre, $direccion, $telefono, $correo, $puesto, $salario, $estudios, $estado);
        while ($consult->fetch()) {
            array_push($opc, new Empleados($id_empleado, $rfc, $nombre, $direccion, $telefono, $correo, $puesto, $salario, $estudios));
        }
        $consult->close();
        return $opc;
    }
    /**Para modificar un nuevo empleado, tiene que modificar los datos incorectos
     */
    public function update()
    {
        if ($consult = self::$bd->prepare("update empleados set rfc = ?, nombre = ?, 
        direccion = ?, telefono = ?, correo = ?, puesto = ?, salario = ?, estudios = ? where id_empleado = ?")) {
            $consult->bind_param(
                "sssissisi",
                $this->rfc,
                $this->nombre,
                $this->direccion,
                $this->telefono,
                $this->correo,
                $this->puesto,
                $this->salario,
                $this->estudios,
                $this->id_empleado
            );

            $consult->execute();
            $consult->close();
        }
    }
    public static function consultaEmpleadoId($id)
    {

        $emple = null;
        $consult = self::$bd->prepare("select * from empleados where id_empleado = ? AND estado = 1");
        $consult->bind_param("i", $id);
        $consult->execute();
        $consult->bind_result(
            $id,
            $rfc,
            $nombre,
            $direccion,
            $telefono,
            $correo,
            $puesto,
            $salario,
            $estudios,
            $estado
        );
        if ($consult->fetch()) {
            $emple = new Empleados(
                $id,
                $rfc,
                $nombre,
                $direccion,
                $telefono,
                $correo,
                $puesto,
                $salario,
                $estudios
            );
        }
        return $emple;
    }

    public function Eliminar()
    {
        if ($consult = self::$bd->prepare("update empleados set estado = 0 where id_empleado=?")) {
            $consult->bind_param("i", $this->id_empleado);
            $consult->execute();
            $consult->close();
        }
    }

    public static function totalPaginas($contenido)
    {
        $totalFilas = 0;
        $consult = self::$bd->prepare("SELECT COUNT(id_empleado) FROM empleados WHERE estado = 1");
        $consult->execute();
        $consult->bind_result($totalFilas);
        $consult->fetch();
        $consult->close();

        $totalPagina = ceil($totalFilas / $contenido);

        return $totalPagina;
    }
}

/* 
if (isset($argc) && $argc == 2) {
    $mysqli = new mysqli("localhost", "root", "", "agenciaBD");
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
        case 'modificar':
            #Empleados::init($mysqli);
            $emple = Empleados::consultaEmpleadoId(1);
            print_r($emple);
            $emple->rfc = "RFC1234";
            $emple->nombre = "josue";
            $emple->update();
            print_r($emple);
            break;
        case 'Eliminar':
            Empleados::consultaEmpleadoId($mysqli, 1);
            $empledos = Empleados::consultaEmpleadoId(1);
            print_r($empledos);
            $empledos->Eliminar($mysqli);
            print("Registro estado");
            break;
    }
}
*/