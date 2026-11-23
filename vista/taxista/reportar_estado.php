<?php
$tituloPagina = 'Estado de la Moto y Reporte de Daños - Taxistas';
$subtituloNavbar = 'Mantenimiento, Limpieza y Daños';
$menuActivo = 'reportar_estado';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';

require_once dirname(dirname(__DIR__)) . '/modelos/Taxista.php';
require_once dirname(dirname(__DIR__)) . '/modelos/ReporteVehiculo.php';

$usuario = obtenerUsuarioActual();
$taxistaMod = new Taxista();
$repMod = new ReporteVehiculo();

$ficha = $taxistaMod->obtenerFichaTaxista($usuario['id']);
$vehiculo = $ficha['vehiculo'] ?? null;
$conductor = $ficha['conductor'] ?? null;

$misReportes = $repMod->listarPorUsuario($usuario['id']);
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h1 class="page-title">Estado de la Moto: Daños, Limpieza y Revisión</h1>
        <p class="page-subtitle">Comunícate directamente con la Administración para reportar averías, programar revisiones técnicas y registrar el estado de tu mototaxi</p>
    </div>
    <div>
        <a href="<?= vistaUrl('taxista/dashboard.php') ?>" class="btn btn-outline">&larr; Volver al Panel</a>
    </div>
</div>

<!-- TARJETAS DE ESTADO ACTUAL DE LA UNIDAD DE LA EMPRESA -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom: 25px;">
    
    <div class="stat-card">
        <div class="stat-info">
            <h3>Mototaxi Asignada</h3>
            <div class="stat-number" style="font-size: 1.35rem; color: #0f172a; font-weight: 700;"><?= htmlspecialchars($vehiculo['placa'] ?? 'Sin Placa') ?></div>
            <div class="stat-desc">
                Padrón: <strong><?= htmlspecialchars($vehiculo['numero_padron'] ?? '-') ?></strong> • <?= !empty($vehiculo['es_empresa']) ? 'Unidad de la Empresa' : 'Asociada' ?>
            </div>
        </div>
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="17" r="3"></circle><circle cx="19" cy="17" r="3"></circle><path d="M5 17h14"></path><path d="M9 17l2-7h4l2 7"></path></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Próxima Revisión</h3>
            <div class="stat-number" style="font-size: 1.35rem; color: #0f172a; font-weight: 700;">
                <?= !empty($vehiculo['fecha_proxima_revision']) ? date('d/m/Y', strtotime($vehiculo['fecha_proxima_revision'])) : 'Por Programar' ?>
            </div>
            <div class="stat-desc">Fecha sugerida o confirmada</div>
        </div>
        <div class="stat-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Estado de Limpieza</h3>
            <div class="stat-number" style="font-size: 1.35rem; color: #0f172a; font-weight: 700;">
                <?= htmlspecialchars($vehiculo['estado_limpieza'] ?? 'Limpia') ?>
            </div>
            <div class="stat-desc">Higiene interior y exterior</div>
        </div>
        <div class="stat-icon warning">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Condición Mecánica</h3>
            <div class="stat-number" style="font-size: 1.35rem; color: #0f172a; font-weight: 700;">
                <?= htmlspecialchars($vehiculo['estado_mecanico'] ?? 'Operativo') ?>
            </div>
            <div class="stat-desc">Disponibilidad para servicio</div>
        </div>
        <div class="stat-icon dark">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
        </div>
    </div>

</div>

