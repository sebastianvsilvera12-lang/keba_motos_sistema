<?php
$tituloPagina = 'Control de Asistencias y Uso de Mototaxis';
$subtituloNavbar = 'Monitoreo de Choferes y Unidades en Ruta';
$menuActivo = 'asistencias_taxistas';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';

require_once dirname(dirname(__DIR__)) . '/modelos/Asistencia.php';

$usuarioSesion = obtenerUsuarioActual();
$rolId = (int)($usuarioSesion['rol_id'] ?? 0);
if ($rolId === ROL_TAXISTAS || $rolId === ROL_CLIENTES) {
    redireccionar('vista/taxista/dashboard.php');
}

$asistenciaMod = new Asistencia();

// Filtros
$filtroFecha = $_GET['fecha'] ?? date('Y-m-d');
$filtroEstado = $_GET['estado'] ?? '';
$filtroBuscar = trim($_GET['buscar'] ?? '');
$verTodas = isset($_GET['todas']) && $_GET['todas'] == '1';

if ($verTodas) {
    $filtroFecha = '';
}

$filtros = [];
if (!empty($filtroFecha)) {
    $filtros['fecha'] = $filtroFecha;
}
if (!empty($filtroEstado)) {
    $filtros['estado'] = $filtroEstado;
}
if (!empty($filtroBuscar)) {
    $filtros['buscar'] = $filtroBuscar;
}

$asistencias = $asistenciaMod->listarTodas($filtros, 150);

// Métricas de hoy
$enTurnoHoy = $asistenciaMod->contarEnTurnoHoy();
$totalHoy = $asistenciaMod->contarHoy();
$finalizadosHoy = $asistenciaMod->contarFinalizadosHoy();

// Conteo de unidades únicas activas ahora
$motosEnUsoAhora = 0;
$motosSet = [];
foreach ($asistencias as $asig) {
    if ($asig['estado'] === 'En Turno' && !empty($asig['id_vehiculo'])) {
        if (!in_array($asig['id_vehiculo'], $motosSet)) {
            $motosSet[] = $asig['id_vehiculo'];
            $motosEnUsoAhora++;
        }
    }
}
?>

<style>
    .plate-badge {
        display: inline-block;
        background: #fef08a;
        color: #854d0e;
        border: 1.5px solid #ca8a04;
        font-family: monospace;
        font-weight: 800;
        font-size: 0.95rem;
        padding: 3px 8px;
        border-radius: 4px;
        letter-spacing: 0.5px;
    }
    .user-avatar-initials {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    .filter-pill {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid #cbd5e1;
        color: #475569;
        background: #ffffff;
        transition: all 0.15s ease;
    }
    .filter-pill.active {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }
</style>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h1 class="page-title">Control de Asistencias y Uso de Mototaxis</h1>
        <p class="page-subtitle">Registro en tiempo real de choferes, unidades en servicio, fecha y hora exacta de entrada y salida</p>
    </div>
    <div style="display: flex; align-items: center; gap: 10px;">
        <button type="button" class="btn btn-outline" onclick="window.location.reload();" style="display: flex; align-items: center; gap: 6px;">
            <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
            Actualizar
        </button>
        <button type="button" class="btn btn-primary" onclick="window.print();" style="display: flex; align-items: center; gap: 6px;">
            <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Imprimir Reporte
        </button>
    </div>
</div>

<!-- TARJETAS DE MÉTRICAS OPERATIVAS -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); margin-bottom: 25px;">
    <div class="stat-card">
        <div class="stat-info">
            <h3>En Ruta Ahora (Activos)</h3>
            <div class="stat-number" style="color: #10b981;"><?= $enTurnoHoy ?></div>
            <div class="stat-desc">Taxistas en servicio ahora</div>
        </div>
        <div class="stat-icon green" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Motos en Uso Hoy</h3>
            <div class="stat-number" style="color: #2563eb;"><?= $motosEnUsoAhora ?></div>
            <div class="stat-desc">Unidades rodando en la calle</div>
        </div>
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="17" r="3"></circle><circle cx="19" cy="17" r="3"></circle><path d="M5 17h14"></path><path d="M9 17l2-7h4l2 7"></path><path d="M11 10h4"></path></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Ingresos de Hoy</h3>
            <div class="stat-number" style="color: #0f172a;"><?= $totalHoy ?></div>
            <div class="stat-desc">Turnos iniciados el día <?= date('d/m/Y') ?></div>
        </div>
        <div class="stat-icon dark">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><line x1="15" y1="8" x2="17" y2="8"></line><line x1="15" y1="12" x2="17" y2="12"></line><line x1="7" y1="16" x2="17" y2="16"></line></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Turnos Finalizados</h3>
            <div class="stat-number" style="color: #64748b;"><?= $finalizadosHoy ?></div>
            <div class="stat-desc">Salidas y motos entregadas hoy</div>
        </div>
        <div class="stat-icon warning" style="background: rgba(100, 116, 139, 0.15); color: #475569;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        </div>
    </div>
</div>

