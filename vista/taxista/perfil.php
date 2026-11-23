<?php
$tituloPagina = 'Perfil de Datos del Taxista';
$subtituloNavbar = 'Mi Perfil y Ficha Operativa';
$menuActivo = 'perfil';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';

require_once dirname(dirname(__DIR__)) . '/modelos/Taxista.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Asistencia.php';
require_once dirname(dirname(__DIR__)) . '/modelos/ReporteVehiculo.php';

$usuario = obtenerUsuarioActual();
$taxistaMod = new Taxista();
$asistenciaMod = new Asistencia();
$repMod = new ReporteVehiculo();

$ficha = $taxistaMod->obtenerFichaTaxista($usuario['id']);
$conductor = $ficha['conductor'] ?? null;
$vehiculo = $ficha['vehiculo'] ?? null;
$documentos = $ficha['documentos'] ?? [];

$asistenciaHoy = $asistenciaMod->obtenerAsistenciaHoy($usuario['id']);
$misReportes = $repMod->listarPorUsuario($usuario['id']);

$nombreCompleto = trim(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellidos'] ?? ''));
if (empty($nombreCompleto)) {
    $nombreCompleto = $usuario['usuario'] ?? 'Taxista';
}
?>

<!-- ENCABEZADO DE BIENVENIDA AL PERFIL -->
<div class="card" style="margin-bottom: 25px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border: 1px solid #334155; padding: 25px;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 20px;">
        
        <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
            <!-- FOTO DEL CONDUCTOR -->
            <div style="width: 110px; height: 110px; border-radius: 50%; overflow: hidden; background: #1e293b; border: 4px solid #2563eb; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.5); flex-shrink: 0;">
                <?php if (!empty($conductor['foto']) && file_exists(ROOT_PATH . 'subidas/fotos_conductores/' . $conductor['foto'])): ?>
                    <img src="<?= subidaUrl('fotos_conductores/' . $conductor['foto']) ?>" alt="Foto Conductor" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <span style="font-size: 2.8rem; color: #94a3b8; font-weight: 700;"><?= strtoupper(substr($nombreCompleto, 0, 1)) ?></span>
                <?php endif; ?>
            </div>

            <!-- DATOS PRINCIPALES -->
            <div>
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 4px;">
                    <h1 style="font-size: 1.5rem; font-weight: 800; color: #f8fafc; margin: 0;"><?= htmlspecialchars($nombreCompleto) ?></h1>
                    <span class="badge badge-success" style="font-size: 0.8rem; padding: 4px 10px;">Habilitado</span>
                    <span class="badge badge-info" style="font-size: 0.8rem; padding: 4px 10px;">Taxista Municipal</span>
                </div>
                <p style="margin: 0 0 8px 0; color: #94a3b8; font-size: 0.9rem;">
                    DNI: <strong style="color: #cbd5e1;"><?= htmlspecialchars($usuario['dni'] ?? '-') ?></strong> • 
                    Licencia: <strong style="color: #38bdf8;"><?= htmlspecialchars($conductor['numero_licencia'] ?? 'No registrada') ?></strong> (Cat. <?= htmlspecialchars($conductor['categoria_licencia'] ?? 'B-IIc') ?>)
                </p>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="<?= vistaUrl('taxista/fotos.php') ?>" class="btn btn-outline btn-sm" style="font-size: 0.8rem; padding: 4px 10px;">Cambiar Mi Foto</a>
                    <a href="<?= vistaUrl('taxista/asistencia.php') ?>" class="btn btn-primary btn-sm" style="font-size: 0.8rem; padding: 4px 10px;">Marcar Turno / Asistencia</a>
                </div>
            </div>
        </div>

        <!-- ESTADO RÁPIDO DE HOY -->
        <div style="background: rgba(0,0,0,0.3); padding: 15px 20px; border-radius: 10px; border: 1px solid #334155; min-width: 240px;">
            <div style="color: #94a3b8; font-size: 0.8rem; text-transform: uppercase; font-weight: 600;">Estado de Turno Hoy</div>
            <div style="font-size: 1.15rem; font-weight: 700; margin: 4px 0;">
                <?php if ($asistenciaHoy && $asistenciaHoy['estado'] === 'En Turno'): ?>
                    <span style="color: #10b981;">● En Turno Activo</span>
                <?php elseif ($asistenciaHoy && $asistenciaHoy['estado'] === 'Finalizado'): ?>
                    <span style="color: #3b82f6;">✓ Turno Finalizado</span>
                <?php else: ?>
                    <span style="color: #f59e0b;">○ Sin Ingreso Hoy</span>
                <?php endif; ?>
            </div>
            <div style="color: #64748b; font-size: 0.85rem;">
                Fecha: <?= date('d/m/Y') ?>
            </div>
        </div>

    </div>
</div>

<div class="row">

    <!-- ======================================================== -->
    <!-- COLUMNA IZQUIERDA: DATOS PERSONALES & LICENCIA           -->
    <!-- ======================================================== -->
    <div class="col-12 col-lg-6" style="margin-bottom: 25px;">
        
        <!-- FICHA DEL CONDUCTOR Y LICENCIA -->
        <div class="card" style="margin-bottom: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 15px;">
                <h2 class="card-title" style="margin: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 18px; height: 18px; color: #2563eb;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Datos Personales y de Conductor
                </h2>
                <a href="<?= vistaUrl('taxista/fotos.php') ?>" class="btn btn-outline btn-sm">Actualizar Brevete</a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; font-size: 0.9rem;">
                <div>
                    <span style="color: #64748b; font-size: 0.8rem; display: block;">Nombres y Apellidos</span>
                    <strong style="color: #0f172a;"><?= htmlspecialchars($nombreCompleto) ?></strong>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 0.8rem; display: block;">Documento de Identidad (DNI)</span>
                    <strong style="color: #0f172a;"><?= htmlspecialchars($usuario['dni'] ?? '-') ?></strong>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 0.8rem; display: block;">N° Licencia de Conducir</span>
                    <strong style="color: #0f172a;"><?= htmlspecialchars($conductor['numero_licencia'] ?? 'No registrada') ?></strong>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 0.8rem; display: block;">Categoría Autorizada</span>
                    <strong style="color: #0f172a;"><?= htmlspecialchars($conductor['categoria_licencia'] ?? 'B-IIc') ?> (Mototaxis)</strong>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 0.8rem; display: block;">Vencimiento de Licencia</span>
                    <strong style="color: #0f172a;"><?= !empty($conductor['fecha_vencimiento_licencia']) ? date('d/m/Y', strtotime($conductor['fecha_vencimiento_licencia'])) : 'No registrada' ?></strong>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 0.8rem; display: block;">Teléfono de Contacto</span>
                    <strong style="color: #0f172a;"><?= htmlspecialchars($usuario['telefono'] ?? ($conductor['telefono'] ?? '-')) ?></strong>
                </div>
                <div style="grid-column: 1 / -1;">
                    <span style="color: #64748b; font-size: 0.8rem; display: block;">Dirección Domiciliaria</span>
                    <strong style="color: #334155;"><?= htmlspecialchars($conductor['direccion'] ?? 'No registrada') ?></strong>
                </div>
            </div>
        </div>

        <!-- RESUMEN DE ASISTENCIAS -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 15px;">
                <h2 class="card-title" style="margin: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 18px; height: 18px; color: #2563eb;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    Resumen de Turnos y Asistencia
                </h2>
                <a href="<?= vistaUrl('taxista/asistencia.php') ?>" class="btn btn-outline btn-sm">Ver Historial</a>
            </div>

            <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 15px;">
                Cumplimiento obligatorio de turnos para el registro de fiscalización municipal y relevo de unidades.
            </p>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div>
                    <div style="color: #64748b; font-size: 0.8rem;">Registro de Hoy (<?= date('d/m/Y') ?>)</div>
                    <div style="font-weight: 700; font-size: 1rem; color: #0f172a;">
                        Turno: <?= htmlspecialchars($asistenciaHoy['turno'] ?? 'No iniciado') ?>
                    </div>
                    <?php if ($asistenciaHoy): ?>
                        <div style="color: #64748b; font-size: 0.8rem;">
                            Ingreso: <?= htmlspecialchars(substr($asistenciaHoy['hora_ingreso'], 0, 5)) ?> hrs 
                            <?= !empty($asistenciaHoy['hora_salida']) ? '• Salida: ' . htmlspecialchars(substr($asistenciaHoy['hora_salida'], 0, 5)) . ' hrs' : '' ?>
                        </div>
                    <?php endif; ?>
                </div>
                <a href="<?= vistaUrl('taxista/asistencia.php') ?>" class="btn btn-primary btn-sm">
                    <?= (!$asistenciaHoy || $asistenciaHoy['estado'] !== 'En Turno') ? 'Marcar Entrada' : 'Marcar Salida' ?>
                </a>
            </div>
        </div>

    </div>

    <!-- ======================================================== -->
    <!-- COLUMNA DERECHA: MOTO ASIGNADA & ESTADO / DAÑOS          -->
    <!-- ======================================================== -->
    <div class="col-12 col-lg-6" style="margin-bottom: 25px;">
        
        <!-- FICHA DE LA MOTO ASIGNADA (DE LA EMPRESA) -->
        <div class="card" style="margin-bottom: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 15px;">
                <h2 class="card-title" style="margin: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 18px; height: 18px; color: #2563eb;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="17" r="3"></circle><circle cx="19" cy="17" r="3"></circle><path d="M5 17h14"></path><path d="M9 17l2-7h4l2 7"></path></svg>
                    Mototaxi Asignada (Unidad de la Empresa)
                </h2>
                <a href="<?= vistaUrl('taxista/fotos.php') ?>" class="btn btn-outline btn-sm">Cambiar Foto</a>
            </div>

            <!-- FOTO DE LA MOTO -->
            <div style="width: 100%; height: 150px; border-radius: 8px; overflow: hidden; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                <?php if (!empty($vehiculo['foto']) && file_exists(ROOT_PATH . 'subidas/fotos_vehiculos/' . $vehiculo['foto'])): ?>
                    <img src="<?= subidaUrl('fotos_vehiculos/' . $vehiculo['foto']) ?>" alt="Foto Mototaxi" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <div style="color: #64748b; text-align: center;">
                        <svg style="width: 48px; height: 48px; margin-bottom: 4px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="17" r="3"></circle><circle cx="19" cy="17" r="3"></circle><path d="M5 17h14"></path><path d="M9 17l2-7h4l2 7"></path></svg>
                        <div style="font-size: 0.8rem;">Sin foto registrada de la unidad</div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ESPECIFICACIONES DE LA MOTO -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; font-size: 0.88rem;">
                <div>
                    <span style="color: #64748b; font-size: 0.75rem; display: block;">Placa de Rodaje</span>
                    <strong style="color: #0f172a; font-size: 1.05rem;"><?= htmlspecialchars($vehiculo['placa'] ?? 'Sin asignar') ?></strong>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 0.75rem; display: block;">Padrón Municipal</span>
                    <strong style="color: #0f172a;"><?= htmlspecialchars($vehiculo['numero_padron'] ?? '-') ?></strong>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 0.75rem; display: block;">Marca y Modelo</span>
                    <strong style="color: #0f172a;"><?= htmlspecialchars(($vehiculo['marca'] ?? '-') . ' ' . ($vehiculo['modelo'] ?? '')) ?></strong>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 0.75rem; display: block;">Color Oficial</span>
                    <strong style="color: #0f172a;"><?= htmlspecialchars($vehiculo['color'] ?? '-') ?></strong>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 0.75rem; display: block;">Año de Fabricación</span>
                    <strong style="color: #0f172a;"><?= htmlspecialchars($vehiculo['anio_fabricacion'] ?? '-') ?></strong>
                </div>
                <div>
                    <span style="color: #64748b; font-size: 0.75rem; display: block;">Empresa / Asociación</span>
                    <strong style="color: #0f172a;"><?= htmlspecialchars($vehiculo['nombre_asociacion'] ?? 'KEBA MOTOS') ?></strong>
                </div>
            </div>
        </div>

        <!-- ESTADO OPERATIVO, DAÑOS Y REVISIÓN DE LA MOTO -->
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 15px;">
                <h2 class="card-title" style="margin: 0; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 18px; height: 18px; color: #2563eb;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    Estado Operativo, Daños y Revisión
                </h2>
                <a href="<?= vistaUrl('taxista/reportar_estado.php') ?>" class="btn btn-primary btn-sm">Reportar Avería</a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 15px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; border-radius: 8px;">
                    <span style="color: #64748b; font-size: 0.75rem; display: block;">Próxima Revisión Técnica</span>
                    <strong style="color: #0f172a; font-size: 0.95rem;">
                        <?= !empty($vehiculo['fecha_proxima_revision']) ? date('d/m/Y', strtotime($vehiculo['fecha_proxima_revision'])) : 'Por programar' ?>
                    </strong>
                </div>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; border-radius: 8px;">
                    <span style="color: #64748b; font-size: 0.75rem; display: block;">Estado de Limpieza</span>
                    <strong style="color: #0f172a; font-size: 0.95rem;">
                        <?= htmlspecialchars($vehiculo['estado_limpieza'] ?? 'Limpia') ?>
                    </strong>
                </div>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; border-radius: 8px;">
                    <span style="color: #64748b; font-size: 0.75rem; display: block;">Condición Mecánica</span>
                    <strong style="color: #0f172a; font-size: 0.95rem;">
                        <?= htmlspecialchars($vehiculo['estado_mecanico'] ?? 'Operativo') ?>
                    </strong>
                </div>
            </div>

            <!-- ÚLTIMO MENSAJE O RESPUESTA DE ADMINISTRACIÓN -->
            <?php if (!empty($misReportes)): 
                $ultimoRep = $misReportes[0];
            ?>
                <div style="background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.85rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                        <span style="color: #0f172a; font-weight: 600;">Último reporte: <?= htmlspecialchars($ultimoRep['tipo_asunto']) ?></span>
                        <span class="badge badge-info" style="font-size: 0.75rem;"><?= htmlspecialchars($ultimoRep['estado']) ?></span>
                    </div>
                    <div style="color: #334155; margin-bottom: 6px;">
                        <?= htmlspecialchars(mb_strimwidth($ultimoRep['descripcion_danos'], 0, 90, '...')) ?>
                    </div>
                    <?php if (!empty($ultimoRep['respuesta_admin'])): ?>
                        <div style="border-left: 3px solid #059669; padding-left: 8px; color: #065f46; font-size: 0.8rem; background: #ecfdf5; padding: 6px 10px; border-radius: 4px;">
                            <strong>Respuesta Admin:</strong> <?= htmlspecialchars($ultimoRep['respuesta_admin']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div style="color: #64748b; font-size: 0.85rem; text-align: center; padding: 10px;">
                    No registras reportes recientes de daños o revisiones.
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>

<!-- ======================================================== -->
<!-- TABLA DE DOCUMENTOS DE LA UNIDAD (SOAT / TUC)             -->
<!-- ======================================================== -->
<div class="card" style="margin-bottom: 25px;">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #334155; padding-bottom: 12px; margin-bottom: 15px; flex-wrap: wrap; gap: 10px;">
        <div>
            <h2 class="card-title" style="margin: 0; font-size: 1.1rem;">Documentación de la Unidad (SOAT, TUC, Rev. Técnica)</h2>
            <p class="card-subtitle" style="margin: 3px 0 0 0; color: #64748b; font-size: 0.85rem;">Verifica la vigencia de tus documentos antes de salir a ruta</p>
        </div>
        <a href="<?= vistaUrl('taxista/documentos.php') ?>" class="btn btn-primary btn-sm">+ Subir Documento</a>
    </div>

    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>N° Documento</th>
                    <th>Entidad Emisora</th>
                    <th>Vencimiento</th>
                    <th>Estado</th>
                    <th>Archivo Digital</th>
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
                                <span style="color: #94a3b8; font-size: 0.8rem;">Sin adjunto</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #94a3b8; padding: 25px;">
                            No hay documentos registrados para esta unidad.
                            <br><a href="<?= vistaUrl('taxista/documentos.php') ?>" style="color: #2563eb; font-weight: 600;">Sube tu SOAT o TUC aquí</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

