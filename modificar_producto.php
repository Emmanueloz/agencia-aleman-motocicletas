<?php
require_once ('SpynTPL.php');
require_once ('models/config.php');
require_once ('models/Productos.php');
require_once ('models/funciones.php');
$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Productos::init($mysqli);
Productos::init($mysqli, 1);

$title = 'Editar película';
$target = 'modificar_producto.php';
$productos = Productos::findAll();
$html = new SpynTPL('views/');
$html->Fichero('productos.html');
$html->Asigna('title', $title);
$html->Asigna('target', $target);
if(isset($_GET['id']))
{
 $id = $_GET['id'];
 $productos = Productos::findId($id);
 $html->Asigna('idProducto', $productos->id_producto);
 $html->Asigna('numeroSerie', $productos->numero_serie);
 $html->Asigna('marca', $productos->marca);
 $html->Asigna('descripcion', $productos->descripcion);
 $html->Asigna('modelo', $productos->Modelo);
 $html->Asigna('precio', $productos->precio);
 foreach($productos as $producto)
 {
 if($producto->id == $productos->idGenero)
 {
 $producto->selected = 'selected';
 }
 else
 {
 $producto->selected = '';
 }
 }
}
echo $html->Muestra();