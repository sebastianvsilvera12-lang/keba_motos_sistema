<?php
$tituloPagina = 'Avisos de Taxistas: Daños y Revisión de Motos';
$subtituloNavbar = 'Bandeja de Novedades de Unidades';
$menuActivo = 'reportes_taxistas';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';

require_once dirname(dirname(__DIR__)) . '/modelos/ReporteVehiculo.php';

$filtroEstado = $_GET['estado'] ?? null;
$repMod = new ReporteVehiculo();
$reportes = $repMod->listarTodos($filtroEstado);
$pendientesCount = $repMod->contarPendientes();

$totalReportes = count($reportes);
$urgentesCount = 0;
$limpiezaCount = 0;

foreach ($reportes as $r) {
    if ($r['prioridad'] === 'Urgente' || $r['prioridad'] === 'Alta') $urgentesCount++;
    if (strpos($r['estado_limpieza'], 'Requiere Lavado') !== false) $limpiezaCount++;
}
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h1 class="page-title">Avisos de Taxistas: Daños, Limpieza y Revisión</h1>
        <p class="page-subtitle">Recepción de mensajes y novedades reportadas por los choferes sobre las mototaxis de la empresa</p>
    </div>
    <div>
        <a href="<?= vistaUrl('vehiculos/listar_vehiculos.php') ?>" class="btn btn-outline">&larr; Volver a Mototaxis</a>
    </div>
</div>

<!-- TARJETAS DE MÉTRICAS -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 25px;">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Avisos</h3>
            <div class="stat-number"><?= $totalReportes ?></div>
            <div class="stat-desc">Reportes registrados</div>
        </div>
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Por Atender</h3>
            <div class="stat-number" style="color: #f59e0b;"><?= $pendientesCount ?></div>
            <div class="stat-desc">Esperando respuesta admin</div>
        </div>
        <div class="stat-icon warning">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Alta Prioridad</h3>
            <div class="stat-number" style="color: #ef4444;"><?= $urgentesCount ?></div>
            <div class="stat-desc">Averías o desperfectos graves</div>
        </div>
        <div class="stat-icon danger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Requieren Lavado</h3>
            <div class="stat-number" style="color: #38bdf8;"><?= $limpiezaCount ?></div>
            <div class="stat-desc">Solicitud de limpieza profunda</div>
        </div>
        <div class="stat-icon dark">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>
        </div>
    </div>
</div>

<!-- FILTROS POR ESTADO -->
<div class="card" style="margin-bottom: 25px; padding: 15px;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <span style="font-weight: 600; font-size: 0.9rem; color: #94a3b8; margin-right: 5px;">Filtrar por:</span>
            <a href="<?= vistaUrl('vehiculos/reportes_taxistas.php') ?>" class="btn btn-sm <?= empty($filtroEstado) ? 'btn-primary' : 'btn-outline' ?>">Todos (<?= $totalReportes ?>)</a>
            <a href="<?= vistaUrl('vehiculos/reportes_taxistas.php?estado=Pendiente') ?>" class="btn btn-sm <?= $filtroEstado === 'Pendiente' ? 'btn-primary' : 'btn-outline' ?>" style="<?= $pendientesCount > 0 ? 'border-color: #f59e0b; color: #f59e0b;' : '' ?>">
                Pendientes (<?= $pendientesCount ?>)
            </a>
            <a href="<?= vistaUrl('vehiculos/reportes_taxistas.php?estado=En Revisión') ?>" class="btn btn-sm <?= $filtroEstado === 'En Revisión' ? 'btn-primary' : 'btn-outline' ?>">En Revisión</a>
            <a href="<?= vistaUrl('vehiculos/reportes_taxistas.php?estado=Atendido') ?>" class="btn btn-sm <?= $filtroEstado === 'Atendido' ? 'btn-primary' : 'btn-outline' ?>">Atendidos</a>
            <a href="<?= vistaUrl('vehiculos/reportes_taxistas.php?estado=Resuelto') ?>" class="btn btn-sm <?= $filtroEstado === 'Resuelto' ? 'btn-primary' : 'btn-outline' ?>">Resueltos</a>
        </div>
    </div>
