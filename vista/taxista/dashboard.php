<?php
$tituloPagina = 'Panel del Taxista';
$subtituloNavbar = 'Mi Unidad y Turno';
$menuActivo = 'dashboard_taxista';

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
$documentos = $ficha['documentos'] ?? [];

$asistenciaHoy = $asistenciaMod->obtenerAsistenciaHoy($usuario['id']);
$ultimasAsistencias = $asistenciaMod->listarPorUsuario($usuario['id'], 5);

$nombreCompleto = trim(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellidos'] ?? ''));
if (empty($nombreCompleto)) {
    $nombreCompleto = $usuario['usuario'] ?? 'Taxista';
}
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h1 class="page-title">¡Bienvenido(a), <?= htmlspecialchars($nombreCompleto) ?>!</h1>
        <p class="page-subtitle">Panel de operaciones del taxista • DNI: <strong><?= htmlspecialchars($usuario['dni'] ?? '-') ?></strong></p>
    </div>
    <div>
        <?php if (!$asistenciaHoy || $asistenciaHoy['estado'] !== 'En Turno'): ?>
            <a href="<?= vistaUrl('taxista/asistencia.php') ?>" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 600; padding: 10px 20px;">
                <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                Marcar Entrada / Iniciar Turno
            </a>
        <?php else: ?>
            <a href="<?= vistaUrl('taxista/asistencia.php') ?>" class="btn btn-danger" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 600; padding: 10px 20px;">
                <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="9" x2="15" y2="15"></line><line x1="15" y1="9" x2="9" y2="15"></line></svg>
                Marcar Salida / Finalizar Turno
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- TARJETA DESTACADA: ESTADO DEL TURNO HOY -->
<div class="card" style="margin-bottom: 25px; border-left: 5px solid <?= ($asistenciaHoy && $asistenciaHoy['estado'] === 'En Turno') ? '#10b981' : (($asistenciaHoy && $asistenciaHoy['estado'] === 'Finalizado') ? '#3b82f6' : '#f59e0b') ?>;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <div style="width: 55px; height: 55px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: <?= ($asistenciaHoy && $asistenciaHoy['estado'] === 'En Turno') ? 'rgba(16, 185, 129, 0.15)' : 'rgba(245, 158, 11, 0.15)' ?>; color: <?= ($asistenciaHoy && $asistenciaHoy['estado'] === 'En Turno') ? '#10b981' : '#f59e0b' ?>;">
                <svg style="width: 28px; height: 28px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 700; margin: 0 0 4px 0;">
                    <?php if ($asistenciaHoy && $asistenciaHoy['estado'] === 'En Turno'): ?>
                        <span style="color: #10b981;">● TURNO ACTIVO</span> (Iniciado a las <?= htmlspecialchars(substr($asistenciaHoy['hora_ingreso'], 0, 5)) ?> hrs)
                    <?php elseif ($asistenciaHoy && $asistenciaHoy['estado'] === 'Finalizado'): ?>
                        <span style="color: #3b82f6;">✓ TURNO FINALIZADO</span> (De <?= htmlspecialchars(substr($asistenciaHoy['hora_ingreso'], 0, 5)) ?> a <?= htmlspecialchars(substr($asistenciaHoy['hora_salida'], 0, 5)) ?> hrs)
                    <?php else: ?>
                        <span style="color: #f59e0b;">○ SIN ASISTENCIA REGISTRADA HOY</span>
                    <?php endif; ?>
                </h3>
                <p style="color: #64748b; margin: 0; font-size: 0.9rem;">
                    Fecha: <strong><?= date('d/m/Y') ?></strong> | 
                    Turno: <strong><?= htmlspecialchars($asistenciaHoy['turno'] ?? 'No iniciado') ?></strong> |
                    Mototaxi asignada: <strong><?= htmlspecialchars($vehiculo ? $vehiculo['placa'] . ' (' . $vehiculo['marca'] . ')' : 'Sin unidad fija') ?></strong>
                </p>
            </div>
        </div>

        <div>
            <a href="<?= vistaUrl('taxista/asistencia.php') ?>" class="btn btn-outline btn-sm">Ver Historial de Asistencias &rarr;</a>
        </div>
    </div>
</div>

<!-- TARJETAS DE ACCESO RÁPIDO -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); margin-bottom: 25px;">
    
    <!-- 1. ASISTENCIA -->
    <a href="<?= vistaUrl('taxista/asistencia.php') ?>" class="stat-card" style="text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
        <div class="stat-info">
            <h3>Registro de Turnos</h3>
            <div class="stat-number" style="font-size: 1.4rem;">Asistencia</div>
            <div class="stat-desc" style="color: #2563eb;">Marcar entrada / salida diaria &rarr;</div>
        </div>
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
        </div>
    </a>

    <!-- 2. DOCUMENTOS -->
    <a href="<?= vistaUrl('taxista/documentos.php') ?>" class="stat-card" style="text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
        <div class="stat-info">
            <h3>SOAT / TUC / Brevete</h3>
            <div class="stat-number" style="font-size: 1.4rem;"><?= count($documentos) ?> Registrados</div>
            <div class="stat-desc" style="color: #2563eb;">Subir y consultar documentos &rarr;</div>
        </div>
        <div class="stat-icon warning">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
        </div>
    </a>

    <!-- 3. FOTOS DE PERSONA Y MOTO -->
    <a href="<?= vistaUrl('taxista/fotos.php') ?>" class="stat-card" style="text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
        <div class="stat-info">
            <h3>Galería Digital</h3>
            <div class="stat-number" style="font-size: 1.4rem;">Foto & Moto</div>
            <div class="stat-desc" style="color: #2563eb;">Actualizar fotos y brevete &rarr;</div>
        </div>
        <div class="stat-icon dark">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="4"></circle><path d="M5 7h1a2 2 0 0 0 2-2 1 1 0 0 1 1-1h6a1 1 0 0 1 1 1 2 2 0 0 0 2 2h1a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2z"></path></svg>
        </div>
    </a>

    <!-- 4. MOTO DE LA EMPRESA: DAÑOS, LIMPIEZA Y REVISIÓN -->
    <a href="<?= vistaUrl('taxista/reportar_estado.php') ?>" class="stat-card" style="text-decoration: none; color: inherit; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
        <div class="stat-info">
            <h3>Unidad de la Empresa</h3>
            <div class="stat-number" style="font-size: 1.35rem; color: #0f172a;">Daños & Revisión</div>
            <div class="stat-desc" style="color: #2563eb;">Reportar avería / Enviar mensaje &rarr;</div>
        </div>
        <div class="stat-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
        </div>
    </a>

    <!-- 5. CUOTA MENSUAL (YAPE) -->
    <a href="<?= vistaUrl('taxista/cuotas.php') ?>" class="stat-card" style="text-decoration: none; color: inherit; transition: transform 0.2s; border-color: #d8b4fe;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
        <div class="stat-info">
            <h3>Abono Mensual</h3>
            <div class="stat-number" style="font-size: 1.35rem; color: #6b21a8;">Cuota (Yape)</div>
            <div class="stat-desc" style="color: #7c3aed;">Subir captura de pago &rarr;</div>
        </div>
        <div class="stat-icon" style="background: transparent; padding: 0; display: flex; align-items: center; justify-content: center;">
            <img src="<?= asset('img/yape_app.png') ?>" alt="Yape" style="width: 44px; height: 44px; border-radius: 11px; box-shadow: 0 3px 8px rgba(116, 34, 132, 0.25);">
        </div>
    </a>

