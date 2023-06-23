<?php

require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/Clientes.php';

$html = new SpynTPL('views/');
$html->Fichero('clientes.html');
$html->Asigna('titulo', 'Lista de Clientes');

$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Clientes::init($mysqli);

$clientes = Clientes::consulta();

foreach($clientes as $cliente)
{
    $html->AsignaBloque('general', $cliente);
}
echo $html->Muestra();