<div class="row" style="margin-bottom: 30px;">
    <!-- FORMULARIO DE REPORTE / MENSAJE A LA ADMINISTRACIÓN -->
    <div class="col-12 col-lg-5" style="margin-bottom: 25px;">
        <div class="card">
            <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 20px;">
                <h2 class="card-title" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 20px; height: 20px; color: #2563eb;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    Enviar Mensaje a la Administración
                </h2>
                <p class="card-subtitle" style="margin: 4px 0 0 0; color: #64748b; font-size: 0.85rem;">El Administrador recibirá tu aviso para coordinar taller, lavado o revisión</p>
            </div>

            <form action="<?= controladorUrl('taxista_controlador.php') ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="enviar_reporte_moto">
                <input type="hidden" name="id_vehiculo" value="<?= (int)($vehiculo['id_vehiculo'] ?? 0) ?>">

                <div class="form-group" style="margin-bottom: 15px;">
                    <label style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px; display: block; color: #334155;">Unidad Mototaxi</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars(($vehiculo['placa'] ?? 'Sin asignar') . ' - ' . ($vehiculo['marca'] ?? '') . ' ' . ($vehiculo['modelo'] ?? '')) ?>" readonly style="background: #f8fafc; color: #1e293b; border: 1.5px solid #cbd5e1; font-weight: 600;">
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="tipoAsunto" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px; display: block;">Tipo de Novedad / Asunto *</label>
                    <select name="tipo_asunto" id="tipoAsunto" class="form-control" required style="font-weight: 600;">
                        <option value="Daños y Desperfectos">⚠️ Daños y Desperfectos (Golpes, raspones, abolladuras, fallas)</option>
                        <option value="Programación de Revisión">🔧 Programación de Revisión Técnica / Mecánica</option>
                        <option value="Estado de Limpieza">🧼 Estado de Limpieza / Solicitud de Lavado</option>
                        <option value="Mantenimiento General">⚙️ Mantenimiento General (Aceite, frenos, llantas)</option>
                        <option value="Otro">📝 Otra Consulta Operativa</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="danosTxt" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px; display: block;">Detalle de Daños o Novedades *</label>
                    <textarea name="descripcion_danos" id="danosTxt" class="form-control" rows="3" required placeholder="Describe con claridad los daños o el problema: ej. raspón en tapabarro derecho, chirrido en freno delantero, foco trasero quemado..."></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div class="form-group">
                        <label for="limpiezaSelect" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 6px; display: block;">Estado Limpieza *</label>
                        <select name="estado_limpieza" id="limpiezaSelect" class="form-control">
                            <option value="Excelente / Limpia">Excelente / Limpia</option>
                            <option value="Regular / Aceptable" selected>Regular / Aceptable</option>
                            <option value="Requiere Lavado Urgente">Requiere Lavado Urgente</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="prioridadSelect" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 6px; display: block;">Prioridad *</label>
                        <select name="prioridad" id="prioridadSelect" class="form-control">
                            <option value="Baja">Baja</option>
                            <option value="Normal" selected>Normal</option>
                            <option value="Alta">Alta</option>
                            <option value="Urgente">Urgente (No circular)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="fechaRevSugerida" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px; display: block;">Fecha Sugerida para Revisión / Taller</label>
                    <input type="date" name="fecha_revision_sugerida" id="fechaRevSugerida" class="form-control" value="<?= date('Y-m-d', strtotime('+2 days')) ?>">
                    <small style="color: #64748b;">¿Cuándo sugieres o necesitas que la moto ingrese a revisión?</small>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="fotoDano" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px; display: block;">Foto de Evidencia del Daño (Opcional)</label>
                    <input type="file" name="foto_evidencia" id="fotoDano" class="form-control" accept="image/*" onchange="previewEvidencia(this)">
                    <small style="color: #64748b;">Adjunta una foto del golpe, rayadura o pieza averiada</small>
                    <div id="boxPreviewDano" style="display:none; margin-top: 10px; border-radius: 8px; overflow: hidden; max-height: 140px; border: 1px solid #334155;">
                        <img id="imgPreviewDano" src="" alt="Foto daño" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-weight: 700; font-size: 1rem; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    Enviar Reporte al Administrador
                </button>
            </form>
        </div>
    </div>

    <!-- BANDEJA DE MENSAJES Y RESPUESTAS DEL ADMINISTRADOR -->
    <div class="col-12 col-lg-7">
        <div class="card">
            <div style="border-bottom: 1px solid #334155; padding-bottom: 12px; margin-bottom: 15px;">
                <h2 class="card-title" style="margin: 0;">Bandeja de Reportes y Respuestas</h2>
                <p class="card-subtitle" style="margin: 4px 0 0 0; color: #64748b; font-size: 0.85rem;">Historial de novedades enviadas y respuestas de la administración</p>
            </div>

            <?php if (!empty($misReportes)): ?>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php foreach ($misReportes as $rep): 
                        $badgePrioridad = 'badge-info';
                        if ($rep['prioridad'] === 'Urgente') $badgePrioridad = 'badge-danger';
                        elseif ($rep['prioridad'] === 'Alta') $badgePrioridad = 'badge-warning';

                        $badgeEstado = 'badge-warning';
                        if ($rep['estado'] === 'Atendido' || $rep['estado'] === 'Resuelto') $badgeEstado = 'badge-success';
                        elseif ($rep['estado'] === 'En Revisión') $badgeEstado = 'badge-info';
                    ?>
                    <div style="background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; padding: 16px;">
                        
                        <!-- ENCABEZADO DEL REPORTE -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px; margin-bottom: 10px;">
                            <div>
                                <span class="badge <?= $badgePrioridad ?>" style="margin-right: 6px;"><?= htmlspecialchars($rep['prioridad']) ?></span>
                                <strong style="font-size: 0.95rem; color: #0f172a;"><?= htmlspecialchars($rep['tipo_asunto']) ?></strong>
                                <span style="color: #64748b; font-size: 0.8rem; margin-left: 8px;">(<?= date('d/m/Y H:i', strtotime($rep['created_at'])) ?>)</span>
                            </div>
                            <div>
                                <span class="badge <?= $badgeEstado ?>" style="font-size: 0.85rem; padding: 4px 10px;"><?= htmlspecialchars($rep['estado']) ?></span>
                            </div>
                        </div>

                        <!-- DESCRIPCIÓN DE DAÑOS Y ESTADO DE LIMPIEZA -->
                        <div style="color: #334155; font-size: 0.9rem; line-height: 1.5; margin-bottom: 12px; background: #ffffff; border: 1px solid #e2e8f0; padding: 10px 14px; border-radius: 6px;">
                            <div><strong>Daños reportados:</strong> <?= nl2br(htmlspecialchars($rep['descripcion_danos'])) ?></div>
                            <div style="margin-top: 6px; font-size: 0.8rem; color: #64748b;">
                                Limpieza: <strong><?= htmlspecialchars($rep['estado_limpieza']) ?></strong> • 
                                Revisión sugerida: <strong><?= !empty($rep['fecha_revision_sugerida']) ? date('d/m/Y', strtotime($rep['fecha_revision_sugerida'])) : 'No indicada' ?></strong>
                            </div>
                        </div>

                        <!-- FOTO DEL DAÑO SI ADJUNTÓ -->
                        <?php if (!empty($rep['foto_evidencia']) && file_exists(ROOT_PATH . 'subidas/fotos_danos/' . $rep['foto_evidencia'])): ?>
                            <div style="margin-bottom: 12px;">
                                <a href="<?= subidaUrl('fotos_danos/' . $rep['foto_evidencia']) ?>" target="_blank" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.8rem;">
                                    <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    Ver Foto Adjunta del Daño
                                </a>
                            </div>
                        <?php endif; ?>

                        <!-- RESPUESTA DEL ADMINISTRADOR -->
                        <?php if (!empty($rep['respuesta_admin'])): ?>
                            <div style="border-left: 4px solid #059669; background: #ecfdf5; padding: 12px 14px; border-radius: 0 8px 8px 0; margin-top: 10px; border: 1px solid #a7f3d0; border-left-width: 4px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                    <strong style="color: #065f46; font-size: 0.85rem; display: flex; align-items: center; gap: 6px;">
                                        <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        Respuesta de Administración (<?= htmlspecialchars($rep['admin_nombres'] ?? 'Administrador') ?>)
                                    </strong>
                                    <span style="font-size: 0.75rem; color: #047857;"><?= !empty($rep['fecha_respuesta']) ? date('d/m/Y H:i', strtotime($rep['fecha_respuesta'])) : '' ?></span>
                                </div>
                                <div style="font-size: 0.88rem; color: #064e3b;"><?= nl2br(htmlspecialchars($rep['respuesta_admin'])) ?></div>
                            </div>
                        <?php else: ?>
                            <div style="display: flex; align-items: center; gap: 8px; color: #64748b; font-size: 0.8rem; margin-top: 8px;">
                                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;"></span>
                                <em>Esperando revisión y respuesta del Administrador...</em>
                            </div>
                        <?php endif; ?>

                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; color: #94a3b8; padding: 40px 20px;">
                    <svg style="width: 48px; height: 48px; margin-bottom: 10px; color: #475569;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    <p style="margin: 0; font-size: 0.95rem;">No has enviado reportes o solicitudes de mantenimiento recientemente.</p>
                    <p style="margin: 4px 0 0 0; font-size: 0.85rem; color: #64748b;">Si tu unidad presenta alguna avería, suciedad o requiere revisión, usa el formulario de la izquierda.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>
    function previewEvidencia(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const box = document.getElementById('boxPreviewDano');
                const img = document.getElementById('imgPreviewDano');
                img.src = e.target.result;
                box.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

