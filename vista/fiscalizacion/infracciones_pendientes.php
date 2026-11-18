<?php
$tituloPagina = 'Infracciones Pendientes';
$subtituloNavbar = 'Multas por Cobrar';
$menuActivo = 'fiscalizacion';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Infraccion.php';

$infMod = new Infraccion();
$pendientes = $infMod->listarPendientes();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Infracciones Pendientes de Pago</h1>
        <p class="page-subtitle">Cobranzas coactivas y pagos pendientes de mototaxis</p>
    </div>
    <a href="<?= vistaUrl('fiscalizacion/listar_infracciones.php') ?>" class="btn btn-outline">&larr; Ver Todas</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>N° Acta</th>
                    <th>Placa</th>
                    <th>Conductor</th>
                    <th>Infracción</th>
                    <th>Monto (S/.)</th>
                    <th>Estado</th>
                    <th style="text-align: right;">Acción de Cobro</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pendientes)): ?>
                <tr><td colspan="7" style="text-align:center; padding:25px; color:#10b981;"><strong>¡Excelente! No hay infracciones pendientes de pago en este momento.</strong></td></tr>
                <?php else: ?>
                <?php foreach ($pendientes as $p): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($p['numero_acta']) ?></strong></td>
                    <td><strong><?= htmlspecialchars($p['placa']) ?></strong></td>
                    <td><?= htmlspecialchars($p['conductor_nombres'] ? $p['conductor_nombres'] . ' ' . $p['conductor_apellidos'] : 'No asignado') ?></td>
                    <td><?= htmlspecialchars($p['codigo_infraccion']) ?></td>
                    <td><strong style="color:#ef4444;">S/. <?= number_format($p['monto'], 2) ?></strong></td>
                    <td><span class="badge badge-danger">Pendiente</span></td>
                    <td style="text-align: right;">
                        <form action="<?= controladorUrl('infraccion_controlador.php') ?>" method="POST" style="display:inline-block;">
                            <input type="hidden" name="accion" value="pagar">
                            <input type="hidden" name="id_infraccion" value="<?= $p['id_infraccion'] ?>">
                            <input type="hidden" name="comprobante_pago" value="REC-<?= time() ?>">
                            <button type="submit" class="btn btn-primary btn-sm">Registrar Pago</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

