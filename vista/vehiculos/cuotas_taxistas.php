<?php
$tituloPagina = 'Validación de Cuotas Mensuales (Yape) - Administración';
$subtituloNavbar = 'Control Financiero de Cuotas';
$menuActivo = 'cuotas_taxistas_admin';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';

require_once dirname(dirname(__DIR__)) . '/modelos/CuotaMensual.php';

$filtroEstado = $_GET['estado'] ?? null;
$cuotaMod = new CuotaMensual();
$cuotas = $cuotaMod->listarTodas($filtroEstado);

$totalCuotas = count($cuotas);
$pendientes = 0;
$aprobadas = 0;
$montoTotalAprobado = 0;

foreach ($cuotas as $c) {
    if ($c['estado'] === 'Pendiente') $pendientes++;
    if ($c['estado'] === 'Aprobado') {
        $aprobadas++;
        $montoTotalAprobado += (float)$c['monto'];
    }
}
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div style="display: flex; align-items: center; gap: 14px;">
        <img src="<?= asset('img/yape.svg') ?>" alt="Yape" style="height: 44px; border-radius: 8px; box-shadow: 0 2px 8px rgba(116, 34, 132, 0.25);">
        <div>
            <h1 class="page-title" style="margin-bottom: 2px;">Validación de Cuotas Mensuales (Yape)</h1>
            <p class="page-subtitle" style="margin: 0;">Revisión de capturas y constancias de pago por Yape enviadas por los taxistas</p>
        </div>
    </div>
    <div>
        <a href="<?= vistaUrl('vehiculos/listar_vehiculos.php') ?>" class="btn btn-outline">&larr; Volver a Flota</a>
    </div>
</div>

<!-- TARJETAS DE MÉTRICAS -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 25px;">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Comprobantes</h3>
            <div class="stat-number" style="font-size: 1.35rem; color: #0f172a; font-weight: 700;"><?= $totalCuotas ?></div>
            <div class="stat-desc">En este filtro</div>
        </div>
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Pendientes de Validar</h3>
            <div class="stat-number" style="font-size: 1.35rem; color: #0f172a; font-weight: 700;"><?= $pendientes ?></div>
            <div class="stat-desc">Requieren verificar en Yape</div>
        </div>
        <div class="stat-icon warning">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Cuotas Aprobadas</h3>
            <div class="stat-number" style="font-size: 1.35rem; color: #0f172a; font-weight: 700;"><?= $aprobadas ?></div>
            <div class="stat-desc">Abonos conformes</div>
        </div>
        <div class="stat-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Recaudado (S/)</h3>
            <div class="stat-number" style="font-size: 1.35rem; color: #0f172a; font-weight: 700;">S/ <?= number_format($montoTotalAprobado, 2) ?></div>
            <div class="stat-desc">Suma de pagos aprobados</div>
        </div>
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
        </div>
    </div>
</div>

<!-- FILTROS POR ESTADO -->
<div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
    <a href="<?= vistaUrl('vehiculos/cuotas_taxistas.php') ?>" class="btn <?= empty($filtroEstado) ? 'btn-primary' : 'btn-outline' ?>" style="font-size: 0.85rem; padding: 6px 14px;">
        Todos (<?= $totalCuotas ?>)
    </a>
    <a href="<?= vistaUrl('vehiculos/cuotas_taxistas.php?estado=Pendiente') ?>" class="btn <?= $filtroEstado === 'Pendiente' ? 'btn-primary' : 'btn-outline' ?>" style="font-size: 0.85rem; padding: 6px 14px;">
        ⏳ Pendientes (<?= $pendientes ?>)
    </a>
    <a href="<?= vistaUrl('vehiculos/cuotas_taxistas.php?estado=Aprobado') ?>" class="btn <?= $filtroEstado === 'Aprobado' ? 'btn-primary' : 'btn-outline' ?>" style="font-size: 0.85rem; padding: 6px 14px;">
        ✓ Aprobados (<?= $aprobadas ?>)
    </a>
    <a href="<?= vistaUrl('vehiculos/cuotas_taxistas.php?estado=Rechazado') ?>" class="btn <?= $filtroEstado === 'Rechazado' ? 'btn-primary' : 'btn-outline' ?>" style="font-size: 0.85rem; padding: 6px 14px;">
        ✕ Rechazados
    </a>
</div>

