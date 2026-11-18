<?php
$tituloPagina = 'Reporte de Conductores';
$subtituloNavbar = 'Reporte Oficial';
$menuActivo = 'reportes';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Conductor.php';

$condMod = new Conductor();
$conductores = $condMod->listarTodos();
?>

<div class="page-header no-print">
    <div>
        <h1 class="page-title">Reporte de Conductores Habilitados</h1>
        <p class="page-subtitle">Padrón de choferes con estado de sus licencias</p>
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
            <strong>Reporte:</strong> Padrón de Choferes
        </div>
    </div>

    <table class="table-custom">
        <thead>
            <tr>
                <th>DNI</th>
                <th>Nombres y Apellidos</th>
                <th>Teléfono</th>
                <th>N° Licencia</th>
                <th>Categoría</th>
                <th>Vencimiento</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($conductores as $c): ?>
            <tr>
                <td><strong><?= htmlspecialchars($c['dni']) ?></strong></td>
                <td><?= htmlspecialchars($c['nombres'] . ' ' . $c['apellidos']) ?></td>
                <td><?= htmlspecialchars($c['telefono'] ?? '-') ?></td>
                <td><?= htmlspecialchars($c['numero_licencia']) ?></td>
                <td><?= htmlspecialchars($c['categoria_licencia']) ?></td>
                <td><?= htmlspecialchars($c['fecha_vencimiento_licencia']) ?></td>
                <td><?= htmlspecialchars($c['estado']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

