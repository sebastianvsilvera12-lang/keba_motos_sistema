<?php
$tituloPagina = 'Detalle del Conductor';
$subtituloNavbar = 'Ficha del Chofer';
$menuActivo = 'conductores';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/models/Conductor.php';

$id = (int)($_GET['id'] ?? 0);
$conductorMod = new Conductor();
$c = $conductorMod->obtenerPorId($id);

if (!$c) {
    echo "<div class='alert alert-danger'>Conductor no encontrado.</div>";
    require_once dirname(__DIR__) . '/plantillas/footer.php';
    exit;
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><?= htmlspecialchars($c['nombres'] . ' ' . $c['apellidos']) ?></h1>
        <p class="page-subtitle">DNI: <?= htmlspecialchars($c['dni']) ?> | Licencia: <?= htmlspecialchars($c['numero_licencia']) ?></p>
    </div>
    <div>
        <a href="<?= vistaUrl('conductores/editar_conductor.php?id=' . $c['id_conductor']) ?>" class="btn btn-primary">Editar</a>
        <a href="<?= vistaUrl('conductores/listar_conductores.php') ?>" class="btn btn-outline">&larr; Volver</a>
    </div>
</div>

<div class="card">
    <div class="row">
        <div class="col-md-6 mb-3">
            <h3 style="font-size: 1.1rem; color: #0b1329; border-bottom: 2px solid #2563eb; padding-bottom: 6px; margin-bottom: 15px;">Datos Personales</h3>
            <p><strong>DNI:</strong> <?= htmlspecialchars($c['dni']) ?></p>
            <p><strong>Nombres y Apellidos:</strong> <?= htmlspecialchars($c['nombres'] . ' ' . $c['apellidos']) ?></p>
            <p><strong>Teléfono:</strong> <?= htmlspecialchars($c['telefono'] ?? '-') ?></p>
            <p><strong>Dirección:</strong> <?= htmlspecialchars($c['direccion'] ?? '-') ?></p>
            <p><strong>Fecha de Nacimiento:</strong> <?= htmlspecialchars($c['fecha_nacimiento'] ?? '-') ?></p>
        </div>
        <div class="col-md-6 mb-3">
            <h3 style="font-size: 1.1rem; color: #0b1329; border-bottom: 2px solid #2563eb; padding-bottom: 6px; margin-bottom: 15px;">Licencia de Conducir</h3>
            <p><strong>N° Licencia:</strong> <?= htmlspecialchars($c['numero_licencia']) ?></p>
            <p><strong>Categoría:</strong> <?= htmlspecialchars($c['categoria_licencia']) ?></p>
            <p><strong>Fecha Emisión:</strong> <?= htmlspecialchars($c['fecha_emision_licencia'] ?? '-') ?></p>
            <p><strong>Fecha Vencimiento:</strong> <?= htmlspecialchars($c['fecha_vencimiento_licencia']) ?></p>
            <p><strong>Estado:</strong> <span class="badge badge-success"><?= htmlspecialchars($c['estado']) ?></span></p>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

