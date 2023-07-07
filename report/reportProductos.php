<?php
require('../fpdf/fpdf.php');
require('../models/config.php');
$pdf = new FPDF();
$pdf->AddPage('H');
$pdf->SetFont('times');


if(isset($_GET['opcion']) && !empty(trim($_GET['value'])))
{
    $pdf->Cell(180,1,utf8_decode("reporte de consulta"),0,0,'C');
}
elseif(isset($_GET['producto'])){
    $pdf->Cell(180,1,utf8_decode('reporte de un producto'),0,0,'C');
}
else {
    $pdf->Cell(180,1,utf8_decode('reporte general'),0,0,'C');
}

$pdf->Output();
