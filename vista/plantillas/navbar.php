<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/session.php';
require_once dirname(dirname(__DIR__)) . '/configuracion/constantes.php';
require_once dirname(dirname(__DIR__)) . '/ayudantes/funciones.php';

$usuarioActivo = obtenerUsuarioActual();
$rolId = (int)($usuarioActivo['rol_id'] ?? 3);
$esTaxista = ($rolId === ROL_TAXISTAS || $rolId === ROL_CONSULTA);
$perfilDestino = $esTaxista ? vistaUrl('taxista/perfil.php') : vistaUrl('dashboard/dashboard_admin.php');
$nombreUsuario = htmlspecialchars(trim(($usuarioActivo['nombre'] ?? '') . ' ' . ($usuarioActivo['apellidos'] ?? '')) ?: 'Usuario');
?>
<div class="app-main">
    <header class="app-navbar">
        <div class="navbar-title">
            <?= htmlspecialchars($subtituloNavbar ?? 'Sistema Integrado de Control') ?>
        </div>
        <div class="navbar-user">
            <a href="<?= $perfilDestino ?>" title="Ver Mi Perfil de <?= htmlspecialchars($usuarioActivo['rol_nombre'] ?? 'Taxistas') ?>" style="text-decoration: none;">
                <span class="user-badge" style="cursor: pointer;"><?= htmlspecialchars($usuarioActivo['rol_nombre'] ?? 'Taxistas') ?></span>
            </a>

            <a href="<?= $perfilDestino ?>" class="user-name" title="Ver Mi Perfil y Panel de Datos" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none; color: inherit; cursor: pointer;">
                <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                <?= $nombreUsuario ?>
            </a>

            <a href="<?= controladorUrl('auth/logout_controlador.php') ?>" class="btn btn-outline btn-sm">Salir</a>
        </div>
    </header>
    <main class="app-content">
        <?php include_once __DIR__ . '/mensajes.php'; ?>

