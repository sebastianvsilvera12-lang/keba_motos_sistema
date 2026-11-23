<?php
$tituloPagina = 'Detalle y Validación de Documento';
$subtituloNavbar = 'Inspección de Archivo y Póliza';
$menuActivo = 'documentos';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Documento.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Vehiculo.php';

$id = (int)($_GET['id'] ?? 0);
$docMod = new Documento();
$d = $docMod->obtenerPorId($id);

if (!$d) {
    echo "<div class='alert alert-danger'>Documento no encontrado.</div>";
    require_once dirname(__DIR__) . '/plantillas/footer.php';
    exit;
}

$vehMod = new Vehiculo();
$vehiculo = $vehMod->obtenerPorId($d['id_vehiculo']);

$tieneArchivo = !empty($d['archivo_digital']) && file_exists(ROOT_PATH . 'subidas/documentos/' . $d['archivo_digital']);
$archivoUrl = $tieneArchivo ? subidaUrl('documentos/' . $d['archivo_digital']) : '';
$esImagen = $tieneArchivo && preg_match('/\.(jpg|jpeg|png|webp)$/i', $d['archivo_digital']);
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h1 class="page-title"><?= htmlspecialchars($d['tipo_documento']) ?></h1>
        <p class="page-subtitle">
            Unidad: <strong><?= htmlspecialchars($vehiculo['placa'] ?? 'Placa desconocida') ?></strong> (<?= htmlspecialchars(($vehiculo['marca'] ?? '') . ' ' . ($vehiculo['modelo'] ?? '')) ?>)
            <?php if (!empty($d['remitente_nombre'])): ?>
                &bull; Subido por: <strong style="color: #2563eb;"><?= htmlspecialchars($d['remitente_nombre']) ?></strong>
            <?php endif; ?>
        </p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="<?= vistaUrl('documentos/listar_documentos.php') ?>" class="btn btn-outline">&larr; Volver al Listado</a>
        <?php if ($tieneArchivo): ?>
            <a href="<?= $archivoUrl ?>" target="_blank" class="btn btn-primary">Abrir Archivo Completo &nearr;</a>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <!-- COLUMNA IZQUIERDA: VISOR DE FOTO O PDF -->
    <div class="col-12 col-lg-7" style="margin-bottom: 25px;">
        <div class="card" style="box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);">
            <div style="border-bottom: 1px solid #334155; padding-bottom: 12px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 1.05rem; display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 20px; height: 20px; color: #2563eb;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    Archivo Adjunto por el Taxista
                </h3>
                <span class="badge badge-info" style="font-size: 0.78rem;">
                    <?= $esImagen ? 'Foto / Imagen' : ($tieneArchivo ? 'Documento PDF' : 'Sin archivo') ?>
                </span>
            </div>

            <div style="text-align: center; background: #0f172a; padding: 15px; border-radius: 8px; min-height: 380px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                <?php if ($tieneArchivo): ?>
                    <?php if ($esImagen): ?>
                        <a href="<?= $archivoUrl ?>" target="_blank" title="Haz clic para ver tamaño original">
                            <img src="<?= $archivoUrl ?>" alt="Foto Documento" style="max-width: 100%; max-height: 520px; border-radius: 6px; object-fit: contain; box-shadow: 0 4px 12px rgba(0,0,0,0.4);">
                        </a>
                    <?php else: ?>
                        <div style="padding: 40px 20px; color: #ffffff;">
                            <svg style="width: 64px; height: 64px; color: #ef4444; margin-bottom: 15px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                            <h4 style="color: #f1f5f9; margin-bottom: 8px;">Documento en Formato PDF</h4>
                            <p style="color: #94a3b8; font-size: 0.85rem; margin-bottom: 16px;">El conductor adjuntó un archivo PDF escaneado.</p>
                            <a href="<?= $archivoUrl ?>" target="_blank" class="btn btn-primary" style="padding: 8px 18px; font-weight: 600;">
                                Abrir y Leer PDF en Nueva Pestaña &rarr;
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div style="color: #64748b; padding: 40px 20px;">
                        <svg style="width: 48px; height: 48px; margin-bottom: 10px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <div>No se encontró el archivo digital adjunto en el servidor.</div>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($esImagen): ?>
                <small style="color: #94a3b8; font-size: 0.78rem; display: block; margin-top: 8px; text-align: center;">
                    💡 Puedes hacer clic sobre la foto para abrirla en alta resolución y revisar las fechas y número de póliza.
                </small>
            <?php endif; ?>
        </div>
    </div>

    <!-- COLUMNA DERECHA: VALIDACIÓN Y ACTUALIZACIÓN POR EL ADMINISTRADOR -->
    <div class="col-12 col-lg-5">
        <div class="card" style="box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);">
            <div style="border-bottom: 1px solid #334155; padding-bottom: 12px; margin-bottom: 18px;">
                <h3 style="margin: 0; font-size: 1.05rem; display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 20px; height: 20px; color: #10b981;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Validación y Datos Oficiales
                </h3>
                <p style="margin: 4px 0 0 0; color: #64748b; font-size: 0.8rem;">
                    Revisa la foto adjunta y valida los datos oficiales de la póliza
                </p>
            </div>

            <!-- Ficha del remitente -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <span style="font-size: 0.78rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Remitente</span>
                    <span class="badge" style="background: #eff6ff; color: #1d4ed8; font-size: 0.75rem; border: 1px solid #bfdbfe;">Taxista</span>
                </div>
                <div style="font-weight: 700; color: #0f172a; font-size: 0.95rem;">
                    <?= htmlspecialchars($d['remitente_nombre'] ?? 'Administración Central') ?>
                </div>
                <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">
                    Mototaxi: <strong><?= htmlspecialchars($vehiculo['placa'] ?? '-') ?></strong> &bull; Padrón: <?= htmlspecialchars($vehiculo['numero_padron'] ?? '-') ?>
                </div>
            </div>

            <form action="<?= controladorUrl('documento_controlador.php') ?>" method="POST">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="id_documento" value="<?= (int)$d['id_documento'] ?>">
                <input type="hidden" name="id_vehiculo" value="<?= (int)$d['id_vehiculo'] ?>">

                <div class="form-group" style="margin-bottom: 14px;">
                    <label for="tipo_documento" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 4px; display: block;">Tipo de Documento</label>
                    <select name="tipo_documento" id="tipo_documento" class="form-control" required style="font-weight: 600;">
                        <option value="Licencia de Conducir" <?= $d['tipo_documento'] === 'Licencia de Conducir' ? 'selected' : '' ?>>Licencia de Conducir (B-IIc)</option>
                        <option value="SOAT" <?= $d['tipo_documento'] === 'SOAT' ? 'selected' : '' ?>>SOAT (Seguro Obligatorio)</option>
                        <option value="Tarjeta de Propiedad" <?= $d['tipo_documento'] === 'Tarjeta de Propiedad' ? 'selected' : '' ?>>Tarjeta de Propiedad / TIV</option>
                        <option value="Tarjeta de Circulacion" <?= $d['tipo_documento'] === 'Tarjeta de Circulacion' ? 'selected' : '' ?>>Tarjeta Única de Circulación (TUC)</option>
                        <option value="Revision Tecnica" <?= $d['tipo_documento'] === 'Revision Tecnica' ? 'selected' : '' ?>>Inspección Técnica Vehicular</option>
                        <option value="Otro" <?= $d['tipo_documento'] === 'Otro' ? 'selected' : '' ?>>Otro Certificado</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 14px;">
                    <label for="numero_documento" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 4px; display: block;">N° de Póliza, Placa o Licencia *</label>
                    <input type="text" name="numero_documento" id="numero_documento" class="form-control" value="<?= htmlspecialchars($d['numero_documento']) ?>" required placeholder="Ej: SOAT-2026-98124 o Q-61205626">
                </div>

                <div class="form-group" style="margin-bottom: 14px;">
                    <label for="entidad_emisora" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 4px; display: block;">Entidad Emisora / Aseguradora</label>
                    <input type="text" name="entidad_emisora" id="entidad_emisora" class="form-control" value="<?= htmlspecialchars($d['entidad_emisora'] ?? '') ?>" placeholder="Ej: La Positiva, Pacífico, MTC, Municipalidad...">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                    <div class="form-group">
                        <label for="fecha_emision" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 4px; display: block;">Fecha Emisión</label>
                        <input type="date" name="fecha_emision" id="fecha_emision" class="form-control" value="<?= htmlspecialchars($d['fecha_emision'] ?? date('Y-m-d')) ?>">
                    </div>
                    <div class="form-group">
                        <label for="fecha_vencimiento" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 4px; display: block;">Fecha Vencimiento *</label>
                        <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" class="form-control" value="<?= htmlspecialchars($d['fecha_vencimiento'] ?? date('Y-m-d', strtotime('+1 year'))) ?>" required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 14px;">
                    <label for="estado" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 4px; display: block;">Estado de Validación</label>
                    <select name="estado" id="estado" class="form-control" style="font-weight: 600;">
                        <option value="Vigente" <?= $d['estado'] === 'Vigente' ? 'selected' : '' ?>>Vigente (Aprobado)</option>
                        <option value="Por Vencer" <?= $d['estado'] === 'Por Vencer' ? 'selected' : '' ?>>Por Vencer</option>
                        <option value="Vencido" <?= $d['estado'] === 'Vencido' ? 'selected' : '' ?>>Vencido / No Válido</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="observaciones" style="font-weight: 600; font-size: 0.88rem; margin-bottom: 4px; display: block;">Observaciones / Notas del Administrador</label>
                    <textarea name="observaciones" id="observaciones" class="form-control" rows="2" placeholder="Notas de validación interna..."><?= htmlspecialchars($d['observaciones'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-weight: 700; font-size: 0.95rem; border-radius: 8px;">
                    💾 Guardar y Validar Documento
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>