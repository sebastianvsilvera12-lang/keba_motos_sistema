<?php
$tituloPagina = 'Panel Principal Administrador';
$subtituloNavbar = 'Dashboard General';
$menuActivo = 'dashboard';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';

require_once dirname(dirname(__DIR__)) . '/modelos/Vehiculo.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Conductor.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Infraccion.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Documento.php';

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
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="17" r="3"></circle><circle cx="19" cy="17" r="3"></circle><path d="M5 17h14"></path><path d="M9 17l2-7h4l2 7"></path><path d="M11 10h4"></path><circle cx="12" cy="6" r="1"></circle></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Conductores</h3>
            <div class="stat-number" data-target="<?= $totalConductores ?>"><?= $totalConductores ?></div>
            <div class="stat-desc">Choferes con licencia</div>
        </div>
        <div class="stat-icon dark">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><line x1="15" y1="8" x2="17" y2="8"></line><line x1="15" y1="12" x2="17" y2="12"></line><line x1="7" y1="16" x2="17" y2="16"></line></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Actas Pendientes</h3>
            <div class="stat-number" data-target="<?= $infraccionesPendientes ?>"><?= $infraccionesPendientes ?></div>
            <div class="stat-desc">Infracciones por cobrar</div>
        </div>
        <div class="stat-icon danger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Docs. por Vencer</h3>
            <div class="stat-number" data-target="<?= $docsPorVencer ?>"><?= $docsPorVencer ?></div>
            <div class="stat-desc">Próximos 30 días</div>
        </div>
        <div class="stat-icon warning">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
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
                    <div class="tile-icon-wrapper" style="width: 40px; height: 40px; margin-bottom: 2px;">
                        <svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><line x1="15" y1="8" x2="17" y2="8"></line><line x1="15" y1="12" x2="17" y2="12"></line><line x1="7" y1="16" x2="17" y2="16"></line></svg>
                    </div>
                    <span>Registrar Chofer</span>
                </a>
                <a href="<?= vistaUrl('documentos/documentos_por_vencer.php') ?>" class="action-tile">
                    <div class="tile-icon-wrapper warning" style="width: 40px; height: 40px; margin-bottom: 2px;">
                        <svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <span>Alertas Vencimiento</span>
                </a>
                <a href="<?= vistaUrl('asociaciones/listar_asociaciones.php') ?>" class="action-tile">
                    <div class="tile-icon-wrapper" style="width: 40px; height: 40px; margin-bottom: 2px;">
                        <svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="9" y1="6" x2="9" y2="6.01"></line><line x1="15" y1="6" x2="15" y2="6.01"></line><line x1="9" y1="10" x2="9" y2="10.01"></line><line x1="15" y1="10" x2="15" y2="10.01"></line><line x1="9" y1="14" x2="9" y2="14.01"></line><line x1="15" y1="14" x2="15" y2="14.01"></line><line x1="9" y1="18" x2="15" y2="18"></line></svg>
                    </div>
                    <span>Paraderos</span>
                </a>
                <a href="<?= vistaUrl('reportes/reporte_general.php') ?>" class="action-tile">
                    <div class="tile-icon-wrapper" style="width: 40px; height: 40px; margin-bottom: 2px;">
                        <svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </div>
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

