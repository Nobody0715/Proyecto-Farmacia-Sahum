<?php
require_once '../includes/auth.php';
requireLogin();
requireAdmin();
require_once '../config/database.php';

// Generar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("❌ Error de seguridad.");
    }
    
    $nombre = trim($_POST['nombre']);
    if (!empty($nombre)) {
        $stmt = $pdo->prepare("INSERT INTO categorias (nombre) VALUES (?)");
        $stmt->execute([$nombre]);
        $mensaje = "✅ Categoría agregada con éxito.";
    }
}

$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nombre ASC");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

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
        min-height: 100vh; /* Cuerpo ocupa toda la pantalla */
    }

    .wrapper { flex: 1; } /* Empuja el footer hacia abajo */

    .glass-container { 
        background: rgba(255, 255, 255, 0.25); 
        backdrop-filter: blur(15px); 
        border-radius: 20px; 
        padding: 40px; 
        margin: 40px auto; 
        max-width: 600px; 
        width: 90%; 
        box-shadow: 0 8px 32px rgba(0,0,0,0.15); 
    }

    .styled-table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; margin-top: 20px; }
    .styled-table th { background: #34495e; color: white; padding: 12px; text-align: left; }
    .styled-table td { padding: 12px; border-bottom: 1px solid #ddd; }
</style>

<div class="wrapper">
    <div class="glass-container">
        <h2 style="text-align:center; color:#2c3e50;">🏷️ Gestión de Categorías</h2>
        
        <?php if ($mensaje): ?><div style="text-align:center; color:green; font-weight:bold;"><?= $mensaje ?></div><?php endif; ?>

        <form method="POST" style="text-align:center; margin-bottom: 30px;">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="text" name="nombre" placeholder="Nombre de nueva categoría" required style="padding:10px; width:60%; border-radius:5px; border:1px solid #ccc;">
            <button type="submit" style="padding:10px 20px; background:#3498db; color:white; border:none; cursor:pointer; border-radius:5px;">➕ Agregar</button>
        </form>

        <table class="styled-table">
            <thead><tr><th>ID</th><th>Nombre</th></tr></thead>
            <tbody>
                <?php foreach ($categorias as $cat): ?>
                    <tr>
                        <td><?= $cat['id'] ?></td>
                        <td><?= htmlspecialchars($cat['nombre']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="text-align:center; margin-top:20px;">
            <a href="../dashboard.php" style="color: #2c3e50; font-weight:bold; text-decoration:none;">← Volver al Panel</a>
        </div>
    </div>
</div>

<footer style="text-align:center; padding:20px; font-weight:bold; color:#2c3e50;">
    © <?= date('Y') ?> Farmacia SAHUM - Sistema de Inventario
</footer>

<?php include '../includes/footer.php'; ?>