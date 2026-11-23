<?php
$tituloPagina = 'Subir Cuota Mensual (Yape) - Taxistas';
$subtituloNavbar = 'Gestión de Cuotas y Pagos';
$menuActivo = 'cuotas_taxista';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';

require_once dirname(dirname(__DIR__)) . '/modelos/Taxista.php';
require_once dirname(dirname(__DIR__)) . '/modelos/CuotaMensual.php';

$usuario = obtenerUsuarioActual();
$taxistaMod = new Taxista();
$cuotaMod = new CuotaMensual();

$ficha = $taxistaMod->obtenerFichaTaxista($usuario['id']);
$conductor = $ficha['conductor'] ?? null;
$vehiculo = $ficha['vehiculo'] ?? null;

// Nombre que se pone automáticamente
$nombreTaxista = trim(($usuario['nombres'] ?? $usuario['nombre'] ?? '') . ' ' . ($usuario['apellidos'] ?? ''));
if (empty($nombreTaxista) && !empty($conductor)) {
    $nombreTaxista = trim(($conductor['nombres'] ?? '') . ' ' . ($conductor['apellidos'] ?? ''));
}
if (empty($nombreTaxista)) {
    $nombreTaxista = $usuario['usuario'] ?? 'Conductor Taxista';
}
$dniTaxista = $conductor['dni'] ?? ($usuario['dni'] ?? '');

// Periodo automático actual (ej: Noviembre 2026)
$mesesEspanol = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];
$mesActualNum = (int)date('n');
$anioActual = date('Y');
$periodoActualDefecto = $mesesEspanol[$mesActualNum] . ' ' . $anioActual;

// Listar pagos y resumen
$misCuotas = $cuotaMod->listarPorUsuario($usuario['id']);
$resumen = $cuotaMod->obtenerResumenTaxista($usuario['id']);
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h1 class="page-title">Subir Cuota Mensual</h1>
        <p class="page-subtitle">Abono exclusivo mediante Yape: sube la captura de pantalla de tu pago</p>
    </div>
    <div>
        <a href="<?= vistaUrl('taxista/dashboard.php') ?>" class="btn btn-outline">&larr; Volver al Panel</a>
    </div>
</div>

<!-- RESUMEN DE PAGOS -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 25px;">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Cuotas Enviadas</h3>
            <div class="stat-number" style="font-size: 1.35rem; color: #0f172a; font-weight: 700;"><?= (int)($resumen['total'] ?? 0) ?></div>
            <div class="stat-desc">Comprobantes registrados</div>
        </div>
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Aprobadas / Validadas</h3>
            <div class="stat-number" style="font-size: 1.35rem; color: #0f172a; font-weight: 700;"><?= (int)($resumen['aprobados'] ?? 0) ?></div>
            <div class="stat-desc">Abonos conformes</div>
        </div>
        <div class="stat-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>En Verificación</h3>
            <div class="stat-number" style="font-size: 1.35rem; color: #0f172a; font-weight: 700;"><?= (int)($resumen['pendientes'] ?? 0) ?></div>
            <div class="stat-desc">Pendientes de revisión</div>
        </div>
        <div class="stat-icon warning">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Abonado</h3>
            <div class="stat-number" style="font-size: 1.35rem; color: #0f172a; font-weight: 700;">S/ <?= number_format((float)($resumen['monto_total_aprobado'] ?? 0), 2) ?></div>
            <div class="stat-desc">Suma de pagos aprobados</div>
        </div>
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
        </div>
    </div>
</div>

