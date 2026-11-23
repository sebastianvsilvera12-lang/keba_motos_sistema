<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/constantes.php';
require_once dirname(dirname(__DIR__)) . '/configuracion/session.php';
require_once dirname(dirname(__DIR__)) . '/ayudantes/funciones.php';
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
        <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 16px;">
            <img src="<?= asset('img/logo_keba.png') ?>" alt="KEBA" style="max-height: 54px; width: auto; max-width: 250px; object-fit: contain; filter: drop-shadow(0 4px 10px rgba(0,0,0,0.1));">
        </div>
        <h1 class="login-title">Control y <span>Fiscalización</span></h1>
        <p class="login-subtitle">Sistema Integrado de Gestión y Transporte</p>
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

    <div style="margin-top: 22px; padding-top: 18px; border-top: 1px solid #e2e8f0; text-align: center;">
        <span style="display: block; font-size: 0.85rem; color: #64748b; margin-bottom: 8px;">
            ¿Eres pasajero o cliente y no tienes cuenta?
        </span>
        <a href="<?= vistaUrl('auth/registro.php') ?>" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%; box-sizing: border-box; padding: 11px 16px; background: #eff6ff; color: #1d4ed8; border: 1.5px solid #bfdbfe; border-radius: 8px; font-weight: 700; font-size: 0.92rem; text-decoration: none;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
            <span>Regístrate aquí (Solo Clientes)</span>
        </a>
    </div>

    <div class="login-footer" style="margin-top: 16px;">
        <a href="<?= vistaUrl('auth/recuperar.php') ?>">¿Olvidaste tu contraseña?</a>
    </div>
</div>

</body>
</html>

