<?php
$tituloPagina = 'Registrar Vehículo';
$subtituloNavbar = 'Nueva Mototaxi';
$menuActivo = 'vehiculos';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';

require_once dirname(dirname(__DIR__)) . '/models/Propietario.php';
require_once dirname(dirname(__DIR__)) . '/models/Asociacion.php';

$propMod = new Propietario();
$asocMod = new Asociacion();
$propietarios = $propMod->listarTodos();
$asociaciones = $asocMod->listarTodos();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Registrar Nueva Mototaxi</h1>
        <p class="page-subtitle">Ingresa la información técnica y de registro de la unidad</p>
    </div>
    <a href="<?= vistaUrl('vehiculos/listar_vehiculos.php') ?>" class="btn btn-outline">&larr; Volver a la Lista</a>
</div>

<div class="card">
    <form action="<?= controladorUrl('vehiculo_controlador.php') ?>" method="POST">
        <input type="hidden" name="accion" value="crear">

        <div class="form-grid">
            <div class="form-group">
                <label for="placa">Placa <span class="required">*</span></label>
                <input type="text" id="placa" name="placa" class="form-control" placeholder="Ej: 1234-5B" required>
            </div>

            <div class="form-group">
                <label for="numero_padron">N° de Padrón Municipal</label>
                <input type="text" id="numero_padron" name="numero_padron" class="form-control" placeholder="Ej: PAD-025">
            </div>

            <div class="form-group">
                <label for="marca">Marca <span class="required">*</span></label>
                <input type="text" id="marca" name="marca" class="form-control" placeholder="Ej: Bajaj / TVS / Wanxin" required>
            </div>

            <div class="form-group">
                <label for="modelo">Modelo</label>
                <input type="text" id="modelo" name="modelo" class="form-control" placeholder="Ej: RE 205 Torito">
            </div>

            <div class="form-group">
                <label for="color">Color Principal <span class="required">*</span></label>
                <input type="text" id="color" name="color" class="form-control" placeholder="Ej: Azul con Blanco" required>
            </div>

            <div class="form-group">
                <label for="anio_fabricacion">Año de Fabricación</label>
                <input type="number" id="anio_fabricacion" name="anio_fabricacion" class="form-control" placeholder="Ej: 2022" min="1990" max="2030">
            </div>

            <div class="form-group">
                <label for="numero_motor">Número de Motor</label>
                <input type="text" id="numero_motor" name="numero_motor" class="form-control" placeholder="Ej: BJ205-123456">
            </div>

            <div class="form-group">
                <label for="numero_chasis">Número de Chasis / VIN</label>
                <input type="text" id="numero_chasis" name="numero_chasis" class="form-control" placeholder="Ej: CHASIS-987456">
            </div>

            <div class="form-group">
                <label for="id_propietario">Propietario <span class="required">*</span></label>
                <select id="id_propietario" name="id_propietario" class="form-control" required>
                    <option value="">-- Selecciona un Propietario --</option>
                    <?php foreach ($propietarios as $p): ?>
                    <option value="<?= $p['id_propietario'] ?>"><?= htmlspecialchars($p['nombres_razon_social'] . ' - ' . $p['numero_documento']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_asociacion">Asociación / Empresa</label>
                <select id="id_asociacion" name="id_asociacion" class="form-control">
                    <option value="">-- Sin Asociación (Independiente) --</option>
                    <?php foreach ($asociaciones as $a): ?>
                    <option value="<?= $a['id_asociacion'] ?>"><?= htmlspecialchars($a['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" class="form-control">
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                    <option value="Observado">Observado</option>
                    <option value="De Baja">De Baja</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= vistaUrl('vehiculos/listar_vehiculos.php') ?>" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Vehículo</button>
        </div>
    </form>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

