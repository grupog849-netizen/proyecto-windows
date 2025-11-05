<?php
// logout.php — Cierra la sesión y redirige al login
require_once 'config.php';
require_once 'functions.php';

// Si existe la función registrarSalida(), la ejecutamos
if (function_exists('registrarSalida')) {
    registrarSalida();
}

// Eliminar todos los datos de la sesión
$_SESSION = [];
session_unset();
session_destroy();

// Redirigir al login con una transición visual
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cerrando sesión...</title>
<link rel="stylesheet" href="styles.css">
<style>
body {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100vh;
  background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
  color: white;
  font-family: Arial, sans-serif;
}
.spinner {
  border: 5px solid rgba(255,255,255,0.3);
  border-top: 5px solid #fff;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  animation: spin 1s linear infinite;
  margin-bottom: 20px;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
<meta http-equiv="refresh" content="2;url=login.php">
</head>
<body>
  <div class="spinner"></div>
  <h2>🔒 Cerrando sesión...</h2>
  <p>Serás redirigido al inicio en unos segundos.</p>
</body>
</html>