<!-- TABLA PRINCIPAL DE CUOTAS YAPE -->
<div class="card">
    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Conductor / Remitente</th>
                    <th>Mototaxi</th>
                    <th>Periodo</th>
                    <th>Monto</th>
                    <th>Captura Yape</th>
                    <th>Estado</th>
                    <th>Fecha Envío</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($cuotas)): ?>
                    <?php foreach ($cuotas as $c): 
                        $badgeClass = 'badge-warning';
                        if ($c['estado'] === 'Aprobado') $badgeClass = 'badge-success';
                        elseif ($c['estado'] === 'Rechazado') $badgeClass = 'badge-danger';

                        $fotoUrl = CuotaMensual::obtenerSrcComprobante($c);
                        $tieneFoto = !empty($fotoUrl);
                    ?>
                    <tr>
                        <td>
                            <strong style="color: #0f172a;"><?= htmlspecialchars($c['remitente_nombre']) ?></strong>
                            <div style="font-size: 0.75rem; color: #64748b;">DNI: <?= htmlspecialchars($c['usuario_dni'] ?? '-') ?></div>
                        </td>
                        <td>
                            <?php if (!empty($c['placa'])): ?>
                                <strong><?= htmlspecialchars($c['placa']) ?></strong>
                                <div style="font-size: 0.75rem; color: #64748b;">Padrón: <?= htmlspecialchars($c['numero_padron'] ?? '-') ?></div>
                            <?php else: ?>
                                <span style="color: #64748b; font-size: 0.8rem;">Sin asignar</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-info" style="font-size: 0.8rem;"><?= htmlspecialchars($c['mes_periodo']) ?></span>
                            <?php if (!empty($c['numero_operacion'])): ?>
                                <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Ref: <?= htmlspecialchars($c['numero_operacion']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong style="font-size: 1rem; color: #0f172a;">S/ <?= number_format((float)$c['monto'], 2) ?></strong>
                        </td>
                        <td>
                            <?php if ($tieneFoto): ?>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <img src="<?= $fotoUrl ?>" alt="Captura Yape" 
                                         class="admin-yape-thumb"
                                         data-src="<?= $fotoUrl ?>"
                                         data-title="Yape: <?= htmlspecialchars($c['remitente_nombre']) ?> - S/ <?= number_format((float)$c['monto'], 2) ?>"
                                         data-meta="<?= htmlspecialchars($c['mes_periodo']) ?> | Ref: <?= htmlspecialchars($c['numero_operacion'] ?? 'S/N') ?>"
                                         style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1; cursor: pointer;"
                                         title="Clic para ampliar captura">
                                    <a href="<?= $fotoUrl ?>" target="_blank" class="btn btn-outline btn-sm" style="padding: 2px 6px; font-size: 0.75rem;">Ver</a>
                                </div>
                            <?php else: ?>
                                <span style="color: #64748b; font-size: 0.8rem;">Sin captura</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($c['estado']) ?></span>
                            <?php if (!empty($c['respuesta_admin'])): ?>
                                <div style="font-size: 0.75rem; color: #64748b; max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($c['respuesta_admin']) ?>">
                                    <?= htmlspecialchars($c['respuesta_admin']) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem;"><?= date('d/m/Y', strtotime($c['created_at'])) ?></div>
                            <div style="font-size: 0.72rem; color: #64748b;"><?= date('H:i', strtotime($c['created_at'])) ?> hrs</div>
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <button type="button" class="btn btn-sm btn-primary btn-abrir-validador" 
                                    data-id="<?= (int)$c['id_cuota'] ?>"
                                    data-nombre="<?= htmlspecialchars($c['remitente_nombre']) ?>"
                                    data-periodo="<?= htmlspecialchars($c['mes_periodo']) ?>"
                                    data-monto="<?= number_format((float)$c['monto'], 2) ?>"
                                    data-foto="<?= $fotoUrl ?>"
                                    data-estado="<?= htmlspecialchars($c['estado']) ?>"
                                    data-respuesta="<?= htmlspecialchars($c['respuesta_admin'] ?? '') ?>">
                                <?= $c['estado'] === 'Pendiente' ? 'Verificar / Validar' : 'Modificar Estado' ?>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: #64748b; padding: 40px 20px;">
                            No hay comprobantes de Yape en este filtro.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL DE VALIDACIÓN DE CUOTA YAPE -->
<div id="modalValidador" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #ffffff; border-radius: 12px; max-width: 580px; width: 100%; max-height: 90vh; overflow-y: auto; border: 1px solid #cbd5e1; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.4);">
        
        <div style="padding: 16px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 style="margin: 0; font-size: 1.15rem; color: #0f172a;">Validar Comprobante de Yape</h3>
                <p id="validadorSubtitulo" style="margin: 2px 0 0 0; color: #64748b; font-size: 0.85rem;"></p>
            </div>
            <button type="button" id="btnCerrarValidador" style="background: none; border: none; font-size: 1.5rem; color: #64748b; cursor: pointer; line-height: 1;">&times;</button>
        </div>

        <form action="<?= controladorUrl('taxista_controlador.php') ?>" method="POST" style="padding: 20px;">
            <input type="hidden" name="accion" value="validar_cuota_yape">
            <input type="hidden" name="id_cuota" id="validadorIdCuota" value="">

            <!-- VISTA PREVIA DE LA CAPTURA -->
            <div style="text-align: center; margin-bottom: 15px; background: #0f172a; padding: 12px; border-radius: 8px;">
                <img id="validadorFotoImg" src="" alt="Captura Yape" style="max-height: 240px; max-width: 100%; object-fit: contain; border-radius: 6px;">
                <div style="margin-top: 6px;">
                    <a id="validadorFotoLink" href="#" target="_blank" class="btn btn-outline btn-sm" style="color: #ffffff; border-color: #64748b; font-size: 0.75rem;">
                        🔍 Ver Captura en Tamaño Completo
                    </a>
                </div>
            </div>

            <!-- DECISIÓN DE ESTADO -->
            <div class="form-group" style="margin-bottom: 15px;">
                <label style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px; display: block; color: #334155;">Estado de Validación *</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <label style="border: 2px solid #10b981; border-radius: 8px; padding: 10px; display: flex; align-items: center; gap: 8px; cursor: pointer; background: #ecfdf5;">
                        <input type="radio" name="estado" id="rbAprobado" value="Aprobado" required>
                        <div>
                            <strong style="color: #065f46; display: block; font-size: 0.9rem;">✓ Aprobar Pago</strong>
                            <small style="color: #047857;">Saldo verificado en Yape</small>
                        </div>
                    </label>

                    <label style="border: 2px solid #ef4444; border-radius: 8px; padding: 10px; display: flex; align-items: center; gap: 8px; cursor: pointer; background: #fef2f2;">
                        <input type="radio" name="estado" id="rbRechazado" value="Rechazado" required>
                        <div>
                            <strong style="color: #991b1b; display: block; font-size: 0.9rem;">✕ Rechazar Pago</strong>
                            <small style="color: #b91c1c;">Monto o constancia inválida</small>
                        </div>
                    </label>
                </div>
            </div>

            <!-- OBSERVACIÓN / RESPUESTA DEL ADMINISTRADOR -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="validadorRespuesta" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px; display: block; color: #334155;">Observación o Mensaje al Taxista</label>
                <textarea name="respuesta_admin" id="validadorRespuesta" class="form-control" rows="2" placeholder="Ej: Pago de S/ 30.00 confirmado en cuenta Yape, gracias. / Constancia borrosa o no coincide número..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #e2e8f0; padding-top: 15px;">
                <button type="button" id="btnCerrarValidador2" class="btn btn-outline btn-sm">Cancelar</button>
                <button type="submit" class="btn btn-primary btn-sm" style="font-weight: 700; padding: 8px 18px;">Guardar Validación</button>
            </div>
        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalValidador');
    const idCuotaInput = document.getElementById('validadorIdCuota');
    const subtitulo = document.getElementById('validadorSubtitulo');
    const fotoImg = document.getElementById('validadorFotoImg');
    const fotoLink = document.getElementById('validadorFotoLink');
    const rbAprobado = document.getElementById('rbAprobado');
    const rbRechazado = document.getElementById('rbRechazado');
    const respuestaTxt = document.getElementById('validadorRespuesta');
    const btnCerrar1 = document.getElementById('btnCerrarValidador');
    const btnCerrar2 = document.getElementById('btnCerrarValidador2');

    document.querySelectorAll('.btn-abrir-validador').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            const periodo = this.getAttribute('data-periodo');
            const monto = this.getAttribute('data-monto');
            const foto = this.getAttribute('data-foto');
            const estado = this.getAttribute('data-estado');
            const respuesta = this.getAttribute('data-respuesta');

            idCuotaInput.value = id;
            subtitulo.textContent = nombre + ' • Periodo: ' + periodo + ' • Monto: S/ ' + monto;
            fotoImg.src = foto;
            fotoLink.href = foto;
            respuestaTxt.value = respuesta || '';

            if (estado === 'Aprobado') {
                rbAprobado.checked = true;
            } else if (estado === 'Rechazado') {
                rbRechazado.checked = true;
            } else {
                rbAprobado.checked = true; // Por defecto aprobado si va a validar
            }

            modal.style.display = 'flex';
        });
    });

    function cerrar() {
        modal.style.display = 'none';
    }

    if (btnCerrar1) btnCerrar1.addEventListener('click', cerrar);
    if (btnCerrar2) btnCerrar2.addEventListener('click', cerrar);
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) cerrar();
        });
    }
});
</script>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

