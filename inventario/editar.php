<?php
require_once '../init.php';
use App\Medicamento;
use App\Categoria;

requireLogin();
requireAdmin();

$medModel = new Medicamento($pdo);
$catModel = new Categoria($pdo);

$id = (int)$_GET['id'] ?? 0;
if ($id <= 0) die("ID inválido");

$med = $medModel->obtenerPorId($id);
if (!$med) die("Medicamento no encontrado");

$categorias = $catModel->listar();
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("❌ Error de seguridad.");
    }

    if ($medModel->actualizar($id, $_POST['nombre'], $_POST['presentacion'], $_POST['concentracion'], (int)$_POST['categoria_id'], (int)$_POST['stock'], (int)$_POST['stock_minimo'])) {
        $mensaje = "✅ Medicamento actualizado correctamente";
        $med = $medModel->obtenerPorId($id); // Refrescar datos
    } else {
        $mensaje = "❌ Error al actualizar.";
    }
}

include '../views/inventario/editar_view.php';
?>