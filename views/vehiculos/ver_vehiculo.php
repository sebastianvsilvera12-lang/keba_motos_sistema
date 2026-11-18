<?php
$tituloPagina = 'Detalle del Vehículo';
$subtituloNavbar = 'Ficha Técnica';
$menuActivo = 'vehiculos';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/models/Vehiculo.php';

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
                    <span class="icon">📄</span>
                    <span>Adjuntar Documento</span>
                </a>
                <a href="<?= vistaUrl('fiscalizacion/registrar_infraccion.php') ?>" class="action-tile">
                    <span class="icon">👮</span>
                    <span>Levantar Acta</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

