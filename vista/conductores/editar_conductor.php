<?php
$tituloPagina = 'Editar Conductor';
$subtituloNavbar = 'Modificar Chofer';
$menuActivo = 'conductores';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Conductor.php';

$id = (int)($_GET['id'] ?? 0);
$conductorMod = new Conductor();
$c = $conductorMod->obtenerPorId($id);

if (!$c) {
    echo "<div class='alert alert-danger'>Conductor no encontrado.</div>";
    require_once dirname(__DIR__) . '/plantillas/footer.php';
    exit;
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Editar Conductor: <?= htmlspecialchars($c['nombres'] . ' ' . $c['apellidos']) ?></h1>
        <p class="page-subtitle">Actualizar datos de filiación o vigencia de licencia</p>
    </div>
    <a href="<?= vistaUrl('conductores/listar_conductores.php') ?>" class="btn btn-outline">&larr; Volver</a>
</div>

<div class="card">
    <form action="<?= controladorUrl('conductor_controlador.php') ?>" method="POST">
        <input type="hidden" name="accion" value="editar">
        <input type="hidden" name="id_conductor" value="<?= $c['id_conductor'] ?>">

        <div class="form-grid">
            <div class="form-group">
                <label for="dni">DNI</label>
                <input type="text" id="dni" name="dni" class="form-control" value="<?= htmlspecialchars($c['dni']) ?>" required maxlength="8">
            </div>

            <div class="form-group">
                <label for="nombres">Nombres</label>
                <input type="text" id="nombres" name="nombres" class="form-control" value="<?= htmlspecialchars($c['nombres']) ?>" required>
            </div>

            <div class="form-group">
                <label for="apellidos">Apellidos</label>
                <input type="text" id="apellidos" name="apellidos" class="form-control" value="<?= htmlspecialchars($c['apellidos']) ?>" required>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" class="form-control" value="<?= htmlspecialchars($c['telefono'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="numero_licencia">N° Licencia</label>
                <input type="text" id="numero_licencia" name="numero_licencia" class="form-control" value="<?= htmlspecialchars($c['numero_licencia']) ?>" required>
            </div>

            <div class="form-group">
                <label for="categoria_licencia">Categoría</label>
                <input type="text" id="categoria_licencia" name="categoria_licencia" class="form-control" value="<?= htmlspecialchars($c['categoria_licencia']) ?>" required>
            </div>

            <div class="form-group">
                <label for="fecha_vencimiento_licencia">Vencimiento Licencia</label>
                <input type="date" id="fecha_vencimiento_licencia" name="fecha_vencimiento_licencia" class="form-control" value="<?= htmlspecialchars($c['fecha_vencimiento_licencia']) ?>" required>
            </div>

            <div class="form-group">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" class="form-control">
                    <option value="Habilitado" <?= $c['estado'] === 'Habilitado' ? 'selected' : '' ?>>Habilitado</option>
                    <option value="Inhabilitado" <?= $c['estado'] === 'Inhabilitado' ? 'selected' : '' ?>>Inhabilitado</option>
                    <option value="Suspendido" <?= $c['estado'] === 'Suspendido' ? 'selected' : '' ?>>Suspendido</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= vistaUrl('conductores/listar_conductores.php') ?>" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

