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
    public function save()
    {
        if ($consult = self::$bd->prepare("insert into productos values (0,?,?,?,?,?,?)")) {
            $consult->bind_param(
                "ssssdi",
                $this->numero_serie,
                $this->marca,
                $this->descripcion,
                $this->modelo,
                $this->precio,
                $this->existencias
            );
            $consult->execute();
            $consult->close();
        }
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
    public static function productoFiltrado($filtro, $value)
    {
        switch ($filtro) {
            case 'id':
                $consult = self::$bd->prepare("select * from productos where id_producto = ?");
                $consult->bind_param("i", $value);
                break;
            case 'modelo':
                $consult = self::$bd->prepare("select * from productos where modelo like ?");
                $value = $value . '%';
                $consult->bind_param("s", $value);
                break;
            case 'marca':
                $consult = self::$bd->prepare("select * from productos where marca like ?");
                $value = $value . '%';
                $consult->bind_param("s", $value);
                break;
            case 'precio';
                $consult = self::$bd->prepare("select * from productos where precio = ?");
                $consult->bind_param("d", $value);
                break;
        }
        $producto = [];
        $consult->execute();
        $consult->bind_result($id_producto, $numero_serie, $marca, $descripcion, $modelo, $precio, $existencias);
        while ($consult->fetch()) {
            array_push($producto,  new Productos($id_producto, $numero_serie, $marca, $descripcion, $modelo, $precio, $existencias));
        }
        $consult->close();
        return $producto;
    }
    public function modificar()
    {
        if ($consult = self::$bd->prepare("update productos set numero_serie = ?, marca = ?, descripcion = ?, modelo = ?, precio = ?, existencias = ? where id_producto = ?")) {
            $consult->bind_param(
                "ssssdii",
                $this->numero_serie,
                $this->marca,
                $this->descripcion,
                $this->modelo,
                $this->precio,
                $this->existencias,
                $this->id_producto
            );
            $consult->execute();
            $consult->close();
        }
    }
    public static function consultaProductoId($id)
    {
        $consult = null;
        $consult = self::$bd->prepare("select * from productos where id_producto = ?");
        $consult->bind_param("i", $id);
        $consult->execute();
        $consult->bind_result($id_producto, $numero_serie, $marca, $descripcion, $modelo, $precio, $existencias);
        if ($consult->fetch()) {
            $producto = new Productos(
                $id_producto,
                $numero_serie,
                $marca,
                $descripcion,
                $modelo,
                $precio,
                $existencias
            );
        }
        return $producto;
    }
    public function eliminarProducto()
    {
        if($consult = self::$bd->prepare("delete from productos where id_producto = ?"))
        {
            $consult->bind_param("i", $this->id_producto );
            $consult->execute();
            $consult->close();
        }
    }
}

if (isset($argc) && $argc == 2) {
    $mysqli = new mysqli("localhost", "root", "", "agenciabd");
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
        case 'consulta':
            $producto = Productos::consultPrecioMarcaModelo(1);
            print_r($producto);
            break;
        case 'filtro':
            Productos::init($mysqli);
            $producto = Productos::productoFiltrado('precio', '19.99');
            print_r($producto);
            break;
        case 'nuevo':
            $producto = new Productos(6, 'JPG23', 'Marca 6', 'Rojo', 'Modelo F', 1900, 3);
            $producto->save();
            break;
        case 'modificar':
            $producto = Productos::consultaProductoId(2);
            print_r($producto);
            $producto->numero_serie = "cambio del numero de serie";
            $producto->modelo = "cambio del modelo";
            $producto->modificar();
            print_r($producto);
            break;
        case "eliminar":
            Productos::init($mysqli);
            $producto = Productos::consultaProductoId(9);
            print_r($producto);
            $producto->eliminarProducto($mysqli);
            print("Registro eliminado");
            break;    
    }
}
