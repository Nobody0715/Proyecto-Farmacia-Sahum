<?php
require_once '../init.php';
use App\Medicamento;
use App\Categoria;

requireLogin();
requireOperator();

$medModel = new Medicamento($pdo);
$catModel = new Categoria($pdo);
$categorias = $catModel->listar();
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("❌ Error de seguridad.");
    }

    $nombre = trim($_POST['nombre']);
    $stock_nuevo = (int)$_POST['stock'];
    $cat_id = (int)$_POST['categoria_id'];

    if ($nombre != '' && $stock_nuevo > 0 && $cat_id > 0) {
        try {
            $pdo->beginTransaction();
            $existente = $medModel->buscarExistente($nombre);

            if ($existente) {
                $medModel->actualizar($existente['id'], $nombre, $_POST['presentacion'], $_POST['concentracion'], $cat_id, $existente['stock'] + $stock_nuevo, $_POST['stock_minimo']);
                $medModel->registrarMovimiento($existente['id'], 'entrada', $stock_nuevo, $_SESSION['user_id'], 'Entrada adicional - Existente');
                $mensaje = "✅ Stock actualizado.";
            } else {
                $medModel->nombre = $nombre;
                $medModel->presentacion = $_POST['presentacion'];
                $medModel->concentracion = $_POST['concentracion'];
                $medModel->categoria_id = $cat_id;
                $medModel->stock = $stock_nuevo;
                $medModel->stock_minimo = $_POST['stock_minimo'];
                $medModel->crear();
                $mensaje = "✅ Nuevo medicamento <strong>$nombre</strong> creado.";
            }
            $pdo->commit();
        } catch (\Exception $e) {
            $pdo->rollBack();
            $mensaje = "❌ Error: " . $e->getMessage();
        }
    } else {
        $mensaje = "❌ Datos incompletos.";
    }
}

// CARGAMOS LA VISTA
include '../views/inventario/agregar_view.php';
?>