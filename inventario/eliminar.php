<?php
require_once '../init.php';
use App\Medicamento;

requireLogin();
requireAdmin();

$medModel = new Medicamento($pdo);
$id = (int)$_GET['id'] ?? 0;
$mensaje = '';

$med = ($id > 0) ? $medModel->obtenerPorId($id) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0 && $med) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("❌ Error de seguridad.");
    }

    $observacion = trim($_POST['observacion']);
    if (empty($observacion)) {
        $mensaje = "❌ Debes escribir un motivo de eliminación.";
    } else {
        try {
            $medModel->eliminarLogico($id, $_SESSION['user_id'], $observacion, $med['stock']);
            header('Location: ../inventario/index.php?msg=eliminado');
            exit;
        } catch (\Exception $e) {
            $mensaje = "❌ Error: " . $e->getMessage();
        }
    }
}

include '../views/inventario/eliminar_view.php';
?>