</div>

<!-- LISTA DE AVISOS Y MENSAJES -->
<div class="card">
    <div style="border-bottom: 1px solid #334155; padding-bottom: 12px; margin-bottom: 20px;">
        <h2 class="card-title" style="margin: 0;">Bandeja de Mensajes de Taxistas</h2>
        <p class="card-subtitle" style="margin: 4px 0 0 0; color: #64748b; font-size: 0.85rem;">Selecciona un reporte para leer los detalles, programar su taller y responderle al conductor</p>
    </div>

    <?php if (!empty($reportes)): ?>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($reportes as $rep): 
                $badgePrioridad = 'badge-info';
                if ($rep['prioridad'] === 'Urgente') $badgePrioridad = 'badge-danger';
                elseif ($rep['prioridad'] === 'Alta') $badgePrioridad = 'badge-warning';

                $badgeEstado = 'badge-warning';
                if ($rep['estado'] === 'Atendido' || $rep['estado'] === 'Resuelto') $badgeEstado = 'badge-success';
                elseif ($rep['estado'] === 'En Revisión') $badgeEstado = 'badge-info';
            ?>
            <div style="background: #0f172a; border-radius: 12px; border: 1px solid <?= $rep['estado'] === 'Pendiente' ? '#f59e0b' : '#1e293b' ?>; padding: 18px;">
                
                <!-- CABECERA DE LA TARJETA -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; margin-bottom: 12px;">
                    <div>
                        <span class="badge <?= $badgePrioridad ?>" style="margin-right: 8px;"><?= htmlspecialchars($rep['prioridad']) ?></span>
                        <strong style="font-size: 1.1rem; color: #f8fafc;"><?= htmlspecialchars($rep['tipo_asunto']) ?></strong>
                        <span style="color: #64748b; font-size: 0.85rem; margin-left: 8px;">• Recibido el <?= date('d/m/Y H:i', strtotime($rep['created_at'])) ?></span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span class="badge <?= $badgeEstado ?>" style="font-size: 0.85rem; padding: 4px 10px;"><?= htmlspecialchars($rep['estado']) ?></span>
                        <button type="button" class="btn btn-primary btn-sm" onclick="abrirModalRespuesta(<?= htmlspecialchars(json_encode($rep)) ?>)">
                            💬 Responder / Atender
                        </button>
                    </div>
                </div>

                <!-- DATOS DEL VEHÍCULO Y CONDUCTOR -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; margin-bottom: 12px; background: #1e293b; padding: 12px; border-radius: 8px; font-size: 0.85rem;">
                    <div>
                        <span style="color: #94a3b8; display: block; font-size: 0.75rem; text-transform: uppercase;">Mototaxi</span>
                        <strong style="color: #38bdf8; font-size: 0.95rem;">Placa: <?= htmlspecialchars($rep['placa']) ?></strong>
                        <div style="color: #cbd5e1;"><?= htmlspecialchars($rep['marca'] . ' ' . $rep['modelo']) ?> (Padrón: <?= htmlspecialchars($rep['numero_padron'] ?? '-') ?>)</div>
                    </div>
                    <div>
                        <span style="color: #94a3b8; display: block; font-size: 0.75rem; text-transform: uppercase;">Conductor / Remitente</span>
                        <strong style="color: #f8fafc;"><?= htmlspecialchars($rep['conductor_nombres'] . ' ' . $rep['conductor_apellidos']) ?></strong>
                        <div style="color: #cbd5e1;">Usuario: @<?= htmlspecialchars($rep['usuario']) ?> • Tel: <?= htmlspecialchars($rep['conductor_telefono'] ?? '-') ?></div>
                    </div>
                    <div>
                        <span style="color: #94a3b8; display: block; font-size: 0.75rem; text-transform: uppercase;">Limpieza & Fecha Revisión Sugerida</span>
                        <div style="color: #f8fafc;">Limpieza: <strong><?= htmlspecialchars($rep['estado_limpieza']) ?></strong></div>
                        <div style="color: #10b981;">Revisión sugerida: <strong><?= !empty($rep['fecha_revision_sugerida']) ? date('d/m/Y', strtotime($rep['fecha_revision_sugerida'])) : 'No indicada' ?></strong></div>
                    </div>
                </div>

                <!-- DAÑOS / DESPERFECTOS DESCRITOS POR EL CHOFER -->
                <div style="margin-bottom: 12px; background: rgba(0,0,0,0.25); padding: 12px; border-radius: 8px; border-left: 3px solid #38bdf8;">
                    <div style="color: #94a3b8; font-size: 0.8rem; margin-bottom: 4px; font-weight: 600;">Descripción de Daños o Avería:</div>
                    <div style="color: #f1f5f9; font-size: 0.95rem; line-height: 1.5;"><?= nl2br(htmlspecialchars($rep['descripcion_danos'])) ?></div>
                </div>

                <!-- FOTO DE EVIDENCIA SI SUBIÓ -->
                <?php if (!empty($rep['foto_evidencia']) && file_exists(ROOT_PATH . 'subidas/fotos_danos/' . $rep['foto_evidencia'])): ?>
                    <div style="margin-bottom: 12px;">
                        <a href="<?= subidaUrl('fotos_danos/' . $rep['foto_evidencia']) ?>" target="_blank" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
                            <svg style="width: 15px; height: 15px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            Ver Foto Adjunta de Evidencia del Daño
                        </a>
                    </div>
                <?php endif; ?>

                <!-- RESPUESTA ACTUAL DE ADMINISTRACIÓN (SI YA RESPONDIÓ) -->
                <?php if (!empty($rep['respuesta_admin'])): ?>
                    <div style="border-left: 4px solid #10b981; background: rgba(16, 185, 129, 0.08); padding: 12px 14px; border-radius: 0 8px 8px 0; margin-top: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <strong style="color: #10b981; font-size: 0.85rem; display: flex; align-items: center; gap: 6px;">
                                <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                Respuesta Enviada al Conductor por: <?= htmlspecialchars($rep['admin_nombres'] ?? 'Administración') ?>
                            </strong>
                            <span style="font-size: 0.75rem; color: #64748b;"><?= !empty($rep['fecha_respuesta']) ? date('d/m/Y H:i', strtotime($rep['fecha_respuesta'])) : '' ?></span>
                        </div>
                        <div style="font-size: 0.9rem; color: #f1f5f9;"><?= nl2br(htmlspecialchars($rep['respuesta_admin'])) ?></div>
                    </div>
                <?php endif; ?>

            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; color: #94a3b8; padding: 40px 20px;">
            <svg style="width: 48px; height: 48px; margin-bottom: 10px; color: #475569;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            <p style="margin: 0; font-size: 1rem; color: #f8fafc;">No hay reportes de taxistas registrados bajo este filtro.</p>
            <p style="margin: 4px 0 0 0; font-size: 0.85rem; color: #64748b;">Cuando los taxistas reporten averías, limpieza o revisiones desde su panel, aparecerán aquí automáticamente.</p>
        </div>
    <?php endif; ?>
