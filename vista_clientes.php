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
$html->Asigna('limpiar_filtro', '');

$mysqli = new mysqli($servidor, $usuario, $password, $bd);
Clientes::init($mysqli);

if(isset($_GET['value']) && !empty(trim($_GET['value'])))
{
    $opcion = $_GET['opcion'];
    $value = $_GET['value'];

    $buttonFiltro = '<a href="./vista_clientes.php" type="button" class="btn btn-outline-secondary">
    <svg
    xmlns="http://www.w3.org/2000/svg"
    width="16"
    height="16"
    fill="currentColor"
    class="bi bi-x-circle"
    viewBox="0 0 16 16"
    >
    <path
        d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"
    ></path>
    <path
        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"
    ></path>
    </svg>
    </a>';

    $html->Asigna('link_report', "reportClientes.php?opcion=$opcion&value=$value");
    $html->Asigna('reporte', 'Reporte de consulta');
    $html->Asigna('limpiar_filtro', $buttonFiltro);

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