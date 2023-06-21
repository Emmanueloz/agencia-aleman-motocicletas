<?php
require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/DetallesVentas.php';
require_once 'models/Ventas.php';

$titulo = "Agregar una nueva venta";
$html = new SpynTPL('views/');
$html->Fichero('agregar_ventas.html');
$html->Asigna('title', $titulo);
$html->Asigna('mensaje', ' ');


echo $html->Muestra();