<!-- FILTROS Y BÚSQUEDA -->
<div class="card" style="margin-bottom: 25px; padding: 18px 20px;">
    <form method="GET" action="<?= vistaUrl('vehiculos/asistencias_taxistas.php') ?>" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 200px;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;">Buscar Chofer, DNI, Placa o Padrón</label>
            <input type="text" name="buscar" class="form-control" value="<?= htmlspecialchars($filtroBuscar) ?>" placeholder="Ej: Sebastian, 61205629, 1234-5B...">
        </div>

        <div style="min-width: 170px;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;">Filtrar por Fecha</label>
            <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($filtroFecha) ?>">
        </div>

        <div style="min-width: 160px;">
            <label style="font-size: 0.82rem; font-weight: 700; color: #334155; margin-bottom: 6px; display: block;">Estado de Turno</label>
            <select name="estado" class="form-control">
                <option value="">-- Todos los estados --</option>
                <option value="En Turno" <?= $filtroEstado === 'En Turno' ? 'selected' : '' ?>>🟢 En Turno (Activos)</option>
                <option value="Finalizado" <?= $filtroEstado === 'Finalizado' ? 'selected' : '' ?>>✓ Finalizados</option>
            </select>
        </div>

        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary" style="height: 42px; display: flex; align-items: center; gap: 6px;">
                <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                Filtrar
            </button>
            <a href="<?= vistaUrl('vehiculos/asistencias_taxistas.php') ?>" class="btn btn-outline" style="height: 42px; display: flex; align-items: center;">
                Hoy
            </a>
            <a href="<?= vistaUrl('vehiculos/asistencias_taxistas.php?todas=1') ?>" class="btn btn-outline" style="height: 42px; display: flex; align-items: center;">
                Ver Todas
            </a>
        </div>
    </form>
</div>

