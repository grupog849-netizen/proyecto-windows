<?php
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ===========================
   🧼 Utilidades
=========================== */
function limpiarDato($dato) {
    return htmlspecialchars(trim((string)$dato), ENT_QUOTES, 'UTF-8');
}

/* ===========================
   📦 CRUD de Productos
=========================== */
function obtenerProductos() {
    $pdo = getConnection();
    $sql = "SELECT * FROM productos ORDER BY id DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();  // FETCH_ASSOC ya está por defecto en config.php
}

function obtenerProductoPorId($id) {
    $pdo = getConnection();
    $sql = "SELECT * FROM productos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([ (int)$id ]);
    return $stmt->fetch();
}

function crearProducto($nombre, $descripcion, $precio, $stock) {
    try {
        $pdo = getConnection();
        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock)
                VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $nombre,
            $descripcion !== '' ? $descripcion : null,
            (float)$precio,
            (int)$stock
        ]);
    } catch (PDOException $e) {
        error_log("crearProducto error: " . $e->getMessage());
        return false;
    }
}

function actualizarProducto($id, $nombre, $descripcion, $precio, $stock) {
    try {
        $pdo = getConnection();
        $sql = "UPDATE productos
                SET nombre = ?, descripcion = ?, precio = ?, stock = ?
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $nombre,
            $descripcion !== '' ? $descripcion : null,
            (float)$precio,
            (int)$stock,
            (int)$id
        ]);
    } catch (PDOException $e) {
        error_log("actualizarProducto error: " . $e->getMessage());
        return false;
    }
}

function eliminarProducto($id) {
    try {
        $pdo = getConnection();
        $sql = "DELETE FROM productos WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([ (int)$id ]);
    } catch (PDOException $e) {
        error_log("eliminarProducto error: " . $e->getMessage());
        return false;
    }
}

/* ===========================
   🔐 Autenticación
=========================== */
function autenticarUsuario($username, $password) {
    $pdo = getConnection();

    $sql = "SELECT id, username, correo, password_hash
            FROM usuarios
            WHERE username = ?
            LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([ trim($username) ]);
    $user = $stmt->fetch();

    if (!$user) {
        return ['success' => false, 'message' => 'Usuario no encontrado.'];
    }
    if (!password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'message' => 'Contraseña incorrecta.'];
    }

    // Login OK
    $_SESSION['usuario'] = $user['username'];
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['correo'] = $user['correo'] ?? '';
    $_SESSION['ingreso_timestamp'] = time();

    // Abre un registro de acceso si no hay uno abierto
    if (empty($_SESSION['registro_id'])) {
        registrarEntrada($user['id'], $user['username']);
    }

    return ['success' => true, 'message' => 'Autenticación correcta.'];
}

/* ===========================
   📝 Registro de Accesos
=========================== */
function registrarEntrada($userId, $usuario) {
    $pdo = getConnection();
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';

    // Tu tabla NO tiene ip_address, así que no la usamos
    $sql = "INSERT INTO registro_accesos (usuario, user_agent, fecha_ingreso)
            VALUES (?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([ $usuario, $ua ]);

    $_SESSION['registro_id'] = (int)$pdo->lastInsertId();
    $_SESSION['ingreso_timestamp'] = time();
}

// ⚠️ NO declarar registrarSalida() aquí; ya existe en config.php

function obtenerRegistrosAcceso($limite = 50) {
    $pdo = getConnection();
    $limite = (int)$limite;

    $sql = "
        SELECT
            id,
            usuario,
            user_agent,
            fecha_ingreso,
            fecha_salida,
            -- Mantén el nombre que usa tu visual:
            COALESCE(duracion_sesion, TIMESTAMPDIFF(SECOND, fecha_ingreso, NOW())) AS duracion_sesion
        FROM registro_accesos
        ORDER BY fecha_ingreso DESC
        LIMIT $limite
    ";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('obtenerRegistrosAcceso error: ' . $e->getMessage());
        return [];
    }
}

function formatearDuracion($segundos) {
    if ($segundos === null) return 'Sesión activa';
    $segundos = (int)$segundos;
    $h = floor($segundos / 3600);
    $m = floor(($segundos % 3600) / 60);
    $s = $segundos % 60;

    $partes = [];
    if ($h > 0) $partes[] = $h . 'h';
    if ($m > 0) $partes[] = $m . 'm';
    if ($s > 0 || empty($partes)) $partes[] = $s . 's';
    return implode(' ', $partes);
}
