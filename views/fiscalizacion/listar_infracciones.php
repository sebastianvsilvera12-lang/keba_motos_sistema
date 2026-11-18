<?php
$tituloPagina = 'Fiscalización e Infracciones';
$subtituloNavbar = 'Actas de Control';
$menuActivo = 'fiscalizacion';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/models/Infraccion.php';

$infMod = new Infraccion();
$infracciones = $infMod->listarTodos();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Actas de Infracción y Fiscalización</h1>
        <p class="page-subtitle">Registro de sanciones aplicadas en operativos de campo</p>
    </div>
    <div>
        <a href="<?= vistaUrl('fiscalizacion/infracciones_pendientes.php') ?>" class="btn btn-dark">Pendientes de Pago</a>
        <a href="<?= vistaUrl('fiscalizacion/registrar_infraccion.php') ?>" class="btn btn-primary">+ Nueva Acta</a>
    </div>
</div>

<div class="card">
    <div class="table-toolbar">
        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" id="inputBuscar" placeholder="Buscar por acta, placa o chofer...">
        </div>
        <div style="font-size: 0.9rem; color: #64748b;">
            Total: <strong><?= count($infracciones) ?></strong> actas emitidas
        </div>
    </div>

    <div class="table-responsive">
        <table class="table-custom" id="tablaRegistros">
            <thead>
                <tr>
                    <th>N° Acta</th>
                    <th>Fecha</th>
                    <th>Placa</th>
                    <th>Conductor</th>
                    <th>Infracción</th>
                    <th>Gravedad</th>
                    <th>Monto (S/.)</th>
                    <th>Estado Pago</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($infracciones)): ?>
                <tr><td colspan="9" style="text-align:center; padding:25px;">No se registran infracciones.</td></tr>
                <?php else: ?>
                <?php foreach ($infracciones as $i): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($i['numero_acta']) ?></strong></td>
                    <td><?= htmlspecialchars($i['fecha_infraccion']) ?></td>
                    <td><strong><?= htmlspecialchars($i['placa']) ?></strong></td>
                    <td><?= htmlspecialchars($i['conductor_nombres'] ? $i['conductor_nombres'] . ' ' . $i['conductor_apellidos'] : 'No identificado') ?></td>
                    <td><span title="<?= htmlspecialchars($i['descripcion_infraccion']) ?>"><?= htmlspecialchars($i['codigo_infraccion']) ?></span></td>
                    <td>
                        <span class="badge <?= $i['gravedad'] === 'Muy Grave' ? 'badge-danger' : ($i['gravedad'] === 'Grave' ? 'badge-warning' : 'badge-info') ?>">
                            <?= htmlspecialchars($i['gravedad']) ?>
                        </span>
                    </td>
                    <td><strong>S/. <?= number_format($i['monto'], 2) ?></strong></td>
                    <td>
                        <span class="badge <?= $i['estado_pago'] === 'Pagado' ? 'badge-success' : ($i['estado_pago'] === 'Pendiente' ? 'badge-danger' : 'badge-dark') ?>">
                            <?= htmlspecialchars($i['estado_pago']) ?>
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <?php if ($i['estado_pago'] === 'Pendiente'): ?>
                        <form action="<?= controladorUrl('infraccion_controlador.php') ?>" method="POST" style="display:inline-block;">
                            <input type="hidden" name="accion" value="pagar">
                            <input type="hidden" name="id_infraccion" value="<?= $i['id_infraccion'] ?>">
                            <input type="hidden" name="comprobante_pago" value="REC-<?= time() ?>">
                            <button type="submit" class="btn btn-primary btn-sm">Cobrar</button>
                        </form>
                        <a href="<?= controladorUrl('infraccion_controlador.php?accion=anular&id=' . $i['id_infraccion']) ?>" class="btn btn-danger btn-sm btn-confirmar-eliminar">Anular</a>
                        <?php else: ?>
                        <span style="font-size: 0.8rem; color:#64748b;">Completado</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

