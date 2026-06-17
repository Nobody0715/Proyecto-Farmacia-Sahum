<?php
require_once '../init.php';
use App\Categoria;

requireLogin();
requireAdmin();

$catModel = new Categoria($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("❌ Error de seguridad.");
    }
    
    $nombre = trim($_POST['nombre']);
    if (!empty($nombre)) {
        $catModel->crear($nombre);
        header('Location: categorias.php?msg=exito');
        exit;
    }
}

$categorias = $catModel->listar();
$mensaje = (isset($_GET['msg']) && $_GET['msg'] == 'exito') ? "✅ Categoría agregada con éxito." : '';
$mensaje_edit = (isset($_GET['msg']) && $_GET['msg'] == 'editado') ? "✅ Categoría actualizada." : '';
$mensaje_del = (isset($_GET['msg']) && $_GET['msg'] == 'eliminado') ? "🗑️ Categoría eliminada." : '';
?>

<?php include '../includes/header.php'; ?>

<style>
    body { background-image: linear-gradient(rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.5)), url('https://i.imgur.com/ArjbuJ2.jpeg'); background-size: cover; background-position: center; background-attachment: fixed; min-height: 100vh; display: flex; flex-direction: column; }
    .wrapper { flex: 1; }
    .glass-container { background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(15px); padding: 40px; margin: 40px auto; max-width: 600px; width: 90%; border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.15); }
    .styled-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; margin-top: 20px; }
    .styled-table th { background: #34495e; color: white; padding: 12px; text-align: left; }
    .styled-table td { padding: 12px; border-bottom: 1px solid #ddd; }
    
    .btn-action { padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px; color: white; font-weight: bold; display: inline-block; margin: 2px; }
    .btn-edit { background-color: #f39c12; }
    .btn-delete { background-color: #e74c3c; }
</style>

<div class="wrapper">
    <div class="glass-container">
        <h2 style="text-align:center; color:#2c3e50;"> Gestión de Categorías</h2>
        <?php if ($mensaje || $mensaje_edit || $mensaje_del): ?>
            <div style="text-align:center; color:green; font-weight:bold;"><?= $mensaje ?: ($mensaje_edit ?: $mensaje_del) ?></div>
        <?php endif; ?>

        <form method="POST" style="text-align:center; margin-bottom: 30px;">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="text" name="nombre" placeholder="Nombre de nueva categoría" required style="padding:12px; width:60%; border-radius:8px; border:1px solid #ccc;">
            <button type="submit" style="padding:12px 20px; background:#3498db; color:white; border:none; cursor:pointer; border-radius:8px; font-weight:bold;"> Agregar</button>
        </form>

        <table class="styled-table">
            <thead><tr><th>#</th><th>Nombre</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php $c = 1; foreach ($categorias as $cat): ?>
                    <tr>
                        <td><?= $c++ ?></td>
                        <td><?= htmlspecialchars($cat['nombre']) ?></td>
                        <td>
                            <a href="editar_categoria.php?id=<?= $cat['id'] ?>" class="btn-action btn-edit"> Editar</a>
                            <a href="eliminar_categoria.php?id=<?= $cat['id'] ?>" class="btn-action btn-delete" onclick="return confirm('¿Seguro?')"> Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div style="text-align:center; margin-top:20px;"><a href="../dashboard.php" style="color:#2c3e50; font-weight:bold; text-decoration:none;">← Volver al Panel</a></div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>