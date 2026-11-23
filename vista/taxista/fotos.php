<?php
$tituloPagina = 'Fotos de Persona y Mototaxi - Taxistas';
$subtituloNavbar = 'Galería y Registro Fotográfico';
$menuActivo = 'fotos_taxista';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';

require_once dirname(dirname(__DIR__)) . '/modelos/Taxista.php';

$usuario = obtenerUsuarioActual();
$taxistaMod = new Taxista();

$ficha = $taxistaMod->obtenerFichaTaxista($usuario['id']);
$conductor = $ficha['conductor'] ?? null;
$vehiculo = $ficha['vehiculo'] ?? null;

$nombreCompleto = trim(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellidos'] ?? ''));
if (empty($nombreCompleto)) {
    $nombreCompleto = $usuario['usuario'] ?? 'Taxista';
}
?>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h1 class="page-title">Registro Fotográfico: Persona y Mototaxi</h1>
        <p class="page-subtitle">Actualiza tu fotografía de perfil de conductor y la fotografía oficial de tu unidad mototaxi</p>
    </div>
    <div>
        <a href="<?= vistaUrl('taxista/dashboard.php') ?>" class="btn btn-outline">&larr; Volver al Panel</a>
    </div>
</div>

<div class="row">

    <!-- ======================================================== -->
    <!-- 1. SECCIÓN: FOTO DE LA PERSONA (CONDUCTOR)              -->
    <!-- ======================================================== -->
    <div class="col-12 col-lg-6" style="margin-bottom: 25px;">
        <div class="card" style="height: 100%; display: flex; flex-direction: column;">
            <div style="border-bottom: 1px solid #334155; padding-bottom: 12px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 class="card-title" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                        <svg style="width: 20px; height: 20px; color: #2563eb;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        1. Foto de Mi Persona (Conductor)
                    </h2>
                    <p class="card-subtitle" style="margin: 3px 0 0 0; color: #64748b; font-size: 0.85rem;">Fotografía para tu carnet y verificación en operativos de tránsito</p>
                </div>
                <div>
                    <?php if (!empty($conductor['foto']) && file_exists(ROOT_PATH . 'subidas/fotos_conductores/' . $conductor['foto'])): ?>
                        <span class="badge badge-success">Foto Activa</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Sin Foto</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- VISTA PREVIA ACTUAL -->
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 15px; margin-bottom: 20px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
                <div style="width: 140px; height: 140px; border-radius: 50%; overflow: hidden; background: #ffffff; border: 4px solid #2563eb; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 15px;">
                    <?php if (!empty($conductor['foto']) && file_exists(ROOT_PATH . 'subidas/fotos_conductores/' . $conductor['foto'])): ?>
                        <img id="previewPersonaImg" src="<?= subidaUrl('fotos_conductores/' . $conductor['foto']) ?>" alt="Foto Conductor" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <img id="previewPersonaImg" src="" alt="Previsualización" style="display:none; width: 100%; height: 100%; object-fit: cover;">
                        <span id="placeholderPersona" style="font-size: 3rem; color: #94a3b8; font-weight: 700;"><?= strtoupper(substr($nombreCompleto, 0, 1)) ?></span>
                    <?php endif; ?>
                </div>

                <h3 style="margin: 0; font-size: 1.15rem; color: #0f172a; text-align: center; font-weight: 700;"><?= htmlspecialchars($nombreCompleto) ?></h3>
                <p style="margin: 4px 0 0 0; color: #64748b; font-size: 0.85rem; text-align: center;">
                    DNI: <strong><?= htmlspecialchars($usuario['dni']) ?></strong> • 
                    Licencia: <strong><?= htmlspecialchars($conductor['numero_licencia'] ?? 'No registrada') ?></strong>
                </p>
            </div>

            <!-- FORMULARIO SUBIR FOTO PERSONA -->
            <form action="<?= controladorUrl('taxista_controlador.php') ?>" method="POST" enctype="multipart/form-data" style="margin-bottom: 20px;">
                <input type="hidden" name="accion" value="subir_foto_persona">
                <input type="hidden" name="id_conductor" value="<?= (int)($conductor['id_conductor'] ?? 0) ?>">

                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="fotoPersonaInput" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px; display: block;">Seleccionar Foto Personal (JPG, PNG, WEBP)</label>
                    <input type="file" name="foto_persona" id="fotoPersonaInput" class="form-control" accept="image/*" required onchange="previsualizarFotoPersona(this)">
                    <small style="color: #64748b;">Debe ser una foto clara, de frente y con buena iluminación (máx. 5 MB)</small>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="4"></circle><path d="M5 7h1a2 2 0 0 0 2-2 1 1 0 0 1 1-1h6a1 1 0 0 1 1 1 2 2 0 0 0 2 2h1a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2z"></path></svg>
                    Actualizar Mi Foto de Conductor
                </button>
            </form>

            <!-- DATOS DE LA LICENCIA -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; margin-top: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <h4 style="margin: 0; font-size: 0.95rem; color: #0f172a; font-weight: 700;">Licencia de Conducir (Brevete)</h4>
                    <button type="button" class="btn btn-outline btn-sm" style="padding: 2px 8px; font-size: 0.75rem;" onclick="toggleEditarLicencia()">Editar Datos Brevete</button>
                </div>

                <div id="infoLicenciaBox" style="font-size: 0.85rem; color: #334155; line-height: 1.6;">
                    <div>N° Licencia: <strong><?= htmlspecialchars($conductor['numero_licencia'] ?? 'Pendiente de registrar') ?></strong></div>
                    <div>Categoría: <strong><?= htmlspecialchars($conductor['categoria_licencia'] ?? 'B-IIc (Mototaxis)') ?></strong></div>
                    <div>Vencimiento: <strong><?= !empty($conductor['fecha_vencimiento_licencia']) ? date('d/m/Y', strtotime($conductor['fecha_vencimiento_licencia'])) : 'No especificado' ?></strong></div>
                    <div>Teléfono de contacto: <strong><?= htmlspecialchars($usuario['telefono'] ?? ($conductor['telefono'] ?? '-')) ?></strong></div>
                </div>

                <!-- FORMULARIO EDITAR LICENCIA (OCULTO POR DEFECTO) -->
                <form id="formLicenciaBox" action="<?= controladorUrl('taxista_controlador.php') ?>" method="POST" style="display: none; margin-top: 10px; border-top: 1px solid #e2e8f0; padding-top: 10px;">
                    <input type="hidden" name="accion" value="actualizar_licencia">

                    <div class="form-group" style="margin-bottom: 8px;">
                        <label style="font-size: 0.8rem; color: #334155;">Número de Licencia *</label>
                        <input type="text" name="numero_licencia" class="form-control" value="<?= htmlspecialchars($conductor['numero_licencia'] ?? '') ?>" required placeholder="Ej: Q-72458963">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 8px;">
                        <div class="form-group">
                            <label style="font-size: 0.8rem;">Categoría</label>
                            <input type="text" name="categoria_licencia" class="form-control" value="<?= htmlspecialchars($conductor['categoria_licencia'] ?? 'B-IIc') ?>" required>
                        </div>
                        <div class="form-group">
                            <label style="font-size: 0.8rem;">Fecha Vencimiento *</label>
                            <input type="date" name="fecha_vencimiento_licencia" class="form-control" value="<?= htmlspecialchars($conductor['fecha_vencimiento_licencia'] ?? date('Y-m-d', strtotime('+2 years'))) ?>" required>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 10px;">
                        <label style="font-size: 0.8rem;">Dirección Actual</label>
                        <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($conductor['direccion'] ?? '') ?>" placeholder="Calle, Av, Mz, Lote...">
                    </div>

                    <div style="display: flex; gap: 8px;">
                        <button type="submit" class="btn btn-primary btn-sm" style="flex: 1;">Guardar Datos</button>
                        <button type="button" class="btn btn-outline btn-sm" onclick="toggleEditarLicencia()">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- ======================================================== -->
    <!-- 2. SECCIÓN: FOTO DE LA MOTOTAXI (VEHÍCULO)              -->
    <!-- ======================================================== -->
    <div class="col-12 col-lg-6" style="margin-bottom: 25px;">
        <div class="card" style="height: 100%; display: flex; flex-direction: column;">
            <div style="border-bottom: 1px solid #334155; padding-bottom: 12px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 class="card-title" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                        <svg style="width: 20px; height: 20px; color: #10b981;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="17" r="3"></circle><circle cx="19" cy="17" r="3"></circle><path d="M5 17h14"></path><path d="M9 17l2-7h4l2 7"></path></svg>
                        2. Foto de la Mototaxi
                    </h2>
                    <p class="card-subtitle" style="margin: 3px 0 0 0; color: #64748b; font-size: 0.85rem;">Fotografía panorámica lateral o frontal con placa y padrón visibles</p>
                </div>
                <div>
                    <?php if (!empty($vehiculo['foto']) && file_exists(ROOT_PATH . 'subidas/fotos_vehiculos/' . $vehiculo['foto'])): ?>
                        <span class="badge badge-success">Foto Registrada</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Sin Foto</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- VISTA PREVIA ACTUAL DE LA MOTO -->
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 15px; margin-bottom: 20px; background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;">
                <div style="width: 100%; height: 180px; border-radius: 8px; overflow: hidden; background: #ffffff; border: 1px solid #cbd5e1; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 15px; position: relative;">
                    <?php if (!empty($vehiculo['foto']) && file_exists(ROOT_PATH . 'subidas/fotos_vehiculos/' . $vehiculo['foto'])): ?>
                        <img id="previewMotoImg" src="<?= subidaUrl('fotos_vehiculos/' . $vehiculo['foto']) ?>" alt="Foto Mototaxi" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <img id="previewMotoImg" src="" alt="Previsualización" style="display:none; width: 100%; height: 100%; object-fit: cover;">
                        <div id="placeholderMoto" style="text-align: center; color: #64748b;">
                            <svg style="width: 60px; height: 60px; margin-bottom: 8px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="17" r="3"></circle><circle cx="19" cy="17" r="3"></circle><path d="M5 17h14"></path><path d="M9 17l2-7h4l2 7"></path></svg>
                            <div style="font-size: 0.85rem;">Aún no has subido foto de tu unidad</div>
                        </div>
                    <?php endif; ?>
                </div>

                <div style="width: 100%; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <h3 style="margin: 0; font-size: 1.15rem; color: #0f172a; font-weight: 700;">Placa: <?= htmlspecialchars($vehiculo['placa'] ?? 'Sin asignar') ?></h3>
                        <p style="margin: 3px 0 0 0; color: #64748b; font-size: 0.85rem;">
                            <?= htmlspecialchars(($vehiculo['marca'] ?? 'Marca') . ' ' . ($vehiculo['modelo'] ?? '')) ?> • Padrón: <strong><?= htmlspecialchars($vehiculo['numero_padron'] ?? '-') ?></strong>
                        </p>
                    </div>
                    <div>
                        <span class="badge badge-info" style="font-size: 0.85rem;">
                            <?= htmlspecialchars($vehiculo['nombre_asociacion'] ?? 'Independiente') ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- FORMULARIO SUBIR FOTO MOTO -->
            <?php if ($vehiculo): ?>
                <form action="<?= controladorUrl('taxista_controlador.php') ?>" method="POST" enctype="multipart/form-data" style="margin-bottom: 20px;">
                    <input type="hidden" name="accion" value="subir_foto_moto">
                    <input type="hidden" name="id_vehiculo" value="<?= (int)$vehiculo['id_vehiculo'] ?>">

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="fotoMotoInput" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px; display: block;">Seleccionar Foto de la Moto (JPG, PNG, WEBP)</label>
                        <input type="file" name="foto_moto" id="fotoMotoInput" class="form-control" accept="image/*" required onchange="previsualizarFotoMoto(this)">
                        <small style="color: #64748b;">Asegúrate de que la placa y el color distintivo sean claramente legibles</small>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 11px; font-weight: 700; background: #059669; border-color: #059669; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <svg style="width: 18px; height: 18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="4"></circle><path d="M5 7h1a2 2 0 0 0 2-2 1 1 0 0 1 1-1h6a1 1 0 0 1 1 1 2 2 0 0 0 2 2h1a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2z"></path></svg>
                        Actualizar Foto de la Mototaxi
                    </button>
                </form>
            <?php else: ?>
                <div class="alert alert-warning" style="margin-bottom: 20px;">
                    Debes tener una mototaxi asignada para subir su fotografía. Comunícate con la administración municipal o tu empresa de transportes.
                </div>
            <?php endif; ?>

            <!-- ESPECIFICACIONES TÉCNICAS DE LA MOTO -->
            <div style="background: #1e293b; padding: 15px; border-radius: 8px; margin-top: auto;">
                <h4 style="margin: 0 0 8px 0; font-size: 0.95rem; color: #10b981;">Ficha Técnica Registrada</h4>
                <div style="font-size: 0.85rem; color: #cbd5e1; line-height: 1.6; display: grid; grid-template-columns: 1fr 1fr; gap: 5px;">
                    <div>Color: <strong><?= htmlspecialchars($vehiculo['color'] ?? '-') ?></strong></div>
                    <div>Año Fab.: <strong><?= htmlspecialchars($vehiculo['anio_fabricacion'] ?? '-') ?></strong></div>
                    <div>N° Motor: <strong><?= htmlspecialchars($vehiculo['numero_motor'] ?? '-') ?></strong></div>
                    <div>N° Chasis: <strong><?= htmlspecialchars($vehiculo['numero_chasis'] ?? '-') ?></strong></div>
                    <div style="grid-column: span 2;">Propietario: <strong><?= htmlspecialchars(($vehiculo['propietario_nombres'] ?? '') . ' ' . ($vehiculo['propietario_apellidos'] ?? '')) ?: 'Registrado' ?></strong></div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- SCRIPTS DE PREVISUALIZACIÓN -->
<script>
    function previsualizarFotoPersona(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('previewPersonaImg');
                const ph = document.getElementById('placeholderPersona');
                img.src = e.target.result;
                img.style.display = 'block';
                if (ph) ph.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previsualizarFotoMoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('previewMotoImg');
                const ph = document.getElementById('placeholderMoto');
                img.src = e.target.result;
                img.style.display = 'block';
                if (ph) ph.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function toggleEditarLicencia() {
        const info = document.getElementById('infoLicenciaBox');
        const form = document.getElementById('formLicenciaBox');
        if (form.style.display === 'none') {
            form.style.display = 'block';
            info.style.display = 'none';
        } else {
            form.style.display = 'none';
            info.style.display = 'block';
        }
    }
</script>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>

