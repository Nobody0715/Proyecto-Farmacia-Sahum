<?php 
require_once 'config/database.php'; 
session_start(); 

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<?php include 'includes/header.php'; ?>

<style>
    :root {
        --dark-text: #000000;
        --header-bg: #2c3e50;
        --row-alt: #f8f9fa;
    }

    body {
        background-image: linear-gradient(rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0.4)), url('https://i.imgur.com/ArjbuJ2.jpeg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        color: var(--dark-text);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .wrapper { flex: 1; }

    .menu-admin-toggle {
        position: fixed; top: 20px; right: 20px; z-index: 1001; cursor: pointer;
        background: var(--header-bg); padding: 12px; border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3); display: flex; flex-direction: column; gap: 4px;
    }
    .menu-admin-toggle span { display: block; width: 22px; height: 3px; background: white; border-radius: 2px; }

    .login-dropdown {
        position: fixed; top: 80px; right: -400px; width: 320px; z-index: 1000; transition: 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .login-dropdown.active { right: 20px; }

    .inventory-card {
        max-width: 1250px;
        margin: 30px auto;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid #bdc3c7;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .sheet-title { padding: 20px; text-align: center; font-weight: 900; font-size: 24px; text-transform: uppercase; background: #2c3e50; color: white; }
    .sheet-sub-header { display: grid; grid-template-columns: 1fr 1fr; border-bottom: 2px solid var(--dark-text); background: #ecf0f1; }
    .info-cell { padding: 10px 30px; font-weight: bold; font-size: 16px; }
    
    .category-separator { background: var(--header-bg); color: white; text-align: center; padding: 10px; font-weight: bold; text-transform: uppercase; }

    .inventory-table { width: 100%; border-collapse: collapse; }
    .inventory-table thead th { background-color: var(--header-bg); color: white; padding: 12px; border: 1px solid #bdc3c7; font-size: 14px; }
    .inventory-table td { border: 1px solid #dcdde1; padding: 10px; font-size: 14px; }
    .inventory-table tbody tr:nth-child(even) { background-color: var(--row-alt); }

    .col-desc { width: 35%; font-weight: 600; }
    .col-pres { width: 10%; text-align: center; }
    .col-cant { width: 5%; text-align: center; font-weight: 800; font-size: 18px; }
    .stock-low { color: #e74c3c; font-weight: bold; }
    .stock-ok { color: #27ae60; font-weight: bold; }

    .search-container { max-width: 500px; margin: 20px auto; text-align: center; }
    .search-input { padding: 12px 20px; width: 80%; border-radius: 5px; border: 1px solid #ccc; font-size: 16px; }
</style>

<div class="wrapper">
    <div class="menu-admin-toggle" onclick="toggleLogin()"><span></span><span></span><span></span></div>

    <div id="loginPanel" class="login-dropdown">
        <div style="background: white; padding: 25px; border-radius: 15px; border: 2px solid var(--dark-text); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <h3 style="margin-top:0; text-align:center;"> Login</h3>
            <form method="POST" action="login.php">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="text" name="username" placeholder="Usuario" required style="width:100%; padding:10px; margin-bottom:10px;">
                <input type="password" name="password" placeholder="Contraseña" required style="width:100%; padding:10px; margin-bottom:15px;">
                <button type="submit" style="width:100%; padding:12px; background:var(--dark-text); color:white; border:none; border-radius:5px; font-weight:bold; cursor:pointer;">Entrar</button>
            </form>
        </div>
    </div>

    <div class="search-container">
        <form method="GET">
            <input type="text" name="search" class="search-input" placeholder="🔍 Buscar medicamento o categoría..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        </form>
    </div>

    <div class="inventory-card">
        <div class="sheet-title">Inventario Diario de Medicamentos Farmacia SAHUM</div>
        <div class="sheet-sub-header">
            <div class="info-cell">📅 FECHA: <span id="fechaActual"><?= date('d / m / Y') ?></span></div>
            <div class="info-cell">🕒 HORA: <span id="relojActual"><?= date('h:i:s A') ?></span></div>
        </div>
        <div class="category-separator">Medicamentos y Ampollas Varias</div>

        <?php
        $search = "%" . ($_GET['search'] ?? '') . "%";
        $stmt = $pdo->prepare("SELECT m.* FROM medicamentos m 
                               LEFT JOIN categorias c ON m.categoria_id = c.id 
                               WHERE (m.nombre LIKE ? OR c.nombre LIKE ?) 
                               AND m.deleted_at IS NULL 
                               ORDER BY m.nombre");
        $stmt->execute([$search, $search]);
        $meds = $stmt->fetchAll();

        $total = count($meds);
        $half = ceil($total / 2);
        $leftCol = array_slice($meds, 0, $half);
        $rightCol = array_slice($meds, $half);
        ?>

        <table class="inventory-table">
            <thead>
                <tr>
                    <th>DESCRIPCIÓN</th><th>PRESENTACION</th><th>CANTIDAD</th>
                    <th style="border-left: 3px solid #bdc3c7;">DESCRIPCIÓN</th><th>PRESENTACION</th><th>CANTIDAD</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 0; $i < $half; $i++): ?>
                    <tr>
                        <td class="col-desc"><?= htmlspecialchars($leftCol[$i]['nombre']) ?></td>
                        <td class="col-pres"><?= htmlspecialchars($leftCol[$i]['presentacion'] ?? 'Ampolla') ?></td>
                        <td class="col-cant <?= ($leftCol[$i]['stock'] <= $leftCol[$i]['stock_minimo']) ? 'stock-low' : 'stock-ok' ?>">
                            <?= $leftCol[$i]['stock'] ?>
                        </td>
                        <?php if (isset($rightCol[$i])): ?>
                            <td class="col-desc" style="border-left: 3px solid #bdc3c7;"><?= htmlspecialchars($rightCol[$i]['nombre']) ?></td>
                            <td class="col-pres"><?= htmlspecialchars($rightCol[$i]['presentacion'] ?? 'Ampolla') ?></td>
                            <td class="col-cant <?= ($rightCol[$i]['stock'] <= $rightCol[$i]['stock_minimo']) ? 'stock-low' : 'stock-ok' ?>">
                                <?= $rightCol[$i]['stock'] ?>
                            </td>
                        <?php else: ?>
                            <td style="border-left: 3px solid #bdc3c7;"></td><td></td><td></td>
                        <?php endif; ?>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function actualizarReloj() {
        const ahora = new Date();
        document.getElementById('relojActual').textContent = ahora.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
    }
    setInterval(actualizarReloj, 1000);
    function toggleLogin() { document.getElementById('loginPanel').classList.toggle('active'); }
</script>

<footer style="text-align:center; padding:30px; font-weight:bold; color:var(--dark-text);">
    © <?= date('Y') ?> FARMACIA SAHUM - Hospital Universitario de Maracaibo
</footer>