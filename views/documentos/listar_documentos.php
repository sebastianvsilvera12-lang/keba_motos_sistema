<?php
$tituloPagina = 'Gestión de Documentos';
$subtituloNavbar = 'Control de SOAT y TUC';
$menuActivo = 'documentos';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/models/Documento.php';

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
            <span class="search-icon">🔍</span>
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
                    <th>Entidad Emisora</th>
                    <th>Emisión</th>
                    <th>Vencimiento</th>
                    <th>Días Restantes</th>
                    <th>Estado</th>
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
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($d['placa']) ?></strong></td>
                    <td><span class="badge badge-info"><?= htmlspecialchars($d['tipo_documento']) ?></span></td>
                    <td><?= htmlspecialchars($d['numero_documento']) ?></td>
                    <td><?= htmlspecialchars($d['entidad_emisora'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($d['fecha_emision']) ?></td>
                    <td><strong><?= htmlspecialchars($d['fecha_vencimiento']) ?></strong></td>
                    <td><span class="badge <?= $badgeClass ?>"><?= $textoEstado ?></span></td>
                    <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($d['estado']) ?></span></td>
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

