<?php

require('../fpdf/fpdf.php');

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Times');

if(isset($_GET['value']) && !empty(trim($_GET['value'])))
{
    $pdf->Cell(180, 1, utf8_decode('Reporte de Consulta'), 0, 0, 'C');
}
elseif(isset($_GET['id_cliente']))
{
    $pdf->Cell(180, 1, utf8_decode('Reporte de Cliente'), 0, 0, 'C');
}
else
{
    $pdf->Cell(180, 1, utf8_decode('Reporte General'), 0, 0, 'C');
}

$pdf->Output();