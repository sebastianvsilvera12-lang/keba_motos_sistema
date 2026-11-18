<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/session.php';
$usuarioActivo = obtenerUsuarioActual();
?>
<div class="app-main">
    <header class="app-navbar">
        <div class="navbar-title">
            <?= htmlspecialchars($subtituloNavbar ?? 'Sistema Integrado de Control') ?>
        </div>
        <div class="navbar-user">
            <span class="user-badge"><?= htmlspecialchars($usuarioActivo['rol_nombre'] ?? 'Administrador') ?></span>
            <span class="user-name" style="display: inline-flex; align-items: center; gap: 6px;">
                <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                <?= htmlspecialchars(trim(($usuarioActivo['nombre'] ?? '') . ' ' . ($usuarioActivo['apellidos'] ?? '')) ?: 'Usuario') ?>
            </span>
            <a href="<?= controladorUrl('auth/logout_controlador.php') ?>" class="btn btn-outline btn-sm">Salir</a>
        </div>
    </header>
    <main class="app-content">
        <?php include_once __DIR__ . '/mensajes.php'; ?>

