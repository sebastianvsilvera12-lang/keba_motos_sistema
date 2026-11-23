<?php
$tituloPagina = 'Registro de Asistencia y Turnos - Taxistas';
$subtituloNavbar = 'Control de Asistencia y Uso de Mototaxi';
$menuActivo = 'asistencia';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';

require_once dirname(dirname(__DIR__)) . '/modelos/Taxista.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Asistencia.php';

$usuario = obtenerUsuarioActual();
$taxistaMod = new Taxista();
$asistenciaMod = new Asistencia();

$ficha = $taxistaMod->obtenerFichaTaxista($usuario['id']);
$conductor = $ficha['conductor'] ?? null;
$vehiculo = $ficha['vehiculo'] ?? null;

$asistenciaHoy = $asistenciaMod->obtenerAsistenciaHoy($usuario['id']);
$historialAsistencias = $asistenciaMod->listarPorUsuario($usuario['id'], 30);

$nombreCompleto = trim(($usuario['nombres'] ?? $usuario['nombre'] ?? '') . ' ' . ($usuario['apellidos'] ?? ''));
if (empty($nombreCompleto)) {
    $nombreCompleto = $usuario['usuario'] ?? 'Taxista';
}

$turnoAuto = Asistencia::detectarTurnoActual();
$estaEnTurno = ($asistenciaHoy && $asistenciaHoy['estado'] === 'En Turno');
$turnoCompletadoHoy = ($asistenciaHoy && $asistenciaHoy['estado'] === 'Finalizado');

// Nombre del día en español
$diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
$meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
$fechaTexto = $diasSemana[date('w')] . ', ' . date('d') . ' de ' . $meses[(int)date('m')] . ' ' . date('Y');
?>

