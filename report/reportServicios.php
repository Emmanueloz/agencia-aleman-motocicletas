<?php
// Comprobar si hay una sesión iniciada
session_start();
require_once '../models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
}

require('../models/PDF.php');
require_once '../models/config.php';
require_once '../models/Servicios.php';

$mysqli = new mysqli($servidor, $usuario, $password, $bd);

// Iniciando parámetros
Servicios::init($mysqli);

// Configurando fecha
date_default_timezone_set('America/Mexico_City');
$fecha = date("d-m-Y");

$tipo = isset($_GET['opcion']) ? 'Consulta' : (isset($_GET['venta']) ? 'Individual' : 'General');

$pdf = new PDF('L');
$pdf->AliasNbPages();
$pdf->AddPage();
// Titulo
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(272, 12, 'Reporte de Servicios', 0, 1, 'C');
$pdf->Ln(5);

// Datos del reporte
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(136, 10, "Fecha del reporte: $fecha", 1, 0);
$pdf->Cell(136, 10, "Tipo de reporte: $tipo", 1, 1);
$pdf->Ln(10);

// Tabla del reporte
// Encabezado
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(20, 5, "ID", 1, 0, 'C');
$pdf->Cell(76, 5, "Cliente", 1, 0, 'C');
$pdf->Cell(32, 5, "Fecha", 1, 0, 'C');
$pdf->Cell(80, 5, "Productos", 1, 0, 'C');
$pdf->Cell(64, 5, "Servicio", 1, 0, 'C');

$pdf->Ln();

// Contenido de la tabla

if (isset($_GET['opcion']) && isset($_GET['search'])) {
    $opcion = $_GET['opcion'];
    $search = $_GET['search'];
    $serviciosArray = Servicios::consultaFiltrada($opcion, $search);
} elseif (isset($_GET['venta'])) {
    $id = $_GET['venta'];
    $serviciosArray = Servicios::consultaFiltrada('id', $id);
} else {
    $serviciosArray = Servicios::consultarServicios();
}

$pdf->SetFont('Arial', '', 10);

$row = 0;
/**
 * @var Servicios $servicio
 */
foreach ($serviciosArray as $servicio) {

    $row += 1;
    if ($row > 6) {
        $pdf->AddPage();
        $row = 0;
    }

    $listProductos = explode("<br/>", $servicio->productos);
    $listaServicios = explode("<br/>", $servicio->tipoServicios);

    $productos = "";

    for ($i = 0; $i < count($listProductos); $i++) {
        if (($i + 1) < count($listProductos)) {
            $productos .= "$listProductos[$i]\n";
        } else {
            $productos .= "$listProductos[$i]";
        }
    }

    $numLineas = substr_count($productos, "\n");
    $altura = $numLineas == 0 ? 10 : 10 * $numLineas;

    $y_axis = $pdf->GetY();

    $pdf->Cell(20, $altura, $servicio->idServicio, "B", 0, 'C');

    $pdf->Cell(76, $altura, utf8_decode($servicio->idCliente), "B", 0, 'L');

    $pdf->Cell(32, $altura, "$" . $servicio->fechaServicio, "B", 0, 'R');

    $pdf->MultiCell(80, $altura, $productos, "B", 'L');
    $pdf->SetXY(218, $y_axis);

    $tipoServicios = "";

    for ($i = 0; $i < count($listaServicios); $i++) {
        if (($i + 1) < count($listaServicios)) {
            $tipoServicios .= "$listaServicios[$i]\n";
        } else {
            $tipoServicios .= "$listaServicios[$i]";
        }
    }

    $pdf->MultiCell(64, 10, $tipoServicios, "B", 'L');

    $pdf->Ln();
}

$pdf->Output('', "Reporte-Servicios-$fecha-$tipo"); 
#print_r($ventasArray);
