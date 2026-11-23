<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/constantes.php';
require_once dirname(dirname(__DIR__)) . '/configuracion/session.php';
require_once dirname(dirname(__DIR__)) . '/ayudantes/funciones.php';

$usuarioSesion = obtenerUsuarioActual();
if ($usuarioSesion && (int)($usuarioSesion['rol_id'] ?? 0) === ROL_TAXISTAS) {
    header('Location: ' . vistaUrl('taxista/dashboard.php'));
    exit;
}

$tituloPagina = 'Panel Principal Administrador';
$subtituloNavbar = 'Panel General';
$menuActivo = 'dashboard';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';

require_once dirname(dirname(__DIR__)) . '/modelos/Vehiculo.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Conductor.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Infraccion.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Documento.php';
require_once dirname(dirname(__DIR__)) . '/modelos/ReporteVehiculo.php';
require_once dirname(dirname(__DIR__)) . '/modelos/CuotaMensual.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Asistencia.php';

$vehiculoMod = new Vehiculo();
$conductorMod = new Conductor();
$infraccionMod = new Infraccion();
$documentoMod = new Documento();
$repMod = new ReporteVehiculo();
$cuotaMod = new CuotaMensual();
$asistenciaMod = new Asistencia();

$totalVehiculos = $vehiculoMod->contarTotal();
$totalConductores = $conductorMod->contarTotal();
$infraccionesPendientes = $infraccionMod->contarPendientes();
$docsPorVencer = $documentoMod->contarPorVencer(30);
$reportesPendientesMotos = $repMod->contarPendientes();
$cuotasYapePendientes = $cuotaMod->contarPendientes();
$taxistasEnTurno = $asistenciaMod->contarEnTurnoHoy();

$ultimosVehiculos = array_slice($vehiculoMod->listarTodos(), 0, 5);
$ultimasInfracciones = array_slice($infraccionMod->listarTodos(), 0, 5);
$ultimosReportesMotos = array_slice($repMod->listarTodos(), 0, 5);
$ultimasCuotasYape = array_slice($cuotaMod->listarTodas(), 0, 5);
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

    <a href="<?= vistaUrl('vehiculos/reportes_taxistas.php') ?>" class="stat-card" style="text-decoration: none; color: inherit; border-color: <?= $reportesPendientesMotos > 0 ? '#f59e0b' : '#334155' ?>;">
        <div class="stat-info">
            <h3>Avisos de Taxistas</h3>
            <div class="stat-number" style="color: <?= $reportesPendientesMotos > 0 ? '#f59e0b' : '#10b981' ?>;" data-target="<?= $reportesPendientesMotos ?>"><?= $reportesPendientesMotos ?></div>
            <div class="stat-desc" style="color: #38bdf8;">Daños / Revisiones &rarr;</div>
        </div>
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
        </div>
    </a>

    <a href="<?= vistaUrl('vehiculos/cuotas_taxistas.php?estado=Pendiente') ?>" class="stat-card" style="text-decoration: none; color: inherit; border-color: <?= $cuotasYapePendientes > 0 ? '#871c99' : '#334155' ?>;">
        <div class="stat-info">
            <h3>Cuotas Yape</h3>
            <div class="stat-number" style="color: <?= $cuotasYapePendientes > 0 ? '#871c99' : '#64748b' ?>;" data-target="<?= $cuotasYapePendientes ?>"><?= $cuotasYapePendientes ?></div>
            <div class="stat-desc" style="color: #38bdf8;">Por validar en Yape &rarr;</div>
        </div>
        <div class="stat-icon" style="background: transparent; padding: 0; display: flex; align-items: center; justify-content: center;">
            <img src="<?= asset('img/yape_app.png') ?>" alt="Yape" style="width: 44px; height: 44px; border-radius: 11px; box-shadow: 0 3px 8px rgba(116, 34, 132, 0.25);">
        </div>
    </a>

    <a href="<?= vistaUrl('vehiculos/asistencias_taxistas.php') ?>" class="stat-card" style="text-decoration: none; color: inherit; border-color: <?= $taxistasEnTurno > 0 ? '#10b981' : '#334155' ?>;">
        <div class="stat-info">
            <h3>Motos en Ruta (Turnos)</h3>
            <div class="stat-number" style="color: <?= $taxistasEnTurno > 0 ? '#10b981' : '#64748b' ?>;" data-target="<?= $taxistasEnTurno ?>"><?= $taxistasEnTurno ?></div>
            <div class="stat-desc" style="color: #38bdf8;">Choferes en servicio &rarr;</div>
        </div>
        <div class="stat-icon green" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
    </a>
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
                <a href="<?= vistaUrl('vehiculos/cuotas_taxistas.php') ?>" class="action-tile">
                    <div class="tile-icon-wrapper" style="width: 40px; height: 40px; margin-bottom: 2px; padding: 0; background: transparent; display: flex; align-items: center; justify-content: center;">
                        <img src="<?= asset('img/yape_app.png') ?>" alt="Yape" style="width: 36px; height: 36px; border-radius: 8px;">
                    </div>
                    <span>Cuotas Yape</span>
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

