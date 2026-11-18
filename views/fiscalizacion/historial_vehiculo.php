<?php
$tituloPagina = 'Historial de Infracciones por Vehículo';
$subtituloNavbar = 'Antecedentes por Placa';
$menuActivo = 'fiscalizacion';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/models/Infraccion.php';

$placa = trim($_GET['placa'] ?? '');
$infMod = new Infraccion();
$todas = $infMod->listarTodos();

$filtradas = [];
if (!empty($placa)) {
    foreach ($todas as $i) {
        if (!empty($i['placa']) && stripos($i['placa'], $placa) !== false) {
            $filtradas[] = $i;
        }
    }
} else {
    $filtradas = $todas;
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Historial de Infracciones por Vehículo</h1>
        <p class="page-subtitle">Récord de sanciones acumuladas por placa</p>
    </div>
</div>

<div class="card">
    <form method="GET" class="table-toolbar">
        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" name="placa" value="<?= htmlspecialchars($placa) ?>" placeholder="Buscar por número de placa...">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Consultar Placa</button>
    </form>

    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Placa</th>
                    <th>Acta</th>
                    <th>Fecha</th>
                    <th>Infracción</th>
                    <th>Lugar</th>
                    <th>Monto</th>
                    <th>Estado de Pago</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($filtradas)): ?>
                <tr><td colspan="7" style="text-align:center; padding:20px;">No se registran infracciones para esta placa.</td></tr>
                <?php else: ?>
                <?php foreach ($filtradas as $f): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($f['placa']) ?></strong></td>
                    <td><?= htmlspecialchars($f['numero_acta']) ?></td>
                    <td><?= htmlspecialchars($f['fecha_infraccion']) ?></td>
                    <td><?= htmlspecialchars($f['codigo_infraccion']) ?></td>
                    <td><?= htmlspecialchars($f['lugar_infraccion']) ?></td>
                    <td><strong>S/. <?= number_format($f['monto'], 2) ?></strong></td>
                    <td><span class="badge <?= $f['estado_pago'] === 'Pagado' ? 'badge-success' : 'badge-danger' ?>"><?= htmlspecialchars($f['estado_pago']) ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

