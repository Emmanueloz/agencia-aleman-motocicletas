<?php
require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/Productos.php';
$html = new SpynTPL('views/');
$html->Fichero('productos.html');
$html->Asigna('mens','');
$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Productos::init($mysqli);
$products = Productos::findAll();

foreach($products as $producto)
{
 $html->AsignaBloque('productos',$producto);
}
echo $html->Muestra();