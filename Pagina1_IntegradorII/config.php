<?php
// config.php - Configuración principal y control de accesos

// 🔧 Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'crud_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// 🌎 Zona horaria local
date_default_timezone_set('America/Costa_Rica');

// 📡 Conexión PDO
function getConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

// 🧠 Sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Detectar en qué archivo estamos
$archivo_actual = basename($_SERVER['PHP_SELF']);
$paginas_excluidas = ['login.php', 'register.php', 'logout.php'];

/*
   🚦 Registro automático SOLO para usuarios “anónimos” que visitan el sitio
   y no están en login/register/logout. (Esto crea un usuario de sesión
   como "Usuario_xxxx" y abre un registro de acceso.)
   ⚠️ IMPORTANTE: Insert SIN ip_address (tu tabla no la tiene).
*/
if (!in_array($archivo_actual, $paginas_excluidas)) {
    if (!isset($_SESSION['usuario'])) {
        $_SESSION['session_id'] = session_id();
        $_SESSION['usuario'] = 'Usuario_' . substr(session_id(), 0, 8);
        $_SESSION['ingreso_timestamp'] = time();

        try {
            $pdo = getConnection();
            $sql = "INSERT INTO registro_accesos (usuario, user_agent, fecha_ingreso)
                    VALUES (?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $_SESSION['usuario'],
                $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido'
            ]);
            $_SESSION['registro_id'] = (int)$pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error registrando acceso: " . $e->getMessage());
        }
    }
}

/*
   🕐 Registrar salida SOLO cuando se llame explícitamente (logout o cierre manual).
   ❌ Eliminado register_shutdown_function para NO cerrar en cada request.
*/
function registrarSalida() {
    if (isset($_SESSION['registro_id'])) {
        try {
            $pdo = getConnection();
            $duracion = time() - ($_SESSION['ingreso_timestamp'] ?? time());
            $sql = "UPDATE registro_accesos
                    SET fecha_salida = NOW(),
                        duracion_sesion = ?
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$duracion, (int)$_SESSION['registro_id']]);
        } catch (PDOException $e) {
            error_log("Error al registrar salida: " . $e->getMessage());
        }
    }
}
