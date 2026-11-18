<?php
$tituloPagina = 'Registrar Propietario';
$subtituloNavbar = 'Nuevo Dueño';
$menuActivo = 'propietarios';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Registrar Nuevo Propietario</h1>
        <p class="page-subtitle">Información del titular de la tarjeta de propiedad</p>
    </div>
    <a href="<?= vistaUrl('propietarios/listar_propietarios.php') ?>" class="btn btn-outline">&larr; Volver</a>
</div>

<div class="card">
    <form action="<?= controladorUrl('propietario_controlador.php') ?>" method="POST">
        <input type="hidden" name="accion" value="crear">

        <div class="form-grid">
            <div class="form-group">
                <label for="tipo_documento">Tipo de Documento</label>
                <select id="tipo_documento" name="tipo_documento" class="form-control">
                    <option value="DNI">DNI</option>
                    <option value="RUC">RUC</option>
                    <option value="Carnet Extranjeria">Carnet Extranjería</option>
                </select>
            </div>

            <div class="form-group">
                <label for="numero_documento">N° Documento <span class="required">*</span></label>
                <input type="text" id="numero_documento" name="numero_documento" class="form-control" placeholder="Ej: 45892147" required>
            </div>

            <div class="form-group">
                <label for="nombres_razon_social">Nombres o Razón Social <span class="required">*</span></label>
                <input type="text" id="nombres_razon_social" name="nombres_razon_social" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="apellidos">Apellidos (opcional si es RUC)</label>
                <input type="text" id="apellidos" name="apellidos" class="form-control">
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono</label>
                <input type="text" id="telefono" name="telefono" class="form-control">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control">
            </div>

            <div class="form-group full-width">
                <label for="direccion">Dirección Domiciliaria</label>
                <input type="text" id="direccion" name="direccion" class="form-control">
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= vistaUrl('propietarios/listar_propietarios.php') ?>" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">Registrar Propietario</button>
        </div>
    </form>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

