<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/database.php';

// Generar token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("❌ Error de seguridad.");
    }

    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nombre   = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $cedula   = trim($_POST['cedula']);
    $telefono = trim($_POST['telefono']);
    $email    = trim($_POST['email']);
    $rol      = $_POST['rol'];

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, nombre, apellido, cedula, telefono, email, rol) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $password, $nombre, $apellido, $cedula, $telefono, $email, $rol]);
        $mensaje = "✅ Usuario <strong>$nombre</strong> registrado exitosamente.";
    } catch (Exception $e) {
        $mensaje = "❌ Error: " . $e->getMessage();
    }
}
?>

<?php include '../includes/header.php'; ?>

<style>
    body { background-image: linear-gradient(rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.5)), url('https://i.imgur.com/ArjbuJ2.jpeg'); background-size: cover; background-position: center; background-attachment: fixed; min-height: 100vh; display: flex; flex-direction: column; }
    .glass-card { background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(15px); padding: 40px; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.3); max-width: 600px; margin: 50px auto; }
    .form-control { width: 100%; padding: 10px; margin-bottom: 10px; border-radius: 8px; border: 1px solid #ccc; box-sizing: border-box; }
</style>

<div class="glass-card">
    <h2 style="text-align:center;">👤 Registrar Nuevo Personal</h2>
    <?php if ($mensaje): ?><div style="text-align:center; padding:10px; background:#d4edda; margin-bottom:10px;"><?= $mensaje ?></div><?php endif; ?>
    
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
        <input type="text" name="apellido" class="form-control" placeholder="Apellido" required>
        <input type="text" name="cedula" class="form-control" placeholder="Cédula" required>
        <input type="text" name="telefono" class="form-control" placeholder="Teléfono">
        <input type="email" name="email" class="form-control" placeholder="Correo Electrónico">
        <input type="text" name="username" class="form-control" placeholder="Nombre de Usuario" required>
        <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
        
        <label>Rol:</label>
        <select name="rol" class="form-control">
            <option value="visor">Visor (Solo ver)</option>
            <option value="farmaceutico">Farmacéutico (Registrar movimientos)</option>
            <option value="admin">Administrador (Todo)</option>
        </select>
        
        <button type="submit" style="width:100%; padding:15px; background:#2c3e50; color:white; border:none; border-radius:8px; cursor:pointer;">💾 Registrar Usuario</button>
        <a href="../dashboard.php" style="display:block; text-align:center; margin-top:15px;">← Volver al Panel</a>
    </form>
</div>

<footer style="text-align:center; padding:20px; font-weight:bold;">© 2026 Farmacia SAHUM - Sistema de Inventario </footer>