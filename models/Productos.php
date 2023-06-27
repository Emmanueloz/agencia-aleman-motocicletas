<?php

class Productos
{
    public $id_producto;
    public $numero_serie;
    public $marca;
    public $descripcion;
    public $modelo;
    public $precio;
    public $existencias;

    private static $bd;

    public function __construct($id_producto, $numero_serie, $marca, $descripcion, $modelo, $precio, $existencias)
    {
        $this->id_producto = $id_producto;
        $this->numero_serie = $numero_serie;
        $this->marca = $marca;
        $this->descripcion = $descripcion;
        $this->modelo = $modelo;
        $this->precio = $precio;
        $this->existencias = $existencias;
    }
    public static function init($bd)
    {
        self::$bd = $bd;
    }
    public static function findAll()
    {
        $products = [];

        $consult = self::$bd->prepare("select * from productos");
        $consult->execute();
        $consult->bind_result($id_producto, $numero_serie, $marca, $descripcion, $modelo, $precio, $existencias);

        while ($consult->fetch()) {
            array_push($products, new Productos($id_producto, $numero_serie, $marca, $descripcion, $modelo, $precio, $existencias));
        }
        $consult->close();
        return $products;
    }
    public static function cosultMarcaModelo($valor)
    {
        $id = [];
        $valor = "%" . $valor . "%";
        $consult = self::$bd->prepare("select id_producto from productos where (marca like ? or modelo like ?)");
        $consult->bind_param('ss', $valor, $valor);
        $consult->execute();
        $consult->bind_result($id_producto);
        while ($consult->fetch()) {
            array_push($id, $id_producto);
        }
        $consult->close();
        return $id;
    }
    public static function consultPrecioMarcaModelo($id_producto)
    {
        $producto = [];
        $consult = self::$bd->prepare("select marca, modelo, precio from productos where id_producto = ?");
        $consult->bind_param('i', $id_producto);
        $consult->execute();
        $consult->bind_result($precio, $marca, $modelo);
        $consult->fetch();

        array_push($producto, $precio, $marca, $modelo);
        $consult->close();
        return ($producto);
    }
}
/* if (isset($argc) && $argc == 2) {
    $mysqli = new mysqli("localhost", "root", "", "agenciaBD");
    Productos::init($mysqli);
    switch ($argv[1]) {
        case 'todos':
            $products = Productos::findAll();
            print_r($products);
            break;
        case 'id':
            $id = Productos::cosultMarcaModelo('2');
            print_r($id);
            break;
    }
}
 */