<?php
$tituloPagina = 'Registrar Acta de Infracción';
$subtituloNavbar = 'Operativo de Fiscalización';
$menuActivo = 'fiscalizacion';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';

require_once dirname(dirname(__DIR__)) . '/modelos/Vehiculo.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Conductor.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Infraccion.php';

$vehiculoMod = new Vehiculo();
$conductorMod = new Conductor();
$infMod = new Infraccion();

$vehiculos = $vehiculoMod->listarTodos();
$conductores = $conductorMod->listarTodos();
$tipos = $infMod->listarTipos();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Levantar Acta de Infracción</h1>
        <p class="page-subtitle">Sanción aplicada a unidad o chofer en operativo municipal</p>
    </div>
    <a href="<?= vistaUrl('fiscalizacion/listar_infracciones.php') ?>" class="btn btn-outline">&larr; Volver</a>
</div>

<div class="card">
    <form action="<?= controladorUrl('infraccion_controlador.php') ?>" method="POST">
        <input type="hidden" name="accion" value="crear">

        <div class="form-grid">
            <div class="form-group">
                <label for="numero_acta">N° de Acta <span class="required">*</span></label>
                <input type="text" id="numero_acta" name="numero_acta" class="form-control" value="ACT-<?= date('Y') ?>-<?= rand(1000, 9999) ?>" required>
            </div>

            <div class="form-group">
                <label for="id_tipo_infraccion">Tipo de Infracción (Falta) <span class="required">*</span></label>
                <select id="id_tipo_infraccion" name="id_tipo_infraccion" class="form-control" required onchange="actualizarMonto(this)">
                    <option value="">-- Selecciona Infracción --</option>
                    <?php foreach ($tipos as $t): ?>
                    <option value="<?= $t['id_tipo_infraccion'] ?>" data-monto="<?= $t['monto_sancion'] ?>">
                        <?= htmlspecialchars($t['codigo'] . ' - ' . substr($t['descripcion'], 0, 60)) ?>... (S/. <?= number_format($t['monto_sancion'], 2) ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_vehiculo">Vehículo Sancionado <span class="required">*</span></label>
                <select id="id_vehiculo" name="id_vehiculo" class="form-control" required>
                    <option value="">-- Selecciona Vehículo --</option>
                    <?php foreach ($vehiculos as $v): ?>
                    <option value="<?= $v['id_vehiculo'] ?>"><?= htmlspecialchars($v['placa'] . ' - ' . $v['marca']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_conductor">Conductor al Volante</label>
                <select id="id_conductor" name="id_conductor" class="form-control">
                    <option value="">-- No identificado o Propietario --</option>
                    <?php foreach ($conductores as $c): ?>
                    <option value="<?= $c['id_conductor'] ?>"><?= htmlspecialchars($c['dni'] . ' - ' . $c['nombres'] . ' ' . $c['apellidos']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="fecha_infraccion">Fecha y Hora de Intervención <span class="required">*</span></label>
                <input type="datetime-local" id="fecha_infraccion" name="fecha_infraccion" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required>
            </div>

            <div class="form-group">
                <label for="lugar_infraccion">Lugar de Intervención <span class="required">*</span></label>
                <input type="text" id="lugar_infraccion" name="lugar_infraccion" class="form-control" placeholder="Ej: Av. Central con Jr. Comercio" required>
            </div>

            <div class="form-group">
                <label for="monto">Monto de la Multa (S/.) <span class="required">*</span></label>
                <input type="number" step="0.01" id="monto" name="monto" class="form-control" placeholder="0.00" required>
            </div>

            <div class="form-group">
                <label for="estado_pago">Estado de Pago</label>
                <select id="estado_pago" name="estado_pago" class="form-control">
                    <option value="Pendiente">Pendiente</option>
                    <option value="Pagado">Pagado en el acto</option>
                </select>
            </div>

            <div class="form-group full-width">
                <label for="observacion">Observaciones del Fiscalizador</label>
                <textarea id="observacion" name="observacion" class="form-control" placeholder="Detalles de la falta cometida, renuencia o retención de unidad..."></textarea>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= vistaUrl('fiscalizacion/listar_infracciones.php') ?>" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">Emitir Acta de Infracción</button>
        </div>
    </form>
</div>

<script>
function actualizarMonto(select) {
    const opcion = select.options[select.selectedIndex];
    const monto = opcion.getAttribute('data-monto');
    if (monto) {
        document.getElementById('monto').value = parseFloat(monto).toFixed(2);
    }
}
</script>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

