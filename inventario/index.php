<?php
require_once '../includes/auth.php';
requireLogin();
require_once '../config/database.php';
require_once '../models/Medicamento.php';

$medModel = new Medicamento($pdo);
$search = $_GET['search'] ?? '';
$bajo_stock = isset($_GET['bajo_stock']);

$medicamentos = $medModel->buscar($search, $bajo_stock);
?>

<?php include '../includes/header.php'; ?>

<style>
    body { background-image: linear-gradient(rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.5)), url('https://i.imgur.com/ArjbuJ2.jpeg'); background-size: cover; background-position: center; background-attachment: fixed; background-repeat: no-repeat; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; display: flex; flex-direction: column; min-height: 100vh; }
    .wrapper { flex: 1; }
    .glass-container { background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 15px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); padding: 30px; margin: 40px auto; max-width: 1200px; width: 95%; }
    .styled-table { width: 100%; border-collapse: collapse; background: rgba(255, 255, 255, 0.6); border-radius: 10px; overflow: hidden; }
    .styled-table thead tr { background-color: rgba(44, 62, 80, 0.9); color: #ffffff; text-align: left; }
    .styled-table th, .styled-table td { padding: 12px 15px; border-bottom: 1px solid rgba(0, 0, 0, 0.05); }
    .btn-back { display: inline-block; margin-top: 20px; text-decoration: none; color: #2c3e50; font-weight: bold; background: rgba(255, 255, 255, 0.5); padding: 10px 20px; border-radius: 20px; }
    .btn-back:hover { background: rgba(255, 255, 255, 0.8); }
</style>

<div class="wrapper">
    <div class="glass-container">
        <h2 style="color: #2c3e50; text-align:center;">📋 Inventario Actual</h2>

        <form method="GET" style="margin-bottom: 25px; text-align: center; background: rgba(255,255,255,0.3); padding: 20px; border-radius: 10px;">
            <input type="text" name="search" placeholder="Buscar medicamento..." value="<?= htmlspecialchars($search) ?>" style="padding:12px; width:300px; font-size:16px; border-radius: 5px; border: 1px solid #ccc;">
            <button type="submit" style="padding:12px 20px; background:#3498db; color:white; border:none; cursor:pointer; border-radius: 5px; font-weight:bold;">🔍 Buscar</button>
            <a href="index.php" style="padding:12px 15px; background:#95a5a6; color:white; text-decoration:none; margin-left:5px; border-radius: 5px;">Limpiar</a>
            
            <!-- BOTONES DE REPORTE JUNTOS -->
            <a href="exportar_excel.php" style="padding:12px 15px; background:#2ecc71; color:white; text-decoration:none; margin-left:5px; border-radius: 5px; font-weight:bold;">📊 Excel</a>
            <a href="imprimir_pdf.php" target="_blank" style="padding:12px 15px; background:#e74c3c; color:white; text-decoration:none; margin-left:5px; border-radius: 5px; font-weight:bold;">📄 PDF</a>
            
            <label style="margin-left:20px; font-size:16px; color: #2c3e50; font-weight: bold;">
                <input type="checkbox" name="bajo_stock" <?= $bajo_stock ? 'checked' : '' ?>> Solo mostrar bajo stock
            </label>
        </form>

        <table class="styled-table">
            <thead>
                <tr>
                    <th>Medicamento</th><th>Presentación</th><th>Categoría</th><th>Concentración</th>
                    <th style="text-align:center;">Stock</th><th style="text-align:center;">Estado</th>
                    <?php if (isAdmin()): ?> <th style="text-align:center;">Acciones</th> <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (count($medicamentos) > 0): ?>
                    <?php foreach ($medicamentos as $m): $es_bajo = $m['stock'] <= $m['stock_minimo']; ?>
                        <tr>
                            <td style="font-weight:bold;"><?= htmlspecialchars($m['nombre']) ?></td>
                            <td><?= htmlspecialchars($m['presentacion'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($m['categoria_nombre'] ?? 'Sin cat.') ?></td>
                            <td><?= htmlspecialchars($m['concentracion'] ?? '-') ?></td>
                            <td style="text-align:center; font-weight:bold; color: <?= $es_bajo ? '#e74c3c' : '#27ae60' ?>;"><?= $m['stock'] ?></td>
                            <td style="text-align:center;"><?= $es_bajo ? '⚠️ BAJO' : '✅ Normal' ?></td>
                            
                            <?php if (isAdmin()): ?>
                                <td style="text-align:center;">
                                    <a href="editar.php?id=<?= $m['id'] ?>" style="background:#f39c12; color:white; padding:8px 12px; text-decoration:none; border-radius:4px; margin-right:5px; font-size: 14px;">✏️ Editar</a>
                                    <a href="eliminar.php?id=<?= $m['id'] ?>" onclick="return confirm('¿Seguro?')" style="background:#e74c3c; color:white; padding:8px 12px; text-decoration:none; border-radius:4px; font-size: 14px;">🗑️ Eliminar</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="<?= isAdmin() ? '7' : '6' ?>" style="text-align:center; padding:40px;">No se encontraron medicamentos.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="../dashboard.php" class="btn-back">← Volver al Panel Principal</a>
    </div>
</div>

<footer style="text-align: center; color: #2c3e50; font-size: 14px; font-weight: bold; padding: 20px;">
    © <?= date('Y') ?> Farmacia SAHUM - Sistema de Inventario
</footer>

<?php include '../includes/footer.php'; ?>