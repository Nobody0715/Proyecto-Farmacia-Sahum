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
        display: flex; 
        flex-direction: column; 
        min-height: 100vh; 
    }
    
    .wrapper { flex: 1; display: flex; justify-content: center; align-items: center; padding: 20px; }

    .glass-card { 
        background: rgba(255, 255, 255, 0.25); 
        backdrop-filter: blur(15px); 
        -webkit-backdrop-filter: blur(15px); 
        padding: 40px; 
        border-radius: 20px; 
        border: 1px solid rgba(255, 255, 255, 0.3); 
        max-width: 500px; 
        width: 100%; 
        box-shadow: 0 8px 32px rgba(0,0,0,0.15); 
    }

    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; color: #2c3e50; font-weight: bold; }
    .form-control { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(0,0,0,0.1); background: rgba(255,255,255,0.85); font-size: 16px; box-sizing: border-box; }
    .btn-save { width: 100%; padding: 15px; background: #27ae60; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; }
    .btn-save:hover { background: #219150; }
    .alert-success { background: rgba(212, 237, 218, 0.9); color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
</style>

<div class="wrapper">
    <div class="glass-card">
        <h2 style="text-align: center; color: #2c3e50; margin-top: 0;">➕ Agregar / Actualizar</h2>

        <?php if ($mensaje): ?><div class="alert-success"><?= $mensaje ?></div><?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="nombre" class="form-control" required placeholder="Ej: Paracetamol">
            </div>

            <div class="form-group">
                <label>Presentación:</label>
                <select name="presentacion" class="form-control" required>
                    <?php 
                    $op = ['Ampolla', 'Bolsa', 'Crema', 'Frasco', 'Gotas', 'Jarabe', 'Ovulo', 'Puff', 'Solución', 'Spray', 'Suspensión','Kit', 'Tableta', 'Tab-Vaginal', 'Viales'];
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
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="../dashboard.php" style="color: #2c3e50; text-decoration: none; font-weight: bold;">← Volver al Panel</a>
            </div>
        </form>
    </div>
</div>

<footer style="text-align: center; color: #2c3e50; font-size: 14px; font-weight: bold; padding: 20px;">
    © <?= date('Y') ?> Farmacia SAHUM - Sistema de Inventario
</footer>

<?php include '../includes/footer.php'; ?>