<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();
?>

<?php include '../includes/header.php'; ?>

<style>
    body {
        background-image: linear-gradient(rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.5)), url('https://i.imgur.com/ArjbuJ2.jpeg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        min-height: 100vh;
    }

    .glass-container {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin: 40px auto;
        max-width: 1100px;
        width: 95%;
    }

    .search-box { text-align: center; margin-bottom: 25px; background: rgba(255, 255, 255, 0.3); padding: 20px; border-radius: 10px; }
    .search-box input { padding: 12px; width: 300px; border-radius: 5px; border: 1px solid #ccc; font-size: 16px; }
    .btn-search { padding: 12px 25px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }

    .styled-table { width: 100%; border-collapse: collapse; background: rgba(255, 255, 255, 0.7); border-radius: 10px; overflow: hidden; }
    .styled-table thead tr { background-color: rgba(44, 62, 80, 0.95); color: white; }
    .styled-table th, .styled-table td { padding: 12px 15px; border-bottom: 1px solid rgba(0, 0, 0, 0.05); text-align: center; }

    .badge-entrada { color: #27ae60; font-weight: bold; background: rgba(39, 174, 96, 0.1); padding: 5px 10px; border-radius: 5px; }
    .badge-salida { color: #e74c3c; font-weight: bold; background: rgba(231, 76, 60, 0.1); padding: 5px 10px; border-radius: 5px; }
</style>

<div class="glass-container">
    <h2 style="color: #2c3e50; text-align:center;">📊 Historial de Movimientos</h2>

    <div class="search-box">
        <form method="GET">
            <input type="text" name="search" placeholder="Buscar medicamento..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="submit" class="btn-search">🔍 Buscar</button>
            <a href="movimientos.php" style="margin-left:10px; color: #2c3e50; font-weight: bold; text-decoration: none;">Limpiar</a>
        </form>
    </div>

    <?php
    $search = $_GET['search'] ?? '';

    // JOIN con la tabla 'users' para traer el nombre del usuario
    $sql = "SELECT m.*, med.nombre as medicamento, u.nombre as nombre_usuario 
            FROM movimientos m 
            JOIN medicamentos med ON m.medicamento_id = med.id 
            JOIN users u ON m.usuario_id = u.id 
            WHERE med.nombre LIKE ? 
            ORDER BY m.fecha DESC LIMIT 100";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$search%"]);
    $movimientos = $stmt->fetchAll();
    ?>

    <table class="styled-table">
        <thead>
            <tr>
                <th>📅 Fecha / Hora</th>
                <th>💊 Medicamento</th>
                <th>🔃 Tipo</th>
                <th>🔢 Cantidad</th>
                <th>👤 Usuario</th>
                <th>📝 Observación</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($movimientos) > 0): ?>
                <?php foreach ($movimientos as $mov): 
                    // Limpiamos la IP del texto de observación
                    $obs_limpia = preg_replace('/\[IP: .*\]/', '', $mov['observacion']);
                ?>
                    <tr>
                        <td style="font-size: 14px;"><?= date('d/m/Y H:i', strtotime($mov['fecha'])) ?></td>
                        <td><strong><?= htmlspecialchars($mov['medicamento']) ?></strong></td>
                        <td>
                            <?php if ($mov['tipo'] === 'entrada'): ?>
                                <span class="badge-entrada">➕ ENTRADA</span>
                            <?php else: ?>
                                <span class="badge-salida">➖ SALIDA</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size: 18px; font-weight: bold;"><?= $mov['cantidad'] ?></td>
                        <td><?= htmlspecialchars($mov['nombre_usuario']) ?></td>
                        <td style="font-style: italic; color: #555;"><?= htmlspecialchars($obs_limpia) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding:40px; color:#7f8c8d;">
                        No hay movimientos registrados.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div style="text-align: center; margin-top: 30px;">
        <a href="../dashboard.php" style="color: #2c3e50; text-decoration: none; font-weight: bold; background: rgba(255,255,255,0.5); padding: 10px 20px; border-radius: 20px;">
            ← Volver al Panel Principal
        </a>
    </div>
</div>

<footer style="text-align:center; padding:30px; font-weight:bold; color:#2c3e50;">
    © <?= date('Y') ?> Farmacia SAHUM - Sistema de Inventario
</footer>