<?php
$tituloPagina = 'Registrar Usuario';
$subtituloNavbar = 'Nueva Cuenta';
$menuActivo = 'usuarios';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/configuracion/conexion.php';

try {
    $db = Database::conectar();
    $rolesDisponibles = $db->query("SELECT * FROM roles ORDER BY id_rol ASC")->fetchAll();
} catch (Exception $e) {
    $rolesDisponibles = [];
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Registrar Nuevo Usuario</h1>
        <p class="page-subtitle">Crear cuenta con permisos en el sistema</p>
    </div>
    <a href="<?= vistaUrl('usuarios/listar_usuarios.php') ?>" class="btn btn-outline">&larr; Volver</a>
</div>

<div class="card">
    <form action="<?= controladorUrl('auth/registro_usuario.php') ?>" method="POST">
        <input type="hidden" name="accion" value="crear">

        <div class="form-grid">
            <div class="form-group">
                <label for="id_rol">Rol de Acceso <span class="required">*</span></label>
                <select id="id_rol" name="id_rol" class="form-control" required>
                    <?php if (!empty($rolesDisponibles)): ?>
                        <?php foreach ($rolesDisponibles as $r): ?>
                            <option value="<?= $r['id_rol'] ?>" <?= $r['id_rol'] == 2 ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['nombre_rol']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="1">Administrador</option>
                        <option value="2" selected>Analista</option>
                        <option value="3">Taxistas</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="dni">DNI <span class="required">*</span></label>
                <input type="text" id="dni" name="dni" class="form-control" placeholder="8 dígitos" required maxlength="8">
            </div>

            <div class="form-group">
                <label for="nombres">Nombres <span class="required">*</span></label>
                <input type="text" id="nombres" name="nombres" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="apellidos">Apellidos <span class="required">*</span></label>
                <input type="text" id="apellidos" name="apellidos" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="usuario">Nombre de Usuario <span class="required">*</span></label>
                <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Ej: cmendoza" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña <span class="required">*</span></label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" class="form-control">
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" class="form-control">
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= vistaUrl('usuarios/listar_usuarios.php') ?>" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">Registrar Usuario</button>
        </div>
    </form>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

