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

$tipo = isset($_GET['opcion']) ? 'Consulta' : (isset($_GET['Empleados']) ? 'Individual' : 'General');

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('H');
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(272, 12, 'Reporte de Empleados', 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(136, 10, "Fecha del reporte: $fecha", 1, 0);
$pdf->Cell(136, 10, "Tipo de reporte: $tipo", 1, 1);
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 10, "RFC", 1, 0, 'C');
$pdf->Cell(80, 10, "Nombre", 1, 0, 'C');
$pdf->Cell(30, 10, "Telefono", 1, 0, 'C');
$pdf->Cell(40, 10, "Correo", 1, 0, 'C');
$pdf->Cell(30, 10, "Puesto", 1, 0, 'C');
$pdf->Cell(30, 10, "Salario", 1, 0, 'C');
$pdf->Cell(30, 10, "Estudios", 1, 0, 'C');
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



    $pdf->Cell(30, 10, $empleados->rfc, "B", 0, 'L');
    $pdf->Cell(80, 10, utf8_decode($empleados->nombre), "B", 0, 'L');
    $pdf->Cell(30, 10, $empleados->telefono, "B", 0, 'C');
    $pdf->Cell(40, 10, utf8_decode($empleados->correo), "B", 0, 'L');
    $pdf->Cell(30, 10, utf8_decode($empleados->puesto), "B", 0, 'L');
    $pdf->Cell(30, 10, "$" . $empleados->salario, "B", 0, 'R');
    $pdf->Cell(30, 10, utf8_decode($empleados->estudios), "B", 0, 'L');

    $pdf->Ln();
}
$pdf->Output('', "Reporte-Empleados-$fecha-$tipo");
