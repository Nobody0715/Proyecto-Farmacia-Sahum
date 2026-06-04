<?php
require_once '../includes/auth.php';
requireLogin();
requireAdmin();
require_once '../config/database.php';
require_once '../models/Medicamento.php';

$medModel = new Medicamento($pdo);
$id = (int)$_GET['id'] ?? 0;
$mensaje = '';

$med = ($id > 0) ? $medModel->obtenerPorId($id) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0 && $med) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("❌ Error de seguridad.");
    }

    $observacion = trim($_POST['observacion']);
    if (empty($observacion)) {
        $mensaje = "❌ Debes escribir un motivo de eliminación.";
    } else {
        try {
            // Llamada al nuevo método POO
            $medModel->eliminarLogico($id, $_SESSION['user_id'], $observacion, $med['stock']);
            header('Location: ../inventario/index.php?msg=eliminado');
            exit;
        } catch (Exception $e) {
            $mensaje = "❌ Error: " . $e->getMessage();
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<style>
    body { background-image: linear-gradient(rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.5)), url('https://i.imgur.com/ArjbuJ2.jpeg'); background-size: cover; background-position: center; background-attachment: fixed; min-height: 100vh; display: flex; flex-direction: column; }
    .glass-card { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(10px); padding: 40px; border-radius: 20px; max-width: 600px; margin: 40px auto; box-shadow: 0 8px 32px rgba(0,0,0,0.15); }
</style>

<div class="glass-card">
    <h2 style="text-align:center;">🗑️ Eliminar Medicamento</h2>
    <?php if ($mensaje): ?><p style="color:red; background:#ffebee; padding:15px; border-radius:5px; text-align:center;"><?= $mensaje ?></p><?php endif; ?>

    <?php if ($med): ?>
        <p><strong>Medicamento:</strong> <?= htmlspecialchars($med['nombre']) ?></p>
        <p><strong>Stock actual a dar de baja:</strong> <?= $med['stock'] ?> unidades</p>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <label><strong>Motivo de Eliminación:</strong></label>
            <textarea name="observacion" rows="5" style="width:100%; padding:12px; border-radius:8px; border:1px solid #ccc; margin: 10px 0;" required></textarea>
            <button type="submit" style="padding:12px 25px; background:#e74c3c; color:white; border:none; cursor:pointer; border-radius:8px; width:100%; font-weight:bold;">🗑️ Eliminar Medicamento</button>
            <a href="../inventario/index.php" style="display:block; text-align:center; margin-top:15px; color:#34495e; font-weight:bold;">← Cancelar</a>
        </form>
    <?php else: ?>
        <p>Medicamento no encontrado o ya fue eliminado.</p>
        <a href="../inventario/index.php">← Volver al Inventario</a>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>