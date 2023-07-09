<?php
require('../fpdf/fpdf.php');
require_once '../models/config.php';
require_once '../models/Empleados.php';

$mysqli = new mysqli($servidor, $usuario, $password, $bd);

Empleados::init($mysqli);

date_default_timezone_set('America/Mexico_City');
$fecha = date("Y-m-d");

$pdf = new FPDF();
$pdf->AddPage('H');
$pdf->SetFont('Arial', 'B', 16);
#$pdf->Cell(250, 1, "Reporte: Empleados", 0, 1, 'C');
$pdf->Text(120, 12, "Reporte: Empleados");
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(20, 5, "Fecha del reporte: $fecha");
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 5, "ID", 1, 0, 'C');
$pdf->Cell(20, 5, "rfc", 1, 0, 'C');
$pdf->Cell(40, 5, "nombre", 1, 0, 'C');
$pdf->Cell(40, 5, "direccion", 1, 0, 'C');
$pdf->Cell(30, 5, "telefono", 1, 0, 'C');
$pdf->Cell(50, 5, "correo", 1, 0, 'C');
$pdf->Cell(24, 5, "puesto", 1, 0, 'C');
$pdf->Cell(24, 5, "salirio", 1, 0, 'C');
$pdf->Cell(24, 5, "estudios", 1, 0, 'C');
$pdf->Ln();


if (isset($_GET['opcion']) && !empty(trim($_GET['value']))) {
    $opcion = $_GET['opcion'];
    $value = $_GET['value'];
    $empleadosArray = Empleados::filtro($opcion, $value);

} elseif (isset($_GET['id_empleado'])) {
    $id=$_GET['id_empleado'];
    $empleadosArray= Empleados::filtro('id',$id);
    
} else {
    $empleadosArray= Empleados::consul();
}

$pdf->SetFont('Arial', '', 10);

$row = 0;
foreach ($empleadosArray as $empleados) {
    $row += 1;
    if ($row > 6) {
        $pdf->AddPage('H');
        $row = 0;    
    }

    $pdf->Cell(30, 5, $empleados->id_empleado, "B", 0, 'C');
    $pdf->Cell(20, 5, $empleados->rfc, "B", 0, 'L');
    $pdf->Cell(40, 5, utf8_decode($empleados->nombre), "B", 0, 'L');
    $pdf->Cell(40, 5, utf8_decode($empleados->direccion), "B", 0, 'L');
    $pdf->Cell(30, 5, $empleados->telefono, "B", 0, 'C');
    $pdf->Cell(50, 5, utf8_decode($empleados->correo), "B", 0, 'L');
    $pdf->Cell(24, 5, utf8_decode($empleados->puesto), "B", 0, 'L');
    $pdf->Cell(24, 5, $empleados->salario, "B", 0, 'C');
    $pdf->Cell(24, 5, utf8_decode($empleados->estudios), "B", 0, 'L');

    $pdf->Ln();
}
$pdf->Output();
