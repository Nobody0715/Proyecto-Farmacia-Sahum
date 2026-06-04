<?php
require_once 'config/database.php';
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validar Token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("❌ Error de seguridad.");
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // CORRECCIÓN: Quitamos el "AND rol = 'admin'" para que pueda entrar cualquier rol registrado
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['rol'] = $user['rol']; // Ahora guardamos el rol real del usuario
        
        header('Location: dashboard.php');
        exit;
    } else {
        sleep(1);
        header('Location: index.php?error=1');
        exit;
    }
}
header('Location: index.php');
exit;