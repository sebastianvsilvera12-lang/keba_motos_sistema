<?php
$tituloPagina = 'Panel de Fiscalización y Consulta';
$subtituloNavbar = 'Verificación Rápida';
$menuActivo = 'dashboard';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Módulo de Consulta Rápida</h1>
        <p class="page-subtitle">Verificación de placas, antecedentes y licencias en operativos de campo</p>
    </div>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto 30px auto;">
    <h2 class="card-title" style="margin-bottom: 15px;">🔍 Búsqueda Rápida de Placa o DNI</h2>
    <form action="<?= vistaUrl('fiscalizacion/historial_vehiculo.php') ?>" method="GET" class="d-flex" style="gap: 10px;">
        <input type="text" name="placa" class="form-control" placeholder="Ingresa placa (ej: 1234-5B) o DNI..." required>
        <button type="submit" class="btn btn-primary">Consultar</button>
    </form>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

