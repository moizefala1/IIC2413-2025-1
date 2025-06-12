<?php
// Inicia sesión
session_start();
$mensaje_success = $_SESSION['success'] ?? null;
$mensaje_error = $_GET['error'] ?? null;
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de sesión - Booked.com</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>

<body>
    <div class="container">
        <h1>Bienvenido a Booked.com</h1>
        <form action="validar_login.php" method="POST" class="formulario">
            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario" required>

            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>

            <button type="submit">Iniciar sesión</button>
        </form>
        <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
        <?php if ($mensaje_success): ?>
            <p class="success"><?= htmlspecialchars($mensaje_success) ?></p>
        <?php elseif ($mensaje_error): ?>
            <p class="error"><?= htmlspecialchars($mensaje_error) ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
