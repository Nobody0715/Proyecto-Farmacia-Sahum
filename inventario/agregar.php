<?php
require_once '../init.php'; 
use App\Medicamento;

requireLogin();
requireOperator(); 

$medModel = new Medicamento($pdo);
$categorias = $medModel->obtenerCategorias();
$mensaje = '';

if ($_POST) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("❌ Error de seguridad: Solicitud no autorizada.");
    }

    $nombre        = trim($_POST['nombre']);
    $presentacion  = $_POST['presentacion'];
    $concentracion = trim($_POST['concentracion']);
    $categoria_id  = (int)$_POST['categoria_id'];
    $stock_nuevo   = (int)$_POST['stock'];
    $stock_min     = (int)$_POST['stock_minimo'];

    if ($nombre != '' && $stock_nuevo > 0 && $categoria_id > 0) {
        try {
            $pdo->beginTransaction();

            $existente = $medModel->buscarExistente($nombre);

            if ($existente) {

                $nuevo_stock = $existente['stock'] + $stock_nuevo;
                
                $medModel->actualizar($existente['id'], $nombre, $presentacion, $concentracion, $categoria_id, $nuevo_stock, $stock_min);
                $medModel->registrarMovimiento($existente['id'], 'entrada', $stock_nuevo, $_SESSION['user_id'], 'Entrada adicional - Existente');
                
                $mensaje = "✅ Stock actualizado.";
            } else {
                // Asignamos propiedades al objeto
                $medModel->nombre = $nombre;
                $medModel->presentacion = $presentacion;
                $medModel->concentracion = $concentracion;
                $medModel->categoria_id = $categoria_id;
                $medModel->stock = $stock_nuevo;
                $medModel->stock_minimo = $stock_min;
                
                $medModel->crear();
                $nuevo_id = $pdo->lastInsertId();
                $medModel->registrarMovimiento($nuevo_id, 'entrada', $stock_nuevo, $_SESSION['user_id'], 'Stock inicial - Nuevo');

                $mensaje = "✅ Nuevo medicamento <strong>$nombre</strong> creado.";
            }

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $mensaje = "❌ Error: " . $e->getMessage();
        }
    } else {
        $mensaje = "❌ El nombre, la categoría y la cantidad son obligatorios.";
    }
}
?>

<?php include '../includes/header.php'; ?>

<style>
    body { background-image: linear-gradient(rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.5)), url('https://i.imgur.com/ArjbuJ2.jpeg'); background-size: cover; background-position: center; background-attachment: fixed; background-repeat: no-repeat; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; display: flex; flex-direction: column; min-height: 100vh; }
    .main-container { flex: 1; display: flex; justify-content: center; align-items: center; padding: 20px; }
    .glass-card { background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 20px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15); padding: 40px; width: 100%; max-width: 500px; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; color: #2c3e50; font-weight: bold; }
    .form-control { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.1); background: rgba(255,255,255,0.8); font-size: 16px; box-sizing: border-box; }
    .btn-save { width: 100%; padding: 15px; background: #27ae60; color: white; border: none; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; transition: 0.3s; margin-top: 10px; }
    .btn-save:hover { background: #219150; transform: translateY(-2px); }
    .alert-success { background: rgba(212, 237, 218, 0.9); color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; border: 1px solid #c3e6cb; }
</style>

<div class="main-container">
    <div class="glass-card">
        <h2 style="text-align: center; color: #2c3e50; margin-top: 0;">➕ Agregar / Actualizar</h2>
        <?php if ($mensaje): ?><div class="alert-success"><?= $mensaje ?></div><?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="form-group">
                <label>Nombre del Medicamento:</label>
                <input type="text" name="nombre" class="form-control" required placeholder="Ej: Paracetamol">
            </div>
            <div class="form-group">
                <label>Presentación:</label>
                <select name="presentacion" class="form-control" required>
                    <?php $op = ['Ampolla', 'Bolsa', 'Crema', 'Frasco', 'Gotas', 'Jarabe', 'Ovulo', 'Puff', 'Solución', 'Spray', 'Suspensión','Kit', 'Tableta', 'Tab-Vaginal', 'Viales'];
                    foreach ($op as $o) { echo "<option value='$o'>$o</option>"; } ?>
                </select>
            </div>
            <div class="form-group">
                <label>Categoría:</label>
                <select name="categoria_id" class="form-control" required>
                    <option value="">-- Seleccione Categoría --</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Concentración:</label>
                <input type="text" name="concentracion" class="form-control" placeholder="Ej: 500mg">
            </div>
            <div style="display: flex; gap: 10px;">
                <div class="form-group" style="flex: 1;"><label>Cantidad:</label><input type="number" name="stock" class="form-control" min="1" value="10" required></div>
                <div class="form-group" style="flex: 1;"><label>Mínimo:</label><input type="number" name="stock_minimo" class="form-control" value="10" min="1" required></div>
            </div>
            <button type="submit" class="btn-save">💾 Guardar / Actualizar Stock</button>
            <div style="text-align: center; margin-top: 20px;"><a href="../dashboard.php" style="color: #2c3e50; text-decoration: none; font-weight: bold;">← Volver al Panel</a></div>
        </form>
    </div>
</div>
<footer style="position: fixed; bottom: 15px; left: 0; width: 100%; text-align: center; color: #2c3e50; font-size: 14px; font-weight: bold; text-shadow: 1px 1px 1px rgba(255,255,255,0.5);">© <?= date('Y') ?> Farmacia SAHUM - Sistema de Inventario</footer>