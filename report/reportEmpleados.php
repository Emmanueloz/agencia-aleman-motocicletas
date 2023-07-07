<?php
require('../fpdf/fpdf.php');

$pdf= new FPDF();
$pdf->AddPage();
$pdf->SetFont('Times');

if (isset($_GET['value']) && !empty(trim($_GET['value'])))
{
    $pdf->Cell(180,1, utf8_decode('Reporte de la consulta'),0,0,'C');
}
elseif(isset($_GET['id_empleado']))
{
    $pdf->Cell(180,1, utf8_decode('Reporte del empleado'),0,0,'C');
}
else{
    $pdf->Cell(180,1, utf8_decode('Reporte General'),0,0,'C');
}

$pdf->Output();