<?php
require_once dirname(dirname(__DIR__)) . '/config/session.php';
$usuarioActivo = obtenerUsuarioActual();
?>
<div class="app-main">
    <header class="app-navbar">
        <div class="navbar-title">
            <?= htmlspecialchars($subtituloNavbar ?? 'Sistema Integrado de Control') ?>
        </div>
        <div class="navbar-user">
            <span class="user-badge"><?= htmlspecialchars($usuarioActivo['rol_nombre'] ?? 'Administrador') ?></span>
            <span class="user-name">👤 <?= htmlspecialchars(trim(($usuarioActivo['nombre'] ?? '') . ' ' . ($usuarioActivo['apellidos'] ?? '')) ?: 'Usuario') ?></span>
            <a href="<?= controladorUrl('auth/logout_controlador.php') ?>" class="btn btn-outline btn-sm">Salir</a>
        </div>
    </header>
    <main class="app-content">
        <?php include_once __DIR__ . '/mensajes.php'; ?>