<div class="row" style="margin-bottom: 30px;">
    
    <!-- COLUMNA IZQUIERDA: FORMULARIO SIMPLE (SOLO NOMBRE AUTOMÁTICO Y SUBIR IMAGEN) -->
    <div class="col-12 col-lg-5" style="margin-bottom: 25px;">
        
        <!-- DATOS DEL YAPE OFICIAL -->
        <div class="card" style="margin-bottom: 18px; border-left: 4px solid #742284; background: #faf5ff;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="<?= asset('img/yape.svg') ?>" alt="Yape Oficial" style="height: 36px; border-radius: 6px; box-shadow: 0 2px 6px rgba(116, 34, 132, 0.25); object-fit: contain;">
                    <div>
                        <strong style="color: #581c87; font-size: 1.05rem; display: block; line-height: 1.2;">Número Yape Oficial</strong>
                        <span style="font-size: 0.78rem; color: #7e22ce;">Canal de pago autorizado</span>
                    </div>
                </div>
                <span class="badge" style="background: #742284; color: #ffffff; font-size: 0.75rem; padding: 4px 10px; border-radius: 20px;">Solo Yape</span>
            </div>
            <div style="font-size: 0.95rem; color: #4c1d95; line-height: 1.5; background: #ffffff; padding: 10px 14px; border-radius: 8px; border: 1px dashed #d8b4fe;">
                Número: <strong style="font-size: 1.25rem; color: #742284; font-family: monospace; letter-spacing: 1px;">987 654 321</strong><br>
                Titular: <strong>KEBA MOTOS E.I.R.L.</strong> • Cuota: <strong style="color: #15803d;">S/ 30.00</strong>
            </div>
        </div>

        <!-- FORMULARIO DIRECTO -->
        <div class="card">
            <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 18px;">
                <h2 class="card-title" style="margin: 0; display: flex; align-items: center; gap: 8px; font-size: 1.1rem;">
                    <svg style="width: 20px; height: 20px; color: #2563eb;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    Subir Pago de Cuota
                </h2>
                <p class="card-subtitle" style="margin: 4px 0 0 0; color: #64748b; font-size: 0.85rem;">
                    Periodo: <strong><?= htmlspecialchars($periodoActualDefecto) ?></strong>
                </p>
            </div>

            <form action="<?= controladorUrl('taxista_controlador.php') ?>" method="POST" enctype="multipart/form-data" id="formCuotaYape">
                <input type="hidden" name="accion" value="subir_cuota_yape">
                <input type="hidden" name="id_vehiculo" value="<?= (int)($vehiculo['id_vehiculo'] ?? 0) ?>">
                <input type="hidden" name="mes_periodo" value="<?= htmlspecialchars($periodoActualDefecto) ?>">
                <input type="hidden" name="monto" value="30.00">

                <!-- 1. NOMBRE (AUTOMÁTICO) -->
                <div class="form-group" style="margin-bottom: 18px;">
                    <label style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px; display: flex; justify-content: space-between; align-items: center; color: #334155;">
                        <span>Nombre del Conductor</span>
                        <?php if (!empty($dniTaxista)): ?>
                            <span style="font-size: 0.78rem; color: #64748b; font-weight: normal;">DNI: <?= htmlspecialchars($dniTaxista) ?></span>
                        <?php endif; ?>
                    </label>
                    <input type="text" name="remitente_nombre" class="form-control" value="<?= htmlspecialchars($nombreTaxista) ?>" readonly style="background: #f8fafc; color: #0f172a; border: 1.5px solid #cbd5e1; font-weight: 700; font-size: 0.95rem;">
                    <small style="color: #64748b; font-size: 0.78rem; margin-top: 4px; display: block;">Se asigna automáticamente a tu cuenta y unidad.</small>
                </div>

                <!-- 2. SUBIR IMAGEN DEL PAGO -->
                <div class="form-group" style="margin-bottom: 22px;">
                    <label style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px; display: block; color: #334155;">
                        Subir Imagen o Captura del Pago (Yape) *
                    </label>

                    <div id="dropzoneYape" style="border: 2px dashed #cbd5e1; border-radius: 10px; padding: 25px 16px; text-align: center; background: #f8fafc; cursor: pointer; transition: all 0.25s ease;">
                        <input type="file" name="comprobante_yape" id="archivoYape" accept="image/*" required style="display: none;">

                        <!-- Prompt inicial -->
                        <div id="promptYape">
                            <div style="width: 50px; height: 50px; margin: 0 auto 10px; border-radius: 50%; background: #f3e8ff; display: flex; align-items: center; justify-content: center;">
                                <svg style="width: 26px; height: 26px; color: #7c3aed;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                    <polyline points="21 15 16 10 5 21"></polyline>
                                </svg>
                            </div>
                            <div style="font-weight: 700; color: #0f172a; font-size: 0.95rem;">Toca aquí para seleccionar la captura del pago</div>
                            <div style="color: #64748b; font-size: 0.8rem; margin-top: 4px;">
                                Acepta foto o captura de Yape (JPG, PNG o WEBP)
                            </div>
                            <div style="margin-top: 10px;">
                                <span class="btn btn-sm btn-outline" style="pointer-events: none; font-size: 0.8rem;">
                                    📁 Seleccionar Captura
                                </span>
                            </div>
                        </div>

                        <!-- Previsualización en vivo -->
                        <div id="previewContainerYape" style="display: none; flex-direction: column; align-items: center; gap: 10px;">
                            <img id="imgYapePreview" src="" alt="Captura Yape" style="max-height: 220px; max-width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; object-fit: contain; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                            <div id="fileInfoYape" style="color: #0f172a; font-size: 0.85rem; font-weight: 600;"></div>
                            <button type="button" id="btnCambiarYape" class="btn btn-sm btn-outline" style="margin-top: 4px; font-size: 0.8rem;">
                                🔄 Cambiar imagen / captura
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 13px; font-weight: 700; font-size: 1rem; display: flex; align-items: center; justify-content: center; gap: 8px; border-radius: 8px;">
                    <svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    Enviar Comprobante de Pago
                </button>
            </form>
        </div>

    </div>

    <!-- COLUMNA DERECHA: HISTORIAL DE COMPROBANTES -->
    <div class="col-12 col-lg-7">
        <div class="card">
            <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h2 class="card-title" style="margin: 0; font-size: 1.1rem;">Historial de Pagos Enviados</h2>
                    <p class="card-subtitle" style="margin: 4px 0 0 0; color: #64748b; font-size: 0.85rem;">
                        Registro y estado de tus cuotas mensuales
                    </p>
                </div>
                <div>
                    <span class="badge badge-info" style="font-size: 0.8rem; padding: 5px 10px;">
                        <?= count($misCuotas) ?> registro(s)
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Periodo / Mes</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                            <th>Captura</th>
                            <th>Estado</th>
                            <th>Observación Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($misCuotas)): ?>
                            <?php foreach ($misCuotas as $c): 
                                $badgeClass = 'badge-warning';
                                $estadoTxt = 'Pendiente';
                                if ($c['estado'] === 'Aprobado') {
                                    $badgeClass = 'badge-success';
                                    $estadoTxt = 'Aprobado';
                                } elseif ($c['estado'] === 'Rechazado') {
                                    $badgeClass = 'badge-danger';
                                    $estadoTxt = 'Rechazado';
                                }

                                $fotoUrl = CuotaMensual::obtenerSrcComprobante($c);
                                $tieneFoto = !empty($fotoUrl);
                            ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: #0f172a;"><?= htmlspecialchars($c['mes_periodo']) ?></div>
                                </td>
                                <td>
                                    <strong style="color: #0f172a; font-size: 0.95rem;">S/ <?= number_format((float)$c['monto'], 2) ?></strong>
                                </td>
                                <td>
                                    <div style="font-size: 0.85rem; color: #334155;"><?= date('d/m/Y', strtotime($c['created_at'])) ?></div>
                                    <div style="font-size: 0.72rem; color: #64748b;"><?= date('H:i', strtotime($c['created_at'])) ?> hrs</div>
                                </td>
                                <td>
                                    <?php if ($tieneFoto): ?>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <img src="<?= $fotoUrl ?>" alt="Captura Yape" 
                                                 class="yape-thumb"
                                                 data-src="<?= $fotoUrl ?>"
                                                 data-title="Yape: <?= htmlspecialchars($c['remitente_nombre']) ?>"
                                                 data-meta="Periodo: <?= htmlspecialchars($c['mes_periodo']) ?> | S/ <?= number_format((float)$c['monto'], 2) ?>"
                                                 style="width: 44px; height: 44px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1; cursor: pointer; transition: transform 0.2s;"
                                                 onmouseover="this.style.transform='scale(1.1)';" 
                                                 onmouseout="this.style.transform='scale(1)';"
                                                 title="Clic para ampliar captura">
                                            <a href="<?= $fotoUrl ?>" target="_blank" class="btn btn-outline btn-sm" style="padding: 2px 6px; font-size: 0.75rem;">
                                                Ver
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: #64748b; font-size: 0.8rem;">Sin foto</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $badgeClass ?>" style="white-space: nowrap; font-size: 0.78rem;">
                                        <?= htmlspecialchars($estadoTxt) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($c['respuesta_admin'])): ?>
                                        <div style="font-size: 0.82rem; color: #334155;">
                                            <?= htmlspecialchars($c['respuesta_admin']) ?>
                                        </div>
                                        <div style="font-size: 0.72rem; color: #64748b;">
                                            <?= htmlspecialchars($c['admin_nombre'] ?? 'Administración') ?>
                                        </div>
                                    <?php elseif ($c['estado'] === 'Pendiente'): ?>
                                        <span style="color: #64748b; font-size: 0.8rem; font-style: italic;">Esperando validación</span>
                                    <?php else: ?>
                                        <span style="color: #64748b; font-size: 0.8rem;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #64748b; padding: 40px 20px;">
                                    <svg style="width: 42px; height: 42px; margin-bottom: 8px; color: #94a3b8;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                    <div>Aún no has enviado comprobantes de cuotas mensuales.</div>
                                    <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 4px;">Usa el formulario de la izquierda para subir la foto o captura de tu Yape.</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px; padding: 14px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; font-size: 0.85rem; color: #475569;">
                <div style="display: flex; gap: 10px; align-items: flex-start;">
                    <svg style="width: 20px; height: 20px; color: #2563eb; flex-shrink: 0; margin-top: 2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    <div>
                        <strong style="color: #0f172a; display: block; margin-bottom: 2px;">Validación de Yape:</strong>
                        Una vez verificado el depósito en la cuenta oficial, la administración cambiará tu estado a <strong>Aprobado</strong>.
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- MODAL VISOR DE CAPTURA YAPE -->
<div id="modalYapeVisor" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #ffffff; border-radius: 12px; max-width: 650px; width: 100%; max-height: 90vh; overflow: hidden; display: flex; flex-direction: column; border: 1px solid #cbd5e1; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5);">
        <div style="padding: 15px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 id="modalYapeTitulo" style="margin: 0; font-size: 1.1rem; color: #0f172a;">Captura Yape</h3>
                <div id="modalYapeMeta" style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">Comprobante</div>
            </div>
            <button type="button" id="btnCerrarYapeModal" style="background: none; border: none; color: #64748b; font-size: 1.5rem; cursor: pointer; line-height: 1; padding: 5px;">&times;</button>
        </div>
        <div style="padding: 20px; overflow-y: auto; text-align: center; background: #0f172a;">
            <img id="modalYapeImg" src="" alt="Captura ampliada" style="max-width: 100%; max-height: 65vh; object-fit: contain; border-radius: 8px;">
        </div>
        <div style="padding: 12px 20px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px; background: #f8fafc;">
            <a id="modalYapeLink" href="#" target="_blank" class="btn btn-primary btn-sm">Abrir en Pestaña Completa</a>
            <button type="button" id="btnCerrarYapeModal2" class="btn btn-outline btn-sm">Cerrar</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropzoneYape = document.getElementById('dropzoneYape');
    const archivoInput = document.getElementById('archivoYape');
    const promptYape = document.getElementById('promptYape');
    const previewContainer = document.getElementById('previewContainerYape');
    const imgPreview = document.getElementById('imgYapePreview');
    const fileInfo = document.getElementById('fileInfoYape');
    const btnCambiar = document.getElementById('btnCambiarYape');

    dropzoneYape.addEventListener('click', function(e) {
        if (e.target !== btnCambiar) {
            archivoInput.click();
        }
    });

    if (btnCambiar) {
        btnCambiar.addEventListener('click', function(e) {
            e.stopPropagation();
            archivoInput.click();
        });
    }

    archivoInput.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
            const file = this.files[0];
            const sizeKB = (file.size / 1024).toFixed(1);
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            const sizeText = file.size > 1024 * 1024 ? sizeMB + ' MB' : sizeKB + ' KB';

            if (!file.type.startsWith('image/')) {
                alert('Solo se aceptan imágenes o capturas de Yape (JPG, PNG, WEBP).');
                archivoInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                imgPreview.src = e.target.result;
                fileInfo.textContent = file.name + ' (' + sizeText + ')';
                promptYape.style.display = 'none';
                previewContainer.style.display = 'flex';
            };
            reader.readAsDataURL(file);
        }
    });

    // Modal Visor
    const modal = document.getElementById('modalYapeVisor');
    const modalImg = document.getElementById('modalYapeImg');
    const modalTitulo = document.getElementById('modalYapeTitulo');
    const modalMeta = document.getElementById('modalYapeMeta');
    const modalLink = document.getElementById('modalYapeLink');
    const btnCerrar1 = document.getElementById('btnCerrarYapeModal');
    const btnCerrar2 = document.getElementById('btnCerrarYapeModal2');

    document.querySelectorAll('.yape-thumb').forEach(thumb => {
        thumb.addEventListener('click', function(e) {
            e.preventDefault();
            const src = this.getAttribute('data-src');
            const title = this.getAttribute('data-title') || 'Captura Yape';
            const meta = this.getAttribute('data-meta') || '';

            modalImg.src = src;
            modalTitulo.textContent = title;
            modalMeta.textContent = meta;
            modalLink.href = src;
            modal.style.display = 'flex';
        });
    });

    function cerrarModal() {
        modal.style.display = 'none';
        modalImg.src = '';
    }

    if (btnCerrar1) btnCerrar1.addEventListener('click', cerrarModal);
    if (btnCerrar2) btnCerrar2.addEventListener('click', cerrarModal);
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) cerrarModal();
        });
    }
});
</script>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>
