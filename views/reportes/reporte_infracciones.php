<?php
$tituloPagina = 'Reporte de Infracciones';
$subtituloNavbar = 'Reporte de Fiscalización';
$menuActivo = 'reportes';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/models/Infraccion.php';

$infMod = new Infraccion();
$infracciones = $infMod->listarTodos();
$totalRecaudado = 0;
foreach ($infracciones as $i) {
    if ($i['estado_pago'] === 'Pagado') {
        $totalRecaudado += $i['monto'];
    }
}
?>

<div class="page-header no-print">
    <div>
        <h1 class="page-title">Reporte de Fiscalización e Infracciones</h1>
        <p class="page-subtitle">Sanciones aplicadas y recaudación municipal</p>
    </div>
    <button onclick="window.print()" class="btn btn-primary">🖨️ Imprimir Reporte</button>
</div>

<div class="report-container">
    <div class="report-header">
        <div class="report-logo">KEBA <span>MOTOS</span></div>
        <div class="report-meta">
            <strong>Fecha de Emisión:</strong> <?= date('d/m/Y h:i A') ?><br>
            <strong>Total Recaudado:</strong> S/. <?= number_format($totalRecaudado, 2) ?>
        </div>
    </div>

    <table class="table-custom">
        <thead>
            <tr>
                <th>Acta</th>
                <th>Fecha</th>
                <th>Placa</th>
                <th>Conductor</th>
                <th>Infracción</th>
                <th>Monto</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($infracciones as $i): ?>
            <tr>
                <td><?= htmlspecialchars($i['numero_acta']) ?></td>
                <td><?= htmlspecialchars($i['fecha_infraccion']) ?></td>
                <td><strong><?= htmlspecialchars($i['placa']) ?></strong></td>
                <td><?= htmlspecialchars($i['conductor_nombres'] ? $i['conductor_nombres'] . ' ' . $i['conductor_apellidos'] : '-') ?></td>
                <td><?= htmlspecialchars($i['codigo_infraccion']) ?></td>
                <td>S/. <?= number_format($i['monto'], 2) ?></td>
                <td><?= htmlspecialchars($i['estado_pago']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

