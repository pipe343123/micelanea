<?php
require_once 'config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php?error=' . urlencode('ID de tipo inválido.'));
    exit();
}

$conn = getConnection();

// Verificar si el tipo existe
$check_stmt = $conn->prepare('SELECT id FROM tipos_producto WHERE id = ?');
$check_stmt->bind_param('i', $id);
$check_stmt->execute();
$result = $check_stmt->get_result();
if ($result->num_rows === 0) {
    $check_stmt->close();
    $conn->close();
    header('Location: index.php?error=' . urlencode('El tipo no existe.'));
    exit();
}
$check_stmt->close();

// Verificar si hay productos asociados
$product_stmt = $conn->prepare('SELECT COUNT(*) as total FROM productos WHERE tipo_id = ?');
$product_stmt->bind_param('i', $id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();
$product_count = $product_result->fetch_assoc()['total'] ?? 0;
$product_stmt->close();

if ($product_count > 0) {
    $conn->close();
    header('Location: index.php?error=' . urlencode('No se puede eliminar el tipo porque hay productos asociados.'));
    exit();
}

$delete_stmt = $conn->prepare('DELETE FROM tipos_producto WHERE id = ?');
$delete_stmt->bind_param('i', $id);

if ($delete_stmt->execute()) {
    $delete_stmt->close();
    $conn->close();
    header('Location: index.php?mensaje=' . urlencode('Tipo eliminado correctamente.'));
    exit();
}

$delete_stmt->close();
$conn->close();
header('Location: index.php?error=' . urlencode('No se pudo eliminar el tipo.'));
exit();

