<?php
require_once 'config.php';
require_once 'functions.php';

// (Opcional) Asegura zona horaria local
date_default_timezone_set('America/Costa_Rica');

// Obtener registros recientes
$registros = obtenerRegistrosAcceso(100);

// Cerrar sesión manualmente
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
    <title>Registro de Accesos - CRUD</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Registro de Accesos</h1>
            <p>Control de ingresos y salidas del sistema</p>
            <div class="session-info">
                <span>Usuario: <strong><?php echo $_SESSION['usuario']; ?></strong></span>
                <span>Ingreso: <?php echo date('d/m/Y H:i:s', $_SESSION['ingreso_timestamp']); ?></span>
                <a href="?cerrar_sesion=1" class="btn-logout" onclick="return confirm('¿Deseas cerrar sesión?')">Cerrar Sesión</a>
            </div>
        </div>

        <div class="nav-tabs">
            <a href="index.php" class="tab-link"> Productos</a>
            <a href="registros.php" class="tab-link active">Registro de Accesos</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-info">
                    <h3><?php echo count($registros); ?></h3>
                    <p>Total de Accesos</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"></div>
                <div class="stat-info">
                    <h3><?php echo date('d/m/Y'); ?></h3>
                    <p>Fecha Actual</p>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>📋 Historial de Accesos</h2>
            <?php if (count($registros) > 0): ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Ingreso</th>
                                <th>Salida</th>
                                <th>Duración</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $registro): ?>
                                <tr>
                                    <td><?php echo $registro['id']; ?></td>
                                    <td><strong><?php echo $registro['usuario']; ?></strong></td>
                                    <td><?php echo date('d/m/Y H:i:s', strtotime($registro['fecha_ingreso'])); ?></td>
                                    <td>
                                        <?php
                                        echo !empty($registro['fecha_salida']) && $registro['fecha_salida'] !== '0000-00-00 00:00:00'
                                            ? date('d/m/Y H:i:s', strtotime($registro['fecha_salida']))
                                            : '-';
                                        ?>
                                    </td>
                                    <td><?php echo formatearDuracion($registro['duracion_sesion']); ?></td>
                                    
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No hay registros de acceso</h3>
                    <p>Los accesos al sistema se mostrarán aquí</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
