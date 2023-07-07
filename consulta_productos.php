<?php
require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/Productos.php';
$html = new SpynTPL('views/');
$html->Fichero('productos.html');
$html->Asigna('mens','');
$html->Asigna('id_s', '');
$html->Asigna('modelo_s', '');
$html->Asigna('marca_s','');
$html->Asigna('precio_s','');
$html->Asigna('value','');
$html->Asigna('title', 'Lista de productos');

$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Productos::init($mysqli);
if(isset($_GET['value']) && !empty(trim($_GET['value'])))
{
    $opcion = $_GET['opcion'];
    $value = $_GET['value'];
    switch ($opcion) {
        case 'id':
            $html->Asigna('id_s', 'selected');
            break;
        case 'modelo':
            $html->Asigna('modelo_s', 'selected');
            break;
        case 'marca':
            $html->Asigna('marca_s', 'selected');
            break;
        case 'precio';
            $html->Asigna('precio_s', 'selected');
            break;
    }
    $productos = Productos::productoFiltrado($opcion, $value);
    if(count($productos) == 0)
    {
        $html->AsignaBloque('productos', null);
    }
        $html->Asigna('link_report',"reportProductos.php?opcion=$opcion&value=$value" );
        $html->Asigna('reporte',"Reporte de consulta");
        $html->Asigna('value', $value);
}
else
{
    $html->Asigna('link_report',"reportProductos.php" );
    $html->Asigna('reporte',"Reporte general");
    $productos = Productos::findAll();
}

foreach($productos as $producto)
{
 $html->AsignaBloque('productos',$producto);
}
echo $html->Muestra();