<?php
require_once '../init.php';
use App\Categoria;
requireAdmin();

$catModel = new Categoria($pdo);
$id = (int)$_GET['id'] ?? 0;

if ($id > 0) {
    $catModel->eliminar($id);
}
header('Location: categorias.php?msg=eliminado');
?>