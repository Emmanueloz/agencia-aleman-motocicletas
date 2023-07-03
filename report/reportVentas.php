<?php
require('../fpdf/fpdf.php');
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

if (count($_GET) != 0) {

    if (isset($_GET['opcion']) && isset($_GET['search'])) {
        $opcion = $_GET['opcion'];
        $search = $_GET['search'];
        $pdf->Cell(100, 10, "En proceso: reporte filtrado", 0, 1);
        $pdf->Cell(100, 10, utf8_decode("Opción: $opcion"), 0, 1);
        $pdf->Cell(100, 10, utf8_decode("Búsqueda: $search"), 0, 1);
    } elseif (isset($_GET['venta'])) {
        $idVenta = $_GET['venta'];
        $pdf->Cell(100, 10, "En proceso: reporte individual", 0, 1);
        $pdf->Cell(100, 10, "Venta con el id: $idVenta", 0, 1);
    }
} else {
    $pdf->Cell(100, 10, "En proceso: general", 0, 1);
}

$pdf->Output('', 'Reporte de ventas', true);
