<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$mensaje = '';
$tipo_mensaje = '';
$editando = false;
$producto_edit = null;

// Procesar CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear'])) {
    $nombre = limpiarDato($_POST['nombre']);
    $descripcion = limpiarDato($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);

    if (!empty($nombre) && $precio >= 0 && $stock >= 0) {
        if (crearProducto($nombre, $descripcion, $precio, $stock)) {
            $mensaje = "¡Producto creado exitosamente!";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "Error al crear el producto";
            $tipo_mensaje = "error";
        }
    } else {
        $mensaje = "Por favor completa todos los campos correctamente";
        $tipo_mensaje = "error";
    }
}

// Procesar UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    $id = intval($_POST['id']);
    $nombre = limpiarDato($_POST['nombre']);
    $descripcion = limpiarDato($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);

    if (!empty($nombre) && $precio >= 0 && $stock >= 0) {
        if (actualizarProducto($id, $nombre, $descripcion, $precio, $stock)) {
            $mensaje = "¡Producto actualizado exitosamente!";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "Error al actualizar el producto";
            $tipo_mensaje = "error";
        }
    }
}

// Procesar DELETE
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    if (eliminarProducto($id)) {
        $mensaje = "¡Producto eliminado exitosamente!";
        $tipo_mensaje = "success";
    }
    header("Location: index.php");
    exit;
}

// Cargar datos para EDITAR
if (isset($_GET['editar'])) {
    $editando = true;
    $id = intval($_GET['editar']);
    $producto_edit = obtenerProductoPorId($id);
}

// Obtener todos los productos
$productos = obtenerProductos();

// Cerrar sesión
if (isset($_GET['cerrar_sesion'])) {
    registrarSalida();
    session_destroy();
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD - Gestión de Productos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1> Sistema de Gestión de Productos</h1>
            <p>CRUD con MariaDB | Usuario: <strong><?php echo $_SESSION['usuario']; ?></strong></p>
            <div class="session-info">
                <span>Ingreso: <?php echo date('d/m/Y H:i:s', $_SESSION['ingreso_timestamp']); ?></span>
                <a href="logout.php" class="btn-logout" onclick="return confirm('¿Deseas cerrar sesión?')">Cerrar Sesión</a>

            </div>
        </div>

        <!-- Navegación -->
        <div class="nav-tabs">
            <a href="index.php" class="tab-link active">Productos</a>
            <a href="registros.php" class="tab-link">Registro de Accesos</a>
        </div>

        <!-- Mensajes -->
        <?php if ($mensaje): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <div class="card">
            <h2><?php echo $editando ? '✏️ Editar Producto' : '➕ Agregar Nuevo Producto'; ?></h2>
            <form method="POST" action="">
                <?php if ($editando): ?>
                    <input type="hidden" name="id" value="<?php echo $producto_edit['id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="nombre">Nombre del Producto *</label>
                    <input type="text" id="nombre" name="nombre" 
                           value="<?php echo $editando ? $producto_edit['nombre'] : ''; ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3"><?php echo $editando ? $producto_edit['descripcion'] : ''; ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="precio">Precio ($) *</label>
                        <input type="number" id="precio" name="precio" step="0.01" min="0"
                               value="<?php echo $editando ? $producto_edit['precio'] : ''; ?>" 
                               required>
                    </div>

                    <div class="form-group">
                        <label for="stock">Stock *</label>
                        <input type="number" id="stock" name="stock" min="0"
                               value="<?php echo $editando ? $producto_edit['stock'] : ''; ?>" 
                               required>
                    </div>
                </div>

                <div class="form-actions">
                    <?php if ($editando): ?>
                        <button type="submit" name="actualizar" class="btn btn-primary">Actualizar Producto</button>
                        <a href="index.php" class="btn btn-cancel">Cancelar</a>
                    <?php else: ?>
                        <button type="submit" name="crear" class="btn btn-primary">Crear Producto</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Tabla de productos -->
        <div class="card">
            <h2>Lista de Productos</h2>
            <?php if (count($productos) > 0): ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Fecha Creación</th>
                                <th>Última Actualización</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td><?php echo $producto['id']; ?></td>
                                    <td><strong><?php echo $producto['nombre']; ?></strong></td>
                                    <td><?php echo substr($producto['descripcion'], 0, 50) . (strlen($producto['descripcion']) > 50 ? '...' : ''); ?></td>
                                    <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                                    <td><span class="badge <?php echo $producto['stock'] > 10 ? 'badge-success' : ($producto['stock'] > 0 ? 'badge-warning' : 'badge-danger'); ?>">
                                        <?php echo $producto['stock']; ?>
                                    </span></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($producto['fecha_creacion'])); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($producto['fecha_actualizacion'])); ?></td>
                                    <td class="actions">
                                        <a href="?editar=<?php echo $producto['id']; ?>" class="btn btn-edit">✏️</a>
                                        <a href="?eliminar=<?php echo $producto['id']; ?>" 
                                           class="btn btn-delete" 
                                           onclick="return confirm('¿Estás seguro de eliminar este producto?')">🗑️</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h3>No hay productos registrados</h3>
                    <p>Comienza agregando tu primer producto usando el formulario de arriba</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>