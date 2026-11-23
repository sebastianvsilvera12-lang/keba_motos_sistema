<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/constantes.php';
require_once dirname(dirname(__DIR__)) . '/configuracion/session.php';
require_once dirname(dirname(__DIR__)) . '/ayudantes/funciones.php';

$usuarioSesion = obtenerUsuarioActual();
if ($usuarioSesion && (int)($usuarioSesion['rol_id'] ?? 0) === ROL_TAXISTAS) {
    header('Location: ' . vistaUrl('taxista/dashboard.php'));
    exit;
}

$tituloPagina = 'Panel Operativo - Analista';
$subtituloNavbar = 'Módulo Operativo';
$menuActivo = 'dashboard';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Panel Operativo de Analista</h1>
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
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="17" r="3"></circle><circle cx="19" cy="17" r="3"></circle><path d="M5 17h14"></path><path d="M9 17l2-7h4l2 7"></path><path d="M11 10h4"></path><circle cx="12" cy="6" r="1"></circle></svg>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Registro</h3>
            <div class="stat-number">Conductores</div>
            <div class="stat-desc"><a href="<?= vistaUrl('conductores/registrar_conductor.php') ?>" style="color:#2563eb;">Ingresar nuevo chofer &rarr;</a></div>
        </div>
        <div class="stat-icon dark">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><line x1="15" y1="8" x2="17" y2="8"></line><line x1="15" y1="12" x2="17" y2="12"></line><line x1="7" y1="16" x2="17" y2="16"></line></svg>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Registro</h3>
            <div class="stat-number">Documentos</div>
            <div class="stat-desc"><a href="<?= vistaUrl('documentos/registrar_documento.php') ?>" style="color:#2563eb;">Cargar SOAT / TUC &rarr;</a></div>
        </div>
        <div class="stat-icon warning">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><polyline points="9 15 11 17 15 13"></polyline></svg>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

