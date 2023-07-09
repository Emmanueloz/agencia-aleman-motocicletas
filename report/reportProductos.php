<?php
require('../fpdf/fpdf.php');
require('../models/config.php');
require_once '../models/Productos.php';

$mysqli = new mysqli($servidor, $usuario, $password, $bd);

Productos::init($mysqli);

date_default_timezone_set('America/Mexico_City');
$fecha = date("Y-m-d");

$pdf = new FPDF();
$pdf->AddPage('H');
$pdf->SetFont('Arial', 'B', 16);

$pdf->Text(120, 12, "Reporte: Productos");
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(20, 5, "Fecha del reporte: $fecha");
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(20, 5, "ID", 1, 0, 'C');
$pdf->Cell(45, 5, "Numero de Serie", 1, 0, 'C');
$pdf->Cell(50, 5, "Marca", 1, 0, 'C');
$pdf->Cell(45, 5, "Modelo", 1, 0, 'C');
$pdf->Cell(50, 5, "Descripcion", 1, 0, 'C');
$pdf->Cell(30, 5, "Existencias", 1, 0, 'C');
$pdf->Cell(30, 5, "Precio", 1, 0, 'C');
$pdf->Ln();


if (isset($_GET['opcion']) && !empty(trim($_GET['value']))) {
    $opcion = $_GET['opcion'];
    $value = $_GET['value'];
$productoArray=Productos::productoFiltrado($opcion, $value);

    
} elseif (isset($_GET['producto'])) {
    $id=$_GET['producto'];
    $productoArray=Productos::productoFiltrado('id',$id);
  
} else {
    $productoArray=Productos::findAll();

}


$pdf->SetFont('Arial', '', 10);

$row = 0;
foreach ($productoArray as $productos) {
    $row += 1;
    if ($row > 10) {
        $pdf->AddPage('H');
        $row = 0;
    }
    $pdf->Cell(20,10, $productos->id_producto, "B", 0, 'C');
    $pdf->Cell(45,10, utf8_decode($productos->numero_serie), "B", 0, 'L');
    $pdf->Cell(50,10, utf8_decode($productos->marca), "B", 0, 'L');
    $pdf->Cell(45,10, utf8_decode($productos->modelo), "B", 0, 'L');
    $pdf->Cell(50,10, utf8_decode($productos->descripcion), "B", 0, 'L');
    $pdf->Cell(30,10, utf8_decode($productos->existencias), "B", 0, 'C');
    $pdf->Cell(30,10,'$'. $productos->precio, "B", 0, 'R');

    $pdf->Ln();
}
$pdf->Output();
