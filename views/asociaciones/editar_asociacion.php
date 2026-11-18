<?php
$tituloPagina = 'Editar Asociación';
$subtituloNavbar = 'Modificar Empresa';
$menuActivo = 'asociaciones';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/models/Asociacion.php';

$id = (int)($_GET['id'] ?? 0);
$asocMod = new Asociacion();
$a = $asocMod->obtenerPorId($id);

if (!$a) {
    echo "<div class='alert alert-danger'>Asociación no encontrada.</div>";
    require_once dirname(__DIR__) . '/plantillas/footer.php';
    exit;
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Editar Asociación: <?= htmlspecialchars($a['nombre']) ?></h1>
        <p class="page-subtitle">Actualizar resolución o junta directiva</p>
    </div>
    <a href="<?= vistaUrl('asociaciones/listar_asociaciones.php') ?>" class="btn btn-outline">&larr; Volver</a>
</div>

<div class="card">
    <form action="<?= controladorUrl('asociacion_controlador.php') ?>" method="POST">
        <input type="hidden" name="accion" value="editar">
        <input type="hidden" name="id_asociacion" value="<?= $a['id_asociacion'] ?>">

        <div class="form-grid">
            <div class="form-group">
                <label for="nombre">Nombre de la Asociación</label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="<?= htmlspecialchars($a['nombre']) ?>" required>
            </div>

            <div class="form-group">
                <label for="ruc">RUC</label>
                <input type="text" id="ruc" name="ruc" class="form-control" value="<?= htmlspecialchars($a['ruc'] ?? '') ?>" maxlength="11">
            </div>

            <div class="form-group">
                <label for="resolucion_municipal">Resolución Municipal</label>
                <input type="text" id="resolucion_municipal" name="resolucion_municipal" class="form-control" value="<?= htmlspecialchars($a['resolucion_municipal'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="presidente">Presidente</label>
                <input type="text" id="presidente" name="presidente" class="form-control" value="<?= htmlspecialchars($a['presidente'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" class="form-control" value="<?= htmlspecialchars($a['telefono'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="color_distintivo">Color Distintivo</label>
                <input type="text" id="color_distintivo" name="color_distintivo" class="form-control" value="<?= htmlspecialchars($a['color_distintivo'] ?? '') ?>">
            </div>

            <div class="form-group full-width">
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" name="direccion" class="form-control" value="<?= htmlspecialchars($a['direccion'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" class="form-control">
                    <option value="Activo" <?= $a['estado'] === 'Activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="Inactivo" <?= $a['estado'] === 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= vistaUrl('asociaciones/listar_asociaciones.php') ?>" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

