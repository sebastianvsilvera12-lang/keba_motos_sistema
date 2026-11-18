<?php
$tituloPagina = 'Registrar Documento';
$subtituloNavbar = 'Nuevo SOAT / TUC';
$menuActivo = 'documentos';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/models/Vehiculo.php';

$vehiculoMod = new Vehiculo();
$vehiculos = $vehiculoMod->listarTodos();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Registrar Documento Vehicular</h1>
        <p class="page-subtitle">Carga de pólizas de SOAT, tarjeta de circulación o revisión técnica</p>
    </div>
    <a href="<?= vistaUrl('documentos/listar_documentos.php') ?>" class="btn btn-outline">&larr; Volver</a>
</div>

<div class="card">
    <form action="<?= controladorUrl('documento_controlador.php') ?>" method="POST">
        <input type="hidden" name="accion" value="crear">

        <div class="form-grid">
            <div class="form-group">
                <label for="id_vehiculo">Mototaxi (Placa) <span class="required">*</span></label>
                <select id="id_vehiculo" name="id_vehiculo" class="form-control" required>
                    <option value="">-- Selecciona Mototaxi --</option>
                    <?php foreach ($vehiculos as $v): ?>
                    <option value="<?= $v['id_vehiculo'] ?>"><?= htmlspecialchars($v['placa'] . ' - ' . $v['marca'] . ' ' . $v['modelo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="tipo_documento">Tipo de Documento <span class="required">*</span></label>
                <select id="tipo_documento" name="tipo_documento" class="form-control" required>
                    <option value="SOAT">SOAT (Seguro Obligatorio)</option>
                    <option value="Tarjeta de Circulacion">Tarjeta de Circulación (TUC)</option>
                    <option value="Revision Tecnica">Revisión Técnica</option>
                    <option value="Tarjeta de Propiedad">Tarjeta de Propiedad</option>
                    <option value="Seguro Municipal">Seguro Municipal</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>

            <div class="form-group">
                <label for="numero_documento">N° de Documento / Póliza <span class="required">*</span></label>
                <input type="text" id="numero_documento" name="numero_documento" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="entidad_emisora">Entidad Emisora / Aseguradora</label>
                <input type="text" id="entidad_emisora" name="entidad_emisora" class="form-control" placeholder="Ej: La Positiva / Pacífico / Municipalidad">
            </div>

            <div class="form-group">
                <label for="fecha_emision">Fecha de Emisión <span class="required">*</span></label>
                <input type="date" id="fecha_emision" name="fecha_emision" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="fecha_vencimiento">Fecha de Vencimiento <span class="required">*</span></label>
                <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" class="form-control" required>
            </div>

            <div class="form-group full-width">
                <label for="observaciones">Observaciones</label>
                <textarea id="observaciones" name="observaciones" class="form-control" placeholder="Notas sobre la póliza..."></textarea>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= vistaUrl('documentos/listar_documentos.php') ?>" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Documento</button>
        </div>
    </form>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

