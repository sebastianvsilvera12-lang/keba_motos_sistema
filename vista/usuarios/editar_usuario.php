<?php
$tituloPagina = 'Editar Usuario';
$subtituloNavbar = 'Modificar Cuenta';
$menuActivo = 'usuarios';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Usuario.php';

$id = (int)($_GET['id'] ?? 0);
$userMod = new Usuario();
$u = $userMod->obtenerPorId($id);

try {
    $db = Database::conectar();
    $rolesDisponibles = $db->query("SELECT * FROM roles ORDER BY id_rol ASC")->fetchAll();
} catch (Exception $e) {
    $rolesDisponibles = [];
}

if (!$u) {
    echo "<div class='alert alert-danger'>Usuario no encontrado.</div>";
    require_once dirname(__DIR__) . '/plantillas/footer.php';
    exit;
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Editar Usuario: <?= htmlspecialchars($u['usuario']) ?></h1>
        <p class="page-subtitle">Modificar datos personales o restablecer contraseña</p>
    </div>
    <a href="<?= vistaUrl('usuarios/listar_usuarios.php') ?>" class="btn btn-outline">&larr; Volver</a>
</div>

<div class="card">
    <form action="<?= controladorUrl('auth/registro_usuario.php') ?>" method="POST">
        <input type="hidden" name="accion" value="editar">
        <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">

        <div class="form-grid">
            <div class="form-group">
                <label for="id_rol">Rol de Acceso</label>
                <select id="id_rol" name="id_rol" class="form-control" required>
                    <?php if (!empty($rolesDisponibles)): ?>
                        <?php foreach ($rolesDisponibles as $r): ?>
                            <option value="<?= $r['id_rol'] ?>" <?= $u['id_rol'] == $r['id_rol'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['nombre_rol']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="1" <?= $u['id_rol'] == 1 ? 'selected' : '' ?>>Administrador</option>
                        <option value="2" <?= $u['id_rol'] == 2 ? 'selected' : '' ?>>Analista</option>
                        <option value="3" <?= $u['id_rol'] == 3 ? 'selected' : '' ?>>Taxistas</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="dni">DNI</label>
                <input type="text" id="dni" name="dni" class="form-control" value="<?= htmlspecialchars($u['dni']) ?>" required maxlength="8">
            </div>

            <div class="form-group">
                <label for="nombres">Nombres</label>
                <input type="text" id="nombres" name="nombres" class="form-control" value="<?= htmlspecialchars($u['nombres']) ?>" required>
            </div>

            <div class="form-group">
                <label for="apellidos">Apellidos</label>
                <input type="text" id="apellidos" name="apellidos" class="form-control" value="<?= htmlspecialchars($u['apellidos']) ?>" required>
            </div>

            <div class="form-group">
                <label for="usuario">Nombre de Usuario</label>
                <input type="text" id="usuario" name="usuario" class="form-control" value="<?= htmlspecialchars($u['usuario']) ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Nueva Contraseña (dejar en blanco para no cambiarla)</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($u['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" class="form-control">
                    <option value="Activo" <?= $u['estado'] === 'Activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="Inactivo" <?= $u['estado'] === 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= vistaUrl('usuarios/listar_usuarios.php') ?>" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

