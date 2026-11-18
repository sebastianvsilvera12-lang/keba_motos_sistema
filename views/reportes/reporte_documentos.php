<?php
$tituloPagina = 'Reporte de Documentos';
$subtituloNavbar = 'Estado de Pólizas';
$menuActivo = 'reportes';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/models/Documento.php';

$docMod = new Documento();
$documentos = $docMod->listarTodos();
?>

<div class="page-header no-print">
    <div>
        <h1 class="page-title">Reporte de Estado de Documentación</h1>
        <p class="page-subtitle">Pólizas SOAT, tarjetas de circulación e inspecciones técnicas</p>
    </div>
    <button onclick="window.print()" class="btn btn-primary">🖨️ Imprimir Reporte</button>
</div>

<div class="report-container">
    <div class="report-header">
        <div class="report-logo">KEBA <span>MOTOS</span></div>
        <div class="report-meta">
            <strong>Fecha de Emisión:</strong> <?= date('d/m/Y h:i A') ?><br>
            <strong>Reporte:</strong> Pólizas y Permisos
        </div>
    </div>

    <table class="table-custom">
        <thead>
            <tr>
                <th>Placa</th>
                <th>Tipo</th>
                <th>N° Póliza</th>
                <th>Aseguradora</th>
                <th>Emisión</th>
                <th>Vencimiento</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($documentos as $d): ?>
            <tr>
                <td><strong><?= htmlspecialchars($d['placa']) ?></strong></td>
                <td><?= htmlspecialchars($d['tipo_documento']) ?></td>
                <td><?= htmlspecialchars($d['numero_documento']) ?></td>
                <td><?= htmlspecialchars($d['entidad_emisora'] ?? '-') ?></td>
                <td><?= htmlspecialchars($d['fecha_emision']) ?></td>
                <td><strong><?= htmlspecialchars($d['fecha_vencimiento']) ?></strong></td>
                <td><?= htmlspecialchars($d['estado']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

