<?php
require_once 'config.php';
require_once 'functions.php';

// Si ya está logueado, redirigir
if (isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if ($password !== $password2) {
        $mensaje = '⚠️ Las contraseñas no coinciden.';
    } else {
        $res = crearUsuario($username, $correo, $password);
        if ($res['success']) {
            autenticarUsuario($username, $password);
            header('Location: index.php');
            exit;
        } else {
            $mensaje = '❌ ' . $res['message'];
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Crear Cuenta | Sistema CRUD</title>
<style>
/* 🎨 Estilo visual moderno */
body {
  font-family: "Poppins", sans-serif;
  background: linear-gradient(135deg, #667eea, #764ba2);
  height: 100vh;
  margin: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #333;
}

/* 🧾 Caja del formulario */
.register-container {
  background: #fff;
  padding: 40px 50px;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  width: 380px;
  text-align: center;
}

/* Título */
.register-container h2 {
  margin-bottom: 25px;
  color: #4b0082;
  font-weight: 600;
}

/* Campos de formulario */
.register-container label {
  display: block;
  text-align: left;
  margin-bottom: 10px;
  font-weight: 500;
}

.register-container input {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
  margin-bottom: 20px;
  font-size: 15px;
  transition: all 0.2s;
}

.register-container input:focus {
  border-color: #764ba2;
  box-shadow: 0 0 6px rgba(118, 75, 162, 0.3);
  outline: none;
}

/* Botón principal */
.register-container button {
  background: linear-gradient(135deg, #6a11cb, #2575fc);
  color: white;
  border: none;
  padding: 12px 0;
  border-radius: 10px;
  width: 100%;
  font-size: 16px;
  cursor: pointer;
  transition: 0.3s;
}

.register-container button:hover {
  background: linear-gradient(135deg, #5b0db7, #1f63d8);
  transform: translateY(-1px);
}

/* Mensajes */
.alert {
  background: #ffe4e1;
  border: 1px solid #ff6b6b;
  color: #c0392b;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 20px;
}

/* Enlace inferior */
.register-container p {
  margin-top: 15px;
  font-size: 14px;
}

.register-container a {
  color: #2575fc;
  text-decoration: none;
  font-weight: 500;
}

.register-container a:hover {
  text-decoration: underline;
}
</style>
</head>
<body>
<div class="register-container">
  <h2>🧾 Crear nueva cuenta</h2>

  <?php if ($mensaje): ?>
      <div class="alert"><?php echo htmlspecialchars($mensaje); ?></div>
  <?php endif; ?>

  <form method="post" action="">
      <label>👤 Usuario:</label>
      <input type="text" name="username" placeholder="Tu usuario" required>

      <label>📧 Correo (opcional):</label>
      <input type="email" name="correo" placeholder="ejemplo@correo.com">

      <label>🔒 Contraseña:</label>
      <input type="password" name="password" placeholder="********" required>

      <label>🔁 Repetir contraseña:</label>
      <input type="password" name="password2" placeholder="********" required>

      <button type="submit">✅ Crear cuenta</button>
  </form>

  <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
</div>
</body>
</html>
