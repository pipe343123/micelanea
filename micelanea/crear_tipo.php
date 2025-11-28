<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';

    if ($nombre === '') {
        header('Location: index.php?error=' . urlencode('El nombre del tipo es obligatorio.'));
        exit();
    }

    $conn = getConnection();
    $stmt = $conn->prepare('INSERT INTO tipos_producto (nombre) VALUES (?)');

    if (!$stmt) {
        $conn->close();
        header('Location: index.php?error=' . urlencode('No se pudo preparar la creación del tipo.'));
        exit();
    }

    $stmt->bind_param('s', $nombre);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header('Location: index.php?mensaje=' . urlencode('Tipo de producto creado correctamente.'));
        exit();
    }

    $stmt->close();
    $conn->close();
    header('Location: index.php?error=' . urlencode('Ocurrió un error al crear el tipo.'));
    exit();
}

header('Location: index.php');
exit();

