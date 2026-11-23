<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/constantes.php';
require_once dirname(dirname(__DIR__)) . '/configuracion/session.php';
require_once dirname(dirname(__DIR__)) . '/configuracion/permisos.php';
$usuarioActivo = obtenerUsuarioActual();
$rolId = (int)($usuarioActivo['rol_id'] ?? 3);

$dashboardDestino = vistaUrl('dashboard/dashboard_admin.php');
if ($rolId === ROL_TAXISTAS || $rolId === ROL_CONSULTA) {
    $dashboardDestino = vistaUrl('taxista/dashboard.php');
} elseif ($rolId === ROL_ANALISTA || $rolId === ROL_PERSONAL) {
    $dashboardDestino = vistaUrl('dashboard/dashboard_personal.php');
}
?>
<aside class="app-sidebar">
    <div class="sidebar-header" style="justify-content: center; padding: 10px 15px; background: #030712;">
        <a href="<?= BASE_URL ?>index.php" class="sidebar-brand" style="display:flex; justify-content:center; width:100%;">
            <img src="<?= asset('img/logo_keba.png') ?>" alt="KEBA MOTOS" style="max-height: 48px; max-width: 210px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));">
        </a>
    </div>

    <ul class="sidebar-menu">
        <?php if ($rolId === ROL_TAXISTAS || $rolId === ROL_CONSULTA): ?>
            <!-- ============================================== -->
            <!-- MENÚ EXCLUSIVO PARA ROL TAXISTAS               -->
            <!-- ============================================== -->
            <li class="menu-category">Mi Operación</li>
            <li class="menu-item <?= ($menuActivo ?? '') === 'asistencia' ? 'active' : '' ?>">
                <a href="<?= vistaUrl('taxista/asistencia.php') ?>">
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </span>
                    <span>Registrar Asistencia</span>
                </a>
            </li>
            <li class="menu-item <?= ($menuActivo ?? '') === 'documentos_taxista' ? 'active' : '' ?>">
                <a href="<?= vistaUrl('taxista/documentos.php') ?>">
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><polyline points="9 15 11 17 15 13"></polyline></svg>
                    </span>
                    <span>Subir Documentos</span>
                </a>
            </li>
            <li class="menu-item <?= ($menuActivo ?? '') === 'fotos_taxista' ? 'active' : '' ?>">
                <a href="<?= vistaUrl('taxista/fotos.php') ?>">
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="4"></circle><path d="M5 7h1a2 2 0 0 0 2-2 1 1 0 0 1 1-1h6a1 1 0 0 1 1 1 2 2 0 0 0 2 2h1a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2z"></path></svg>
                    </span>
                    <span>Fotos Persona y Moto</span>
                </a>
            </li>
            <li class="menu-item <?= ($menuActivo ?? '') === 'reportar_estado' ? 'active' : '' ?>">
                <a href="<?= vistaUrl('taxista/reportar_estado.php') ?>">
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    </span>
                    <span>Daños & Revisión Moto</span>
                </a>
            </li>
            <li class="menu-item <?= ($menuActivo ?? '') === 'cuotas_taxista' ? 'active' : '' ?>">
                <a href="<?= vistaUrl('taxista/cuotas.php') ?>">
                    <span class="sidebar-icon" style="display: inline-flex; align-items: center; justify-content: center;">
                        <img src="<?= asset('img/yape_app.png') ?>" alt="Yape" style="width: 18px; height: 18px; border-radius: 4px;">
                    </span>
                    <span>Cuota Mensual (Yape)</span>
                </a>
            </li>

            <li class="menu-category">Consultas</li>
            <li class="menu-item <?= ($menuActivo ?? '') === 'keba_ride' ? 'active' : '' ?>">
                <a href="<?= vistaUrl('taxista/viajes.php') ?>">
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
                    </span>
                    <span>Keba Ride (Uber)</span>
                </a>
            </li>
            <li class="menu-item <?= (($menuActivo ?? '') === 'consulta_placa' || ($menuActivo ?? '') === 'fiscalizacion') ? 'active' : '' ?>">
                <a href="<?= vistaUrl('fiscalizacion/historial_vehiculo.php') ?>">
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </span>
                    <span>Consultar Placa</span>
                </a>
            </li>
        <?php else: ?>
            <!-- ============================================== -->
            <!-- MENÚ PARA ADMINISTRADOR Y ANALISTA            -->
            <!-- ============================================== -->
            <li class="menu-category">General</li>
            <li class="menu-item <?= ($menuActivo ?? '') === 'dashboard' ? 'active' : '' ?>">
                <a href="<?= $dashboardDestino ?>">
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                    </span>
                    <span>Panel Principal</span>
                </a>
            </li>

            <?php if (tienePermiso('vehiculos', 'ver') || tienePermiso('conductores', 'ver') || tienePermiso('propietarios', 'ver') || tienePermiso('asociaciones', 'ver')): ?>
            <li class="menu-category">Gestión Operativa</li>
            <?php if (tienePermiso('vehiculos', 'ver')): ?>
            <li class="menu-item <?= ($menuActivo ?? '') === 'vehiculos' ? 'active' : '' ?>">
                <a href="<?= vistaUrl('vehiculos/listar_vehiculos.php') ?>">
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="17" r="3"></circle><circle cx="19" cy="17" r="3"></circle><path d="M5 17h14"></path><path d="M9 17l2-7h4l2 7"></path><path d="M11 10h4"></path><circle cx="12" cy="6" r="1"></circle></svg>
                    </span>
                    <span>Mototaxis</span>
                </a>
            </li>
            <li class="menu-item <?= ($menuActivo ?? '') === 'asistencias_taxistas' ? 'active' : '' ?>">
                <a href="<?= vistaUrl('vehiculos/asistencias_taxistas.php') ?>">
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </span>
                    <span>Uso de Motos / Asistencia</span>
                </a>
            </li>
            <li class="menu-item <?= ($menuActivo ?? '') === 'reportes_taxistas' ? 'active' : '' ?>">
                <a href="<?= vistaUrl('vehiculos/reportes_taxistas.php') ?>">
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    </span>
                    <span>Avisos de Motos (Daños)</span>
                </a>
            </li>
            <li class="menu-item <?= ($menuActivo ?? '') === 'cuotas_taxistas_admin' ? 'active' : '' ?>">
                <a href="<?= vistaUrl('vehiculos/cuotas_taxistas.php') ?>">
                    <span class="sidebar-icon" style="display: inline-flex; align-items: center; justify-content: center;">
                        <img src="<?= asset('img/yape_app.png') ?>" alt="Yape" style="width: 18px; height: 18px; border-radius: 4px;">
                    </span>
                    <span>Cuotas Taxistas (Yape)</span>
                </a>
            </li>
            <li class="menu-item <?= ($menuActivo ?? '') === 'keba_ride_admin' ? 'active' : '' ?>">
                <a href="<?= vistaUrl('cliente/index.php') ?>">
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
                    </span>
                    <span>Keba Ride (Portal Uber)</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if (tienePermiso('conductores', 'ver')): ?>
            <li class="menu-item <?= ($menuActivo ?? '') === 'conductores' ? 'active' : '' ?>">
                <a href="<?= vistaUrl('conductores/listar_conductores.php') ?>">
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><line x1="15" y1="8" x2="17" y2="8"></line><line x1="15" y1="12" x2="17" y2="12"></line><line x1="7" y1="16" x2="17" y2="16"></line></svg>
                    </span>
                    <span>Conductores</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if (tienePermiso('propietarios', 'ver')): ?>
            <li class="menu-item <?= ($menuActivo ?? '') === 'propietarios' ? 'active' : '' ?>">
                <a href="<?= vistaUrl('propietarios/listar_propietarios.php') ?>">
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </span>
                    <span>Propietarios</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if (tienePermiso('asociaciones', 'ver')): ?>
            <li class="menu-item <?= ($menuActivo ?? '') === 'asociaciones' ? 'active' : '' ?>">
                <a href="<?= vistaUrl('asociaciones/listar_asociaciones.php') ?>">
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="9" y1="6" x2="9" y2="6.01"></line><line x1="15" y1="6" x2="15" y2="6.01"></line><line x1="9" y1="10" x2="9" y2="10.01"></line><line x1="15" y1="10" x2="15" y2="10.01"></line><line x1="9" y1="14" x2="9" y2="14.01"></line><line x1="15" y1="14" x2="15" y2="14.01"></line><line x1="9" y1="18" x2="15" y2="18"></line></svg>
                    </span>
                    <span>Asociaciones</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (tienePermiso('documentos', 'ver') || tienePermiso('fiscalizacion', 'ver')): ?>
            <li class="menu-category">Control & Fiscalización</li>
            <?php if (tienePermiso('documentos', 'ver')): ?>
            <li class="menu-item <?= ($menuActivo ?? '') === 'documentos' ? 'active' : '' ?>">
                <a href="<?= vistaUrl('documentos/listar_documentos.php') ?>">
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><polyline points="9 15 11 17 15 13"></polyline></svg>
                    </span>
                    <span>Documentos (SOAT/TUC)</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if (tienePermiso('fiscalizacion', 'ver')): ?>
            <li class="menu-item <?= ($menuActivo ?? '') === 'fiscalizacion' ? 'active' : '' ?>">
                <a href="<?= vistaUrl('fiscalizacion/listar_infracciones.php') ?>">
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    </span>
                    <span>Infracciones</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (tienePermiso('reportes', 'ver') || tienePermiso('usuarios', 'ver')): ?>
            <li class="menu-category">Auditoría & Sistema</li>
            <?php if (tienePermiso('reportes', 'ver')): ?>
            <li class="menu-item <?= ($menuActivo ?? '') === 'reportes' ? 'active' : '' ?>">
                <a href="<?= vistaUrl('reportes/reporte_general.php') ?>">
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </span>
                    <span>Reportes</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if (tienePermiso('usuarios', 'ver')): ?>
            <li class="menu-item <?= ($menuActivo ?? '') === 'usuarios' ? 'active' : '' ?>">
                <a href="<?= vistaUrl('usuarios/listar_usuarios.php') ?>">
                    <span class="sidebar-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </span>
                    <span>Usuarios</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>
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

