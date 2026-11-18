<?php
$tituloPagina = 'Historial de Infracciones por Conductor';
$subtituloNavbar = 'Antecedentes de Chofer';
$menuActivo = 'fiscalizacion';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/models/Infraccion.php';

$dni = trim($_GET['dni'] ?? '');
$infMod = new Infraccion();
$todas = $infMod->listarTodos();

$filtradas = [];
if (!empty($dni)) {
    foreach ($todas as $i) {
        if (!empty($i['conductor_dni']) && strpos($i['conductor_dni'], $dni) !== false) {
            $filtradas[] = $i;
        }
    }
} else {
    $filtradas = $todas;
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Historial de Infracciones por Conductor</h1>
        <p class="page-subtitle">Récord de faltas cometidas por choferes</p>
    </div>
</div>

<div class="card">
    <form method="GET" class="table-toolbar">
        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" name="dni" value="<?= htmlspecialchars($dni) ?>" placeholder="Buscar por DNI del chofer...">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
    </form>

    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Acta</th>
                    <th>DNI Conductor</th>
                    <th>Conductor</th>
                    <th>Placa</th>
                    <th>Falta</th>
                    <th>Monto</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($filtradas)): ?>
                <tr><td colspan="7" style="text-align:center; padding:20px;">No hay infracciones para este criterio.</td></tr>
                <?php else: ?>
                <?php foreach ($filtradas as $f): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($f['numero_acta']) ?></strong></td>
                    <td><?= htmlspecialchars($f['conductor_dni'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($f['conductor_nombres'] . ' ' . $f['conductor_apellidos']) ?></td>
                    <td><?= htmlspecialchars($f['placa']) ?></td>
                    <td><?= htmlspecialchars($f['codigo_infraccion']) ?></td>
                    <td>S/. <?= number_format($f['monto'], 2) ?></td>
                    <td><span class="badge <?= $f['estado_pago'] === 'Pagado' ? 'badge-success' : 'badge-danger' ?>"><?= htmlspecialchars($f['estado_pago']) ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