<!-- Cuotas Mensuales Yape Recientes -->
<div class="card" style="margin-top: 25px;">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div>
            <h2 class="card-title" style="display: flex; align-items: center; gap: 8px;">
                <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #10b981;"></span>
                Últimos Comprobantes de Cuotas Mensuales (Yape)
            </h2>
            <p style="font-size: 0.82rem; color: #64748b; margin: 4px 0 0 0;">Capturas de Yape enviadas por los taxistas y guardadas en la base de datos</p>
        </div>
        <a href="<?= vistaUrl('vehiculos/cuotas_taxistas.php') ?>" class="btn btn-outline btn-sm">Ver Todas las Cuotas &rarr;</a>
    </div>
    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Taxista / Remitente</th>
                    <th>Período</th>
                    <th>Monto</th>
                    <th>Comprobante</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ultimasCuotasYape)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #64748b; padding: 24px;">No hay pagos de cuotas registrados aún.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($ultimasCuotasYape as $cu): ?>
                <?php $srcImg = CuotaMensual::obtenerSrcComprobante($cu); ?>
                <tr>
                    <td style="font-size: 0.82rem; color: #64748b;">
                        <?= date('d/m/Y H:i', strtotime($cu['created_at'])) ?>
                    </td>
                    <td>
                        <strong style="color: #0f172a;"><?= htmlspecialchars($cu['remitente_nombre'] ?: ($cu['conductor_nombre'] ?? 'Taxista')) ?></strong>
                        <?php if (!empty($cu['placa'])): ?>
                            <div style="font-size: 0.75rem; color: #64748b;">Placa: <?= htmlspecialchars($cu['placa']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge" style="background: #e2e8f0; color: #1e293b;"><?= htmlspecialchars($cu['mes_periodo']) ?></span></td>
                    <td><strong style="color: #0f172a;">S/ <?= number_format($cu['monto'], 2) ?></strong></td>
                    <td>
                        <?php if (!empty($srcImg)): ?>
                        <a href="<?= $srcImg ?>" target="_blank" title="Ver comprobante" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none; color: #0284c7; font-size: 0.8rem; font-weight: 600;">
                            <img src="<?= $srcImg ?>" alt="Yape" style="width: 38px; height: 38px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1;">
                            <span>Ver captura</span>
                        </a>
                        <?php else: ?>
                        <span style="font-size: 0.75rem; color: #94a3b8;">Sin imagen</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($cu['estado'] === 'Pendiente'): ?>
                            <span class="badge badge-warning" style="background: #fef3c7; color: #92400e;">Pendiente</span>
                        <?php elseif ($cu['estado'] === 'Aprobado'): ?>
                            <span class="badge badge-success" style="background: #dcfce7; color: #166534;">Aprobado</span>
                        <?php else: ?>
                            <span class="badge badge-danger" style="background: #fee2e2; color: #991b1b;">Rechazado</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= vistaUrl('vehiculos/cuotas_taxistas.php') ?>" class="btn btn-outline btn-sm" style="padding: 4px 10px; font-size: 0.78rem;">
                            Validar
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

