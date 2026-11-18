<?php
$tituloPagina = 'Gestión de Vehículos';
$subtituloNavbar = 'Padrón de Mototaxis';
$menuActivo = 'vehiculos';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Vehiculo.php';

$vehiculoMod = new Vehiculo();
$vehiculos = $vehiculoMod->listarTodos();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Padrón de Mototaxis</h1>
        <p class="page-subtitle">Listado oficial de unidades vehiculares registradas</p>
    </div>
    <a href="<?= vistaUrl('vehiculos/registrar_vehiculo.php') ?>" class="btn btn-primary">+ Nueva Mototaxi</a>
</div>

<div class="card">
    <div class="table-toolbar">
        <div class="search-box">
            <span class="search-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
            <input type="text" id="inputBuscar" placeholder="Buscar por placa, padrón o marca...">
        </div>
        <div style="font-size: 0.9rem; color: #64748b;">
            Total: <strong><?= count($vehiculos) ?></strong> unidades
        </div>
    </div>

    <div class="table-responsive">
        <table class="table-custom" id="tablaRegistros">
            <thead>
                <tr>
                    <th>Placa</th>
                    <th>N° Padrón</th>
                    <th>Marca / Modelo</th>
                    <th>Color</th>
                    <th>Propietario</th>
                    <th>Asociación</th>
                    <th>Estado</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($vehiculos)): ?>
                <tr><td colspan="8" style="text-align:center; padding: 25px;">No se encontraron mototaxis registradas.</td></tr>
                <?php else: ?>
                <?php foreach ($vehiculos as $v): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($v['placa']) ?></strong></td>
                    <td><?= htmlspecialchars($v['numero_padron'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?></td>
                    <td><?= htmlspecialchars($v['color']) ?></td>
                    <td><?= htmlspecialchars($v['propietario_nombre'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($v['asociacion_nombre'] ?? 'Sin Asociación') ?></td>
                    <td>
                        <span class="badge <?= $v['estado'] === 'Activo' ? 'badge-success' : 'badge-danger' ?>">
                            <?= htmlspecialchars($v['estado']) ?>
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <a href="<?= vistaUrl('vehiculos/ver_vehiculo.php?id=' . $v['id_vehiculo']) ?>" class="btn btn-outline btn-sm">Ver</a>
                        <a href="<?= vistaUrl('vehiculos/editar_vehiculo.php?id=' . $v['id_vehiculo']) ?>" class="btn btn-primary btn-sm">Editar</a>
                        <a href="<?= controladorUrl('vehiculo_controlador.php?accion=eliminar&id=' . $v['id_vehiculo']) ?>" class="btn btn-danger btn-sm btn-confirmar-eliminar">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

