<?php
$tituloPagina = 'Gestión de Asociaciones';
$subtituloNavbar = 'Empresas y Paraderos';
$menuActivo = 'asociaciones';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Asociacion.php';

$asocMod = new Asociacion();
$asociaciones = $asocMod->listarTodos();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Padrón de Asociaciones y Paraderos</h1>
        <p class="page-subtitle">Empresas autorizadas por resolución municipal</p>
    </div>
    <a href="<?= vistaUrl('asociaciones/registrar_asociacion.php') ?>" class="btn btn-primary">+ Nueva Asociación</a>
</div>

<div class="card">
    <div class="table-toolbar">
        <div class="search-box">
            <span class="search-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
            <input type="text" id="inputBuscar" placeholder="Buscar por nombre, RUC o presidente...">
        </div>
        <div style="font-size: 0.9rem; color: #64748b;">
            Total: <strong><?= count($asociaciones) ?></strong> asociaciones
        </div>
    </div>

    <div class="table-responsive">
        <table class="table-custom" id="tablaRegistros">
            <thead>
                <tr>
                    <th>Nombre de Asociación / Empresa</th>
                    <th>RUC</th>
                    <th>Resolución Municipal</th>
                    <th>Presidente / Representante</th>
                    <th>Color Distintivo</th>
                    <th>Flota Activa</th>
                    <th>Estado</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($asociaciones)): ?>
                <tr><td colspan="8" style="text-align:center; padding:25px;">No hay asociaciones registradas.</td></tr>
                <?php else: ?>
                <?php foreach ($asociaciones as $a): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($a['nombre']) ?></strong></td>
                    <td><?= htmlspecialchars($a['ruc'] ?? '-') ?></td>
                    <td><span class="badge badge-info"><?= htmlspecialchars($a['resolucion_municipal'] ?? '-') ?></span></td>
                    <td><?= htmlspecialchars($a['presidente'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($a['color_distintivo'] ?? '-') ?></td>
                    <td><strong><?= (int)$a['total_vehiculos'] ?> mototaxis</strong></td>
                    <td><span class="badge badge-success"><?= htmlspecialchars($a['estado']) ?></span></td>
                    <td style="text-align: right;">
                        <a href="<?= vistaUrl('asociaciones/editar_asociacion.php?id=' . $a['id_asociacion']) ?>" class="btn btn-primary btn-sm">Editar</a>
                        <a href="<?= controladorUrl('asociacion_controlador.php?accion=eliminar&id=' . $a['id_asociacion']) ?>" class="btn btn-danger btn-sm btn-confirmar-eliminar">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

