<?php

require_once 'SpynTPL.php';
require_once 'models/config.php';
require_once 'models/Clientes.php';

$msg = '';
if(isset($_GET['msg']))
{
    $contenido = $_GET['msg'];
    $msg = "<div class='alert alert-success alert-dismissible fade show'
    role='alert'><button type='button' class='btn-close'
    data-bs-dismiss='alert' aria-label='Close'></button>$contenido</div>";
}

$html = new SpynTPL('views/');
$html->Fichero('clientes.html');
$html->Asigna('msg', $msg);
$html->Asigna('titulo', 'Lista de Clientes');
$html->Asigna('idcli_s', '');
$html->Asigna('nomcli_s', '');
$html->Asigna('rfccli_s', '');
$html->Asigna('value', '');

$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Clientes::init($mysqli);

if(isset($_GET['value']) && !empty(trim($_GET['value'])))
{
    $opcion = $_GET['opcion'];
    $value = $_GET['value'];
    $html->Asigna('link_report', "reportClientes.php?opcion=$opcion&value=$value");
    $html->Asigna('reporte', 'Reporte de consulta');

    switch($opcion)
    {
        case 'idcli':
            $html->Asigna('idcli_s', 'selected');
            break;
        case 'nomcli':
            $html->Asigna('nomcli_s', 'selected');
            break;
        case 'rfccli':
            $html->Asigna('rfccli_s', 'selected');
            break;
    }
    $clientes = Clientes::busquedafil($opcion, $value);
    if(count($clientes) == 0)
    {
        $html->AsignaBloque('general', null);
    }
    $html->Asigna('value', $value);
}
else
{
    $clientes = Clientes::consulta();
    $html->Asigna('link_report', "reportClientes.php");
    $html->Asigna('reporte', 'Reporte general');
}

foreach($clientes as $cliente)
{
    $html->AsignaBloque('general', $cliente);
}
echo $html->Muestra();