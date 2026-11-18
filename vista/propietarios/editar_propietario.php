<?php
$tituloPagina = 'Editar Propietario';
$subtituloNavbar = 'Modificar Titular';
$menuActivo = 'propietarios';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Propietario.php';

$id = (int)($_GET['id'] ?? 0);
$propMod = new Propietario();
$p = $propMod->obtenerPorId($id);

if (!$p) {
    echo "<div class='alert alert-danger'>Propietario no encontrado.</div>";
    require_once dirname(__DIR__) . '/plantillas/footer.php';
    exit;
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Editar Propietario: <?= htmlspecialchars($p['nombres_razon_social']) ?></h1>
        <p class="page-subtitle">Modificación de datos de contacto o dirección</p>
    </div>
    <a href="<?= vistaUrl('propietarios/listar_propietarios.php') ?>" class="btn btn-outline">&larr; Volver</a>
</div>

<div class="card">
    <form action="<?= controladorUrl('propietario_controlador.php') ?>" method="POST">
        <input type="hidden" name="accion" value="editar">
        <input type="hidden" name="id_propietario" value="<?= $p['id_propietario'] ?>">

        <div class="form-grid">
            <div class="form-group">
                <label for="tipo_documento">Tipo Documento</label>
                <select id="tipo_documento" name="tipo_documento" class="form-control">
                    <option value="DNI" <?= $p['tipo_documento'] === 'DNI' ? 'selected' : '' ?>>DNI</option>
                    <option value="RUC" <?= $p['tipo_documento'] === 'RUC' ? 'selected' : '' ?>>RUC</option>
                    <option value="Carnet Extranjeria" <?= $p['tipo_documento'] === 'Carnet Extranjeria' ? 'selected' : '' ?>>Carnet Extranjería</option>
                </select>
            </div>

            <div class="form-group">
                <label for="numero_documento">N° Documento</label>
                <input type="text" id="numero_documento" name="numero_documento" class="form-control" value="<?= htmlspecialchars($p['numero_documento']) ?>" required>
            </div>

            <div class="form-group">
                <label for="nombres_razon_social">Nombres / Razón Social</label>
                <input type="text" id="nombres_razon_social" name="nombres_razon_social" class="form-control" value="<?= htmlspecialchars($p['nombres_razon_social']) ?>" required>
            </div>

            <div class="form-group">
                <label for="apellidos">Apellidos</label>
                <input type="text" id="apellidos" name="apellidos" class="form-control" value="<?= htmlspecialchars($p['apellidos'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" class="form-control" value="<?= htmlspecialchars($p['telefono'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($p['email'] ?? '') ?>">
            </div>

            <div class="form-group full-width">
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" name="direccion" class="form-control" value="<?= htmlspecialchars($p['direccion'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" class="form-control">
                    <option value="Activo" <?= $p['estado'] === 'Activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="Inactivo" <?= $p['estado'] === 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= vistaUrl('propietarios/listar_propietarios.php') ?>" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar Propietario</button>
        </div>
    </form>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

