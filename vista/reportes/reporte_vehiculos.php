<?php
$tituloPagina = 'Reporte de Vehículos';
$subtituloNavbar = 'Reporte Oficial';
$menuActivo = 'reportes';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Vehiculo.php';

$vehiculoMod = new Vehiculo();
$vehiculos = $vehiculoMod->listarTodos();
?>

<div class="page-header no-print">
    <div>
        <h1 class="page-title">Reporte General de Mototaxis</h1>
        <p class="page-subtitle">Padrón consolidado de vehículos apto para impresión</p>
    </div>
    <button onclick="window.print()" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        Imprimir Reporte
    </button>
</div>

<div class="report-container">
    <div class="report-header">
        <div class="report-logo">KEBA <span>MOTOS</span></div>
        <div class="report-meta">
            <strong>Fecha de Emisión:</strong> <?= date('d/m/Y h:i A') ?><br>
            <strong>Reporte:</strong> Padrón General de Mototaxis
        </div>
    </div>

    <table class="table-custom">
        <thead>
            <tr>
                <th>Placa</th>
                <th>Padrón</th>
                <th>Marca / Modelo</th>
                <th>Color</th>
                <th>Propietario</th>
                <th>Asociación</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vehiculos as $v): ?>
            <tr>
                <td><strong><?= htmlspecialchars($v['placa']) ?></strong></td>
                <td><?= htmlspecialchars($v['numero_padron'] ?? '-') ?></td>
                <td><?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?></td>
                <td><?= htmlspecialchars($v['color']) ?></td>
                <td><?= htmlspecialchars($v['propietario_nombre'] ?? '-') ?></td>
                <td><?= htmlspecialchars($v['asociacion_nombre'] ?? 'Sin Asociación') ?></td>
                <td><?= htmlspecialchars($v['estado']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

