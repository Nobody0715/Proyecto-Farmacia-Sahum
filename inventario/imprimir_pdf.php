<?php
require_once '../includes/auth.php';
requireOperator();
require_once '../config/database.php';
require('../libs/fpdf.php');

$stmt = $pdo->query("SELECT * FROM medicamentos WHERE deleted_at IS NULL ORDER BY nombre ASC");
$medicamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Inventario Farmacia SAHUM', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 10, 'Fecha: ' . date('d/m/Y H:i'), 0, 1, 'C');
$pdf->Ln(10);

// Encabezados de tabla
$pdf->SetFillColor(44, 62, 80);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(70, 10, 'Medicamento', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Presentacion', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Concentracion', 1, 0, 'C', true);
$pdf->Cell(20, 10, 'Stock', 1, 1, 'C', true);

// Datos
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 10);
foreach ($medicamentos as $m) {
    $pdf->Cell(70, 10, utf8_decode($m['nombre']), 1);
    $pdf->Cell(40, 10, utf8_decode($m['presentacion']), 1, 0, 'C');
    $pdf->Cell(40, 10, utf8_decode($m['concentracion']), 1, 0, 'C');
    $pdf->Cell(20, 10, $m['stock'], 1, 1, 'C');
}

$pdf->Output('D', 'Inventario_SAHUM_'.date('Y-m-d').'.pdf');
?>