</div>

<!-- SECCIÓN DUAL: FICHA DEL TAXISTA Y DE LA MOTO -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 25px;">

    <!-- FOTO & DATOS DEL CONDUCTOR (PERSONA) -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h2 class="card-title" style="margin: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                <svg style="width: 18px; height: 18px; color: #2563eb;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                Foto y Datos del Conductor
            </h2>
            <a href="<?= vistaUrl('taxista/fotos.php') ?>" class="btn btn-outline btn-sm">Cambiar Foto</a>
        </div>

        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 15px;">
            <div style="width: 100px; height: 100px; border-radius: 12px; overflow: hidden; background: #1e293b; display: flex; align-items: center; justify-content: center; border: 2px solid #334155; flex-shrink: 0;">
                <?php if (!empty($conductor['foto']) && file_exists(ROOT_PATH . 'subidas/fotos_conductores/' . $conductor['foto'])): ?>
                    <img src="<?= subidaUrl('fotos_conductores/' . $conductor['foto']) ?>" alt="Foto Conductor" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <span style="font-size: 2.2rem; color: #94a3b8; font-weight: 700;"><?= strtoupper(substr($nombreCompleto, 0, 1)) ?></span>
                <?php endif; ?>
            </div>
            <div style="line-height: 1.5;">
                <div style="font-weight: 700; font-size: 1.05rem;"><?= htmlspecialchars($nombreCompleto) ?></div>
                <div style="color: #64748b; font-size: 0.85rem;">DNI: <strong><?= htmlspecialchars($usuario['dni']) ?></strong></div>
                <div style="color: #64748b; font-size: 0.85rem;">Licencia: <strong><?= htmlspecialchars($conductor['numero_licencia'] ?? 'No registrada') ?></strong></div>
                <div style="color: #64748b; font-size: 0.85rem;">Teléfono: <strong><?= htmlspecialchars($usuario['telefono'] ?? ($conductor['telefono'] ?? '-')) ?></strong></div>
            </div>
        </div>
    </div>

    <!-- FOTO & DATOS DE LA MOTO (VEHÍCULO) -->
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h2 class="card-title" style="margin: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                <svg style="width: 18px; height: 18px; color: #2563eb;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="17" r="3"></circle><circle cx="19" cy="17" r="3"></circle><path d="M5 17h14"></path><path d="M9 17l2-7h4l2 7"></path></svg>
                Foto y Datos de la Mototaxi
            </h2>
            <a href="<?= vistaUrl('taxista/fotos.php') ?>" class="btn btn-outline btn-sm">Cambiar Foto</a>
        </div>

        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 15px;">
            <div style="width: 100px; height: 100px; border-radius: 12px; overflow: hidden; background: #1e293b; display: flex; align-items: center; justify-content: center; border: 2px solid #334155; flex-shrink: 0;">
                <?php if (!empty($vehiculo['foto']) && file_exists(ROOT_PATH . 'subidas/fotos_vehiculos/' . $vehiculo['foto'])): ?>
                    <img src="<?= subidaUrl('fotos_vehiculos/' . $vehiculo['foto']) ?>" alt="Foto Mototaxi" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <svg style="width: 45px; height: 45px; color: #64748b;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="17" r="3"></circle><circle cx="19" cy="17" r="3"></circle><path d="M5 17h14"></path><path d="M9 17l2-7h4l2 7"></path></svg>
                <?php endif; ?>
            </div>
            <div style="line-height: 1.5;">
                <?php if ($vehiculo): ?>
                    <div style="font-weight: 700; font-size: 1.05rem; color: #2563eb;">Placa: <?= htmlspecialchars($vehiculo['placa']) ?></div>
                    <div style="color: #64748b; font-size: 0.85rem;">Padrón: <strong><?= htmlspecialchars($vehiculo['numero_padron'] ?? '-') ?></strong></div>
                    <div style="color: #64748b; font-size: 0.85rem;">Marca/Modelo: <strong><?= htmlspecialchars($vehiculo['marca'] . ' ' . $vehiculo['modelo']) ?></strong></div>
                    <div style="color: #64748b; font-size: 0.85rem;">Asociación: <strong><?= htmlspecialchars($vehiculo['nombre_asociacion'] ?? 'Independiente') ?></strong></div>
                    <div style="margin-top: 6px;">
                        <a href="<?= vistaUrl('taxista/reportar_estado.php') ?>" class="btn btn-outline btn-sm" style="font-size: 0.75rem; padding: 2px 8px;">Reportar Daños / Revisión &rarr;</a>
                    </div>
                <?php else: ?>
                    <div style="color: #eab308; font-weight: 600;">Sin mototaxi asignada</div>
                    <p style="color: #64748b; font-size: 0.85rem; margin: 4px 0 0 0;">Comunícate con el Administrador para vincular tu unidad.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<!-- TABLA DE DOCUMENTOS DEL VEHÍCULO -->
