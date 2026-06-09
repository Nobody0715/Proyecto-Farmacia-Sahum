<?php
require_once '../init.php';
use App\Medicamento;

requireLogin();
requireOperator();

$medModel = new Medicamento($pdo);
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("❌ Error de seguridad.");
    }

    try {
        $med = $medModel->obtenerPorId((int)$_POST['medicamento_id']);
        $medModel->aplicarMovimiento(
            (int)$_POST['medicamento_id'], 
            $_POST['tipo'], 
            (int)$_POST['cantidad'], 
            $_SESSION['user_id'], 
            trim($_POST['observacion']),
            (int)$med['stock']
        );
        $mensaje = "✅ Movimiento registrado correctamente";
    } catch (\Exception $e) {
        $mensaje = "❌ Error: " . $e->getMessage();
    }
}

$medicamentos = $medModel->buscar();
include '../views/inventario/movimiento_view.php';
?>