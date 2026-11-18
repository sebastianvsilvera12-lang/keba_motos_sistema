<?php
$tituloPagina = 'Editar Vehículo';
$subtituloNavbar = 'Modificar Mototaxi';
$menuActivo = 'vehiculos';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';

require_once dirname(dirname(__DIR__)) . '/modelos/Vehiculo.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Propietario.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Asociacion.php';

$id = (int)($_GET['id'] ?? 0);
$vehiculoMod = new Vehiculo();
$v = $vehiculoMod->obtenerPorId($id);

if (!$v) {
    echo "<div class='alert alert-danger'>Vehículo no encontrado.</div>";
    require_once dirname(__DIR__) . '/plantillas/footer.php';
    exit;
}

$propMod = new Propietario();
$asocMod = new Asociacion();
$propietarios = $propMod->listarTodos();
$asociaciones = $asocMod->listarTodos();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Editar Mototaxi: <?= htmlspecialchars($v['placa']) ?></h1>
        <p class="page-subtitle">Modificación de características técnicas y afiliación</p>
    </div>
    <a href="<?= vistaUrl('vehiculos/listar_vehiculos.php') ?>" class="btn btn-outline">&larr; Volver</a>
</div>

<div class="card">
    <form action="<?= controladorUrl('vehiculo_controlador.php') ?>" method="POST">
        <input type="hidden" name="accion" value="editar">
        <input type="hidden" name="id_vehiculo" value="<?= $v['id_vehiculo'] ?>">

        <div class="form-grid">
            <div class="form-group">
                <label for="placa">Placa</label>
                <input type="text" id="placa" name="placa" class="form-control" value="<?= htmlspecialchars($v['placa']) ?>" required>
            </div>

            <div class="form-group">
                <label for="numero_padron">N° de Padrón</label>
                <input type="text" id="numero_padron" name="numero_padron" class="form-control" value="<?= htmlspecialchars($v['numero_padron'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="marca">Marca</label>
                <input type="text" id="marca" name="marca" class="form-control" value="<?= htmlspecialchars($v['marca']) ?>" required>
            </div>

            <div class="form-group">
                <label for="modelo">Modelo</label>
                <input type="text" id="modelo" name="modelo" class="form-control" value="<?= htmlspecialchars($v['modelo']) ?>">
            </div>

            <div class="form-group">
                <label for="color">Color</label>
                <input type="text" id="color" name="color" class="form-control" value="<?= htmlspecialchars($v['color']) ?>" required>
            </div>

            <div class="form-group">
                <label for="anio_fabricacion">Año</label>
                <input type="number" id="anio_fabricacion" name="anio_fabricacion" class="form-control" value="<?= htmlspecialchars($v['anio_fabricacion'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="id_propietario">Propietario</label>
                <select id="id_propietario" name="id_propietario" class="form-control" required>
                    <?php foreach ($propietarios as $p): ?>
                    <option value="<?= $p['id_propietario'] ?>" <?= $p['id_propietario'] == $v['id_propietario'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nombres_razon_social'] . ' - ' . $p['numero_documento']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_asociacion">Asociación</label>
                <select id="id_asociacion" name="id_asociacion" class="form-control">
                    <option value="">-- Sin Asociación --</option>
                    <?php foreach ($asociaciones as $a): ?>
                    <option value="<?= $a['id_asociacion'] ?>" <?= $a['id_asociacion'] == $v['id_asociacion'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" class="form-control">
                    <option value="Activo" <?= $v['estado'] === 'Activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="Inactivo" <?= $v['estado'] === 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    <option value="Observado" <?= $v['estado'] === 'Observado' ? 'selected' : '' ?>>Observado</option>
                    <option value="De Baja" <?= $v['estado'] === 'De Baja' ? 'selected' : '' ?>>De Baja</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= vistaUrl('vehiculos/listar_vehiculos.php') ?>" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar Cambios</button>
        </div>
    </form>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

