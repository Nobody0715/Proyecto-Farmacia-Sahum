<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireOperator(); 

$stmt = $pdo->query("SELECT m.fecha, med.nombre, m.tipo, m.cantidad 
                     FROM movimientos m 
                     JOIN medicamentos med ON m.medicamento_id = med.id 
                     ORDER BY m.fecha DESC");
$movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=reporte_movimientos_" . date('Y-m-d') . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr>
        <th style='background-color: #4b6584; color: white;'>FECHA</th>
        <th style='background-color: #4b6584; color: white;'>MEDICAMENTO</th>
        <th style='background-color: #4b6584; color: white;'>TIPO</th>
        <th style='background-color: #4b6584; color: white;'>CANTIDAD</th>
      </tr>";

foreach ($movimientos as $row) {
    echo "<tr>
            <td>" . htmlspecialchars($row['fecha']) . "</td>
            <td>" . htmlspecialchars($row['nombre']) . "</td>
            <td>" . htmlspecialchars($row['tipo']) . "</td>
            <td>" . htmlspecialchars($row['cantidad']) . "</td>
          </tr>";
}
echo "</table>";
exit;