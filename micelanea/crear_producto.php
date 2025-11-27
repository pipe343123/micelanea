<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $tipo_id = $_POST['tipo_id'];
    
    $conn = getConnection();
    
    $stmt = $conn->prepare("INSERT INTO productos (nombre, precio, stock, tipo_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdii", $nombre, $precio, $stock, $tipo_id);
    
    if ($stmt->execute()) {
        header("Location: index.php?mensaje=Producto creado exitosamente");
        exit();
    } else {
        header("Location: index.php?error=Error al crear el producto");
        exit();
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: index.php");
    exit();
}
?>

