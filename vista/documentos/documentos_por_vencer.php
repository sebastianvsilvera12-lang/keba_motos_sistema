<?php
$tituloPagina = 'Alertas de Documentos por Vencer';
$subtituloNavbar = 'Semáforo de Vencimientos';
$menuActivo = 'documentos';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Documento.php';

$docMod = new Documento();
$documentos = $docMod->listarPorVencer(30);
?>

<div class="page-header">
    <div>
        <h1 class="page-title">⚠️ Documentos por Vencer o Vencidos</h1>
        <p class="page-subtitle">Unidades con SOAT o permisos próximos a caducar en los siguientes 30 días</p>
    </div>
    <a href="<?= vistaUrl('documentos/listar_documentos.php') ?>" class="btn btn-outline">&larr; Ver Todos</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Placa</th>
                    <th>Mototaxi</th>
                    <th>Tipo Documento</th>
                    <th>N° Póliza</th>
                    <th>Fecha Vencimiento</th>
                    <th>Días Restantes</th>
                    <th>Acción Inmediata</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($documentos)): ?>
                <tr><td colspan="7" style="text-align:center; padding:25px; color:#10b981;"><strong>¡Excelente! No hay documentos vencidos ni por vencer en los próximos 30 días.</strong></td></tr>
                <?php else: ?>
                <?php foreach ($documentos as $d): ?>
                <?php 
                    $dias = (int)($d['dias_para_vencer'] ?? 0);
                    $badgeClass = $dias < 0 ? 'badge-danger' : 'badge-warning';
                    $mensaje = $dias < 0 ? 'VENCIDO HACE ' . abs($dias) . ' DÍAS' : 'VENCE EN ' . $dias . ' DÍAS';
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($d['placa']) ?></strong></td>
                    <td><?= htmlspecialchars($d['marca'] . ' ' . $d['modelo']) ?></td>
                    <td><span class="badge badge-info"><?= htmlspecialchars($d['tipo_documento']) ?></span></td>
                    <td><?= htmlspecialchars($d['numero_documento']) ?></td>
                    <td><strong><?= htmlspecialchars($d['fecha_vencimiento']) ?></strong></td>
                    <td><span class="badge <?= $badgeClass ?>"><?= $mensaje ?></span></td>
                    <td>
                        <a href="<?= vistaUrl('fiscalizacion/registrar_infraccion.php') ?>" class="btn btn-danger btn-sm">Notificar Faltante</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

