<?php
$tituloPagina = 'Gestión de Propietarios';
$subtituloNavbar = 'Padrón de Dueños';
$menuActivo = 'propietarios';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/models/Propietario.php';

$propMod = new Propietario();
$propietarios = $propMod->listarTodos();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Padrón de Propietarios</h1>
        <p class="page-subtitle">Dueños y titulares legales de las mototaxis</p>
    </div>
    <a href="<?= vistaUrl('propietarios/registrar_propietario.php') ?>" class="btn btn-primary">+ Nuevo Propietario</a>
</div>

<div class="card">
    <div class="table-toolbar">
        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" id="inputBuscar" placeholder="Buscar por documento o nombre...">
        </div>
        <div style="font-size: 0.9rem; color: #64748b;">
            Total: <strong><?= count($propietarios) ?></strong> propietarios
        </div>
    </div>

    <div class="table-responsive">
        <table class="table-custom" id="tablaRegistros">
            <thead>
                <tr>
                    <th>Doc. Identidad</th>
                    <th>Nombres / Razón Social</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Dirección</th>
                    <th>Estado</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($propietarios)): ?>
                <tr><td colspan="7" style="text-align:center; padding:25px;">No hay propietarios registrados.</td></tr>
                <?php else: ?>
                <?php foreach ($propietarios as $p): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($p['tipo_documento'] . ': ' . $p['numero_documento']) ?></strong></td>
                    <td><?= htmlspecialchars($p['nombres_razon_social'] . ' ' . ($p['apellidos'] ?? '')) ?></td>
                    <td><?= htmlspecialchars($p['telefono'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($p['email'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($p['direccion'] ?? '-') ?></td>
                    <td><span class="badge badge-success"><?= htmlspecialchars($p['estado']) ?></span></td>
                    <td style="text-align: right;">
                        <a href="<?= vistaUrl('propietarios/editar_propietario.php?id=' . $p['id_propietario']) ?>" class="btn btn-primary btn-sm">Editar</a>
                        <a href="<?= controladorUrl('propietario_controlador.php?accion=eliminar&id=' . $p['id_propietario']) ?>" class="btn btn-danger btn-sm btn-confirmar-eliminar">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

