<?php

// Comprobar si hay una sesión iniciada
session_start();
require_once '../models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
}


require('../models/PDF.php');
require('../models/config.php');
require_once '../models/Productos.php';

$mysqli = new mysqli($servidor, $usuario, $password, $bd);

Productos::init($mysqli);

date_default_timezone_set('America/Mexico_City');
$fecha = date("Y-m-d");

$tipo = isset($_GET['opcion']) ? 'Consulta' : (isset($_GET['Producos']) ? 'Individual' : 'General');

$pdf = new PDF('L');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(272, 12, 'Reporte de Productos', 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(136, 10, "Fecha del reporte: $fecha", 1, 0);
$pdf->Cell(136, 10, "Tipo de reporte: $tipo", 1, 1);
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(52, 5, "Numero de Serie", 1, 0, 'C');
$pdf->Cell(70, 5, "Marca", 1, 0, 'C');
$pdf->Cell(70, 5, "Modelo", 1, 0, 'C');
$pdf->Cell(40, 5, "Existencias", 1, 0, 'C');
$pdf->Cell(40, 5, "Precio", 1, 0, 'C');
$pdf->Ln();


if (isset($_GET['opcion']) && !empty(trim($_GET['value']))) {
    $opcion = $_GET['opcion'];
    $value = $_GET['value'];
    $productoArray = Productos::productoFiltrado($opcion, $value);
} else {
    $productoArray = Productos::consultaProductos();
}

$pdf->SetFont('Arial', '', 10);

$row = 0;
foreach ($productoArray as $productos) {
    $row += 1;
    if ($row > 10) {
        $pdf->AddPage();
        $row = 0;
    }
    $pdf->Cell(52, 10, utf8_decode($productos->numero_serie), "B", 0, 'L');
    $pdf->Cell(70, 10, utf8_decode($productos->marca), "B", 0, 'L');
    $pdf->Cell(70, 10, utf8_decode($productos->modelo), "B", 0, 'L');
    $pdf->Cell(40, 10, utf8_decode($productos->existencias), "B", 0, 'L');
    $pdf->Cell(40, 10, '$' . $productos->precio, "B", 0, 'R');

    $pdf->Ln();
}
$pdf->Output('', "Reporte-Productos-$fecha-$tipo");
