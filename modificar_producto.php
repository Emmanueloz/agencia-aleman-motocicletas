<?php
require_once ('SpynTPL.php');
require_once ('models/config.php');
require_once ('models/Productos.php');
$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Productos::init($mysqli);

$title = 'Editar película';
$target = 'modificar_producto.php';
$productos = Productos::findAll();
$html = new SpynTPL('views/');
$html->Fichero('frmProducto.html');
$html->Asigna('title', $title);
$html->Asigna('target', $target);
if(isset($_GET['id']))
{
 $id = $_GET['id'];
 $productos = Productos::findId($id);
 $html->Asigna('id_producto', $productos->id_producto);
 $html->Asigna('numero_serie', $productos->numero_serie);
 $html->Asigna('marca', $productos->marca);
 $html->Asigna('descripcion', $productos->descripcion);
 $html->Asigna('modelo', $productos->modelo);
 $html->Asigna('precio', $productos->precio);
 $html->Asigna('existencias', $productos->existencias);
 
}


echo $html->Muestra();