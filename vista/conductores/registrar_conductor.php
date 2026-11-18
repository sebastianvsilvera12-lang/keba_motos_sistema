<?php
$tituloPagina = 'Registrar Conductor';
$subtituloNavbar = 'Nuevo Chofer';
$menuActivo = 'conductores';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Registrar Nuevo Conductor</h1>
        <p class="page-subtitle">Formulario de empadronamiento de chofer con licencia</p>
    </div>
    <a href="<?= vistaUrl('conductores/listar_conductores.php') ?>" class="btn btn-outline">&larr; Volver</a>
</div>

<div class="card">
    <form action="<?= controladorUrl('conductor_controlador.php') ?>" method="POST">
        <input type="hidden" name="accion" value="crear">

        <div class="form-grid">
            <div class="form-group">
                <label for="dni">DNI <span class="required">*</span></label>
                <input type="text" id="dni" name="dni" class="form-control" placeholder="8 dígitos" required maxlength="8">
            </div>

            <div class="form-group">
                <label for="nombres">Nombres <span class="required">*</span></label>
                <input type="text" id="nombres" name="nombres" class="form-control" placeholder="Nombres del chofer" required>
            </div>

            <div class="form-group">
                <label for="apellidos">Apellidos <span class="required">*</span></label>
                <input type="text" id="apellidos" name="apellidos" class="form-control" placeholder="Apellidos completos" required>
            </div>

            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control">
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono / Celular</label>
                <input type="text" id="telefono" name="telefono" class="form-control" placeholder="Ej: 987654321">
            </div>

            <div class="form-group">
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" name="direccion" class="form-control" placeholder="Dirección de residencia">
            </div>

            <div class="form-group">
                <label for="numero_licencia">N° de Licencia <span class="required">*</span></label>
                <input type="text" id="numero_licencia" name="numero_licencia" class="form-control" placeholder="Ej: Q-72458963" required>
            </div>

            <div class="form-group">
                <label for="categoria_licencia">Categoría <span class="required">*</span></label>
                <select id="categoria_licencia" name="categoria_licencia" class="form-control" required>
                    <option value="B-IIc" selected>B-IIc (Especial Mototaxis)</option>
                    <option value="B-IIb">B-IIb</option>
                    <option value="A-I">A-I</option>
                    <option value="A-IIa">A-IIa</option>
                </select>
            </div>

            <div class="form-group">
                <label for="fecha_emision_licencia">Fecha Emisión Licencia</label>
                <input type="date" id="fecha_emision_licencia" name="fecha_emision_licencia" class="form-control">
            </div>

            <div class="form-group">
                <label for="fecha_vencimiento_licencia">Fecha Vencimiento Licencia <span class="required">*</span></label>
                <input type="date" id="fecha_vencimiento_licencia" name="fecha_vencimiento_licencia" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="estado">Estado de Habilitación</label>
                <select id="estado" name="estado" class="form-control">
                    <option value="Habilitado">Habilitado</option>
                    <option value="Inhabilitado">Inhabilitado</option>
                    <option value="Suspendido">Suspendido</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= vistaUrl('conductores/listar_conductores.php') ?>" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary">Registrar Conductor</button>
        </div>
    </form>
</div>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

