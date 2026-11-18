<?php
$tituloPagina = 'Detalle del Vehículo';
$subtituloNavbar = 'Ficha Técnica';
$menuActivo = 'vehiculos';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Vehiculo.php';

$id = (int)($_GET['id'] ?? 0);
$vehiculoMod = new Vehiculo();
$v = $vehiculoMod->obtenerPorId($id);

if (!$v) {
    echo "<div class='alert alert-danger'>Vehículo no encontrado.</div>";
    require_once dirname(__DIR__) . '/plantillas/footer.php';
    exit;
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Ficha Técnica: <?= htmlspecialchars($v['placa']) ?></h1>
        <p class="page-subtitle">Información completa de la mototaxi</p>
    </div>
    <div>
        <a href="<?= vistaUrl('vehiculos/editar_vehiculo.php?id=' . $v['id_vehiculo']) ?>" class="btn btn-primary">Editar</a>
        <a href="<?= vistaUrl('vehiculos/listar_vehiculos.php') ?>" class="btn btn-outline">&larr; Volver</a>
    </div>
</div>

<div class="card">
    <div class="row">
        <div class="col-md-6 mb-3">
            <h3 style="font-size: 1.1rem; color: #0b1329; border-bottom: 2px solid #2563eb; padding-bottom: 6px; margin-bottom: 15px;">Datos del Vehículo</h3>
            <p><strong>Placa:</strong> <?= htmlspecialchars($v['placa']) ?></p>
            <p><strong>N° Padrón:</strong> <?= htmlspecialchars($v['numero_padron'] ?? '-') ?></p>
            <p><strong>Marca / Modelo:</strong> <?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?></p>
            <p><strong>Color:</strong> <?= htmlspecialchars($v['color']) ?></p>
            <p><strong>Año de Fabricación:</strong> <?= htmlspecialchars($v['anio_fabricacion'] ?? '-') ?></p>
            <p><strong>N° de Motor:</strong> <?= htmlspecialchars($v['numero_motor'] ?? '-') ?></p>
            <p><strong>N° de Chasis:</strong> <?= htmlspecialchars($v['numero_chasis'] ?? '-') ?></p>
            <p><strong>Estado:</strong> <span class="badge badge-success"><?= htmlspecialchars($v['estado']) ?></span></p>
        </div>
        <div class="col-md-6 mb-3">
            <h3 style="font-size: 1.1rem; color: #0b1329; border-bottom: 2px solid #2563eb; padding-bottom: 6px; margin-bottom: 15px;">Acciones Vinculadas</h3>
            <div class="quick-actions">
                <a href="<?= vistaUrl('documentos/registrar_documento.php') ?>" class="action-tile">
                    <div class="tile-icon-wrapper" style="width: 40px; height: 40px; margin-bottom: 2px;">
                        <svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </div>
                    <span>Adjuntar Documento</span>
                </a>
                <a href="<?= vistaUrl('fiscalizacion/registrar_infraccion.php') ?>" class="action-tile">
                    <div class="tile-icon-wrapper" style="width: 40px; height: 40px; margin-bottom: 2px;">
                        <svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    </div>
                    <span>Levantar Acta</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

