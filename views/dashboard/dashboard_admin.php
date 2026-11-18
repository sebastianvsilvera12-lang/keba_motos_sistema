<?php
$tituloPagina = 'Panel Principal Administrador';
$subtituloNavbar = 'Dashboard General';
$menuActivo = 'dashboard';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';

require_once dirname(dirname(__DIR__)) . '/models/Vehiculo.php';
require_once dirname(dirname(__DIR__)) . '/models/Conductor.php';
require_once dirname(dirname(__DIR__)) . '/models/Infraccion.php';
require_once dirname(dirname(__DIR__)) . '/models/Documento.php';

$vehiculoMod = new Vehiculo();
$conductorMod = new Conductor();
$infraccionMod = new Infraccion();
$documentoMod = new Documento();

$totalVehiculos = $vehiculoMod->contarTotal();
$totalConductores = $conductorMod->contarTotal();
$infraccionesPendientes = $infraccionMod->contarPendientes();
$docsPorVencer = $documentoMod->contarPorVencer(30);

$ultimosVehiculos = array_slice($vehiculoMod->listarTodos(), 0, 5);
$ultimasInfracciones = array_slice($infraccionMod->listarTodos(), 0, 5);
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Panel de Control Ejecutivo</h1>
        <p class="page-subtitle">Resumen general y métricas del parque automotor de mototaxis</p>
    </div>
    <div>
        <a href="<?= vistaUrl('vehiculos/registrar_vehiculo.php') ?>" class="btn btn-primary">+ Nuevo Vehículo</a>
        <a href="<?= vistaUrl('fiscalizacion/registrar_infraccion.php') ?>" class="btn btn-dark">+ Nueva Acta</a>
    </div>
</div>

<!-- Tarjetas de Estadísticas Principales -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Mototaxis Activas</h3>
            <div class="stat-number" data-target="<?= $totalVehiculos ?>"><?= $totalVehiculos ?></div>
            <div class="stat-desc">Unidades empadronadas</div>
        </div>
        <div class="stat-icon blue">🛵</div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Conductores</h3>
            <div class="stat-number" data-target="<?= $totalConductores ?>"><?= $totalConductores ?></div>
            <div class="stat-desc">Choferes con licencia</div>
        </div>
        <div class="stat-icon dark">👨‍✈️</div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Actas Pendientes</h3>
            <div class="stat-number" data-target="<?= $infraccionesPendientes ?>"><?= $infraccionesPendientes ?></div>
            <div class="stat-desc">Infracciones por cobrar</div>
        </div>
        <div class="stat-icon danger">⚠️</div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Docs. por Vencer</h3>
            <div class="stat-number" data-target="<?= $docsPorVencer ?>"><?= $docsPorVencer ?></div>
            <div class="stat-desc">Próximos 30 días</div>
        </div>
        <div class="stat-icon warning">📅</div>
    </div>
</div>

<!-- Cuadrícula de accesos y tablas recientes -->
<div class="dashboard-grid">
    <!-- Tabla de vehículos recientes -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Últimos Vehículos Registrados</h2>
            <a href="<?= vistaUrl('vehiculos/listar_vehiculos.php') ?>" class="btn btn-outline btn-sm">Ver Todos</a>
        </div>
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Placa</th>
                        <th>Padrón</th>
                        <th>Marca / Modelo</th>
                        <th>Asociación</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ultimosVehiculos)): ?>
                    <tr><td colspan="5" style="text-align:center;">No hay vehículos registrados.</td></tr>
                    <?php else: ?>
                    <?php foreach ($ultimosVehiculos as $v): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($v['placa']) ?></strong></td>
                        <td><?= htmlspecialchars($v['numero_padron'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?></td>
                        <td><?= htmlspecialchars($v['asociacion_nombre'] ?? 'Sin Asociación') ?></td>
                        <td><span class="badge badge-success"><?= htmlspecialchars($v['estado']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Accesos Rápidos y Alertas -->
    <div>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Accesos Rápidos</h2>
            </div>
            <div class="quick-actions">
                <a href="<?= vistaUrl('conductores/registrar_conductor.php') ?>" class="action-tile">
                    <span class="icon">👨‍✈️</span>
                    <span>Registrar Chofer</span>
                </a>
                <a href="<?= vistaUrl('documentos/documentos_por_vencer.php') ?>" class="action-tile">
                    <span class="icon">⏳</span>
                    <span>Alertas Vencimiento</span>
                </a>
                <a href="<?= vistaUrl('asociaciones/listar_asociaciones.php') ?>" class="action-tile">
                    <span class="icon">🏢</span>
                    <span>Paraderos</span>
                </a>
                <a href="<?= vistaUrl('reportes/reporte_general.php') ?>" class="action-tile">
                    <span class="icon">📑</span>
                    <span>Reporte General</span>
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Fiscalización Reciente</h2>
                <a href="<?= vistaUrl('fiscalizacion/listar_infracciones.php') ?>" class="btn btn-outline btn-sm">Ver</a>
            </div>
            <ul style="list-style:none; padding:0;">
                <?php if (empty($ultimasInfracciones)): ?>
                <li style="color:#64748b; font-size:0.88rem;">No hay actas recientes.</li>
                <?php else: ?>
                <?php foreach ($ultimasInfracciones as $inf): ?>
                <li style="padding:10px 0; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <div style="font-weight:700; font-size:0.88rem; color:#0b1329;"><?= htmlspecialchars($inf['numero_acta']) ?> - Placa: <?= htmlspecialchars($inf['placa']) ?></div>
                        <div style="font-size:0.78rem; color:#64748b;">Monto: S/. <?= number_format($inf['monto'], 2) ?></div>
                    </div>
                    <span class="badge <?= $inf['estado_pago'] === 'Pagado' ? 'badge-success' : 'badge-danger' ?>">
                        <?= htmlspecialchars($inf['estado_pago']) ?>
                    </span>
                </li>
                <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

