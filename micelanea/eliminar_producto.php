<?php
require_once 'config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    if ($id <= 0) {
        header("Location: index.php?error=ID de producto inválido");
        exit();
    }
    
    $conn = getConnection();
    
    // Verificar que el producto existe antes de intentar eliminarlo
    $check_stmt = $conn->prepare("SELECT id FROM productos WHERE id = ?");
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows === 0) {
        $check_stmt->close();
        $conn->close();
        header("Location: index.php?error=El producto no existe");
        exit();
    }
    $check_stmt->close();
    
    // Eliminar el producto
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $stmt->close();
            $conn->close();
            header("Location: index.php?mensaje=Producto eliminado exitosamente");
            exit();
        } else {
            $stmt->close();
            $conn->close();
            header("Location: index.php?error=No se pudo eliminar el producto. Puede que ya haya sido eliminado.");
            exit();
        }
    } else {
        $error_msg = "Error al eliminar el producto: " . $conn->error;
        $stmt->close();
        $conn->close();
        header("Location: index.php?error=" . urlencode($error_msg));
        exit();
    }
} else {
    header("Location: index.php?error=No se proporcionó un ID de producto");
    exit();
}
?>

