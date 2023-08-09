<?php

/**
 *  Permite crear los objetos, llamar métodos relacionados al modulo de productos
 */
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


    /**
     * Se pasa los datos necesarios para crear un objeto de ventas
     * @param int $idproducto
     * @param int $numero_serie 
     * @param string $marca
     * @param string $descripcion
     * @param string $modelo
     * @param double $precio
     * @param int $existencias
     */
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


    /**
     * es la conexion de la clase con la base de datos
     */
    public static function init($bd)
    {
        self::$bd = $bd;
    }
    /** 
     * @param int $estado 
     * hace la funcion de dejar ver o ocultar los datos en la aplicacion como si se hubiera eliminado
     */
    public static function totalPaginas($contenido)
    {
        $totalFilas = 0;
        $consulta = self::$bd->prepare("SELECT COUNT(id_producto) FROM productos WHERE estado = 1");
        $consulta->execute();
        $consulta->bind_result($totalFilas);
        $consulta->fetch();
        $consulta->close();

        $totalPaginas = ceil($totalFilas / $contenido);

        return $totalPaginas;
    }

    public function save()
    {
        if ($consult = self::$bd->prepare("insert into productos values (0,?,?,?,?,?,?,1)")) {
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

/**
     * Consulta los productos en la base de datos.
     *
     * @param int $pagina La página actual.
     * @param int $contenido La cantidad de contenido por página.
     * @return array Un array con los productos consultados.
     */

    public static function consultaProductos($pagina = null, $contenido = null)
    {
        $pagina = ($pagina - 1) * $contenido;
        $products = [];
        $id_producto = [];
        $numero_serie = [];
        $marca = [];
        $descripcion = [];
        $modelo = [];
        $precio = [];
        $existencias = [];
        $estado = 1;

        if (!is_null($pagina) && !is_null($contenido)) {
            $consult = self::$bd->prepare("SELECT * FROM productos WHERE estado = 1 LIMIT ?,?");
            $consult->bind_param('ii', $pagina, $contenido);
        } else {
            $consult = self::$bd->prepare("select * from productos WHERE estado = 1");
        }
        $consult->execute();
        $consult->bind_result($id_producto, $numero_serie, $marca, $descripcion, $modelo, $precio, $existencias, $estado);

        while ($consult->fetch()) {
            array_push($products, new Productos($id_producto, $numero_serie, $marca, $descripcion, $modelo, $precio, $existencias));
        }
        $consult->close();
        return $products;
    }
    public static function cosultMarcaModelo($valor)
    {
        $id = [];
        $id_producto = 0;
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
        $id = 0;
        $precio = '';
        $marca = '';
        $modelo = '';

        $producto = [];
        $consult = self::$bd->prepare("select id_producto, marca, modelo, precio from productos where id_producto = ?");
        $consult->bind_param('i', $id_producto);
        $consult->execute();
        $consult->bind_result($id, $precio, $marca, $modelo);
        $consult->fetch();

        array_push($producto, $id, $precio, $marca, $modelo);
        $consult->close();
        return ($producto);
    }
    public static function productoFiltrado($filtro, $value)
    {
        switch ($filtro) {
            case 'id':
                $consult = self::$bd->prepare("select * from productos where id_producto = ? AND estado = 1");
                $consult->bind_param("i", $value);
                break;
            case 'modelo':
                $consult = self::$bd->prepare("select * from productos where modelo like ? AND estado = 1");
                $value = $value . '%';
                $consult->bind_param("s", $value);
                break;
            case 'marca':
                $consult = self::$bd->prepare("select * from productos where marca like ? AND estado = 1");
                $value = $value . '%';
                $consult->bind_param("s", $value);
                break;
            case 'precio';
                $consult = self::$bd->prepare("select * from productos where precio = ? AND estado = 1");
                $consult->bind_param("d", $value);
                break;
        }
        $producto = [];
        $products = [];
        $id_producto = [];
        $numero_serie = [];
        $marca = [];
        $descripcion = [];
        $modelo = [];
        $precio = [];
        $existencias = [];
        $estado = 0;

        $consult->execute();
        $consult->bind_result($id_producto, $numero_serie, $marca, $descripcion, $modelo, $precio, $existencias, $estado);
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
        $id_producto = [];
        $numero_serie = [];
        $marca = [];
        $descripcion = [];
        $modelo = [];
        $precio = [];
        $existencias = [];
        $estado = 1;

        $consult = self::$bd->prepare("select * from productos where id_producto = ? AND estado = 1");
        $consult->bind_param("i", $id);
        $consult->execute();
        $consult->bind_result($id_producto, $numero_serie, $marca, $descripcion, $modelo, $precio, $existencias, $estado);
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
            return $producto;
        }
    }

    public function eliminarProducto()
    { #update clientes set estado = 0 where id_cliente = ?
        if ($consult = self::$bd->prepare("UPDATE productos set estado = 0 where id_producto = ?")) {
            $consult->bind_param("i", $this->id_producto);
            $consult->execute();
            $consult->close();
        }
    }

    public static function actualizarProductos($ids, $cantidades)
    {
        $existencia = 0;
        self::$bd->begin_transaction();

        try {
            for ($i = 0; $i < count($ids); $i++) {
                $consulta = self::$bd->prepare('SELECT existencias FROM productos WHERE id_producto = ?');
                $consulta->bind_param('i', $ids[$i]);
                $consulta->execute();

                $consulta->bind_result($existencia);
                $consulta->fetch();
                $consulta->free_result();

                if ($cantidades[$i] > $existencia) {
                    throw new Exception($ids[$i]);
                }

                $consulta = self::$bd->prepare('UPDATE productos SET existencias = existencias - ? WHERE id_producto = ?');
                $consulta->bind_param('ii', $cantidades[$i], $ids[$i]);
                $consulta->execute();
                $consulta->close();
            }

            self::$bd->commit();
        } catch (Exception $e) {
            self::$bd->rollback();
            throw $e;
        }
    }
}
/* if (isset($argc) && $argc == 2) {
    $mysqli = new mysqli("localhost", "root", "", "agenciaBD");
    Productos::init($mysqli);
    switch ($argv[1]) {
        case 'todos':
            $products = Productos::consultaProductos();
            print_r($products);
            break;
        case 'id':
            #$id = Productos::cosultMarcaModelo('2');
            $producto = Productos::consultaProductoId(1);
            print_r($producto);
            break;
        case 'consulta':
            $producto = Productos::consultPrecioMarcaModelo(1);
            print_r($producto);
            break;
        }
}
 */