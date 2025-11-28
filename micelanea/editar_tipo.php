<?php
require_once 'config.php';

$conn = getConnection();
$error = '';
$tipo = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';

    if ($id <= 0 || $nombre === '') {
        $error = 'Datos inválidos para actualizar el tipo.';
    } else {
        $stmt = $conn->prepare('UPDATE tipos_producto SET nombre = ? WHERE id = ?');
        if ($stmt) {
            $stmt->bind_param('si', $nombre, $id);
            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header('Location: index.php?mensaje=' . urlencode('Tipo actualizado correctamente.'));
                exit();
            }
            $stmt->close();
            $error = 'No se pudo actualizar el tipo.';
        } else {
            $error = 'No se pudo preparar la actualización del tipo.';
        }
    }
}

$tipo_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($tipo_id > 0 && $tipo === null) {
    $stmt = $conn->prepare('SELECT * FROM tipos_producto WHERE id = ?');
    $stmt->bind_param('i', $tipo_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        $stmt->close();
        $conn->close();
        header('Location: index.php?error=' . urlencode('Tipo no encontrado.'));
        exit();
    }
    $tipo = $result->fetch_assoc();
    $stmt->close();
} elseif ($tipo === null) {
    $conn->close();
    header('Location: index.php?error=' . urlencode('ID de tipo inválido.'));
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tipo de Producto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
        }
        .btn-primary {
            background: #667eea;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Editar Tipo de Producto</h1>
        <?php if ($error !== ''): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="editar_tipo.php?id=<?php echo $tipo['id']; ?>">
            <input type="hidden" name="id" value="<?php echo $tipo['id']; ?>">
            <div class="form-group">
                <label for="nombre">Nombre del tipo:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($tipo['nombre']); ?>" required>
            </div>
            <div class="actions">
                <a href="index.php" class="btn btn-secondary" style="text-decoration:none; text-align:center;">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</body>
</html>

