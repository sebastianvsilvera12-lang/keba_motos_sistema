<?php
$tituloPagina = 'Gestión de Usuarios';
$subtituloNavbar = 'Control de Accesos';
$menuActivo = 'usuarios';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Usuario.php';

$userMod = new Usuario();
$usuarios = $userMod->listarTodos();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Administración de Usuarios</h1>
        <p class="page-subtitle">Cuentas con acceso al sistema y roles asignados</p>
    </div>
    <a href="<?= vistaUrl('usuarios/registrar_usuario.php') ?>" class="btn btn-primary">+ Nuevo Usuario</a>
</div>

<div class="card">
    <div class="table-toolbar">
        <div class="search-box">
            <span class="search-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
            <input type="text" id="inputBuscar" placeholder="Buscar por usuario, nombre o DNI...">
        </div>
        <div style="font-size: 0.9rem; color: #64748b;">
            Total: <strong><?= count($usuarios) ?></strong> cuentas
        </div>
    </div>

    <div class="table-responsive">
        <table class="table-custom" id="tablaRegistros">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Nombres y Apellidos</th>
                    <th>DNI</th>
                    <th>Rol</th>
                    <th>Email</th>
                    <th>Último Acceso</th>
                    <th>Estado</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($u['usuario']) ?></strong></td>
                    <td><?= htmlspecialchars($u['nombres'] . ' ' . $u['apellidos']) ?></td>
                    <td><?= htmlspecialchars($u['dni']) ?></td>
                    <td><span class="badge badge-info"><?= htmlspecialchars($u['nombre_rol']) ?></span></td>
                    <td><?= htmlspecialchars($u['email'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($u['ultimo_login'] ?? 'Nunca') ?></td>
                    <td><span class="badge badge-success"><?= htmlspecialchars($u['estado']) ?></span></td>
                    <td style="text-align: right;">
                        <a href="<?= vistaUrl('usuarios/editar_usuario.php?id=' . $u['id_usuario']) ?>" class="btn btn-primary btn-sm">Editar</a>
                        <?php if ($u['id_usuario'] != ($usuarioActivo['id'] ?? 0)): ?>
                        <a href="<?= controladorUrl('auth/registro_usuario.php?accion=eliminar&id=' . $u['id_usuario']) ?>" class="btn btn-danger btn-sm btn-confirmar-eliminar">Eliminar</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

