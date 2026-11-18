<?php
$tituloPagina = 'Panel Operativo - Personal';
$subtituloNavbar = 'Módulo Administrativo';
$menuActivo = 'dashboard';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Panel Operativo del Personal</h1>
        <p class="page-subtitle">Gestión diaria de registros de mototaxis, choferes y documentación</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Registro</h3>
            <div class="stat-number">Vehículos</div>
            <div class="stat-desc"><a href="<?= vistaUrl('vehiculos/registrar_vehiculo.php') ?>" style="color:#2563eb;">Ingresar nueva unidad &rarr;</a></div>
        </div>
        <div class="stat-icon blue">🛵</div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Registro</h3>
            <div class="stat-number">Conductores</div>
            <div class="stat-desc"><a href="<?= vistaUrl('conductores/registrar_conductor.php') ?>" style="color:#2563eb;">Ingresar nuevo chofer &rarr;</a></div>
        </div>
        <div class="stat-icon dark">👨‍✈️</div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Registro</h3>
            <div class="stat-number">Documentos</div>
            <div class="stat-desc"><a href="<?= vistaUrl('documentos/registrar_documento.php') ?>" style="color:#2563eb;">Cargar SOAT / TUC &rarr;</a></div>
        </div>
        <div class="stat-icon warning">📄</div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

