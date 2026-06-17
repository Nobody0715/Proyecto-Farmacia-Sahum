<?php
require_once '../init.php';
use App\Categoria;
requireAdmin();

$catModel = new Categoria($pdo);
$id = (int)$_GET['id'] ?? 0;
if ($id <= 0) die("ID inválido");

$cat = $catModel->obtenerPorId($id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catModel->editar($id, $_POST['nombre']);
    header('Location: categorias.php?msg=editado');
    exit;
}
?>

<?php include '../includes/header.php'; ?>

<style>
    body { background-image: linear-gradient(rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.5)), url('https://i.imgur.com/ArjbuJ2.jpeg'); background-size: cover; background-position: center; background-attachment: fixed; min-height: 100vh; display: flex; flex-direction: column; }
    .wrapper { flex: 1; display: flex; align-items: center; justify-content: center; }
    .glass-card { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(10px); padding: 40px; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.3); max-width: 400px; width: 90%; box-shadow: 0 8px 32px rgba(0,0,0,0.15); }
    .form-control { width: 100%; padding: 12px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #ccc; box-sizing: border-box; }
    .btn-save { width: 100%; padding: 12px; background: #f39c12; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
</style>

<div class="wrapper">
    <div class="glass-card">
        <h2 style="text-align:center; color:#2c3e50;"> Editar Categoría</h2>
        <form method="POST">
            <input type="text" name="nombre" value="<?= htmlspecialchars($cat['nombre']) ?>" class="form-control" required>
            <button type="submit" class="btn-save"> Guardar Cambios</button>
        </form>
        <a href="categorias.php" style="display:block; text-align:center; margin-top:15px; color:#2c3e50; font-weight:bold; text-decoration:none;">← Cancelar</a>
    </div>
</div>

<footer style="text-align:center; padding:20px; font-weight:bold; color:#2c3e50;">
    © <?= date('Y') ?> Farmacia SAHUM - Sistema de Inventario
</footer>

<?php include '../includes/footer.php'; ?>