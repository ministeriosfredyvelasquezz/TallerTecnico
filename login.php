<?php
session_start(); // 1. Encendemos la sesión
require 'conexion.php'; // Traemos la conexión a MySQL

// Si ya tiene sesión iniciada, lo mandamos directo al panel
if (isset($_SESSION['usuario_id'])) {
    header("Location: panel.php");
    exit;
}

$error = '';

// Si el usuario envió el formulario (hizo clic en el botón)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $clave = $_POST['password'];

    // Buscamos ese correo en la tabla usuarios
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Comprobamos si existe el usuario Y si la contraseña coincide
    if ($usuario && password_verify($clave, $usuario['password'])) {
        $_SESSION['usuario_id']     = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_rol']    = $usuario['rol'];

        header("Location: panel.php");
        exit;
    } else {
        $error = "Correo o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ingreso Técnicos - Taller</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #1e272e; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .caja-login { background: white; padding: 35px; border-radius: 10px; width: 350px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
        h2 { margin-top: 0; color: #2c3e50; text-align: center; }
        label { display: block; margin-top: 15px; font-weight: 600; font-size: 14px; color: #34495e; }
        input { width: 100%; padding: 11px; margin-top: 5px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #0984e3; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; margin-top: 20px; cursor: pointer; }
        button:hover { background: #0773c5; }
        .error { background: #ffeaa7; color: #d63031; padding: 10px; border-radius: 5px; text-align: center; font-size: 14px; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="caja-login">
    <h2>Acceso al Taller</h2>
    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <label>Correo electrónico</label>
        <input type="email" name="email" required placeholder="admin@taller.com">
        <label>Contraseña</label>
        <input type="password" name="password" required placeholder="••••••••">
        <button type="submit">Ingresar al Panel</button>
    </form>
</div>
</body>
</html>
