<?php
require_once '../includes/auth.php';
requireLogin();
// CAMBIO: Permitimos acceso a Farmacéuticos y Admins
requireOperator(); 
require_once '../config/database.php';

// Generar token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$mensaje = '';

if ($_POST) {
    // Validar Token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("❌ Error de seguridad: Solicitud no autorizada.");
    }

    $medicamento_id = (int)$_POST['medicamento_id'];
    $tipo           = $_POST['tipo'];
    $cantidad       = (int)$_POST['cantidad'];
    $observacion    = trim($_POST['observacion'] ?? '');
    $ip_registro    = $_SERVER['REMOTE_ADDR']; 

    if ($medicamento_id > 0 && $cantidad > 0) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT stock FROM medicamentos WHERE id = ?");
            $stmt->execute([$medicamento_id]);
            $med = $stmt->fetch();

            if ($tipo === 'salida' && $med['stock'] < $cantidad) {
                throw new Exception("Stock insuficiente. Solo quedan " . $med['stock'] . " unidades disponibles.");
            }

            $signo = ($tipo === 'entrada') ? '+' : '-';
            $stmt = $pdo->prepare("UPDATE medicamentos SET stock = stock $signo ? WHERE id = ?");
            $stmt->execute([$cantidad, $medicamento_id]);

            $obs_final = $observacion . " [IP: " . $ip_registro . "]";
            $stmt = $pdo->prepare("INSERT INTO movimientos (medicamento_id, tipo, cantidad, usuario_id, observacion) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$medicamento_id, $tipo, $cantidad, $_SESSION['user_id'], $obs_final]);

            $pdo->commit();
            $mensaje = "✅ Movimiento registrado correctamente";
        } catch (Exception $e) {
            $pdo->rollBack();
            $mensaje = "❌ Error: " . $e->getMessage();
        }
    } else {
        $mensaje = "❌ Datos incompletos";
    }
}

// Filtramos solo los activos
$stmt = $pdo->query("SELECT id, nombre, stock FROM medicamentos WHERE deleted_at IS NULL ORDER BY nombre");
$medicamentos = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<style>
    body { background-image: linear-gradient(rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.5)), url('https://i.imgur.com/ArjbuJ2.jpeg'); background-size: cover; background-position: center; background-attachment: fixed; background-repeat: no-repeat; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; display: flex; flex-direction: column; min-height: 100vh; }
    .main-container { flex: 1; display: flex; justify-content: center; align-items: center; padding: 20px; }
    .glass-card { background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 20px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15); padding: 40px; width: 100%; max-width: 550px; }
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; margin-bottom: 8px; color: #2c3e50; font-weight: bold; }
    .form-control { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.1); background: rgba(255,255,255,0.85); font-size: 16px; box-sizing: border-box; color: #333; }
    .btn-submit { width: 100%; padding: 15px; background: #27ae60; color: white; border: none; border-radius: 8px; font-size: 18px; font-weight: bold; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    .btn-submit:hover { background: #219150; transform: translateY(-2px); }
    .alert-msg { background: rgba(255, 255, 255, 0.8); color: #2c3e50; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; border-left: 5px solid #3498db; font-weight: bold; }
</style>

<div class="main-container">
    <div class="glass-card">
        <h2 style="text-align: center; color: #2c3e50; margin-top: 0;">📝 Registrar Movimiento</h2>

        <?php if ($mensaje): ?>
            <div class="alert-msg"><strong><?= $mensaje ?></strong></div>
        <?php endif; ?>

        <form method="POST" onsubmit="return validarSalida()">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="form-group">
                <label>Medicamento:</label>
                <select name="medicamento_id" id="med_select" class="form-control" required>
                    <option value="">-- Seleccione un medicamento --</option>
                    <?php foreach ($medicamentos as $med): ?>
                        <option value="<?= $med['id'] ?>" data-stock="<?= $med['stock'] ?>">
                            <?= htmlspecialchars($med['nombre']) ?> (Actual: <?= $med['stock'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Tipo de Movimiento:</label>
                <select name="tipo" id="tipo_mov" class="form-control" required>
                    <option value="entrada">➕ Entrada</option>
                    <option value="salida">➖ Salida</option>
                </select>
            </div>

            <div class="form-group">
                <label>Cantidad:</label>
                <input type="number" name="cantidad" id="cantidad" class="form-control" min="1" required>
            </div>

            <div class="form-group">
                <label>Observación:</label>
                <textarea name="observacion" class="form-control" rows="3"></textarea>
            </div>

            <button type="submit" class="btn-submit">✅ Registrar Movimiento</button>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="../dashboard.php" style="color: #2c3e50; text-decoration: none; font-weight: bold;">← Volver al Panel</a>
            </div>
        </form>
    </div>
</div>

<script>
function validarSalida() {
    const tipo = document.getElementById('tipo_mov').value;
    const select = document.getElementById('med_select');
    if(!select.selectedIndex) return false;
    const stockActual = parseInt(select.options[select.selectedIndex].dataset.stock);
    const cantidad = parseInt(document.getElementById('cantidad').value);

    if (tipo === 'salida' && cantidad > stockActual) {
        alert("¡Error! No hay suficiente stock. Disponible: " + stockActual);
        return false;
    }
    return true;
}
</script>

<footer style="position: fixed; bottom: 15px; left: 0; width: 100%; text-align: center; color: #2c3e50; font-size: 14px; font-weight: bold;">
    © <?= date('Y') ?> Farmacia SAHUM - Sistema de Inventario
</footer>