<?php

require('fpdf.php');

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Times');

$pdf->Image('../img/encabezado.jpg', 10, 10, 190);
$pdf->Ln(50);

$pdf->Cell(180, 1, utf8_decode('PELÍCULAS'), 0, 0, 'C');
$pdf->Ln(10);
$link = mysqli_connect("localhost", "root", "", "pelis");

/* Se coloca el largo, y la altura de la celda para crear una tabla, luego se coloca el contenido,
el borde y la alineación
*/

#$pdf->Cell(75, 5, 'Title', "LR", 1, 'C');
$pdf->Cell(20, 5, "Id", 1, 0, 'C');
$pdf->Cell(55, 5, utf8_decode('Género'), 1, 0, 'C');
$pdf->Cell(40, 5, utf8_decode('Título'), 1, 0, 'C');
$pdf->Cell(40, 5, "Sinopsis", 1, 0, 'C');
$pdf->Cell(40, 5, utf8_decode('Clasificación'), 1, 0, 'C');
$pdf->Ln();

//$sql = "SELECT id, idGenero, titulo, sinopsis, clasificacion FROM pelicula";
$sql = "SELECT p.id, g.genero, titulo, sinopsis, clasificacion FROM pelicula p INNER JOIN genero g ON p.idGenero = g.id";
$result = mysqli_query($link, $sql);

while ($movie = mysqli_fetch_array($result)) {

  $pdf->Cell(20, 5, $movie['id'], 1, 0, 'C');
  $pdf->Cell(55, 5, utf8_decode($movie['genero']), 1, 0, 'C');
  $pdf->Cell(40, 5, utf8_decode($movie['titulo']), 1, 0, 'C');
  $pdf->Cell(40, 5, utf8_decode($movie['sinopsis']), 1, 0, 'C');
  $pdf->Cell(40, 5, $movie['clasificacion'], 1, 0, 'C');
  $pdf->Ln();
}
mysqli_close($link);

$pdf->SetAutoPageBreak(1, 1);
$pdf->SetY(-15);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 10, 'Page ' . $pdf->PageNo() . '/{nb}', 0, 0, 'C');
$pdf->AliasNbPages();

$pdf->Output();
