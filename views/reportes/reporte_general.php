<?php
$tituloPagina = 'Reporte General Consolidado';
$subtituloNavbar = 'Resumen Ejecutivo';
$menuActivo = 'reportes';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/models/Reporte.php';

$repMod = new Reporte();
$metricas = $repMod->obtenerMetricasGenerales();
?>

<div class="page-header no-print">
    <div>
        <h1 class="page-title">Reporte General Consolidado</h1>
        <p class="page-subtitle">Indicadores globales y resumen ejecutivo del sistema KEBA MOTOS</p>
    </div>
    <button onclick="window.print()" class="btn btn-primary">🖨️ Imprimir Resumen</button>
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
            <div class="stat-icon blue">🛵</div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>Total Conductores</h3>
                <div class="stat-number"><?= $metricas['total_conductores'] ?></div>
            </div>
            <div class="stat-icon dark">👨‍✈️</div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>Asociaciones</h3>
                <div class="stat-number"><?= $metricas['total_asociaciones'] ?></div>
            </div>
            <div class="stat-icon warning">🏢</div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3>Infracciones Pendientes</h3>
                <div class="stat-number"><?= $metricas['infracciones_pendientes'] ?></div>
            </div>
            <div class="stat-icon danger">⚠️</div>
        </div>
    </div>

    <h3 style="color:#0b1329; border-bottom: 2px solid #2563eb; padding-bottom: 8px; margin-bottom: 20px;">Módulos de Reportes Específicos</h3>
    <div class="quick-actions no-print">
        <a href="<?= vistaUrl('reportes/reporte_vehiculos.php') ?>" class="action-tile">
            <span class="icon">🛵</span>
            <span>Reporte Mototaxis</span>
        </a>
        <a href="<?= vistaUrl('reportes/reporte_conductores.php') ?>" class="action-tile">
            <span class="icon">👨‍✈️</span>
            <span>Reporte Conductores</span>
        </a>
        <a href="<?= vistaUrl('reportes/reporte_infracciones.php') ?>" class="action-tile">
            <span class="icon">👮</span>
            <span>Reporte Infracciones</span>
        </a>
        <a href="<?= vistaUrl('reportes/reporte_documentos.php') ?>" class="action-tile">
            <span class="icon">📄</span>
            <span>Reporte Documentos</span>
        </a>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

