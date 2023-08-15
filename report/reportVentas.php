<?php
// Comprobar si hay una sesión iniciada
session_start();
require_once '../models/Login.php';
if (!isset($_SESSION['user'])) {
    header('Location: ../index.php');
}

require('../models/PDF.php');
require_once '../models/config.php';
require_once '../models/Ventas.php';

$mysqli = new mysqli($servidor, $usuario, $password, $bd);

// Iniciando parámetros
Ventas::init($mysqli);

// Configurando fecha
date_default_timezone_set('America/Mexico_City');
$fecha = date("d-m-Y");

$tipo = isset($_GET['opcion']) ? 'Consulta' : (isset($_GET['venta']) ? 'Individual' : 'General');

$pdf = new PDF('L');
$pdf->AliasNbPages();
$pdf->AddPage();
// Titulo
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(272, 12, 'Reporte de Ventas', 0, 1, 'C');
$pdf->Ln(5);

// Datos del reporte
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(136, 10, "Fecha del reporte: $fecha", 1, 0);
$pdf->Cell(136, 10, "Tipo de reporte: $tipo", 1, 1);
$pdf->Ln(10);

// Tabla del reporte
// Encabezado
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(20, 5, "ID", 1, 0, 'C');
$pdf->Cell(50, 5, "Empleado", 1, 0, 'C');
$pdf->Cell(50, 5, "Cliente", 1, 0, 'C');
$pdf->Cell(60, 5, "Productos", 1, 0, 'C');
$pdf->Cell(24, 5, "Cantidad", 1, 0, 'C');
$pdf->Cell(20, 5, "IVA", 1, 0, 'C');
$pdf->Cell(24, 5, "Subtotal", 1, 0, 'C');
$pdf->Cell(24, 5, "Total", 1, 0, 'C');
$pdf->Ln();


// Contenido de la tabla

if (isset($_GET['opcion']) && isset($_GET['search'])) {
    $opcion = $_GET['opcion'];
    $search = $_GET['search'];

    if ($opcion == 'id' || $opcion == 'fecha') {
        $ventasArray = Ventas::consultaFiltrada($opcion, $search);
    } else {
        $ventasArray = Ventas::consultaFiltradaRelacionada($opcion, $search);
    }
} elseif (isset($_GET['venta'])) {
    $id = $_GET['venta'];
    $ventasArray = Ventas::consultaFiltrada('id', $id);
} else {
    $ventasArray = Ventas::consultarVentas();
}

$pdf->SetFont('Arial', '', 10);

$row = 0;
foreach ($ventasArray as $venta) {
    $row += 1;
    if ($row > 6) {
        $pdf->AddPage();
        $row = 0;
    }

    $listProductos = explode("<br/>", $venta->idProductos);
    $listaCantidad = explode("<br/>", $venta->cantidades);

    $productos = "";

    for ($i = 0; $i < count($listProductos); $i++) {
        if (($i + 1) < count($listProductos)) {
            $productos .= "$listProductos[$i]\n";
        } else {
            $productos .= "$listProductos[$i]";
        }
    }

    $numLineas = substr_count($productos, "\n");
    $altura = 10 * $numLineas;

    $y_axis = $pdf->GetY();

    $pdf->Cell(20, $altura, $venta->idVenta, "B", 0, 'C');
    $pdf->Cell(50, $altura, utf8_decode($venta->idEmpleado), "B", 0, 'L');
    $pdf->Cell(50, $altura, utf8_decode($venta->idCliente), "B", 0, 'L');


    $pdf->MultiCell(60, 10, $productos, "B", 'L');
    $pdf->SetXY(190, $y_axis);

    $cantidades = "";

    for ($i = 0; $i < count($listaCantidad); $i++) {
        if (($i + 1) < count($listaCantidad)) {
            $cantidades .= "$listaCantidad[$i]\n";
        } else {
            $cantidades .= "$listaCantidad[$i]";
        }
    }

    $pdf->MultiCell(24, 10, $cantidades, "B", 'C');
    $pdf->SetXY(214, $y_axis);

    $pdf->Cell(20, $altura, "$" . $venta->iva, "B", 0, 'R');
    $pdf->Cell(24, $altura, "$" . $venta->subtotal, "B", 0, 'R');
    $pdf->Cell(24, $altura, "$" . $venta->costo, "B", 0, 'R');

    $pdf->Ln();
    #print_r($productos);
}

$pdf->Output('', "Reporte-Ventas-$fecha-$tipo"); 
#print_r($ventasArray);
