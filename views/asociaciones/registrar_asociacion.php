<?php
$tituloPagina = 'Registrar Asociación';
$subtituloNavbar = 'Nueva Empresa';
$menuActivo = 'asociaciones';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Registrar Nueva Asociación</h1>
        <p class="page-subtitle">Alta de empresa o comité de mototaxistas</p>
    </div>
    <a href="<?= vistaUrl('asociaciones/listar_asociaciones.php') ?>" class="btn btn-outline">&larr; Volver</a>
</div>

<div class="card">
    <form action="<?= controladorUrl('asociacion_controlador.php') ?>" method="POST">
        <input type="hidden" name="accion" value="crear">

        <div class="form-grid">
            <div class="form-group">
                <label for="nombre">Nombre de la Asociación / Empresa <span class="required">*</span></label>
                <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej: Asociación Los Halcones" required>
            </div>

            <div class="form-group">
                <label for="ruc">RUC</label>
                <input type="text" id="ruc" name="ruc" class="form-control" placeholder="11 dígitos" maxlength="11">
            </div>

            <div class="form-group">
                <label for="resolucion_municipal">Resolución de Autorización</label>
                <input type="text" id="resolucion_municipal" name="resolucion_municipal" class="form-control" placeholder="Ej: RES-MUN-2024-012">
            </div>

            <div class="form-group">
                <label for="presidente">Presidente / Representante</label>
                <input type="text" id="presidente" name="presidente" class="form-control">
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" class="form-control">
            </div>

            <div class="form-group">
                <label for="color_distintivo">Color Distintivo de la Flota</label>
                <input type="text" id="color_distintivo" name="color_distintivo" class="form-control" placeholder="Ej: Azul con Franja Blanca">
            </div>

            <div class="form-group full-width">
                <label for="direccion">Dirección del Paradero Oficial</label>
                <input type="text" id="direccion" name="direccion" class="form-control">
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= vistaUrl('asociaciones/listar_asociaciones.php') ?>" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">Registrar Asociación</button>
        </div>
    </form>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

