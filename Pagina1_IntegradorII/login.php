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
    $password = $_POST['password'] ?? '';

    $res = autenticarUsuario($username, $password);
    if ($res['success']) {
        header('Location: index.php');
        exit;
    } else {
        $mensaje = '❌ ' . $res['message'];
    }
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iniciar Sesión | Sistema CRUD</title>
<link rel="stylesheet" href="styles.css">
<style>
/* 🎨 Estilo visual igual al de registro */
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

.login-container {
  background: white;
  padding: 40px 50px;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  width: 380px;
  text-align: center;
}

.login-container h2 {
  margin-bottom: 25px;
  color: #4b0082;
  font-weight: 600;
}

.login-container label {
  display: block;
  text-align: left;
  margin-bottom: 10px;
  font-weight: 500;
}

.login-container input {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
  margin-bottom: 20px;
  transition: 0.2s;
  font-size: 15px;
}

.login-container input:focus {
  border-color: #764ba2;
  outline: none;
  box-shadow: 0 0 6px rgba(118,75,162,0.3);
}

.login-container button {
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

.login-container button:hover {
  background: linear-gradient(135deg, #5b0db7, #1f63d8);
  transform: translateY(-1px);
}

.login-container p {
  margin-top: 15px;
  font-size: 14px;
}

.login-container a {
  color: #2575fc;
  text-decoration: none;
  font-weight: 500;
}

.login-container a:hover {
  text-decoration: underline;
}

.alert {
  background: #ffe4e1;
  border: 1px solid #ff6b6b;
  color: #c0392b;
  padding: 10px;
  border-radius: 8px;
  margin-bottom: 20px;
}
</style>
</head>
<body>
<div class="login-container">
  <h2>🔐 Iniciar Sesión</h2>

  <?php if ($mensaje): ?>
      <div class="alert"><?php echo htmlspecialchars($mensaje); ?></div>
  <?php endif; ?>

  <form method="post" action="">
      <label>👤 Usuario:</label>
      <input type="text" name="username" placeholder="Tu usuario" required>

      <label>🔒 Contraseña:</label>
      <input type="password" name="password" placeholder="********" required>

      <button type="submit">🚀 Iniciar sesión</button>
  </form>

  <p>¿No tienes cuenta? <a href="register.php">Crea una aquí</a></p>
</div>
</body>
</html>
