<?php
require_once 'config.php';

// Obtener todos los productos con sus tipos
$conn = getConnection();
$sql = "SELECT p.id, p.nombre, p.precio, p.stock, p.tipo_id, tp.nombre as tipo_nombre 
        FROM productos p 
        INNER JOIN tipos_producto tp ON p.tipo_id = tp.id 
        ORDER BY p.id DESC";
$result = $conn->query($sql);

// Obtener tipos de producto para el formulario
$sql_tipos = "SELECT * FROM tipos_producto";
$tipos_result = $conn->query($sql_tipos);
$tipos = [];

if ($tipos_result && $tipos_result->num_rows > 0) {
    while ($tipo = $tipos_result->fetch_assoc()) {
        $tipos[] = $tipo;
    }
}

$total_productos = $result ? $result->num_rows : 0;
$total_tipos = count($tipos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Miscelánea</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .summary-section {
            max-width: 1200px;
            margin: 0 auto 30px auto;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            border-radius: 12px;
            color: white;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .summary-section h1 {
            margin-bottom: 10px;
            text-align: center;
        }
        
        .summary-cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .summary-card {
            flex: 1;
            min-width: 200px;
            background: rgba(255,255,255,0.15);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            backdrop-filter: blur(4px);
        }
        
        .summary-card h3 {
            margin-bottom: 10px;
            font-size: 18px;
            letter-spacing: 1px;
        }
        
        .summary-card .count {
            font-size: 36px;
            font-weight: bold;
        }

        .summary-actions {
            margin-top: 25px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .summary-actions button {
            border: none;
            padding: 12px 25px;
            border-radius: 30px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            color: #333;
            background: #ffd166;
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
            transition: transform 0.2s, opacity 0.2s;
        }

        .summary-actions button.active {
            transform: translateY(-2px);
            opacity: 1;
        }

        .summary-actions button:not(.active) {
            opacity: 0.8;
        }

        .content-section {
            display: none;
        }

        .content-section.active {
            display: block;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto 30px auto;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        
        .module-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            color: #333;
        }
        
        .module-card h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
            letter-spacing: 1px;
        }
        
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
            box-shadow: 0 6px 15px rgba(102,126,234,0.3);
        }
        
        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-secondary {
            background: #ffd166;
            color: #333;
            box-shadow: 0 6px 15px rgba(255,209,102,0.3);
        }

        .btn-secondary:hover {
            background: #f5b947;
        }
        
        .btn-edit {
            background: #28a745;
            color: white;
            padding: 8px 18px;
            font-size: 13px;
            text-decoration: none;
            display: inline-block;
            border-radius: 6px;
        }
        
        .btn-edit:hover {
            background: #218838;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
            padding: 8px 18px;
            font-size: 13px;
            border-radius: 6px;
        }
        
        .btn-delete:hover {
            background: #c82333;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #667eea;
            color: white;
            font-weight: bold;
        }
        
        tr:hover {
            background: #f9f9f9;
        }
        
        .actions {
            display: flex;
            gap: 10px;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            overflow: auto;
        }
        
        .modal-content {
            background: white;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #000;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
        }
        
        .price {
            font-weight: bold;
            color: #28a745;
        }

    </style>
</head>
<body>
    <section class="summary-section">
        <h1>Misceláneas</h1>
        <div class="summary-cards">
            <div class="summary-card">
                <h3>Productos</h3>
                <div class="count"><?php echo $total_productos; ?></div>
            </div>
            <div class="summary-card">
                <h3>Tipos de Producto</h3>
                <div class="count"><?php echo $total_tipos; ?></div>
            </div>
        </div>
        <div class="summary-actions">
            <button type="button" id="btn-productos" class="active" onclick="showSection('productos')">Productos</button>
            <button type="button" id="btn-tipos" onclick="showSection('tipos')">Tipos de Producto</button>
        </div>
    </section>
    <div id="productos-section" class="content-section active">
        <div class="container">
            <div class="module-card">
                <h1>Gestión de Productos - Miscelánea</h1>
                
                <?php if (isset($_GET['mensaje'])): ?>
                    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                        <?php echo htmlspecialchars($_GET['mensaje']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="header-actions">
                    <a href="login.php" class="btn btn-primary">Login</a>
                    <div class="action-buttons">
                        <button class="btn btn-primary" onclick="openModal('create')">Nuevo Producto</button>
                        <button class="btn btn-secondary" type="button" onclick="openModal('type')">Nuevo Tipo</button>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Tipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                    <td class="price">$<?php echo number_format($row['precio'], 2, ',', '.'); ?></td>
                                    <td><?php echo $row['stock']; ?></td>
                                    <td><?php echo htmlspecialchars($row['tipo_nombre']); ?></td>
                                    <td class="actions">
                                        <a href="editar_producto.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">Editar</a>
                                        <button type="button" class="btn btn-delete" data-product-id="<?php echo $row['id']; ?>" data-product-name="<?php echo htmlspecialchars($row['nombre']); ?>">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No hay productos registrados</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="tipos-section" class="content-section">
        <div class="container">
            <div class="module-card">
                <h1>Gestión de Tipos de Producto</h1>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($tipos) > 0): ?>
                            <?php foreach ($tipos as $tipo): ?>
                                <tr>
                                    <td><?php echo $tipo['id']; ?></td>
                                    <td><?php echo htmlspecialchars($tipo['nombre']); ?></td>
                                    <td class="actions">
                                        <a href="editar_tipo.php?id=<?php echo $tipo['id']; ?>" class="btn btn-edit">Editar</a>
                                        <button type="button" class="btn btn-delete btn-delete-type" data-type-id="<?php echo $tipo['id']; ?>" data-type-name="<?php echo htmlspecialchars($tipo['nombre']); ?>">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align:center;">No hay tipos registrados</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal para Crear Producto -->
    <div id="createModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('create')">&times;</span>
            <h2>Nuevo Producto</h2>
            <form action="crear_producto.php" method="POST">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                
                <div class="form-group">
                    <label for="precio">Precio:</label>
                    <input type="number" id="precio" name="precio" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="stock">Stock:</label>
                    <input type="number" id="stock" name="stock" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="tipo_id">Tipo:</label>
                    <select id="tipo_id" name="tipo_id" required>
                        <option value="">Seleccione un tipo</option>
                        <?php foreach ($tipos as $tipo): ?>
                            <option value="<?php echo $tipo['id']; ?>"><?php echo htmlspecialchars($tipo['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeModal('create')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal para Crear Tipo de Producto -->
    <div id="typeModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('type')">&times;</span>
            <h2>Nuevo Tipo de Producto</h2>
            <form action="crear_tipo.php" method="POST">
                <div class="form-group">
                    <label for="nombre_tipo">Nombre del tipo:</label>
                    <input type="text" id="nombre_tipo" name="nombre" required>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeModal('type')">Cancelar</button>
                    <button type="submit" class="btn btn-secondary">Crear Tipo</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openModal(type) {
            document.getElementById(type + 'Modal').style.display = 'block';
        }
        
        function closeModal(type) {
            document.getElementById(type + 'Modal').style.display = 'none';
        }

        function showSection(section) {
            var productosSection = document.getElementById('productos-section');
            var tiposSection = document.getElementById('tipos-section');
            var btnProductos = document.getElementById('btn-productos');
            var btnTipos = document.getElementById('btn-tipos');

            if (section === 'productos') {
                productosSection.classList.add('active');
                tiposSection.classList.remove('active');
                btnProductos.classList.add('active');
                btnTipos.classList.remove('active');
            } else if (section === 'tipos') {
                tiposSection.classList.add('active');
                productosSection.classList.remove('active');
                btnTipos.classList.add('active');
                btnProductos.classList.remove('active');
            }
        }
        
        function confirmDelete(id, nombre) {
            try {
                if (confirm('¿Está seguro de eliminar el producto "' + nombre + '"?')) {
                    window.location.href = 'eliminar_producto.php?id=' + id;
                }
            } catch (error) {
                console.error('Error al eliminar producto:', error);
                alert('Error al intentar eliminar el producto. Por favor, intente nuevamente.');
            }
        }
        
        // Agregar event listeners a todos los botones de eliminar
        document.addEventListener('DOMContentLoaded', function() {
            showSection('productos');

            var deleteButtons = document.querySelectorAll('.btn-delete[data-product-id]');
            deleteButtons.forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    var productId = this.getAttribute('data-product-id');
                    var productName = this.getAttribute('data-product-name');
                    if (productId && productName) {
                        confirmDelete(productId, productName);
                    } else {
                        console.error('Error: No se encontraron los datos del producto');
                        alert('Error: No se pudieron obtener los datos del producto.');
                    }
                });
            });

            var deleteTypeButtons = document.querySelectorAll('.btn-delete-type');
            deleteTypeButtons.forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    var typeId = this.getAttribute('data-type-id');
                    var typeName = this.getAttribute('data-type-name');
                    if (typeId && typeName) {
                        confirmDeleteType(typeId, typeName);
                    } else {
                        console.error('Error: No se encontraron los datos del tipo');
                        alert('Error: No se pudieron obtener los datos del tipo.');
                    }
                });
            });
        });
        
        function confirmDeleteType(id, nombre) {
            try {
                if (confirm('¿Está seguro de eliminar el tipo "' + nombre + '"?')) {
                    window.location.href = 'eliminar_tipo.php?id=' + id;
                }
            } catch (error) {
                console.error('Error al eliminar tipo:', error);
                alert('Error al intentar eliminar el tipo. Por favor, intente nuevamente.');
            }
        }
        
        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>

