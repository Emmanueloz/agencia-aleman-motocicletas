<?php

// Comprobar si hay una sesión iniciada
session_start();
require_once '../models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
}
require('../models/PDF.php');
require_once '../models/config.php';
require_once '../models/Empleados.php';

$mysqli = new mysqli($servidor, $usuario, $password, $bd);

Empleados::init($mysqli);

date_default_timezone_set('America/Mexico_City');
$fecha = date("Y-m-d");

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('H');
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(272, 12, 'Reporte de Ventas', 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(20, 5, "Fecha del reporte: $fecha");
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(20, 10, "ID", 1, 0, 'C');
$pdf->Cell(30, 10, "rfc", 1, 0, 'C');
$pdf->Cell(40, 10, "nombre", 1, 0, 'C');
$pdf->Cell(30, 10, "direccion", 1, 0, 'C');
$pdf->Cell(30, 10, "telefono", 1, 0, 'C');
$pdf->Cell(50, 10, "correo", 1, 0, 'C');
$pdf->Cell(24, 10, "puesto", 1, 0, 'C');
$pdf->Cell(24, 10, "salario", 1, 0, 'C');
$pdf->Cell(34, 10, "estudios", 1, 0, 'C');
$pdf->Ln();


if (isset($_GET['opcion']) && !empty(trim($_GET['value']))) {
    $opcion = $_GET['opcion'];
    $value = $_GET['value'];
    $empleadosArray = Empleados::filtro($opcion, $value);
} elseif (isset($_GET['id_empleado'])) {
    $id = $_GET['id_empleado'];
    $empleadosArray = Empleados::filtro('id', $id);
} else {
    $empleadosArray = Empleados::consul();
}

$pdf->SetFont('Arial', '', 10);

$row = 0;
foreach ($empleadosArray as $empleados) {
    $row += 1;
    if ($row > 6) {
        $pdf->AddPage('H');
        $row = 0;
    }
    $y_axis = $pdf->GetY();



    $pdf->Cell(20, 20, $empleados->id_empleado, "B", 0, 'C');
    $pdf->Cell(30, 20, $empleados->rfc, "B", 0, 'L');
    // $pdf->Cell(40, 10, utf8_decode($empleados->nombre), "B", 0, 'L');
    $pdf->MultiCell(40, 20, utf8_decode($empleados->nombre), "B");
    $pdf->SetXY(100, $y_axis);
    #$pdf->Cell(40, 10, utf8_decode($empleados->direccion), "B", 0, 'L');
    $pdf->MultiCell(30, 20, utf8_decode($empleados->direccion), "B", 'L');
    $pdf->SetXY(130, $y_axis);
    $pdf->Cell(30, 20, $empleados->telefono, "B", 0, 'C');
    $pdf->Cell(50, 20, utf8_decode($empleados->correo), "B", 0, 'L');
    $pdf->Cell(24, 20, utf8_decode($empleados->puesto), "B", 0, 'L');
    $pdf->Cell(24, 20, $empleados->salario, "B", 0, 'C');
    $pdf->Cell(34, 20, utf8_decode($empleados->estudios), "B", 0, 'L');

    $pdf->Ln();
}
$pdf->Output();