<style>
    .asistencia-hero-compact {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 10px;
        padding: 12px 18px;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
        margin-bottom: 16px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .clock-display-compact {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 6px;
        padding: 5px 12px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-family: 'Courier New', monospace;
        font-size: 0.95rem;
        font-weight: 700;
        color: #38bdf8;
    }
    .pulse-dot-sm {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        animation: pulseSm 1.8s infinite;
    }
    .pulse-green { background: #10b981; box-shadow: 0 0 6px #10b981; }
    .pulse-amber { background: #f59e0b; box-shadow: 0 0 6px #f59e0b; }
    @keyframes pulseSm {
        0% { transform: scale(0.9); opacity: 0.7; }
        50% { transform: scale(1.15); opacity: 1; }
        100% { transform: scale(0.9); opacity: 0.7; }
    }
    .action-btn-compact {
        width: auto;
        min-width: 190px;
        max-width: 250px;
        padding: 7px 18px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.85rem;
        letter-spacing: 0.3px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border: none;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
        text-transform: uppercase;
    }
    .btn-entrada-compact {
        background: #10b981;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.35);
    }
    .btn-entrada-compact:hover {
        background: #059669;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.45);
    }
    .btn-salida-compact {
        background: #ef4444;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.35);
    }
    .btn-salida-compact:hover {
        background: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.45);
    }
    .info-tile-compact {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .info-tile-icon-compact {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .plate-badge-compact {
        display: inline-block;
        background: #fef08a;
        color: #854d0e;
        border: 1px solid #ca8a04;
        font-family: monospace;
        font-weight: 700;
        font-size: 0.88rem;
        padding: 2px 7px;
        border-radius: 4px;
        letter-spacing: 0.5px;
    }
</style>

<!-- ENCABEZADO COMPACTO -->
<div class="asistencia-hero-compact">
    <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 2px;">
            <span class="pulse-dot-sm <?= $estaEnTurno ? 'pulse-green' : 'pulse-amber' ?>"></span>
            <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: <?= $estaEnTurno ? '#34d399' : '#fbbf24' ?>;">
                <?= $estaEnTurno ? 'Servicio Activo en Ruta' : ($turnoCompletadoHoy ? 'Jornada Finalizada Hoy' : 'Pendiente de Marcar Entrada') ?>
            </span>
        </div>
        <h1 style="margin: 0; font-size: 1.15rem; font-weight: 700; letter-spacing: -0.3px;">Control de Asistencia y Turnos</h1>
        <div style="color: #94a3b8; font-size: 0.82rem; margin-top: 1px;">
            <?= $fechaTexto ?> &bull; <span style="color: #cbd5e1; font-weight: 600;">Turno <?= $turnoAuto ?></span>
        </div>
    </div>

    <div style="display: flex; align-items: center; gap: 10px;">
        <div class="clock-display-compact">
            <svg style="width: 14px; height: 14px; color: #38bdf8;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            <span id="relojDigital">--:--:--</span>
        </div>
        <a href="<?= vistaUrl('taxista/dashboard.php') ?>" class="btn btn-outline btn-sm" style="color: #ffffff; border-color: rgba(255,255,255,0.25); padding: 4px 10px; font-size: 0.8rem;">
            &larr; Volver
        </a>
    </div>
</div>

<div class="row" style="margin-bottom: 20px;">
    <!-- COLUMNA PRINCIPAL: MARCADO COMPACTO DE ENTRADA O SALIDA -->
    <div class="col-12 col-lg-7" style="margin-bottom: 16px;">

        <?php if (!$estaEnTurno): ?>
            <!-- CASO 1: NO ESTÁ EN TURNO -> MARCAR ENTRADA DIRECTA (1 CLIC) -->
            <div class="card" style="border: 1.5px solid #10b981; padding: 16px;">
                <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <span class="badge badge-warning" style="font-size: 0.72rem; padding: 2px 7px; margin-bottom: 4px; display: inline-block;">Paso 1: Iniciar Servicio</span>
                        <h2 class="card-title" style="margin: 0; font-size: 1.05rem; color: #0f172a; display: flex; align-items: center; gap: 6px;">
                            <svg style="width: 18px; height: 18px; color: #10b981;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            Marcar Entrada (Inicio de Turno)
                        </h2>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 0.75rem; color: #64748b; font-weight: 600;">Hora Exacta</span>
                        <div id="horaEntradaPreview" style="font-weight: 700; color: #10b981; font-size: 0.95rem;">--:--</div>
                    </div>
                </div>

                <!-- Resumen de datos vinculados (Automáticos y compactos) -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px;">
                    <div class="info-tile-compact">
                        <div class="info-tile-icon-compact" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                            <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                        <div style="overflow: hidden; line-height: 1.25;">
                            <div style="font-size: 0.68rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Conductor</div>
                            <div style="font-weight: 700; color: #0f172a; font-size: 0.85rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($nombreCompleto) ?></div>
                            <div style="font-size: 0.75rem; color: #64748b;">DNI: <?= htmlspecialchars($usuario['dni']) ?></div>
                        </div>
                    </div>

                    <div class="info-tile-compact">
                        <div class="info-tile-icon-compact" style="background: rgba(245, 158, 11, 0.1); color: #d97706;">
                            <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="17" r="3"></circle><circle cx="19" cy="17" r="3"></circle><path d="M5 17h14"></path><path d="M9 17l2-7h4l2 7"></path><path d="M11 10h4"></path></svg>
                        </div>
                        <div style="overflow: hidden; line-height: 1.25;">
                            <div style="font-size: 0.68rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Unidad Mototaxi</div>
                            <?php if ($vehiculo): ?>
                                <div style="margin-top: 1px;">
                                    <span class="plate-badge-compact"><?= htmlspecialchars($vehiculo['placa']) ?></span>
                                    <small style="color: #64748b; margin-left: 2px;">PAD: <?= htmlspecialchars($vehiculo['numero_padron'] ?? '-') ?></small>
                                </div>
                            <?php else: ?>
                                <div style="font-size: 0.8rem; color: #ef4444; font-weight: 600;">Sin unidad asignada</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 8px 12px; margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <svg style="width: 16px; height: 16px; color: #059669;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        <div style="font-size: 0.82rem; color: #065f46;">
                            <strong>Turno Detectado: <?= $turnoAuto ?></strong> &bull; Se notificará al administrador con la hora y fecha de hoy.
                        </div>
                    </div>
                    <span class="badge badge-success" style="font-size: 0.75rem; padding: 2px 6px;">Listo</span>
                </div>

                <!-- FORMULARIO 1-CLIC DE ENTRADA -->
                <form action="<?= controladorUrl('taxista_controlador.php') ?>" method="POST" id="formEntradaDirecta">
                    <input type="hidden" name="accion" value="marcar_entrada">
                    <input type="hidden" name="id_conductor" value="<?= (int)($conductor['id_conductor'] ?? 0) ?>">
                    <input type="hidden" name="id_vehiculo" value="<?= (int)($vehiculo['id_vehiculo'] ?? 0) ?>">
                    <input type="hidden" name="turno" id="inputTurno" value="<?= htmlspecialchars($turnoAuto) ?>">

                    <!-- BOTÓN DE ENTRADA COMPACTO -->
                    <div style="display: flex; justify-content: flex-start; margin: 4px 0 8px 0;">
                        <button type="submit" class="action-btn-compact btn-entrada-compact">
                            <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            MARCAR ENTRADA AHORA
                        </button>
                    </div>

                    <!-- SECCIÓN OPCIONAL (COLAPSADA) -->
                    <details style="margin-top: 10px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0; padding: 8px 12px;">
                        <summary style="cursor: pointer; font-size: 0.8rem; color: #64748b; font-weight: 600; user-select: none;">
                            + Opcional: Cambiar Turno / Agregar Kilometraje u Observación
                        </summary>
                        <div style="margin-top: 10px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div>
                                <label style="font-size: 0.78rem; font-weight: 600; color: #334155; margin-bottom: 4px; display: block;">Turno de Trabajo</label>
                                <select class="form-control form-control-sm" onchange="document.getElementById('inputTurno').value = this.value;" style="font-size: 0.82rem; height: 32px;">
                                    <option value="Mañana" <?= $turnoAuto === 'Mañana' ? 'selected' : '' ?>>Mañana (06:00 AM - 02:00 PM)</option>
                                    <option value="Tarde" <?= $turnoAuto === 'Tarde' ? 'selected' : '' ?>>Tarde (02:00 PM - 10:00 PM)</option>
                                    <option value="Noche" <?= $turnoAuto === 'Noche' ? 'selected' : '' ?>>Noche (10:00 PM - 06:00 AM)</option>
                                    <option value="Completo">Jornada Completa</option>
                                </select>
                            </div>
                            <div>
                                <label style="font-size: 0.78rem; font-weight: 600; color: #334155; margin-bottom: 4px; display: block;">Km Inicial del Odómetro</label>
                                <input type="number" name="kilometraje_inicial" class="form-control form-control-sm" placeholder="Ej: 15420" min="0" style="font-size: 0.82rem; height: 32px;">
                            </div>
                            <div style="grid-column: 1 / -1;">
                                <label style="font-size: 0.78rem; font-weight: 600; color: #334155; margin-bottom: 4px; display: block;">Nota / Estado de la Moto</label>
                                <input type="text" name="observaciones" class="form-control form-control-sm" placeholder="Ej: Unidad limpia, sin desperfectos" style="font-size: 0.82rem; height: 32px;">
                            </div>
                        </div>
                    </details>
                </form>

                <?php if ($turnoCompletadoHoy): ?>
                    <div style="margin-top: 12px; padding: 8px 12px; background: #f0fdf4; border-radius: 6px; border: 1px solid #bbf7d0; font-size: 0.8rem; color: #166534;">
                        ℹ️ Turno previo completado hoy (Entrada: <?= substr($asistenciaHoy['hora_ingreso'], 0, 5) ?> | Salida: <?= substr($asistenciaHoy['hora_salida'], 0, 5) ?>). Al marcar entrada iniciarás un nuevo turno.
                    </div>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <!-- CASO 2: ESTÁ EN TURNO -> MARCAR SALIDA DIRECTA (1 CLIC) -->
            <div class="card" style="border: 1.5px solid #ef4444; padding: 16px;">
                <div style="border-bottom: 1px solid #fecaca; padding-bottom: 10px; margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 3px;">
                            <span class="pulse-dot-sm pulse-green"></span>
                            <span style="color: #059669; font-weight: 700; font-size: 0.75rem; text-transform: uppercase;">EN TURNO ACTIVO</span>
                        </div>
                        <h2 class="card-title" style="margin: 0; font-size: 1.05rem; color: #991b1b; display: flex; align-items: center; gap: 6px;">
                            <svg style="width: 18px; height: 18px; color: #ef4444;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="9" x2="15" y2="15"></line><line x1="15" y1="9" x2="9" y2="15"></line></svg>
                            Finalizar Turno / Marcar Salida
                        </h2>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 0.75rem; color: #64748b; font-weight: 600;">Hora Salida</span>
                        <div id="horaSalidaPreview" style="font-weight: 700; color: #ef4444; font-size: 0.95rem;">--:--</div>
                    </div>
                </div>

                <!-- Detalle compacto del turno activo -->
                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px 14px; margin-bottom: 14px;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 8px;">
                        <div>
                            <div style="font-size: 0.68rem; color: #991b1b; font-weight: 700; text-transform: uppercase;">Hora Ingreso</div>
                            <div style="font-size: 1rem; font-weight: 700; color: #1e293b;"><?= htmlspecialchars(substr($asistenciaHoy['hora_ingreso'], 0, 5)) ?> hrs</div>
                        </div>
                        <div>
                            <div style="font-size: 0.68rem; color: #991b1b; font-weight: 700; text-transform: uppercase;">Turno</div>
                            <div style="font-size: 0.9rem; font-weight: 600; color: #1e293b;"><?= htmlspecialchars($asistenciaHoy['turno']) ?></div>
                        </div>
                        <div>
                            <div style="font-size: 0.68rem; color: #991b1b; font-weight: 700; text-transform: uppercase;">Moto en Uso</div>
                            <div><span class="plate-badge-compact"><?= htmlspecialchars($asistenciaHoy['placa'] ?? 'Sin Placa') ?></span></div>
                        </div>
                        <div>
                            <div style="font-size: 0.68rem; color: #991b1b; font-weight: 700; text-transform: uppercase;">Tiempo en Servicio</div>
                            <div id="cronometroTurno" style="font-size: 0.95rem; font-weight: 800; color: #059669;">Calculando...</div>
                        </div>
                    </div>
                </div>

                <!-- FORMULARIO 1-CLIC DE SALIDA -->
                <form action="<?= controladorUrl('taxista_controlador.php') ?>" method="POST" id="formSalidaDirecta">
                    <input type="hidden" name="accion" value="marcar_salida">
                    <input type="hidden" name="id_asistencia" value="<?= (int)$asistenciaHoy['id_asistencia'] ?>">

                    <!-- BOTÓN DE SALIDA COMPACTO -->
                    <div style="display: flex; justify-content: flex-start; margin: 4px 0 8px 0;">
                    <button type="submit" class="action-btn-compact btn-salida-compact">
                        <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><polyline points="9 11 12 14 22 4"></polyline></svg>
                        MARCAR SALIDA AHORA
                    </button>
                    </div>

                    <!-- DETALLES OPCIONALES AL CERRAR -->
                    <details style="margin-top: 10px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0; padding: 8px 12px;">
                        <summary style="cursor: pointer; font-size: 0.8rem; color: #64748b; font-weight: 600; user-select: none;">
                            + Opcional: Ingresar Kilometraje Final / Novedad de Entrega
                        </summary>
                        <div style="margin-top: 10px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div>
                                <label style="font-size: 0.78rem; font-weight: 600; color: #334155; margin-bottom: 4px; display: block;">Km Final del Odómetro</label>
                                <input type="number" name="kilometraje_final" class="form-control form-control-sm" placeholder="Ej: <?= !empty($asistenciaHoy['kilometraje_inicial']) ? ($asistenciaHoy['kilometraje_inicial'] + 45) : '15480' ?>" min="<?= (int)($asistenciaHoy['kilometraje_inicial'] ?? 0) ?>" style="font-size: 0.82rem; height: 32px;">
                            </div>
                            <div>
                                <label style="font-size: 0.78rem; font-weight: 600; color: #334155; margin-bottom: 4px; display: block;">Novedad / Entrega de Moto</label>
                                <input type="text" name="observaciones" class="form-control form-control-sm" placeholder="Ej: Moto entregada sin novedades" style="font-size: 0.82rem; height: 32px;">
                            </div>
                        </div>
                    </details>
                </form>
            </div>
        <?php endif; ?>

    </div>

    <!-- COLUMNA LATERAL: RESUMEN OPERATIVO COMPACTO -->
    <div class="col-12 col-lg-5">
        <div class="card" style="padding: 16px; height: 100%;">
            <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 12px;">
                <h3 class="card-title" style="margin: 0; font-size: 1rem;">Ficha Operativa de Conductor y Unidad</h3>
                <p class="card-subtitle" style="margin: 2px 0 0 0; color: #64748b; font-size: 0.8rem;">Datos sincronizados con la administración central</p>
            </div>

            <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px;">
                <div class="info-tile-compact">
                    <div class="info-tile-icon-compact" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><line x1="15" y1="8" x2="17" y2="8"></line><line x1="15" y1="12" x2="17" y2="12"></line><line x1="7" y1="16" x2="17" y2="16"></line></svg>
                    </div>
                    <div style="line-height: 1.25;">
                        <div style="font-size: 0.68rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Licencia de Conducir</div>
                        <div style="font-weight: 700; font-size: 0.88rem; color: #0f172a;"><?= htmlspecialchars($conductor['numero_licencia'] ?? 'No registrada') ?></div>
                        <div style="font-size: 0.75rem; color: #64748b;">Cat: <?= htmlspecialchars($conductor['categoria_licencia'] ?? 'B-IIc') ?> &bull; <span style="color: #10b981; font-weight: 600;"><?= htmlspecialchars($conductor['estado'] ?? 'Habilitado') ?></span></div>
                    </div>
                </div>

                <div class="info-tile-compact">
                    <div class="info-tile-icon-compact" style="background: rgba(16, 185, 129, 0.1); color: #059669;">
                        <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                    <div style="line-height: 1.25;">
                        <div style="font-size: 0.68rem; color: #64748b; font-weight: 700; text-transform: uppercase;">SOAT y Habilitación Municipal</div>
                        <div style="font-weight: 700; font-size: 0.88rem; color: #0f172a;">Unidad Autorizada KEBA</div>
                        <div style="font-size: 0.75rem; color: #059669; font-weight: 600;">✓ Registros en regla para circular</div>
                    </div>
                </div>
            </div>

            <div style="background: #f8fafc; padding: 10px 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                <h4 style="margin: 0 0 6px 0; font-size: 0.82rem; color: #0f172a; font-weight: 700; display: flex; align-items: center; gap: 6px;">
                    <svg style="width: 14px; height: 14px; color: #2563eb;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    Instrucciones
                </h4>
                <ul style="margin: 0; padding-left: 16px; font-size: 0.78rem; color: #475569; line-height: 1.5;">
                    <li>Presiona <strong>MARCAR ENTRADA</strong> al iniciar tu jornada.</li>
                    <li>Presiona <strong>MARCAR SALIDA</strong> al entregar o guardar la mototaxi.</li>
                    <li>Tu usuario, la mototaxi y los horarios se envían en vivo al panel del administrador.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- HISTORIAL DE ASISTENCIAS RECIENTES -->
<div class="card" style="padding: 16px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
        <div>
            <h2 class="card-title" style="margin: 0; font-size: 1.05rem;">Historial de Asistencias y Turnos</h2>
            <p class="card-subtitle" style="margin: 2px 0 0 0; color: #64748b; font-size: 0.8rem;">Tus últimos 30 turnos completados</p>
        </div>
        <div>
            <span class="badge badge-info" style="font-size: 0.78rem; padding: 4px 10px;">
                Total: <?= count($historialAsistencias) ?>
            </span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table-custom" style="font-size: 0.88rem;">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Turno</th>
                    <th>Hora Entrada</th>
                    <th>Hora Salida</th>
                    <th>Mototaxi</th>
                    <th>Km Recorrido</th>
                    <th>Estado</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($historialAsistencias)): ?>
                    <?php foreach ($historialAsistencias as $asig): 
                        $kmRecorrido = '-';
                        if (!empty($asig['kilometraje_inicial']) && !empty($asig['kilometraje_final'])) {
                            $dif = $asig['kilometraje_final'] - $asig['kilometraje_inicial'];
                            if ($dif >= 0) {
                                $kmRecorrido = number_format($dif) . ' km';
                            }
                        }
                    ?>
                    <tr>
                        <td>
                            <strong style="color: #0f172a;"><?= htmlspecialchars(date('d/m/Y', strtotime($asig['fecha']))) ?></strong>
                        </td>
                        <td>
                            <span class="badge badge-secondary" style="font-weight: 600; font-size: 0.75rem;"><?= htmlspecialchars($asig['turno']) ?></span>
                        </td>
                        <td>
                            <span style="color: #059669; font-weight: 700; font-family: monospace; font-size: 0.88rem;">
                                <?= htmlspecialchars(substr($asig['hora_ingreso'], 0, 5)) ?> hrs
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($asig['hora_salida'])): ?>
                                <span style="color: #2563eb; font-weight: 700; font-family: monospace; font-size: 0.88rem;">
                                    <?= htmlspecialchars(substr($asig['hora_salida'], 0, 5)) ?> hrs
                                </span>
                            <?php else: ?>
                                <span class="badge badge-warning" style="font-size: 0.72rem;">En Curso</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($asig['placa'])): ?>
                                <span class="plate-badge-compact" style="font-size: 0.82rem;">
                                    <?= htmlspecialchars($asig['placa']) ?>
                                </span>
                                <?php if (!empty($asig['numero_padron'])): ?>
                                    <small style="color: #64748b; margin-left: 2px;">(PAD: <?= htmlspecialchars($asig['numero_padron']) ?>)</small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span style="color: #94a3b8;">Sin unidad</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($kmRecorrido) ?></td>
                        <td>
                            <?php if ($asig['estado'] === 'En Turno'): ?>
                                <span class="badge badge-success" style="font-size: 0.72rem; padding: 2px 7px;">
                                    En Turno
                                </span>
                            <?php else: ?>
                                <span class="badge badge-info" style="font-size: 0.72rem; padding: 2px 7px;">✓ Finalizado</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size: 0.78rem; color: #64748b; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($asig['observaciones'] ?? '') ?>">
                            <?= htmlspecialchars($asig['observaciones'] ?? '-') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: #94a3b8; padding: 25px;">
                            No tienes asistencias registradas aún. Presiona <strong>MARCAR ENTRADA</strong> para comenzar tu primer turno.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Reloj digital en vivo sincronizado
    function actualizarReloj() {
        const ahora = new Date();
        const horas24 = ahora.getHours();
        const horas12 = horas24 % 12 || 12;
        const horasStr = String(horas24).padStart(2, '0');
        const minStr = String(ahora.getMinutes()).padStart(2, '0');
        const secStr = String(ahora.getSeconds()).padStart(2, '0');
        const ampm = horas24 >= 12 ? 'PM' : 'AM';
        
        const el = document.getElementById('relojDigital');
        if (el) {
            el.innerText = `${horasStr}:${minStr}:${secStr} ${ampm}`;
        }

        const horaSimple = `${String(horas12).padStart(2, '0')}:${minStr} ${ampm}`;
        const previewEntrada = document.getElementById('horaEntradaPreview');
        if (previewEntrada) previewEntrada.innerText = horaSimple;

        const previewSalida = document.getElementById('horaSalidaPreview');
        if (previewSalida) previewSalida.innerText = horaSimple;
    }
    setInterval(actualizarReloj, 1000);
    actualizarReloj();

    <?php if ($estaEnTurno && !empty($asistenciaHoy['hora_ingreso'])): ?>
    // Cronómetro en vivo del tiempo en servicio
    function actualizarCronometro() {
        const horaIngresoStr = "<?= $asistenciaHoy['fecha'] . ' ' . $asistenciaHoy['hora_ingreso'] ?>";
        const entrada = new Date(horaIngresoStr.replace(/-/g, "/"));
        const ahora = new Date();
        let diffMs = ahora - entrada;
        if (diffMs < 0) diffMs = 0;
        
        const diffHrs = Math.floor(diffMs / 3600000);
        const diffMin = Math.floor((diffMs % 3600000) / 60000);
        const diffSec = Math.floor((diffMs % 60000) / 1000);

        const elCron = document.getElementById('cronometroTurno');
        if (elCron) {
            elCron.innerText = `${String(diffHrs).padStart(2, '0')}h ${String(diffMin).padStart(2, '0')}m ${String(diffSec).padStart(2, '0')}s`;
        }
    }
    setInterval(actualizarCronometro, 1000);
    actualizarCronometro();
    <?php endif; ?>
</script>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>
