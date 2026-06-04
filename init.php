<?php
// init.php - El puente del sistema
// 1. Carga automática de todas las clases POO (Medicamento, etc.)
require_once __DIR__ . '/vendor/autoload.php';

// 2. Carga de configuración de base de datos
require_once __DIR__ . '/config/database.php';

// 3. Carga de autenticación
require_once __DIR__ . '/includes/auth.php';
?>