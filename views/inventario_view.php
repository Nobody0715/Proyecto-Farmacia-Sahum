<div class="wrapper">
    <div class="glass-container">
        <h2 style="color: #2c3e50; text-align:center;">📋 Inventario Actual</h2>

        <form method="GET" style="margin-bottom: 25px; text-align: center; background: rgba(255,255,255,0.3); padding: 20px; border-radius: 10px;">
            <input type="text" name="search" placeholder="Buscar medicamento..." value="<?= htmlspecialchars($search) ?>" style="padding:12px; width:300px; font-size:16px; border-radius: 5px; border: 1px solid #ccc;">
            <button type="submit" style="padding:12px 20px; background:#3498db; color:white; border:none; cursor:pointer; border-radius: 5px; font-weight:bold;">🔍 Buscar</button>
            <a href="index.php" style="padding:12px 15px; background:#95a5a6; color:white; text-decoration:none; margin-left:5px; border-radius: 5px;">Limpiar</a>
            
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