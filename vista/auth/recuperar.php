<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/constantes.php';
require_once dirname(dirname(__DIR__)) . '/ayudantes/funciones.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - KEBA MOTOS</title>
    <link rel="stylesheet" href="<?= asset('css/estilos.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/login.css') ?>">
</head>
<body class="login-page">

<div class="login-card">
    <div class="login-header">
        <div class="login-logo">🔑</div>
        <h1 class="login-title">Recuperar <span>Acceso</span></h1>
        <p class="login-subtitle">Ingresa tu correo para restablecer tu contraseña</p>
    </div>

    <form action="<?= vistaUrl('auth/cambiar_password.php') ?>" method="GET" class="login-form">
        <div class="form-group">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" required>
        </div>

        <button type="submit" class="login-btn">Enviar Instrucciones</button>
    </form>

    <div class="login-footer">
        <a href="<?= vistaUrl('auth/login.php') ?>">← Volver al Inicio de Sesión</a>
    </div>
</div>

</body>
</html>

