<?php
require_once 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $conn = getConnection();
    
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: index.php?mensaje=Producto eliminado exitosamente");
        exit();
    } else {
        header("Location: index.php?error=Error al eliminar el producto");
        exit();
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: index.php");
    exit();
}
?>

