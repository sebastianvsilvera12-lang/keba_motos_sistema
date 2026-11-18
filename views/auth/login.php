<?php
require_once dirname(dirname(__DIR__)) . '/config/constantes.php';
require_once dirname(dirname(__DIR__)) . '/config/session.php';
require_once dirname(dirname(__DIR__)) . '/helpers/funciones.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - KEBA MOTOS</title>
    <link rel="stylesheet" href="<?= asset('css/estilos.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/login.css') ?>">
</head>
<body class="login-page">

<div class="login-card">
    <div class="login-header">
        <img src="<?= asset('img/logo_keba.png') ?>" alt="KEBA MOTOS" style="width: 100%; max-height: 85px; object-fit: contain; margin-bottom: 15px;">
        <h1 class="login-title">Control y <span>Fiscalización</span></h1>
        <p class="login-subtitle">Sistema Integrado de Gestión de Mototaxis</p>
    </div>

    <?php include_once dirname(__DIR__) . '/plantillas/mensajes.php'; ?>

    <form action="<?= controladorUrl('auth/login_controlador.php') ?>" method="POST" class="login-form">
        <div class="form-group">
            <label for="usuario">Número de DNI o Usuario:</label>
            <input type="text" id="usuario" name="usuario" placeholder="Ingresa tu DNI o Usuario" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="login-btn">Ingresar al Sistema</button>
    </form>

    <div class="login-footer">
        <a href="<?= vistaUrl('auth/recuperar.php') ?>">¿Olvidaste tu contraseña?</a>
    </div>
</div>

</body>
</html>