</div>

<!-- ======================================================== -->
<!-- MODAL PARA QUE EL ADMINISTRADOR RESPONDA AL TAXISTA      -->
<!-- ======================================================== -->
<div id="modalRespuestaAdmin" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.75); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #111827; border: 1px solid #334155; border-radius: 12px; width: 100%; max-width: 600px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.6); max-height: 90vh; overflow-y: auto;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; padding-bottom: 12px; margin-bottom: 20px;">
            <h3 style="margin: 0; font-size: 1.2rem; color: #f8fafc; display: flex; align-items: center; gap: 8px;">
                <svg style="width: 20px; height: 20px; color: #10b981;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                Responder al Taxista / Programar Unidad
            </h3>
            <button type="button" onclick="cerrarModalRespuesta()" style="background: transparent; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer; line-height: 1;">&times;</button>
        </div>

        <div id="modalDetalleResumen" style="background: #1e293b; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.85rem; color: #cbd5e1;">
            <!-- Se llena dinámicamente con JS -->
        </div>

        <form action="<?= controladorUrl('vehiculo_controlador.php') ?>" method="POST">
            <input type="hidden" name="accion" value="responder_reporte_taxista">
            <input type="hidden" name="id_reporte" id="modalIdReporte" value="">

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="modalRespuestaTxt" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px; display: block;">Respuesta / Instrucciones para el Conductor *</label>
                <textarea name="respuesta_admin" id="modalRespuestaTxt" class="form-control" rows="3" required placeholder="Ej: Se autoriza pase al taller mecánico este viernes a las 09:00 hrs. Acercarse con la unidad limpia para la revisión..."></textarea>
                <small style="color: #64748b;">El taxista verá este mensaje directamente en su panel de control</small>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div class="form-group">
                    <label for="modalEstadoSelect" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 6px; display: block;">Estado del Reporte *</label>
                    <select name="estado" id="modalEstadoSelect" class="form-control" required style="font-weight: 600;">
                        <option value="En Revisión">En Revisión</option>
                        <option value="Atendido" selected>Atendido (Coordinado)</option>
                        <option value="Resuelto">Resuelto / Cerrado</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="modalEstadoMec" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 6px; display: block;">Condición Mecánica de la Moto</label>
                    <select name="estado_mecanico" id="modalEstadoMec" class="form-control">
                        <option value="Operativo">Operativo</option>
                        <option value="Con Observaciones">Con Observaciones</option>
                        <option value="En Mantenimiento">En Mantenimiento</option>
                        <option value="Taller">Taller / Fuera de Servicio</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="modalFechaRevision" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px; display: block;">Fecha Definitiva de Próxima Revisión / Mantenimiento</label>
                <input type="date" name="fecha_proxima_revision" id="modalFechaRevision" class="form-control" value="<?= date('Y-m-d', strtotime('+3 days')) ?>">
                <small style="color: #64748b;">Actualiza la fecha oficial de revisión técnica en la ficha de la mototaxi</small>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="cerrarModalRespuesta()">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="font-weight: 700; padding: 10px 20px;">
                    Enviar Respuesta y Guardar
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    function abrirModalRespuesta(reporte) {
        document.getElementById('modalIdReporte').value = reporte.id_reporte;
        document.getElementById('modalRespuestaTxt').value = reporte.respuesta_admin || '';
        document.getElementById('modalEstadoSelect').value = reporte.estado === 'Pendiente' ? 'Atendido' : reporte.estado;
        if (reporte.fecha_revision_sugerida) {
            document.getElementById('modalFechaRevision').value = reporte.fecha_revision_sugerida;
        }

        const resumenHtml = `
            <div><strong>Unidad:</strong> Placa ${reporte.placa} (${reporte.marca} ${reporte.modelo} - Padrón: ${reporte.numero_padron || '-'})</div>
            <div><strong>Conductor:</strong> ${reporte.conductor_nombres} ${reporte.conductor_apellidos} (Tel: ${reporte.conductor_telefono || '-'})</div>
            <div><strong>Asunto:</strong> ${reporte.tipo_asunto} | <strong>Limpieza:</strong> ${reporte.estado_limpieza}</div>
            <div style="margin-top: 4px; color: #f8fafc;"><strong>Daños reportados:</strong> ${reporte.descripcion_danos}</div>
        `;
        document.getElementById('modalDetalleResumen').innerHTML = resumenHtml;

        const modal = document.getElementById('modalRespuestaAdmin');
        modal.style.display = 'flex';
    }

    function cerrarModalRespuesta() {
        document.getElementById('modalRespuestaAdmin').style.display = 'none';
    }
</script>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

