<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $tipo_id = intval($_POST['tipo_id']);
    
    if (empty($nombre) || $precio < 0 || $stock < 0 || $tipo_id <= 0) {
        header("Location: index.php?error=Datos inválidos. Por favor verifique la información.");
        exit();
    }
    
    $conn = getConnection();
    
    $stmt = $conn->prepare("UPDATE productos SET nombre = ?, precio = ?, stock = ?, tipo_id = ? WHERE id = ?");
    $stmt->bind_param("sdiii", $nombre, $precio, $stock, $tipo_id, $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            header("Location: index.php?mensaje=Producto modificado exitosamente");
        } else {
            header("Location: index.php?error=No se realizaron cambios. Verifique que el producto existe.");
        }
        exit();
    } else {
        header("Location: index.php?error=Error al modificar el producto: " . $conn->error);
        exit();
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: index.php");
    exit();
}
?>

