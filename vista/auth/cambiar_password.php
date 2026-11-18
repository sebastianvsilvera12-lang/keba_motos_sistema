<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/constantes.php';
require_once dirname(dirname(__DIR__)) . '/ayudantes/funciones.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña - KEBA MOTOS</title>
    <link rel="stylesheet" href="<?= asset('css/estilos.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/login.css') ?>">
</head>
<body class="login-page">

<div class="login-card">
    <div class="login-header">
        <div class="login-logo">🔒</div>
        <h1 class="login-title">Nueva <span>Contraseña</span></h1>
        <p class="login-subtitle">Ingresa y confirma tu nueva clave de acceso</p>
    </div>

    <form action="<?= vistaUrl('auth/login.php') ?>" method="POST" class="login-form">
        <div class="form-group">
            <label for="nueva_pass">Nueva Contraseña:</label>
            <input type="password" id="nueva_pass" name="nueva_pass" placeholder="••••••••" required>
        </div>

        <div class="form-group">
            <label for="confirm_pass">Confirmar Contraseña:</label>
            <input type="password" id="confirm_pass" name="confirm_pass" placeholder="••••••••" required>
        </div>

        <button type="submit" class="login-btn">Actualizar Contraseña</button>
    </form>

    <div class="login-footer">
        <a href="<?= vistaUrl('auth/login.php') ?>">← Regresar al Login</a>
    </div>
</div>

</body>
</html>

