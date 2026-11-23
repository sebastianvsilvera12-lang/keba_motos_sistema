<?php
$tituloPagina = 'Gestión de Documentos';
$subtituloNavbar = 'Control de SOAT y TUC';
$menuActivo = 'documentos';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Documento.php';

$docMod = new Documento();
$documentos = $docMod->listarTodos();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Documentos Vehiculares</h1>
        <p class="page-subtitle">Control de vigencia de SOAT, TUC y Revisiones Técnicas</p>
    </div>
    <div>
        <a href="<?= vistaUrl('documentos/documentos_por_vencer.php') ?>" class="btn btn-dark">⚠️ Ver Alertas por Vencer</a>
        <a href="<?= vistaUrl('documentos/registrar_documento.php') ?>" class="btn btn-primary">+ Nuevo Documento</a>
    </div>
</div>

<div class="card">
    <div class="table-toolbar">
        <div class="search-box">
            <span class="search-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></span>
            <input type="text" id="inputBuscar" placeholder="Buscar por placa, tipo o número...">
        </div>
        <div style="font-size: 0.9rem; color: #64748b;">
            Total: <strong><?= count($documentos) ?></strong> pólizas/documentos
        </div>
    </div>

    <div class="table-responsive">
        <table class="table-custom" id="tablaRegistros">
            <thead>
                <tr>
                    <th>Placa</th>
                    <th>Tipo Documento</th>
                    <th>N° Documento</th>
                    <th>Remitente</th>
                    <th>Entidad Emisora</th>
                    <th>Vencimiento</th>
                    <th>Estado</th>
                    <th>Archivo</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($documentos)): ?>
                <tr><td colspan="9" style="text-align:center; padding:25px;">No hay documentos registrados.</td></tr>
                <?php else: ?>
                <?php foreach ($documentos as $d): ?>
                <?php 
                    $dias = (int)($d['dias_para_vencer'] ?? 0);
                    $badgeClass = 'badge-success';
                    if ($dias < 0) {
                        $badgeClass = 'badge-danger';
                        $textoEstado = 'VENCIDO (' . abs($dias) . ' d)';
                    } elseif ($dias <= 30) {
                        $badgeClass = 'badge-warning';
                        $textoEstado = 'POR VENCER (' . $dias . ' d)';
                    } else {
                        $textoEstado = 'VIGENTE (' . $dias . ' d)';
                    }
                    $tieneArchivo = !empty($d['archivo_digital']) && file_exists(ROOT_PATH . 'subidas/documentos/' . $d['archivo_digital']);
                    $archivoUrl = $tieneArchivo ? subidaUrl('documentos/' . $d['archivo_digital']) : '';
                    $esImagen = $tieneArchivo && preg_match('/\.(jpg|jpeg|png|webp)$/i', $d['archivo_digital']);
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($d['placa']) ?></strong></td>
                    <td><span class="badge badge-info"><?= htmlspecialchars($d['tipo_documento']) ?></span></td>
                    <td><?= htmlspecialchars($d['numero_documento']) ?></td>
                    <td>
                        <?php if (!empty($d['remitente_nombre'])): ?>
                            <span class="badge" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; padding: 4px 8px;">
                                <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <?= htmlspecialchars($d['remitente_nombre']) ?>
                            </span>
                        <?php else: ?>
                            <span style="color: #64748b; font-size: 0.8rem;">Oficina / Admin</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($d['entidad_emisora'] ?? '-') ?></td>
                    <td><strong><?= htmlspecialchars(date('d/m/Y', strtotime($d['fecha_vencimiento']))) ?></strong></td>
                    <td><span class="badge <?= $badgeClass ?>"><?= $textoEstado ?></span></td>
                    <td>
                        <?php if ($tieneArchivo): ?>
                            <?php if ($esImagen): ?>
                                <a href="<?= $archivoUrl ?>" target="_blank" title="Ver foto">
                                    <img src="<?= $archivoUrl ?>" alt="Doc" style="width: 32px; height: 32px; object-fit: cover; border-radius: 4px; border: 1px solid #334155; vertical-align: middle;">
                                </a>
                            <?php else: ?>
                                <a href="<?= $archivoUrl ?>" target="_blank" class="btn btn-outline btn-sm" style="padding: 2px 6px; font-size: 0.75rem;">PDF</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <span style="color: #64748b; font-size: 0.75rem;">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: right;">
                        <a href="<?= vistaUrl('documentos/ver_documento.php?id=' . $d['id_documento']) ?>" class="btn btn-outline btn-sm">Ver</a>
                        <a href="<?= controladorUrl('documento_controlador.php?accion=eliminar&id=' . $d['id_documento']) ?>" class="btn btn-danger btn-sm btn-confirmar-eliminar">Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