<!-- TABLA PRINCIPAL: USO DE MOTOTAXIS Y ASISTENCIAS -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 14px;">
        <div>
            <h2 class="card-title" style="margin: 0; font-size: 1.25rem;">Registro Detallado de Choferes y Unidades</h2>
            <p class="card-subtitle" style="margin: 4px 0 0 0; color: #64748b; font-size: 0.85rem;">
                Mostrando <?= count($asistencias) ?> registros <?= !empty($filtroFecha) ? 'para el día ' . date('d/m/Y', strtotime($filtroFecha)) : 'en el historial general' ?>
            </p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Conductor / Usuario</th>
                    <th>Mototaxi (Uso de Unidad)</th>
                    <th>Hora de Entrada</th>
                    <th>Hora de Salida</th>
                    <th>Tiempo de Uso</th>
                    <th>Turno</th>
                    <th>Estado</th>
                    <th>Km / Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($asistencias)): ?>
                    <?php foreach ($asistencias as $asig): 
                        $nombreChofer = trim(($asig['usuario_nombre'] ?? '') . ' ' . ($asig['usuario_apellidos'] ?? ''));
                        if (empty($nombreChofer)) {
                            $nombreChofer = trim(($asig['conductor_nombres'] ?? '') . ' ' . ($asig['conductor_apellidos'] ?? ''));
                        }
                        if (empty($nombreChofer)) {
                            $nombreChofer = $asig['usuario'] ?? 'Conductor';
                        }
                        $iniciales = mb_strtoupper(mb_substr($nombreChofer, 0, 2));

                        // Cálculo o formateo de tiempo de servicio
                        $tiempoUso = '-';
                        if (!empty($asig['hora_ingreso'])) {
                            $entradaTs = strtotime($asig['fecha'] . ' ' . $asig['hora_ingreso']);
                            $salidaTs = !empty($asig['hora_salida']) ? strtotime($asig['fecha'] . ' ' . $asig['hora_salida']) : time();
                            $diffSec = $salidaTs - $entradaTs;
                            if ($diffSec > 0) {
                                $hrs = floor($diffSec / 3600);
                                $min = floor(($diffSec % 3600) / 60);
                                $tiempoUso = "{$hrs}h {$min}m";
                            }
                        }

                        // Cálculo de kilometraje recorrido si existe
                        $kmRecorrido = null;
                        if (!empty($asig['kilometraje_inicial']) && !empty($asig['kilometraje_final'])) {
                            $kmRecorrido = $asig['kilometraje_final'] - $asig['kilometraje_inicial'];
                        }
                    ?>
                    <tr>
                        <!-- FECHA -->
                        <td>
                            <strong style="color: #0f172a;"><?= htmlspecialchars(date('d/m/Y', strtotime($asig['fecha']))) ?></strong>
                        </td>

                        <!-- CONDUCTOR -->
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <?php if (!empty($asig['conductor_foto']) && file_exists(dirname(dirname(__DIR__)) . '/' . $asig['conductor_foto'])): ?>
                                    <img src="<?= asset($asig['conductor_foto']) ?>" alt="Foto" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 1.5px solid #cbd5e1;">
                                <?php else: ?>
                                    <div class="user-avatar-initials"><?= htmlspecialchars($iniciales) ?></div>
                                <?php endif; ?>
                                <div>
                                    <div style="font-weight: 700; color: #0f172a; font-size: 0.95rem;"><?= htmlspecialchars($nombreChofer) ?></div>
                                    <div style="font-size: 0.78rem; color: #64748b;">
                                        DNI: <strong><?= htmlspecialchars($asig['usuario_dni'] ?? '-') ?></strong>
                                        <?php if (!empty($asig['numero_licencia'])): ?>
                                            &bull; Lic: <?= htmlspecialchars($asig['numero_licencia']) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- MOTOTAXI (USO DE UNIDAD) -->
                        <td>
                            <?php if (!empty($asig['placa'])): ?>
                                <div>
                                    <span class="plate-badge"><?= htmlspecialchars($asig['placa']) ?></span>
                                    <?php if (!empty($asig['numero_padron'])): ?>
                                        <span class="badge badge-secondary" style="font-size: 0.75rem; margin-left: 4px;">PAD-<?= htmlspecialchars($asig['numero_padron']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size: 0.78rem; color: #64748b; margin-top: 3px;">
                                    <?= htmlspecialchars($asig['marca'] ?? '') ?> <?= htmlspecialchars($asig['modelo'] ?? '') ?>
                                    <?= !empty($asig['color']) ? ' &bull; ' . htmlspecialchars($asig['color']) : '' ?>
                                </div>
                            <?php else: ?>
                                <span style="color: #94a3b8; font-style: italic;">Sin mototaxi asignada</span>
                            <?php endif; ?>
                        </td>

                        <!-- HORA DE ENTRADA -->
                        <td>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                                <strong style="font-family: monospace; font-size: 0.95rem; color: #065f46;">
                                    <?= htmlspecialchars(date('h:i A', strtotime($asig['hora_ingreso']))) ?>
                                </strong>
                            </div>
                            <small style="color: #64748b; font-size: 0.75rem;"><?= htmlspecialchars(substr($asig['hora_ingreso'], 0, 8)) ?></small>
                        </td>

                        <!-- HORA DE SALIDA -->
                        <td>
                            <?php if (!empty($asig['hora_salida'])): ?>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6; display: inline-block;"></span>
                                    <strong style="font-family: monospace; font-size: 0.95rem; color: #1e40af;">
                                        <?= htmlspecialchars(date('h:i A', strtotime($asig['hora_salida']))) ?>
                                    </strong>
                                </div>
                                <small style="color: #64748b; font-size: 0.75rem;"><?= htmlspecialchars(substr($asig['hora_salida'], 0, 8)) ?></small>
                            <?php else: ?>
                                <span class="badge badge-warning" style="display: inline-flex; align-items: center; gap: 4px; font-weight: 700;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #ffffff;"></span>
                                    En Ruta Ahora
                                </span>
                            <?php endif; ?>
                        </td>

                        <!-- TIEMPO DE USO -->
                        <td>
                            <strong style="color: <?= $asig['estado'] === 'En Turno' ? '#059669' : '#0f172a' ?>; font-size: 0.9rem;">
                                <?= $tiempoUso ?>
                            </strong>
                            <?php if ($asig['estado'] === 'En Turno'): ?>
                                <small style="color: #059669; display: block; font-size: 0.72rem;">(en curso)</small>
                            <?php endif; ?>
                        </td>

                        <!-- TURNO -->
                        <td>
                            <span class="badge badge-secondary" style="font-weight: 600;"><?= htmlspecialchars($asig['turno']) ?></span>
                        </td>

                        <!-- ESTADO -->
                        <td>
                            <?php if ($asig['estado'] === 'En Turno'): ?>
                                <span class="badge badge-success" style="padding: 5px 10px; font-weight: 700;">
                                    🟢 En Turno
                                </span>
                            <?php else: ?>
                                <span class="badge badge-info" style="padding: 5px 10px; font-weight: 700;">
                                    ✓ Finalizado
                                </span>
                            <?php endif; ?>
                        </td>

                        <!-- KM / OBSERVACIONES -->
                        <td style="font-size: 0.82rem; color: #475569; max-width: 200px;">
                            <?php if ($kmRecorrido !== null): ?>
                                <div><strong><?= number_format($kmRecorrido) ?> km</strong> recorridos</div>
                            <?php elseif (!empty($asig['kilometraje_inicial'])): ?>
                                <div>Inicio: <?= number_format($asig['kilometraje_inicial']) ?> km</div>
                            <?php endif; ?>

                            <?php if (!empty($asig['observaciones'])): ?>
                                <div style="color: #64748b; font-style: italic; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($asig['observaciones']) ?>">
                                    <?= htmlspecialchars($asig['observaciones']) ?>
                                </div>
                            <?php else: ?>
                                <span style="color: #cbd5e1;">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center; color: #94a3b8; padding: 40px;">
                            <svg style="width: 48px; height: 48px; margin: 0 auto 12px auto; display: block; color: #cbd5e1;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            No se encontraron registros de asistencias con los filtros seleccionados.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>
