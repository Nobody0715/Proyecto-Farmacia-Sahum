<?php
// logout.php
session_start();
session_destroy(); // Destruye toda la sesión

// Redirige a la página principal (donde está el inventario público)
header('Location: index.php');
exit;
