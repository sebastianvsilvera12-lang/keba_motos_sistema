<?php
$tituloPagina = 'Gestión de Conductores';
$subtituloNavbar = 'Padrón de Choferes';
$menuActivo = 'conductores';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Conductor.php';

$conductorMod = new Conductor();
$conductores = $conductorMod->listarTodos();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Padrón de Conductores</h1>
        <p class="page-subtitle">Listado de choferes autorizados para operar mototaxis</p>
    </div>
    <a href="<?= vistaUrl('conductores/registrar_conductor.php') ?>" class="btn btn-primary">+ Nuevo Conductor</a>
</div>

<div class="card">
    <div class="table-toolbar">
        <div class="search-box">
            <span class="search-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
            <input type="text" id="inputBuscar" placeholder="Buscar por DNI, nombres o licencia...">
        </div>
        <div style="font-size: 0.9rem; color: #64748b;">
            Total: <strong><?= count($conductores) ?></strong> conductores
        </div>
    </div>

    <div class="table-responsive">
        <table class="table-custom" id="tablaRegistros">
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Nombres y Apellidos</th>
                    <th>Teléfono</th>
                    <th>N° Licencia</th>
                    <th>Categoría</th>
                    <th>Vencimiento Licencia</th>
                    <th>Estado</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($conductores)): ?>
                <tr><td colspan="8" style="text-align:center; padding:25px;">No hay conductores registrados.</td></tr>
                <?php else: ?>
                <?php foreach ($conductores as $c): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($c['dni']) ?></strong></td>
                    <td><?= htmlspecialchars($c['nombres'] . ' ' . $c['apellidos']) ?></td>
                    <td><?= htmlspecialchars($c['telefono'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($c['numero_licencia']) ?></td>
                    <td><span class="badge badge-dark"><?= htmlspecialchars($c['categoria_licencia']) ?></span></td>
                    <td><?= htmlspecialchars($c['fecha_vencimiento_licencia']) ?></td>
                    <td>
                        <span class="badge <?= $c['estado'] === 'Habilitado' ? 'badge-success' : 'badge-danger' ?>">
                            <?= htmlspecialchars($c['estado']) ?>
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <a href="<?= vistaUrl('conductores/ver_conductor.php?id=' . $c['id_conductor']) ?>" class="btn btn-outline btn-sm">Ver</a>
                        <a href="<?= vistaUrl('conductores/editar_conductor.php?id=' . $c['id_conductor']) ?>" class="btn btn-primary btn-sm">Editar</a>
                        <a href="<?= controladorUrl('conductor_controlador.php?accion=eliminar&id=' . $c['id_conductor']) ?>" class="btn btn-danger btn-sm btn-confirmar-eliminar">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

