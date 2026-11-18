<?php
$tituloPagina = 'Reporte General Consolidado';
$subtituloNavbar = 'Resumen Ejecutivo';
$menuActivo = 'reportes';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Reporte.php';

$repMod = new Reporte();
$metricas = $repMod->obtenerMetricasGenerales();
?>

<div class="page-header no-print">
    <div>
        <h1 class="page-title">Reporte General Consolidado</h1>
        <p class="page-subtitle">Indicadores globales y resumen ejecutivo del sistema KEBA MOTOS</p>
    </div>
    <button onclick="window.print()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        Imprimir Resumen
    </button>
</div>

<div class="report-container">
    <div class="report-header">
        <div class="report-logo">KEBA <span>MOTOS</span></div>
        <div class="report-meta">
            <strong>Fecha de Emisión:</strong> <?= date('d/m/Y h:i A') ?><br>
            <strong>Consolidado Oficial</strong>
        </div>
    </div>

    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-info">
                <h3>Total Mototaxis</h3>
                <div class="stat-number"><?= $metricas['total_vehiculos'] ?></div>
            </div>
            <div class="stat-icon blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="17" r="3"></circle><circle cx="19" cy="17" r="3"></circle><path d="M5 17h14"></path><path d="M9 17l2-7h4l2 7"></path><path d="M11 10h4"></path><circle cx="12" cy="6" r="1"></circle></svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>Total Conductores</h3>
                <div class="stat-number"><?= $metricas['total_conductores'] ?></div>
            </div>
            <div class="stat-icon dark">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><line x1="15" y1="8" x2="17" y2="8"></line><line x1="15" y1="12" x2="17" y2="12"></line><line x1="7" y1="16" x2="17" y2="16"></line></svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>Asociaciones</h3>
                <div class="stat-number"><?= $metricas['total_asociaciones'] ?></div>
            </div>
            <div class="stat-icon warning">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="9" y1="6" x2="9" y2="6.01"></line><line x1="15" y1="6" x2="15" y2="6.01"></line><line x1="9" y1="10" x2="9" y2="10.01"></line><line x1="15" y1="10" x2="15" y2="10.01"></line><line x1="9" y1="14" x2="9" y2="14.01"></line><line x1="15" y1="14" x2="15" y2="14.01"></line><line x1="9" y1="18" x2="15" y2="18"></line></svg>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>Infracciones Pendientes</h3>
                <div class="stat-number"><?= $metricas['infracciones_pendientes'] ?></div>
            </div>
            <div class="stat-icon danger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            </div>
        </div>
    </div>

    <h3 style="color:#0b1329; border-bottom: 2px solid #2563eb; padding-bottom: 8px; margin-bottom: 20px;">Módulos de Reportes Específicos</h3>
    <div class="quick-actions no-print">
        <a href="<?= vistaUrl('reportes/reporte_vehiculos.php') ?>" class="action-tile">
            <div class="tile-icon-wrapper" style="width: 44px; height: 44px; margin-bottom: 4px;">
                <svg style="width: 22px; height: 22px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="17" r="3"></circle><circle cx="19" cy="17" r="3"></circle><path d="M5 17h14"></path><path d="M9 17l2-7h4l2 7"></path><path d="M11 10h4"></path><circle cx="12" cy="6" r="1"></circle></svg>
            </div>
            <span>Reporte Mototaxis</span>
        </a>
        <a href="<?= vistaUrl('reportes/reporte_conductores.php') ?>" class="action-tile">
            <div class="tile-icon-wrapper" style="width: 44px; height: 44px; margin-bottom: 4px;">
                <svg style="width: 22px; height: 22px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><line x1="15" y1="8" x2="17" y2="8"></line><line x1="15" y1="12" x2="17" y2="12"></line><line x1="7" y1="16" x2="17" y2="16"></line></svg>
            </div>
            <span>Reporte Conductores</span>
        </a>
        <a href="<?= vistaUrl('reportes/reporte_infracciones.php') ?>" class="action-tile">
            <div class="tile-icon-wrapper" style="width: 44px; height: 44px; margin-bottom: 4px;">
                <svg style="width: 22px; height: 22px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            </div>
            <span>Reporte Infracciones</span>
        </a>
        <a href="<?= vistaUrl('reportes/reporte_documentos.php') ?>" class="action-tile">
            <div class="tile-icon-wrapper" style="width: 44px; height: 44px; margin-bottom: 4px;">
                <svg style="width: 22px; height: 22px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            </div>
            <span>Reporte Documentos</span>
        </a>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