<div class="card" style="margin-bottom: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 10px;">
        <div>
            <h2 class="card-title" style="margin: 0;">Documentos de la Unidad (SOAT, TUC, Rev. Técnica)</h2>
            <p class="card-subtitle" style="margin: 3px 0 0 0; color: #64748b; font-size: 0.85rem;">Mantén tus pólizas y autorizaciones al día para evitar multas</p>
        </div>
        <a href="<?= vistaUrl('taxista/documentos.php') ?>" class="btn btn-primary btn-sm">+ Subir Documento</a>
    </div>

    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Tipo Documento</th>
                    <th>N° Documento</th>
                    <th>Entidad Emisora</th>
                    <th>Vencimiento</th>
                    <th>Estado</th>
                    <th>Archivo</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($documentos)): ?>
                    <?php foreach ($documentos as $doc): 
                        $badgeClass = 'badge-success';
                        if ($doc['estado'] === 'Vencido' || $doc['dias_para_vencer'] < 0) {
                            $badgeClass = 'badge-danger';
                        } elseif ($doc['estado'] === 'Por Vencer' || $doc['dias_para_vencer'] <= 30) {
                            $badgeClass = 'badge-warning';
                        }
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($doc['tipo_documento']) ?></strong></td>
                        <td><?= htmlspecialchars($doc['numero_documento']) ?></td>
                        <td><?= htmlspecialchars($doc['entidad_emisora'] ?? '-') ?></td>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($doc['fecha_vencimiento']))) ?></td>
                        <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($doc['estado']) ?></span></td>
                        <td>
                            <?php if (!empty($doc['archivo_digital']) && file_exists(ROOT_PATH . 'subidas/documentos/' . $doc['archivo_digital'])): ?>
                                <a href="<?= subidaUrl('documentos/' . $doc['archivo_digital']) ?>" target="_blank" class="btn btn-outline btn-sm" style="padding: 3px 8px; font-size: 0.8rem;">Ver Archivo</a>
                            <?php else: ?>
                                <span style="color: #94a3b8; font-size: 0.85rem;">Sin archivo</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #94a3b8; padding: 25px;">
                            No hay documentos registrados para esta mototaxi.
                            <br><a href="<?= vistaUrl('taxista/documentos.php') ?>" style="color: #2563eb; font-weight: 600;">Haz clic aquí para subir tu SOAT o TUC</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>
