<?php
$tituloPagina = 'Editar Infracción';
$subtituloNavbar = 'Modificar Acta';
$menuActivo = 'fiscalizacion';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Modificar Acta de Infracción</h1>
        <p class="page-subtitle">Actualización de estado o comprobante de pago</p>
    </div>
    <a href="<?= vistaUrl('fiscalizacion/listar_infracciones.php') ?>" class="btn btn-outline">&larr; Volver</a>
</div>

<div class="card">
    <p>Para registrar pagos o anulación de infracciones, puedes hacerlo directamente desde el listado general con un solo clic.</p>
    <a href="<?= vistaUrl('fiscalizacion/listar_infracciones.php') ?>" class="btn btn-primary">Ir a Lista de Infracciones</a>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

