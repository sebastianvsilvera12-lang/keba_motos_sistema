<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/constantes.php';
require_once dirname(dirname(__DIR__)) . '/configuracion/session.php';
require_once dirname(dirname(__DIR__)) . '/ayudantes/funciones.php';

// Si ya tiene sesión activa como cliente, enviarlo a su panel
if (estaAutenticado()) {
    $u = obtenerUsuarioActual();
    if (($u['rol_id'] ?? 0) == ROL_CLIENTES) {
        redireccionar('vista/cliente/index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Pasajero / Cliente - KEBA</title>
    <link rel="stylesheet" href="<?= asset('css/estilos.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/login.css') ?>">
    <style>
        .register-card {
            background-color: #ffffff;
            width: 100%;
            max-width: 540px;
            border-radius: 14px;
            padding: 35px 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin: 20px auto;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            .register-card {
                padding: 25px 20px;
            }
        }
        .badge-client-register {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
    </style>
</head>
<body class="login-page">

<div class="register-card">
    <div class="login-header" style="margin-bottom: 22px;">
        <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 12px;">
            <img src="<?= asset('img/logo_keba.png') ?>" alt="KEBA" style="height: 48px; width: auto; max-width: 240px; object-fit: contain; filter: drop-shadow(0 4px 10px rgba(0,0,0,0.1));">
        </div>
        
        <div>
            <span class="badge-client-register">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v8"/><path d="M8 12h8"/></svg>
                Solo para Clientes / Pasajeros
            </span>
        </div>

        <h1 class="login-title" style="font-size: 1.45rem; margin: 4px 0 2px 0;">Crear Cuenta de <span>Pasajero</span></h1>
        <p class="login-subtitle" style="font-size: 0.85rem; margin-top: 4px;">
            Pide autos, colectivos y camionetas en Huanta y rutas al VRAEM, Ayacucho, Lima y Cusco
        </p>
    </div>

    <?php include_once dirname(__DIR__) . '/plantillas/mensajes.php'; ?>

    <form action="<?= controladorUrl('auth/registro_controlador.php') ?>" method="POST" class="login-form">
        
        <div class="form-row">
            <!-- DNI -->
            <div class="form-group">
                <label for="dni">DNI / Documento *</label>
                <input type="text" id="dni" name="dni" placeholder="8 dígitos" maxlength="8" pattern="[0-9]{8}" required autofocus inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
            </div>

            <!-- TELÉFONO / CELULAR -->
            <div class="form-group">
                <label for="telefono">Celular / WhatsApp *</label>
                <input type="text" id="telefono" name="telefono" placeholder="Ej: 984512301" maxlength="9" required inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
            </div>
        </div>

        <div class="form-row">
            <!-- NOMBRES -->
            <div class="form-group">
                <label for="nombres">Nombres *</label>
                <input type="text" id="nombres" name="nombres" placeholder="Tus nombres" required>
            </div>

            <!-- APELLIDOS -->
            <div class="form-group">
                <label for="apellidos">Apellidos *</label>
                <input type="text" id="apellidos" name="apellidos" placeholder="Tus apellidos" required>
            </div>
        </div>

        <!-- CORREO ELECTRÓNICO (OPCIONAL) -->
        <div class="form-group">
            <label for="email">Correo Electrónico <small style="color: #64748b; font-weight: normal;">(Opcional)</small></label>
            <input type="email" id="email" name="email" placeholder="ejemplo@correo.com">
        </div>

        <div class="form-row">
            <!-- CONTRASEÑA -->
            <div class="form-group">
                <label for="password">Contraseña *</label>
                <input type="password" id="password" name="password" placeholder="Mínimo 6 caracteres" minlength="6" required>
            </div>

            <!-- CONFIRMAR CONTRASEÑA -->
            <div class="form-group">
                <label for="password_confirm">Repetir Contraseña *</label>
                <input type="password" id="password_confirm" name="password_confirm" placeholder="••••••••" minlength="6" required>
            </div>
        </div>

        <button type="submit" class="login-btn" style="margin-top: 10px; font-weight: 800; font-size: 1rem; padding: 13px;">
            Crear Mi Cuenta de Pasajero
        </button>
    </form>

    <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid #e2e8f0; text-align: center;">
        <span style="font-size: 0.88rem; color: #64748b;">¿Ya tienes una cuenta registrada?</span><br>
        <a href="<?= vistaUrl('auth/login.php') ?>" style="display: inline-block; margin-top: 6px; color: #2563eb; font-weight: 700; font-size: 0.92rem; text-decoration: none;">
            &larr; Iniciar Sesión en el Sistema
        </a>
    </div>
</div>

</body>
</html>
