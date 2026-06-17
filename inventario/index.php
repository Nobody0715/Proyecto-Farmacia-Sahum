<?php
require_once '../init.php'; 
use App\Medicamento; 

$medModel = new Medicamento($pdo);
$search = $_GET['search'] ?? '';
$bajo_stock = isset($_GET['bajo_stock']);

$medicamentos = $medModel->buscar($search, $bajo_stock);
?>

<?php include '../includes/header.php'; ?>

<style>
    body { background-image: linear-gradient(rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.5)), url('https://i.imgur.com/ArjbuJ2.jpeg'); background-size: cover; background-position: center; background-attachment: fixed; background-repeat: no-repeat; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; display: flex; flex-direction: column; min-height: 100vh; }
    .wrapper { flex: 1; }
    .glass-container { background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 15px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); padding: 30px; margin: 40px auto; max-width: 1200px; width: 95%; }
    .styled-table { width: 100%; border-collapse: collapse; background: rgba(255, 255, 255, 0.6); border-radius: 10px; overflow: hidden; }
    .styled-table thead tr { background-color: rgba(44, 62, 80, 0.9); color: #ffffff; text-align: left; }
    .styled-table th, .styled-table td { padding: 12px 15px; border-bottom: 1px solid rgba(0, 0, 0, 0.05); }
    .btn-back { display: inline-block; margin-top: 20px; text-decoration: none; color: #2c3e50; font-weight: bold; background: rgba(255, 255, 255, 0.5); padding: 10px 20px; border-radius: 20px; }
    .btn-back:hover { background: rgba(255, 255, 255, 0.8); }
</style>

<?php include '../views/inventario_view.php'; ?>

<footer style="text-align: center; color: #2c3e50; font-size: 14px; font-weight: bold; padding: 20px;">
    © <?= date('Y') ?> Farmacia SAHUM - Sistema de Inventario
</footer>

<?php include '../includes/footer.php'; ?>