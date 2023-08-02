<?php
require('../fpdf/fpdf.php');

// Crear una nueva clase que extienda FPDF
class PDF extends FPDF
{
  // Sobrescrita del método Header
  function Header()
  {
    // Selección de tipo de letra en negrita
    $this->SetFont('Arial', 'B', 18);

    // Mover a la derecha
    #$this->Cell(90);

    // Título
    $this->Cell(272, 5, 'Agencia Aleman', 0, 0, 'C');

    // Salto de línea
    $this->Ln(2);
    // Insertar imagen, asumiendo que logo.png está en el directorio de trabajo
    $this->Image('../public/favicon.png', 20, 3, 20);

    // Línea de separación
    $this->Line(10, 24, 280, 24);

    // Salto de línea
    $this->Ln(15);
  }

  // Sobrescrita del método Footer
  function Footer()
  {
    // Posición: a 1.5 cm del final
    $this->SetY(-15);

    // Arial itálico 8
    $this->SetFont('Arial', 'I', 8);

    // Número de página
    $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
  }
}
