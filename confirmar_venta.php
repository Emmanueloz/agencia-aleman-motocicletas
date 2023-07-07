<?php

#print_r($_POST);
require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/DetallesVentas.php';
require_once 'models/Ventas.php';

$mysqli = new mysqli($servidor, $usuario, $password, $bd);


Ventas::init($mysqli);
Empleados::init($mysqli);
Clientes::init($mysqli);
Productos::init($mysqli);



if (isset($_POST["accion"]) && $_POST["accion"] == "agregar") {

    $idEmpleado = $_POST['empleado'];
    $idCliente = $_POST['cliente'];
    $idProductos = $_POST['productos'];
    $fechaVenta = $_POST['fecha-venta'];
    $subtotal = $_POST['subtotal'];
    $iva = $_POST['iva'];
    $cantidades = $_POST['cantidades'];
    $costo = $_POST['costo'];
    $venta = new Ventas(0, $subtotal, $iva, $idEmpleado, $idCliente, $fechaVenta, $idProductos, $cantidades, $costo);
    $venta->agregarVenta();
    header('Location: consultar_ventas.php');
} else {
    $idEmpleado = $_POST["empleado"];
    $nombreEmpleado =  Empleados::id_emple($idEmpleado);
    $idCliente = $_POST["cliente"];;
    $nombreCliente = Clientes::buscarnom($idCliente);

    $idProductos = $_POST['productos'];
    $cantidades = isset($_POST['cantidades']) ? $_POST['cantidades'] : [];

    $html = new SpynTPL('views/');
    $html->Fichero('confirmar_ventas.html');

    $fechaVenta = $_POST["fecha-venta"];

    if (isset($_POST["accion"]) && $_POST["accion"] == "procesar") {
        $html->Asigna('accion', 'agregar');
        $subtotal = DetallesVentas::obtenerSubtotal($idProductos, $cantidades);
        $iva = Ventas::generarIva($subtotal);
        $costo = DetallesVentas::calcularCosto($subtotal, $iva);
        # valores del formulario
        $html->Asigna("subtotal", $subtotal);
        $html->Asigna("iva", $iva);

        $html->Asigna("costo", $costo);

        $botonModificar = '<button class="btn btn-warning m-1" type="submit" name="accion"
        value="modificar">Modificar</button>';

        $html->Asigna('boton_modificar', $botonModificar);
        $html->Asigna('accion', 'agregar');
        # valores de estilo
        $html->Asigna('ocultar', '');
        $html->Asigna('solo_lectura', 'readonly');
        $html->Asigna('solo_lectura_estilo', 'form-control-plaintext');
    } else {
        # valores del formulario
        $html->Asigna("iva", null);
        $html->Asigna("subtotal", null);
        $html->Asigna("costo", null);
        $html->Asigna('accion', 'procesar');

        if (isset($_POST["accion"]) && $_POST["accion"] == "modificar") {
            # code...
        }
        $botonModificar = '<a href="./agregar_venta.php" class="btn btn-warning m-1"> Modificar</a>';
        $html->Asigna('boton_modificar', $botonModificar);

        # valores de estilo
        $html->Asigna('ocultar', 'd-none');
        $html->Asigna('solo_lectura', '');
        $html->Asigna('solo_lectura_estilo', 'form-control');
    }
    # valores del formulario
    $html->Asigna("id_empleado", $idEmpleado);
    $html->Asigna("nombre_empleado", $nombreEmpleado);
    $html->Asigna("id_cliente", $idCliente);
    $html->Asigna("nombre_cliente", $nombreCliente);

    $html->Asigna("fecha-venta", $fechaVenta);

    $productos = [];

    foreach ($idProductos as $idProducto) {
        $productoNom = Productos::consultPrecioMarcaModelo($idProducto);

        array_push($productos, $productoNom);
    }

    for ($i = 0; $i < count($productos); $i++) {
        $producto = $productos[$i];
        $html->AsignaBloque('productos', $producto);
    }

    for ($i = 0; $i < count($productos); $i++) {
        $producto = $productos[$i];
        if (count($cantidades) == 0) {
            $cantidad['valor'] = 1;

            $html->AsignaBloque('cantidades', $cantidad);
        } else {
            $cantidad['valor'] = $cantidades[$i];
            $html->AsignaBloque('cantidades', $cantidad);
        }
    }



    echo $html->Muestra();
}
