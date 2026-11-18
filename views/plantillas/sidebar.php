<?php
require_once dirname(dirname(__DIR__)) . '/config/constantes.php';
require_once dirname(dirname(__DIR__)) . '/config/session.php';
$usuarioActivo = obtenerUsuarioActual();
$rolId = (int)($usuarioActivo['rol_id'] ?? 3);
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
            <a href="<?= vistaUrl('dashboard/dashboard_admin.php') ?>">
                <span>📊</span> Dashboard
            </a>
        </li>

        <li class="menu-category">Gestión Operativa</li>
        <li class="menu-item <?= ($menuActivo ?? '') === 'vehiculos' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('vehiculos/listar_vehiculos.php') ?>">
                <span>🛵</span> Mototaxis
            </a>
        </li>
        <li class="menu-item <?= ($menuActivo ?? '') === 'conductores' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('conductores/listar_conductores.php') ?>">
                <span>👨‍✈️</span> Conductores
            </a>
        </li>
        <li class="menu-item <?= ($menuActivo ?? '') === 'propietarios' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('propietarios/listar_propietarios.php') ?>">
                <span>👤</span> Propietarios
            </a>
        </li>
        <li class="menu-item <?= ($menuActivo ?? '') === 'asociaciones' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('asociaciones/listar_asociaciones.php') ?>">
                <span>🏢</span> Asociaciones
            </a>
        </li>

        <li class="menu-category">Control & Fiscalización</li>
        <li class="menu-item <?= ($menuActivo ?? '') === 'documentos' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('documentos/listar_documentos.php') ?>">
                <span>📄</span> Documentos (SOAT/TUC)
            </a>
        </li>
        <li class="menu-item <?= ($menuActivo ?? '') === 'fiscalizacion' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('fiscalizacion/listar_infracciones.php') ?>">
                <span>👮</span> Infracciones
            </a>
        </li>

        <li class="menu-category">Auditoría & Sistema</li>
        <li class="menu-item <?= ($menuActivo ?? '') === 'reportes' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('reportes/reporte_general.php') ?>">
                <span>📑</span> Reportes
            </a>
        </li>
        <?php if ($rolId === ROL_ADMIN): ?>
        <li class="menu-item <?= ($menuActivo ?? '') === 'usuarios' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('usuarios/listar_usuarios.php') ?>">
                <span>👥</span> Usuarios
            </a>
        </li>
        <?php endif; ?>

        <li class="menu-item" style="margin-top: 25px;">
            <a href="<?= controladorUrl('auth/logout_controlador.php') ?>" style="color: #ef4444;">
                <span>🚪</span> Cerrar Sesión
            </a>
        </li>
    </ul>
</aside>

