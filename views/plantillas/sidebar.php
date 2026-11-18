<?php
require_once dirname(dirname(__DIR__)) . '/config/constantes.php';
require_once dirname(dirname(__DIR__)) . '/config/session.php';
require_once dirname(dirname(__DIR__)) . '/config/permisos.php';
$usuarioActivo = obtenerUsuarioActual();
$rolId = (int)($usuarioActivo['rol_id'] ?? 3);

$dashboardUrl = 'dashboard/dashboard_admin.php';
if ($rolId === ROL_CONSULTA) {
    $dashboardUrl = 'dashboard/dashboard_consulta.php';
} elseif ($rolId === ROL_ANALISTA || $rolId === ROL_PERSONAL) {
    $dashboardUrl = 'dashboard/dashboard_personal.php';
}
?>
<aside class="app-sidebar">
    <div class="sidebar-header" style="justify-content: center; padding: 10px 15px; background: #030712;">
        <a href="<?= BASE_URL ?>index.php" class="sidebar-brand" style="display:flex; justify-content:center; width:100%;">
            <img src="<?= asset('img/logo_keba.png') ?>" alt="KEBA MOTOS" style="max-height: 48px; max-width: 210px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));">
        </a>
    </div>

    <ul class="sidebar-menu">
        <li class="menu-category">General</li>
        <li class="menu-item <?= ($menuActivo ?? '') === 'dashboard' ? 'active' : '' ?>">
            <a href="<?= vistaUrl($dashboardUrl) ?>">
                <span>📊</span> Dashboard
            </a>
        </li>

        <?php if (tienePermiso('vehiculos', 'ver') || tienePermiso('conductores', 'ver') || tienePermiso('propietarios', 'ver') || tienePermiso('asociaciones', 'ver')): ?>
        <li class="menu-category">Gestión Operativa</li>
        <?php if (tienePermiso('vehiculos', 'ver')): ?>
        <li class="menu-item <?= ($menuActivo ?? '') === 'vehiculos' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('vehiculos/listar_vehiculos.php') ?>">
                <span>🛵</span> Mototaxis
            </a>
        </li>
        <?php endif; ?>
        <?php if (tienePermiso('conductores', 'ver')): ?>
        <li class="menu-item <?= ($menuActivo ?? '') === 'conductores' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('conductores/listar_conductores.php') ?>">
                <span>👨‍✈️</span> Conductores
            </a>
        </li>
        <?php endif; ?>
        <?php if (tienePermiso('propietarios', 'ver')): ?>
        <li class="menu-item <?= ($menuActivo ?? '') === 'propietarios' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('propietarios/listar_propietarios.php') ?>">
                <span>👤</span> Propietarios
            </a>
        </li>
        <?php endif; ?>
        <?php if (tienePermiso('asociaciones', 'ver')): ?>
        <li class="menu-item <?= ($menuActivo ?? '') === 'asociaciones' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('asociaciones/listar_asociaciones.php') ?>">
                <span>🏢</span> Asociaciones
            </a>
        </li>
        <?php endif; ?>
        <?php endif; ?>

        <?php if (tienePermiso('documentos', 'ver') || tienePermiso('fiscalizacion', 'ver')): ?>
        <li class="menu-category">Control & Fiscalización</li>
        <?php if (tienePermiso('documentos', 'ver')): ?>
        <li class="menu-item <?= ($menuActivo ?? '') === 'documentos' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('documentos/listar_documentos.php') ?>">
                <span>📄</span> Documentos (SOAT/TUC)
            </a>
        </li>
        <?php endif; ?>
        <?php if (tienePermiso('fiscalizacion', 'ver')): ?>
        <li class="menu-item <?= ($menuActivo ?? '') === 'fiscalizacion' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('fiscalizacion/listar_infracciones.php') ?>">
                <span>👮</span> Infracciones
            </a>
        </li>
        <?php endif; ?>
        <?php endif; ?>

        <?php if (tienePermiso('reportes', 'ver') || tienePermiso('usuarios', 'ver')): ?>
        <li class="menu-category">Auditoría & Sistema</li>
        <?php if (tienePermiso('reportes', 'ver')): ?>
        <li class="menu-item <?= ($menuActivo ?? '') === 'reportes' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('reportes/reporte_general.php') ?>">
                <span>📑</span> Reportes
            </a>
        </li>
        <?php endif; ?>
        <?php if (tienePermiso('usuarios', 'ver')): ?>
        <li class="menu-item <?= ($menuActivo ?? '') === 'usuarios' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('usuarios/listar_usuarios.php') ?>">
                <span>👥</span> Usuarios
            </a>
        </li>
        <?php endif; ?>
        <?php endif; ?>

        <li class="menu-item" style="margin-top: 25px;">
            <a href="<?= controladorUrl('auth/logout_controlador.php') ?>" style="color: #ef4444;">
                <span>🚪</span> Cerrar Sesión
            </a>
        </li>
    </ul>
</aside>

