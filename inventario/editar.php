<?php
require_once '../init.php'; // Usamos nuestro nuevo archivo init.php
use App\Medicamento;
use App\Categoria; // 1. IMPORTA LA CLASE CATEGORIA

requireLogin();
requireAdmin();

$medModel = new Medicamento($pdo);
$catModel = new Categoria($pdo); // 2. INSTANCIA LA CLASE CATEGORIA

// 3. Obtener categorías usando el modelo de categoría, NO el de medicamento
$categorias = $catModel->listar();

$id = (int)$_GET['id'] ?? 0;
$mensaje = '';

if ($id <= 0) die("ID inválido");

$med = $medModel->obtenerPorId($id);
if (!$med) die("Medicamento no encontrado");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("❌ Error de seguridad.");
    }

    $nombre        = trim($_POST['nombre']);
    $presentacion  = $_POST['presentacion'];
    $concentracion = trim($_POST['concentracion']);
    $categoria_id  = (int)$_POST['categoria_id'];
    $stock         = (int)$_POST['stock'];
    $stock_min     = (int)$_POST['stock_minimo'];

    // AQUÍ ESTÁ LA MAGIA DEL POO: Solo llamamos al método, el SQL está oculto
    if ($medModel->actualizar($id, $nombre, $presentacion, $concentracion, $categoria_id, $stock, $stock_min)) {
        $mensaje = "✅ Medicamento actualizado correctamente";
        $med = $medModel->obtenerPorId($id); // Recargar
    } else {
        $mensaje = "❌ Error al actualizar.";
    }
}
?>

<?php include '../includes/header.php'; ?>

<style>
    body { background-image: linear-gradient(rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.5)), url('https://i.imgur.com/ArjbuJ2.jpeg'); background-size: cover; background-position: center; background-attachment: fixed; min-height: 100vh; display: flex; flex-direction: column; }
    .glass-card { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(10px); padding: 40px; border-radius: 20px; max-width: 600px; margin: 40px auto; box-shadow: 0 8px 32px rgba(0,0,0,0.15); }
    .form-control { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 10px; }
</style>

<div class="glass-card">
    <h2>✏️ Editar: <?= htmlspecialchars($med['nombre']) ?></h2>
    <?php if ($mensaje): ?><p style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px;"><strong><?= $mensaje ?></strong></p><?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <label>Nombre del Medicamento:</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($med['nombre']) ?>" required class="form-control">

        <label>Presentación:</label>
        <select name="presentacion" class="form-control" required>
            <?php 
            $op = ['Ampolla','Bolsa', 'Crema', 'Frasco', 'Gotas', 'Jarabe', 'Ovulo', 'Puff', 'Solución', 'Spray', 'Suspensión', 'Kit', 'Tableta', 'Tab-Vaginal', 'Viales'];
            foreach ($op as $o) { echo "<option value='$o' ".($med['presentacion']==$o?'selected':'').">$o</option>"; }
            ?>
        </select>

        <label>Categoría:</label>
        <select name="categoria_id" class="form-control" required>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($med['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Concentración:</label>
        <input type="text" name="concentracion" value="<?= htmlspecialchars($med['concentracion'] ?? '') ?>" class="form-control">

        <label>Stock Actual:</label>
        <input type="number" name="stock" value="<?= (int)$med['stock'] ?>" required class="form-control">

        <label>Stock Mínimo:</label>
        <input type="number" name="stock_minimo" value="<?= (int)$med['stock_minimo'] ?>" required class="form-control">

        <button type="submit" style="padding:12px 25px; background:#f39c12; color:white; border:none; cursor:pointer; border-radius:8px; width:100%;">Guardar Cambios</button>
        <a href="../inventario/index.php" style="display:block; text-align:center; margin-top:15px; color: #34495e;">← Cancelar</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>