<?php

#print_r($_POST);
require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/DetallesVentas.php';
require_once 'models/Ventas.php';

$mysqli = new mysqli($servidor, $usuario, $password, $bd);


Ventas::init($mysqli);
DetallesVentas::init($mysqli);
Empleados::init($mysqli);
Clientes::init($mysqli);
Productos::init($mysqli);



if (isset($_POST["accion"]) && $_POST["accion"] == "agregar") {
    print_r($_POST);
} else {
    $idEmpleado = $_POST["empleado"];
    $nombreEmpleado =  Empleados::id_emple($idEmpleado);
    $idCliente = $_POST["cliente"];;
    $nombreCliente = Clientes::buscarnom($idCliente);


    $idProductos = $_POST["productos"];
    $cantidad = count($idProductos);

    $subtotal = DetallesVentas::obtenerSubtotal($idProductos);
    $iva = Ventas::generarIva($subtotal);
    $costo = DetallesVentas::calcularCosto($subtotal, $iva);

    $idVenta = Ventas::obtenerIdVenta();

    $html = new SpynTPL('views/');
    $html->Fichero('confirmar_ventas.html');

    $fechaVenta = $_POST["fecha-venta"];

    $html->Asigna("id_empleado", $idEmpleado);
    $html->Asigna("nombre_empleado", $nombreEmpleado);
    $html->Asigna("id_cliente", $idCliente);
    $html->Asigna("nombre_cliente", $nombreCliente);

    $html->Asigna("fecha-venta", $fechaVenta);
    $html->Asigna("iva", $iva);
    $html->Asigna("subtotal", $subtotal);
    $html->Asigna("costo", $costo);


    foreach ($idProductos as $idProducto) {
        $html->AsignaBloque("productos", $idProducto);
    }
    echo $html->Muestra();
}
