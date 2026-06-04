<?php
require_once 'includes/auth.php';
requireLogin();
?>
<?php include 'includes/header.php'; ?>

<style>
    body {
        /* Usamos !important para asegurar que el fondo se vea sobre cualquier estilo del header */
        background-image: linear-gradient(rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.8)), url('https://i.imgur.com/ArjbuJ2.jpeg') !important;
        background-size: cover !important;
        background-position: center !important;
        background-attachment: fixed !important;
        background-repeat: no-repeat !important;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
</style>

<!-- Contenedor Principal -->
<div style="
    display: flex; 
    flex-direction: column; 
    justify-content: center; 
    align-items: center; 
    min-height: 80vh; 
    text-align: center;
    flex: 1;
">

    <!-- RECUADRO CON TRANSPARENCIA (Efecto Cristal) -->
    <div style="
        background: rgba(255, 255, 255, 0.2); 
        backdrop-filter: blur(10px);          
        -webkit-backdrop-filter: blur(10px);   
        padding: 50px; 
        border-radius: 20px; 
        border: 1px solid rgba(255, 255, 255, 0.3); 
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);  
        max-width: 1000px;
        width: 90%;
    ">
        
        <h2 style="margin-bottom: 40px; color: #1a252f; text-shadow: 1px 1px 2px rgba(255,255,255,0.5);">
            Bienvenido <?= htmlspecialchars($_SESSION['nombre']) ?> <br>
            <span style="font-size: 0.8em; color: #2c3e50;">Sistema de Gestión Farmacéutica</span>
        </h2>

        <!-- Contenedor de Botones - Botones Grandes -->
        <div style="display: flex; gap: 25px; justify-content: center; flex-wrap: wrap; margin-bottom: 40px;">
            <a href="inventario/index.php" style="padding:20px 35px; background:#3498db; color:white; text-decoration:none; border-radius:12px; font-size: 18px; font-weight:bold; box-shadow: 0 6px 10px rgba(0,0,0,0.15);">Ver Inventario</a>
            
            <a href="inventario/agregar.php" style="padding:20px 35px; background:#27ae60; color:white; text-decoration:none; border-radius:12px; font-size: 18px; font-weight:bold; box-shadow: 0 6px 10px rgba(0,0,0,0.15);">➕ Agregar</a>
            
            <a href="inventario/categorias.php" style="padding:20px 35px; background:#e67e22; color:white; text-decoration:none; border-radius:12px; font-size: 18px; font-weight:bold; box-shadow: 0 6px 10px rgba(0,0,0,0.15);">🏷️ Categorías</a>
            
            <?php if (isAdmin()): ?>
                <a href="admin/registro_usuario.php" style="padding:20px 35px; background:#e74c3c; color:white; text-decoration:none; border-radius:12px; font-size: 18px; font-weight:bold; box-shadow: 0 6px 10px rgba(0,0,0,0.15);">👤 Registrar Personal</a>
            <?php endif; ?>
            
            <a href="inventario/registrar_movimiento.php" style="padding:20px 35px; background:#9b59b6; color:white; text-decoration:none; border-radius:12px; font-size: 18px; font-weight:bold; box-shadow: 0 6px 10px rgba(0,0,0,0.15);">📝 Movimientos</a>
            
            <a href="inventario/movimientos.php" style="padding:20px 35px; background:#f39c12; color:white; text-decoration:none; border-radius:12px; font-size: 18px; font-weight:bold; box-shadow: 0 6px 10px rgba(0,0,0,0.15);">📊 Historial</a>
        </div>

        <p><a href="logout.php" style="color: #c0392b; text-decoration: none; font-weight: bold; background: rgba(255,255,255,0.5); padding: 10px 20px; border-radius: 20px;">✕ Cerrar Sesión</a></p>
    </div>

</div>

<!-- Pie de página -->
<footer style="text-align: center; color: #2c3e50; font-size: 14px; font-weight: bold; margin-top: auto; padding-bottom: 20px;">
    © <?= date('Y') ?> Farmacia SAHUM - Sistema de Inventario
</footer>