<?php
include('conexion.php');

// 1. Lógica para AGREGAR
if (isset($_POST['agregar'])) {
    $nombre = $_POST['nombre'];
    $cantidad = $_POST['cantidad'];
    $cant_stock = $_POST['cantidad'];
    $fecha = $_POST['fecha'];

    $stmt = mysqli_prepare($conn, "INSERT INTO productos (nombre, cantidad_ingreso, stock_actual, fecha_ingreso) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "siis", $nombre, $cantidad, $cant_stock, $fecha);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php");
    }
    mysqli_stmt_close($stmt);
}

// 2. Lógica para EDITAR ÚNICAMENTE EL STOCK
if (isset($_POST['editar_stock'])) {
    $id = $_POST['id_editar'];
    $nuevo_stock = $_POST['nuevo_stock'];

    $stmt = mysqli_prepare($conn, "UPDATE productos SET stock_actual = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $nuevo_stock, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php?status=updated");
    }
    mysqli_stmt_close($stmt);
}

// 3. Lógica para ELIMINAR
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];

    $stmt = mysqli_prepare($conn, "DELETE FROM productos WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php");
    }
    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>