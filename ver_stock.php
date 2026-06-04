<?php
// 1. CONFIGURACIÓN DE CONEXIÓN
$host = "localhost";
$user = "root";
$pass = "";
$db   = "farmacia_db"; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// 2. CONSULTA USANDO TUS VARIABLES REALES
// Tabla: productos | Columnas: nombre, stock_actual
$sql = "SELECT nombre, stock_actual, fecha_ingreso FROM productos ORDER BY nombre ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario Farmacia SAHUM</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f0f2f5; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden; }
        
        .header { background-color: #004d40; color: white; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 1.4em; text-transform: uppercase; }
        
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #e0f2f1; color: #00695c; padding: 12px; text-align: left; border-bottom: 2px solid #b2dfdb; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 0.95em; }
        tr:hover { background-color: #f1f8e9; }
        
        .stock-tag { padding: 4px 8px; border-radius: 4px; font-weight: bold; }
        .en-stock { background-color: #c8e6c9; color: #2e7d32; }
        .agotado { background-color: #ffcdd2; color: #c62828; }
        
        .footer { padding: 15px; text-align: center; font-size: 0.8em; color: #777; background: #fafafa; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Control de Stock Actual</h1>
        <small>Farmacia SAHUM - Consulta Pública</small>
    </div>

    <table>
        <thead>
            <tr>
                <th>Medicamento / Producto</th>
                <th style="text-align: center;">Cantidad Disponible</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['nombre']); ?></strong></td>
                        <td style="text-align: center;">
                            <?php if ($row['stock_actual'] > 0): ?>
                                <span class="stock-tag en-stock"><?php echo $row['stock_actual']; ?> unidades</span>
                            <?php else: ?>
                                <span class="stock-tag agotado">Agotado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2" style="text-align:center; padding: 30px;">
                        No hay productos registrados actualmente.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="footer">
        Información obtenida de la base de datos: <strong>farmacia_db</strong>
    </div>
</div>

</body>
</html>
<?php $conn->close(); ?>