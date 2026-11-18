<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/constantes.php';
require_once dirname(dirname(__DIR__)) . '/configuracion/session.php';
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
                <span class="sidebar-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                </span>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="menu-category">Gestión Operativa</li>
        <li class="menu-item <?= ($menuActivo ?? '') === 'vehiculos' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('vehiculos/listar_vehiculos.php') ?>">
                <span class="sidebar-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="17" r="3"></circle><circle cx="19" cy="17" r="3"></circle><path d="M5 17h14"></path><path d="M9 17l2-7h4l2 7"></path><path d="M11 10h4"></path><circle cx="12" cy="6" r="1"></circle></svg>
                </span>
                <span>Mototaxis</span>
            </a>
        </li>
        <li class="menu-item <?= ($menuActivo ?? '') === 'conductores' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('conductores/listar_conductores.php') ?>">
                <span class="sidebar-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><line x1="15" y1="8" x2="17" y2="8"></line><line x1="15" y1="12" x2="17" y2="12"></line><line x1="7" y1="16" x2="17" y2="16"></line></svg>
                </span>
                <span>Conductores</span>
            </a>
        </li>
        <li class="menu-item <?= ($menuActivo ?? '') === 'propietarios' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('propietarios/listar_propietarios.php') ?>">
                <span class="sidebar-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </span>
                <span>Propietarios</span>
            </a>
        </li>
        <li class="menu-item <?= ($menuActivo ?? '') === 'asociaciones' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('asociaciones/listar_asociaciones.php') ?>">
                <span class="sidebar-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="9" y1="6" x2="9" y2="6.01"></line><line x1="15" y1="6" x2="15" y2="6.01"></line><line x1="9" y1="10" x2="9" y2="10.01"></line><line x1="15" y1="10" x2="15" y2="10.01"></line><line x1="9" y1="14" x2="9" y2="14.01"></line><line x1="15" y1="14" x2="15" y2="14.01"></line><line x1="9" y1="18" x2="15" y2="18"></line></svg>
                </span>
                <span>Asociaciones</span>
            </a>
        </li>

        <li class="menu-category">Control & Fiscalización</li>
        <li class="menu-item <?= ($menuActivo ?? '') === 'documentos' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('documentos/listar_documentos.php') ?>">
                <span class="sidebar-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><polyline points="9 15 11 17 15 13"></polyline></svg>
                </span>
                <span>Documentos (SOAT/TUC)</span>
            </a>
        </li>
        <li class="menu-item <?= ($menuActivo ?? '') === 'fiscalizacion' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('fiscalizacion/listar_infracciones.php') ?>">
                <span class="sidebar-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                </span>
                <span>Infracciones</span>
            </a>
        </li>

        <li class="menu-category">Auditoría & Sistema</li>
        <li class="menu-item <?= ($menuActivo ?? '') === 'reportes' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('reportes/reporte_general.php') ?>">
                <span class="sidebar-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </span>
                <span>Reportes</span>
            </a>
        </li>
        <?php if ($rolId === ROL_ADMIN): ?>
        <li class="menu-item <?= ($menuActivo ?? '') === 'usuarios' ? 'active' : '' ?>">
            <a href="<?= vistaUrl('usuarios/listar_usuarios.php') ?>">
                <span class="sidebar-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </span>
                <span>Usuarios</span>
            </a>
        </li>
        <?php endif; ?>

        <li class="menu-item" style="margin-top: 25px;">
            <a href="<?= controladorUrl('auth/logout_controlador.php') ?>" style="color: #ef4444;">
                <span class="sidebar-icon" style="color: #ef4444;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                </span>
                <span>Cerrar Sesión</span>
            </a>
        </li>
    </ul>
</aside>

