<?php
require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/Empleados.php';

$html = new SpynTPL('views/');
$html->Fichero('empleados.html');
$html->Asigna('title', 'lista de empleados');
$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Empleados::init($mysqli,);

$emples = Empleados::consul();
foreach ($emples as $Empleados){
    $html->AsignaBloque('emplea2', $Empleados);
}
echo $html->Muestra();
