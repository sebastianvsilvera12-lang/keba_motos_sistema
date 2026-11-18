<?php
$tituloPagina = 'Detalle de Documento';
$subtituloNavbar = 'Información de Póliza';
$menuActivo = 'documentos';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/models/Documento.php';

$id = (int)($_GET['id'] ?? 0);
$docMod = new Documento();
$d = $docMod->obtenerPorId($id);

if (!$d) {
    echo "<div class='alert alert-danger'>Documento no encontrado.</div>";
    require_once dirname(__DIR__) . '/plantillas/footer.php';
    exit;
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><?= htmlspecialchars($d['tipo_documento']) ?>: <?= htmlspecialchars($d['numero_documento']) ?></h1>
        <p class="page-subtitle">Emisor: <?= htmlspecialchars($d['entidad_emisora'] ?? 'No especificado') ?></p>
    </div>
    <a href="<?= vistaUrl('documentos/listar_documentos.php') ?>" class="btn btn-outline">&larr; Volver</a>
</div>

<div class="card">
    <p><strong>Tipo de Documento:</strong> <?= htmlspecialchars($d['tipo_documento']) ?></p>
    <p><strong>Número de Póliza / Registro:</strong> <?= htmlspecialchars($d['numero_documento']) ?></p>
    <p><strong>Entidad Emisora:</strong> <?= htmlspecialchars($d['entidad_emisora'] ?? '-') ?></p>
    <p><strong>Fecha de Emisión:</strong> <?= htmlspecialchars($d['fecha_emision']) ?></p>
    <p><strong>Fecha de Vencimiento:</strong> <?= htmlspecialchars($d['fecha_vencimiento']) ?></p>
    <p><strong>Estado:</strong> <span class="badge badge-success"><?= htmlspecialchars($d['estado']) ?></span></p>
    <p><strong>Observaciones:</strong> <?= htmlspecialchars($d['observaciones'] ?? 'Ninguna') ?></p>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

