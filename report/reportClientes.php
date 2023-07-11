<?php

session_start();
require_once '../models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ../index.html');
}

require('../models/PDF.php');
require_once '../models/config.php';
require_once '../models/Clientes.php';

$mysqli = new mysqli($servidor, $usuario, $password, $bd);

Clientes::init($mysqli);

date_default_timezone_set('America/Mexico_City');
$fecha = date("Y-m-d");

$pdf = new PDF();
$pdf->AddPage('H');
$pdf->AliasNbPages();

$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(272, 12, 'Reporte de Clientes', 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(20, 5, "Fecha del reporte: $fecha");
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 10, "Id Cliente", 1, 0, 'C');
$pdf->Cell(45, 10, "RFC", 1, 0, 'C');
$pdf->Cell(45, 10, utf8_decode("Nombre"), 1, 0, 'C');
$pdf->Cell(40, 10, utf8_decode("Dirección"), 1, 0, 'C');
$pdf->Cell(35, 10, utf8_decode("Teléfono"), 1, 0, 'C');
$pdf->Cell(55, 10, "Correo", 1, 0, 'C');
$pdf->Cell(25, 10, utf8_decode("Género"), 1, 0, 'C');
$pdf->Ln();

if (isset($_GET['opcion']) && !empty(trim($_GET['value']))) {
    $opcion = $_GET['opcion'];
    $value = $_GET['value'];
    $clientesArray = Clientes::busquedafil($opcion, $value);
} elseif (isset($_GET['id_cliente'])) {
    $id = $_GET['id_cliente'];
    $clientesArray = Clientes::busquedafil('idcli', $id);
} else {
    $clientesArray = Clientes::consulta();
}

$pdf->SetFont('Arial', '', 10);

$row = 0;
foreach ($clientesArray as $clientes) {
    $row += 1;
    if ($row > 10) {
        $pdf->AddPage('H');
        $row = 0;
    }

    $pdf->Cell(30, 10, $clientes->id_cliente, "B", 0, 'C');
    $pdf->Cell(45, 10, utf8_decode($clientes->rfc), "B", 0, 'C');
    $pdf->Cell(45, 10, utf8_decode($clientes->nombre), "B", 0, 'C');
    $pdf->Cell(40, 10, utf8_decode($clientes->direccion), "B", 0, 'C');
    $pdf->Cell(35, 10, $clientes->telefono, "B", 0, 'C');
    $pdf->Cell(55, 10, utf8_decode($clientes->correo), "B", 0, 'C');
    $pdf->Cell(25, 10, $clientes->genero, "B", 0, 'C');

    $pdf->Ln();
}

$pdf->Output();
