<?php include '../includes/header.php'; ?>
<style>
    body { background-image: linear-gradient(rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.5)), url('https://i.imgur.com/ArjbuJ2.jpeg'); background-size: cover; background-position: center; background-attachment: fixed; min-height: 100vh; display: flex; flex-direction: column; }
    .main-container { flex: 1; display: flex; justify-content: center; align-items: center; padding: 20px; }
    .glass-card { background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(15px); padding: 40px; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.3); max-width: 550px; width: 100%; box-shadow: 0 8px 32px rgba(0,0,0,0.15); }
    .form-control { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.1); background: rgba(255,255,255,0.85); box-sizing: border-box; margin-bottom: 15px; }
    .btn-submit { width: 100%; padding: 15px; background: #27ae60; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
    .alert-msg { background: rgba(255, 255, 255, 0.8); padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; border-left: 5px solid #3498db; font-weight: bold; }
</style>

<div class="main-container">
    <div class="glass-card">
        <h2 style="text-align:center;">📝 Registrar Movimiento</h2>
        <?php if ($mensaje): ?><div class="alert-msg"><?= $mensaje ?></div><?php endif; ?>
        
        <form method="POST" onsubmit="return validarSalida()">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <label>Medicamento:</label>
            <select name="medicamento_id" id="med_select" class="form-control" required>
                <option value="">-- Seleccione un medicamento --</option>
                <?php foreach ($medicamentos as $med): ?>
                    <option value="<?= $med['id'] ?>" data-stock="<?= $med['stock'] ?>">
                        <?= htmlspecialchars($med['nombre']) ?> (Actual: <?= $med['stock'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label>Tipo:</label>
            <select name="tipo" id="tipo_mov" class="form-control" required>
                <option value="entrada">➕ Entrada</option>
                <option value="salida">➖ Salida</option>
            </select>
            
            <label>Cantidad:</label>
            <input type="number" name="cantidad" id="cantidad" class="form-control" min="1" required>
            
            <label>Observación:</label>
            <textarea name="observacion" class="form-control" rows="3"></textarea>
            
            <button type="submit" class="btn-submit"> Registrar Movimiento</button>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="../dashboard.php" style="color:#2c3e50; font-weight:bold; text-decoration:none;">← Volver al Panel</a>
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
        alert("¡Error! Stock insuficiente. Disponible: " + stockActual);
        return false;
    }
    return true;
}
</script>

<footer style="text-align: center; color: #2c3e50; font-size: 14px; font-weight: bold; padding: 20px;">
    © <?= date('Y') ?> Farmacia SAHUM - Sistema de Inventario
</footer>

<?php include '../includes/footer.php'; ?>