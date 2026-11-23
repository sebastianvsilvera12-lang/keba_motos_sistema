<?php
$tituloPagina = 'Subir y Consultar Documentos - Taxistas';
$subtituloNavbar = 'Gestión Documental de la Unidad';
$menuActivo = 'documentos_taxista';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';

require_once dirname(dirname(__DIR__)) . '/modelos/Taxista.php';

$usuario = obtenerUsuarioActual();
$taxistaMod = new Taxista();

$ficha = $taxistaMod->obtenerFichaTaxista($usuario['id']);
$conductor = $ficha['conductor'] ?? null;
$vehiculo = $ficha['vehiculo'] ?? null;
$documentos = $ficha['documentos'] ?? [];
$vehiculosDisponibles = $taxistaMod->listarVehiculosActivos();

// Si el usuario no tiene vehículo asignado pero hay vehículos disponibles, tomar el primero como referencia visual
if (!$vehiculo && !empty($vehiculosDisponibles)) {
    $vehiculo = $vehiculosDisponibles[0];
}

// Determinar el nombre completo del remitente / taxista
$nombreTaxista = trim(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellidos'] ?? ''));
if (empty($nombreTaxista) && !empty($conductor)) {
    $nombreTaxista = trim(($conductor['nombres'] ?? '') . ' ' . ($conductor['apellidos'] ?? ''));
}
if (empty($nombreTaxista)) {
    $nombreTaxista = $usuario['usuario'] ?? 'Conductor Taxista';
}
$dniTaxista = $conductor['dni'] ?? ($usuario['dni'] ?? '');

// Verificar documentos ya subidos para el checklist de moto propia
$tieneLicencia = false;
$tieneSoat = false;
$tieneTarjetaPropiedad = false;
$totalDocs = count($documentos);
$vigentes = 0;
$porVencer = 0;
$vencidos = 0;

foreach ($documentos as $d) {
    $tipoNorm = strtoupper($d['tipo_documento'] ?? '');
    if (strpos($tipoNorm, 'LICENCIA') !== false) {
        $tieneLicencia = true;
    }
    if (strpos($tipoNorm, 'SOAT') !== false) {
        $tieneSoat = true;
    }
    if (strpos($tipoNorm, 'PROPIEDAD') !== false) {
        $tieneTarjetaPropiedad = true;
    }

    if ($d['estado'] === 'Vencido' || $d['dias_para_vencer'] < 0) {
        $vencidos++;
    } elseif ($d['estado'] === 'Por Vencer' || $d['dias_para_vencer'] <= 30) {
        $porVencer++;
    } else {
        $vigentes++;
    }
}

// Determinar modalidad inicial: si el vehículo asignado es de la empresa (o por defecto si es_empresa = 1)
$esEmpresaInicial = !isset($vehiculo['es_empresa']) || (int)$vehiculo['es_empresa'] === 1;
?>

<style>
/* Estilos modernos para selector de modalidad y apartados */
.modalidad-switcher {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 20px;
}
.tab-modalidad {
    background: #ffffff;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 16px;
    cursor: pointer;
    text-align: left;
    display: flex;
    align-items: center;
    gap: 14px;
    transition: all 0.25s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.tab-modalidad:hover {
    border-color: #3b82f6;
    background: #f8fafc;
    transform: translateY(-2px);
}
.tab-modalidad.active.empresa {
    border-color: #2563eb;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
}
.tab-modalidad.active.propia {
    border-color: #d97706;
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    box-shadow: 0 4px 12px rgba(217, 119, 6, 0.15);
}
.tab-modalidad .tab-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.tab-modalidad#tabEmpresa .tab-icon {
    background: #eff6ff;
    color: #2563eb;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.12);
}
.tab-modalidad#tabPropia .tab-icon {
    background: #fffbeb;
    color: #d97706;
    box-shadow: 0 2px 6px rgba(217, 119, 6, 0.12);
}
.tab-modalidad.active.empresa .tab-icon {
    background: #2563eb;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
}
.tab-modalidad.active.propia .tab-icon {
    background: #d97706;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(217, 119, 6, 0.35);
}
.tab-modalidad .tab-icon svg {
    width: 24px;
    height: 24px;
    transition: transform 0.25s ease;
}
.tab-modalidad:hover .tab-icon svg {
    transform: scale(1.08);
}

/* Botones rápidos de selección de documento para moto propia */
.btn-tipo-doc {
    background: #ffffff;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 8px;
    cursor: pointer;
    text-align: center;
    transition: all 0.2s ease;
    color: #475569;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.btn-tipo-doc:hover {
    border-color: #3b82f6;
    background: #f8fafc;
    transform: translateY(-2px);
}
.btn-tipo-doc.active {
    border-color: #2563eb;
    background: #eff6ff;
    color: #1e40af;
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.15);
}
.tab-modalidad .tab-title {
    font-weight: 700;
    font-size: 0.95rem;
    color: #0f172a;
    line-height: 1.2;
}
.tab-modalidad .tab-sub {
    font-size: 0.78rem;
    color: #64748b;
    margin-top: 3px;
    line-height: 1.3;
}

/* Card Apartado Moto Empresa */
.apartado-empresa-box {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    color: #ffffff;
    border-radius: 12px;
    padding: 18px 20px;
    margin-bottom: 20px;
    border: 1px solid #334155;
    box-shadow: 0 6px 16px rgba(15, 23, 42, 0.25);
    position: relative;
    overflow: hidden;
}
.apartado-empresa-box::after {
    content: '';
    position: absolute;
    right: -20px;
    bottom: -20px;
    width: 130px;
    height: 130px;
    background: radial-gradient(circle, rgba(59,130,246,0.2) 0%, transparent 70%);
    pointer-events: none;
}
.apartado-badge-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
    flex-wrap: wrap;
    gap: 8px;
}
.badge-flota-keba {
    background: #2563eb;
    color: #ffffff;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    padding: 4px 10px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.specs-moto-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 12px;
    background: rgba(255, 255, 255, 0.05);
    padding: 14px;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}
.spec-box {
    display: flex;
    flex-direction: column;
}
.spec-box .lbl {
    font-size: 0.72rem;
    color: #94a3b8;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}
.spec-box .val {
    font-size: 0.95rem;
    font-weight: 700;
    color: #f8fafc;
}

/* Badges de estado mecánico y limpieza */
.badge-mecanico-ok {
    background: #059669;
    color: #ffffff;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 600;
    display: inline-block;
}
.badge-mecanico-obs {
    background: #d97706;
    color: #ffffff;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 600;
    display: inline-block;
}
.badge-limpieza-ok {
    background: #0284c7;
    color: #ffffff;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.78rem;
    font-weight: 600;
    display: inline-block;
}

/* Banner de regla empresa */
.alerta-regla-empresa {
    margin-top: 14px;
    background: rgba(37, 99, 235, 0.15);
    border-left: 4px solid #3b82f6;
    padding: 10px 14px;
    border-radius: 6px;
    font-size: 0.83rem;
    line-height: 1.45;
    color: #cbd5e1;
}
.alerta-regla-empresa strong {
    color: #ffffff;
}

/* Card Apartado Moto Propia */
.apartado-propia-box {
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
    border: 1px solid #fde68a;
    border-radius: 12px;
    padding: 18px 20px;
    margin-bottom: 20px;
    box-shadow: 0 4px 10px rgba(217, 119, 6, 0.08);
}
.checklist-propia-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 10px;
    margin-top: 12px;
}
.check-doc-item {
    background: #ffffff;
    border-radius: 8px;
    padding: 10px 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.check-doc-item.cumplido {
    border-color: #10b981;
    background: #f0fdf4;
}
.check-doc-item.pendiente {
    border-color: #f59e0b;
    background: #fffbeb;
}
.check-doc-item .doc-estado-badge {
    font-size: 0.72rem;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
    display: inline-block;
    margin-bottom: 4px;
}
.check-doc-item.cumplido .doc-estado-badge {
    background: #dcfce7;
    color: #15803d;
}
.check-doc-item.pendiente .doc-estado-badge {
    background: #fef3c7;
    color: #b45309;
}
.check-doc-item .doc-nombre {
    font-weight: 700;
    font-size: 0.85rem;
    color: #0f172a;
    display: block;
}
.check-doc-item .doc-desc {
    font-size: 0.73rem;
    color: #64748b;
    display: block;
    margin-top: 2px;
}

@media (max-width: 768px) {
    .modalidad-switcher {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h1 class="page-title">Subir y Consultar Documentos</h1>
        <p class="page-subtitle">Gestión documental diferenciada: Unidad de KEBA MOTOS (Solo Licencia) vs Unidad Propia (SOAT, Licencia y Tarjeta de Propiedad)</p>
    </div>
    <div>
        <a href="<?= vistaUrl('taxista/dashboard.php') ?>" class="btn btn-outline">&larr; Volver al Panel</a>
    </div>
</div>

<!-- TARJETAS DE RESUMEN DE VIGENCIA -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 25px;">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Documentos</h3>
            <div class="stat-number"><?= $totalDocs ?></div>
            <div class="stat-desc">Registrados en la unidad</div>
        </div>
        <div class="stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Vigentes</h3>
            <div class="stat-number" style="color: #10b981;"><?= $vigentes ?></div>
            <div class="stat-desc">En regla para circular</div>
        </div>
        <div class="stat-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Por Vencer</h3>
            <div class="stat-number" style="color: #f59e0b;"><?= $porVencer ?></div>
            <div class="stat-desc">En los próximos 30 días</div>
        </div>
        <div class="stat-icon warning">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Vencidos</h3>
            <div class="stat-number" style="color: #ef4444;"><?= $vencidos ?></div>
            <div class="stat-desc">¡Riesgo de sanción municipal!</div>
        </div>
        <div class="stat-icon danger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
        </div>
    </div>
</div>

<!-- SELECTOR DE CONDICIÓN: MOTO DE LA EMPRESA VS MOTO PROPIA -->
<div class="modalidad-switcher">
    <button type="button" id="tabEmpresa" class="tab-modalidad <?= $esEmpresaInicial ? 'active empresa' : '' ?>" onclick="seleccionarModalidad('empresa')">
        <div class="tab-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"></path>
                <path d="M6 12H4a2 2 0 0 0-2 2v8h4"></path>
                <path d="M18 9h2a2 2 0 0 1 2 2v11h-4"></path>
                <line x1="10" y1="6" x2="14" y2="6"></line>
                <line x1="10" y1="10" x2="14" y2="10"></line>
                <line x1="10" y1="14" x2="14" y2="14"></line>
                <line x1="10" y1="18" x2="14" y2="18"></line>
            </svg>
        </div>
        <div>
            <div class="tab-title">Moto de la Empresa (KEBA MOTOS)</div>
            <div class="tab-sub">Unidad asignada por la empresa. Solo presentas tu <strong>Licencia de Conducir (B-IIc)</strong>.</div>
        </div>
    </button>

    <button type="button" id="tabPropia" class="tab-modalidad <?= !$esEmpresaInicial ? 'active propia' : '' ?>" onclick="seleccionarModalidad('propia')">
        <div class="tab-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="5" cy="16" r="3"></circle>
                <circle cx="19" cy="16" r="3"></circle>
                <path d="M5 16h3l3-5 5 1 2 4h1"></path>
                <path d="M12 6h3l2 4"></path>
                <path d="M8 11l2-5"></path>
            </svg>
        </div>
        <div>
            <div class="tab-title">Moto Propia del Taxista</div>
            <div class="tab-sub">Unidad particular. Debes presentar obligatoriamente <strong>SOAT, Licencia y Tarjeta de Propiedad</strong>.</div>
        </div>
    </button>
</div>

<!-- ========================================================================= -->
<!-- APARTADO 1: INFORMACIÓN DE LA MOTO ASIGNADA POR LA EMPRESA                -->
<!-- ========================================================================= -->
<div id="apartadoEmpresaBox" class="apartado-empresa-box" style="<?= $esEmpresaInicial ? '' : 'display: none;' ?>">
    <div class="apartado-badge-top">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span class="badge-flota-keba">
                <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                UNIDAD OFICIAL ASIGNADA
            </span>
            <span style="font-size: 0.85rem; color: #94a3b8; font-weight: 600;">KEBA MOTOS S.A.C.</span>
        </div>
        <div>
            <span style="font-size: 0.78rem; background: rgba(16, 185, 129, 0.2); color: #34d399; padding: 4px 10px; border-radius: 20px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                <svg style="width: 13px; height: 13px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                SOAT y Tarjeta Gestionados por la Empresa
            </span>
        </div>
    </div>

    <!-- ESPECIFICACIONES DE LA MOTO: MARCA, COLOR, ESTADO -->
    <div class="specs-moto-grid">
        <!-- Marca y Modelo -->
        <div class="spec-box">
            <span class="lbl">Marca / Modelo</span>
            <span class="val" id="dispMarcaModelo">
                <?= htmlspecialchars(($vehiculo['marca'] ?? 'Bajaj') . ' ' . ($vehiculo['modelo'] ?? 'RE 205')) ?>
            </span>
        </div>

        <!-- Color de la moto -->
        <div class="spec-box">
            <span class="lbl">Color de la Moto</span>
            <div class="val" style="display: flex; align-items: center; gap: 6px;">
                <span id="dispColorDot" style="width: 12px; height: 12px; border-radius: 50%; background: #3b82f6; display: inline-block;"></span>
                <span id="dispColor"><?= htmlspecialchars($vehiculo['color'] ?? 'Azul con Negro') ?></span>
            </div>
        </div>

        <!-- Placa y Padrón -->
        <div class="spec-box">
            <span class="lbl">Placa y Padrón</span>
            <span class="val" style="color: #38bdf8;">
                <span id="dispPlaca"><?= htmlspecialchars($vehiculo['placa'] ?? '1234-5B') ?></span>
                <span style="font-size: 0.8rem; color: #94a3b8; font-weight: 500;">(Padrón: <span id="dispPadron"><?= htmlspecialchars($vehiculo['numero_padron'] ?? 'PAD-010') ?></span>)</span>
            </span>
        </div>

        <!-- Estado Mecánico -->
        <div class="spec-box">
            <span class="lbl">Estado Mecánico</span>
            <span class="val">
                <?php 
                    $estMec = $vehiculo['estado_mecanico'] ?? 'Operativo';
                    $badgeMecClass = ($estMec === 'Operativo') ? 'badge-mecanico-ok' : 'badge-mecanico-obs';
                ?>
                <span class="<?= $badgeMecClass ?>" id="dispEstadoMecanico"><?= htmlspecialchars($estMec) ?></span>
            </span>
        </div>

        <!-- Estado de Limpieza -->
        <div class="spec-box">
            <span class="lbl">Estado de Limpieza</span>
            <span class="val">
                <span class="badge-limpieza-ok" id="dispEstadoLimpieza"><?= htmlspecialchars($vehiculo['estado_limpieza'] ?? 'Limpia') ?></span>
            </span>
        </div>
    </div>

    <!-- Aviso normativo de la empresa -->
    <div class="alerta-regla-empresa">
        <div style="display: flex; align-items: flex-start; gap: 10px;">
            <svg style="width: 22px; height: 22px; color: #60a5fa; flex-shrink: 0; margin-top: 2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            <div>
                <strong>Requisito Único para Taxistas con Moto de la Empresa:</strong>
                Dado que esta mototaxi es propiedad de <strong>KEBA MOTOS</strong>, la empresa ya gestiona y mantiene en regla el <strong>SOAT</strong>, la <strong>Tarjeta de Propiedad</strong> y el <strong>TUC</strong>.
                <br>Como conductor autorizado, <strong>únicamente debes subir y mantener vigente tu Licencia de Conducir (Categoría B-IIc)</strong>.
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- APARTADO 2: MOTO PROPIA DEL TAXISTA (REQUISITOS OBLIGATORIOS)            -->
<!-- ========================================================================= -->
<div id="apartadoPropiaBox" class="apartado-propia-box" style="<?= !$esEmpresaInicial ? '' : 'display: none;' ?>">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span style="background: #d97706; color: #ffffff; font-size: 0.72rem; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; padding: 4px 10px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;">
                <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="5" cy="16" r="3"></circle><circle cx="19" cy="16" r="3"></circle><path d="M5 16h3l3-5 5 1 2 4h1"></path></svg>
                UNIDAD PARTICULAR / PROPIA
            </span>
            <strong style="color: #92400e; font-size: 0.95rem;">Requisitos Obligatorios de Fiscalización</strong>
        </div>
        <div>
            <span style="font-size: 0.78rem; color: #78350f; font-weight: 600;">Reglamento Municipal de Transporte</span>
        </div>
    </div>

    <p style="margin: 8px 0 10px 0; font-size: 0.88rem; color: #78350f; line-height: 1.4;">
        Al circular con tu propia mototaxi, la normativa de tránsito y el reglamento interno de KEBA MOTOS exige presentar <strong>obligatoriamente los 3 documentos vehiculares</strong>:
    </p>

    <!-- Checklist de los 3 documentos obligatorios -->
    <div class="checklist-propia-grid">
        <!-- 1. SOAT -->
        <div class="check-doc-item <?= $tieneSoat ? 'cumplido' : 'pendiente' ?>">
            <span class="doc-estado-badge">
                <?php if ($tieneSoat): ?>
                    <svg style="width: 11px; height: 11px; display: inline-block; vertical-align: -1px; margin-right: 2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> PRESENTADO
                <?php else: ?>
                    <svg style="width: 11px; height: 11px; display: inline-block; vertical-align: -1px; margin-right: 2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> PENDIENTE
                <?php endif; ?>
            </span>
            <span class="doc-nombre">1. SOAT Vigente</span>
            <span class="doc-desc">Póliza obligatoria de accidentes</span>
        </div>

        <!-- 2. Licencia de Conducir -->
        <div class="check-doc-item <?= $tieneLicencia ? 'cumplido' : 'pendiente' ?>">
            <span class="doc-estado-badge">
                <?php if ($tieneLicencia): ?>
                    <svg style="width: 11px; height: 11px; display: inline-block; vertical-align: -1px; margin-right: 2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> PRESENTADO
                <?php else: ?>
                    <svg style="width: 11px; height: 11px; display: inline-block; vertical-align: -1px; margin-right: 2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> PENDIENTE
                <?php endif; ?>
            </span>
            <span class="doc-nombre">2. Licencia B-IIc</span>
            <span class="doc-desc">Brevete oficial vigente del conductor</span>
        </div>

        <!-- 3. Tarjeta de Propiedad -->
        <div class="check-doc-item <?= $tieneTarjetaPropiedad ? 'cumplido' : 'pendiente' ?>">
            <span class="doc-estado-badge">
                <?php if ($tieneTarjetaPropiedad): ?>
                    <svg style="width: 11px; height: 11px; display: inline-block; vertical-align: -1px; margin-right: 2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> PRESENTADO
                <?php else: ?>
                    <svg style="width: 11px; height: 11px; display: inline-block; vertical-align: -1px; margin-right: 2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg> PENDIENTE
                <?php endif; ?>
            </span>
            <span class="doc-nombre">3. Tarjeta Propiedad</span>
            <span class="doc-desc">Identificación Vehicular (TIV SUNARP)</span>
        </div>
    </div>
</div>

<div class="row" style="margin-bottom: 30px;">
    <!-- ===================================================================== -->
    <!-- FORMULARIO INTELIGENTE PARA SUBIR DOCUMENTOS                          -->
    <!-- ===================================================================== -->
    <div class="col-12 col-lg-5" style="margin-bottom: 25px;">
        <div class="card" style="box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);">
            <div style="border-bottom: 1px solid #334155; padding-bottom: 12px; margin-bottom: 18px;">
                <h2 class="card-title" id="formTitulo" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                    <svg style="width: 22px; height: 22px; color: #2563eb;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    Subir Documento (Imagen o PDF)
                </h2>
                <p class="card-subtitle" style="margin: 4px 0 0 0; color: #64748b; font-size: 0.85rem;">
                    Adjunta directamente la foto o documento PDF de tu brevete o póliza sin trámites extensos
                </p>
            </div>

            <form action="<?= controladorUrl('taxista_controlador.php') ?>" method="POST" enctype="multipart/form-data" id="formSubirDoc">
                <input type="hidden" name="accion" value="subir_documento">
                <input type="hidden" name="tipo_propiedad" id="inputTipoPropiedad" value="<?= $esEmpresaInicial ? 'empresa' : 'propia' ?>">
                <input type="hidden" name="remitente_nombre" value="<?= htmlspecialchars($nombreTaxista) ?>">
                <input type="hidden" name="tipo_documento" id="tipoDocFinal" value="<?= $esEmpresaInicial ? 'Licencia de Conducir' : 'SOAT' ?>">

                <!-- 1. IDENTIFICACIÓN DEL CONDUCTOR (BADGE INFORMATIVO SIN CAMPOS DE TEXTO) -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 14px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <svg style="width: 18px; height: 18px; color: #2563eb;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        <span style="font-size: 0.88rem; font-weight: 700; color: #0f172a;"><?= htmlspecialchars($nombreTaxista) ?></span>
                    </div>
                    <?php if (!empty($dniTaxista)): ?>
                        <span style="font-size: 0.78rem; color: #475569; font-weight: 600; background: #ffffff; padding: 2px 8px; border-radius: 4px; border: 1px solid #cbd5e1;">
                            DNI: <?= htmlspecialchars($dniTaxista) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- 2. MOTOTAXI ASIGNADA / SELECCIONADA -->
                <div class="form-group" style="margin-bottom: 16px;">
                    <label for="idVehiculo" style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px; display: block;">Mototaxi *</label>
                    <?php if (!empty($vehiculosDisponibles)): ?>
                        <select name="id_vehiculo" id="idVehiculo" class="form-control" required style="font-weight: 600;" onchange="actualizarVehiculoSeleccionado(this)">
                            <?php foreach ($vehiculosDisponibles as $v): 
                                $esSel = ($vehiculo && $vehiculo['id_vehiculo'] == $v['id_vehiculo']);
                                $esEmp = !empty($v['es_empresa']) && $v['es_empresa'] == 1;
                            ?>
                                <option value="<?= (int)$v['id_vehiculo'] ?>" 
                                        <?= $esSel ? 'selected' : '' ?>
                                        data-marca="<?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?>"
                                        data-color="<?= htmlspecialchars($v['color'] ?? 'Azul con Negro') ?>"
                                        data-placa="<?= htmlspecialchars($v['placa']) ?>"
                                        data-padron="<?= htmlspecialchars($v['numero_padron'] ?? '-') ?>"
                                        data-mecanico="<?= htmlspecialchars($v['estado_mecanico'] ?? 'Operativo') ?>"
                                        data-limpieza="<?= htmlspecialchars($v['estado_limpieza'] ?? 'Limpia') ?>"
                                        data-es-empresa="<?= $esEmp ? '1' : '0' ?>">
                                    Placa: <?= htmlspecialchars($v['placa']) ?> - <?= htmlspecialchars($v['marca'] . ' ' . $v['modelo']) ?> (Padrón: <?= htmlspecialchars($v['numero_padron'] ?? '-') ?>) <?= $esEmp ? '★ [KEBA MOTOS]' : '[Particular]' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <div class="alert alert-warning">No hay mototaxis registradas en el sistema.</div>
                    <?php endif; ?>
                </div>

                <!-- 3. DOCUMENTO A SUBIR (BOTONES SIMPLES SIN TEXTOS INNECESARIOS) -->
                <div class="form-group" style="margin-bottom: 18px;">
                    <label style="font-weight: 600; font-size: 0.9rem; margin-bottom: 8px; display: block;">
                        Documento que vas a subir *
                    </label>
                    
                    <!-- Para Moto Empresa: Fijo Licencia -->
                    <div id="contenedorDocEmpresa" style="<?= $esEmpresaInicial ? '' : 'display: none;' ?>">
                        <div style="background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 8px; padding: 12px 14px; display: flex; align-items: center; gap: 12px;">
                            <div style="width: 38px; height: 38px; border-radius: 8px; background: #2563eb; color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><line x1="15" y1="8" x2="17" y2="8"></line><line x1="15" y1="12" x2="17" y2="12"></line><line x1="7" y1="16" x2="17" y2="16"></line></svg>
                            </div>
                            <div>
                                <strong style="color: #1e3a8a; font-size: 0.95rem; display: block;">Licencia de Conducir (B-IIc)</strong>
                                <span style="color: #3b82f6; font-size: 0.8rem;">Documento requerido para conducir la unidad de KEBA MOTOS</span>
                            </div>
                        </div>
                    </div>

                    <!-- Para Moto Propia: Selector de los 3 documentos obligatorios con un click -->
                    <div id="contenedorDocPropia" style="<?= !$esEmpresaInicial ? '' : 'display: none;' ?>">
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;" id="grupoBtnsDocPropia">
                            <button type="button" class="btn-tipo-doc active" data-tipo="SOAT" onclick="elegirDocPropia('SOAT', this)">
                                <div style="font-size: 1.2rem; margin-bottom: 2px;">🛡️</div>
                                <strong style="font-size: 0.82rem; display: block;">SOAT</strong>
                            </button>
                            <button type="button" class="btn-tipo-doc" data-tipo="Licencia de Conducir" onclick="elegirDocPropia('Licencia de Conducir', this)">
                                <div style="font-size: 1.2rem; margin-bottom: 2px;">🪪</div>
                                <strong style="font-size: 0.82rem; display: block;">Licencia</strong>
                            </button>
                            <button type="button" class="btn-tipo-doc" data-tipo="Tarjeta de Propiedad" onclick="elegirDocPropia('Tarjeta de Propiedad', this)">
                                <div style="font-size: 1.2rem; margin-bottom: 2px;">📄</div>
                                <strong style="font-size: 0.82rem; display: block;">T. Propiedad</strong>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 4. SUBIDA DIRECTA DE FOTO O PDF (DROPZONE VISUAL) -->
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="font-weight: 600; font-size: 0.9rem; margin-bottom: 6px; display: block;">
                        Selecciona o toma foto del Documento (Imagen o PDF) *
                    </label>

                    <div id="dropzoneBox" style="border: 2px dashed #93c5fd; border-radius: 12px; padding: 26px 16px; text-align: center; background: #f8fafc; cursor: pointer; transition: all 0.25s ease;">
                        <input type="file" name="archivo_documento" id="archivoDoc" accept="image/*,.pdf" required style="display: none;">

                        <!-- Estado inicial -->
                        <div id="dropzonePrompt">
                            <div style="width: 52px; height: 52px; margin: 0 auto 10px; border-radius: 50%; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #2563eb;">
                                <svg style="width: 26px; height: 26px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                            </div>
                            <div style="font-weight: 700; color: #0f172a; font-size: 0.98rem;">Toca aquí para tomar foto o elegir archivo</div>
                            <div style="color: #64748b; font-size: 0.82rem; margin-top: 4px;">
                                Acepta fotos <strong>(JPG, PNG, WEBP)</strong> o documento <strong>PDF</strong>
                            </div>
                            <div style="margin-top: 12px;">
                                <span class="btn btn-sm btn-outline" style="pointer-events: none; font-size: 0.82rem; font-weight: 600; border-radius: 6px;">
                                    📁 Seleccionar Archivo
                                </span>
                            </div>
                        </div>

                        <!-- Estado con vista previa de imagen o PDF -->
                        <div id="dropzonePreview" style="display: none; flex-direction: column; align-items: center; gap: 12px;">
                            <div style="position: relative; max-width: 100%;">
                                <img id="previewImg" src="" alt="Vista previa" style="max-height: 220px; max-width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; object-fit: contain; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: none;">
                                <div id="previewPdf" style="display: none; padding: 22px 28px; background: #ffffff; border-radius: 8px; border: 1px solid #cbd5e1; align-items: center; gap: 14px; box-shadow: 0 4px 10px rgba(0,0,0,0.06);">
                                    <svg style="width: 42px; height: 42px; color: #dc2626;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                    <div style="text-align: left;">
                                        <div id="pdfTitle" style="font-weight: 700; color: #991b1b; font-size: 0.95rem;">Documento PDF</div>
                                        <div style="font-size: 0.78rem; color: #16a34a; font-weight: 600;">✓ Listo para subir</div>
                                    </div>
                                </div>
                            </div>
                            <div id="previewFileInfo" style="color: #0f172a; font-size: 0.85rem; font-weight: 700; word-break: break-all;"></div>
                            <button type="button" id="btnCambiarFile" class="btn btn-sm btn-outline" style="font-size: 0.8rem;">
                                🔄 Cambiar archivo seleccionado
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" id="btnSubmitDoc" class="btn btn-primary" style="width: 100%; padding: 13px; font-weight: 700; font-size: 1rem; display: flex; align-items: center; justify-content: center; gap: 10px; border-radius: 8px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);">
                    <svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                    <span id="btnSubmitTexto">Subir Documento (Imagen o PDF)</span>
                </button>
            </form>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- LISTADO DE DOCUMENTOS DE LA UNIDAD                                   -->
    <!-- ===================================================================== -->
    <div class="col-12 col-lg-7">
        <div class="card" style="box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);">
            <div style="border-bottom: 1px solid #334155; padding-bottom: 12px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h2 class="card-title" style="margin: 0;">Documentos Registrados</h2>
                    <p class="card-subtitle" style="margin: 4px 0 0 0; color: #64748b; font-size: 0.85rem;">
                        Mototaxi: <strong><?= htmlspecialchars($vehiculo ? $vehiculo['placa'] . ' (' . $vehiculo['marca'] . ' ' . $vehiculo['modelo'] . ')' : 'Sin unidad asignada') ?></strong>
                    </p>
                </div>
                <div>
                    <span class="badge badge-info" style="font-size: 0.8rem; padding: 6px 10px;">
                        <?= $totalDocs ?> archivo(s)
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>N° Documento</th>
                            <th>Remitente</th>
                            <th>Emisor</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th>Archivo / Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($documentos)): ?>
                            <?php foreach ($documentos as $doc): 
                                $badgeClass = 'badge-success';
                                $estadoTexto = 'Vigente';
                                if ($doc['estado'] === 'Vencido' || $doc['dias_para_vencer'] < 0) {
                                    $badgeClass = 'badge-danger';
                                    $estadoTexto = 'Vencido (' . abs($doc['dias_para_vencer']) . ' d)';
                                } elseif ($doc['estado'] === 'Por Vencer' || $doc['dias_para_vencer'] <= 30) {
                                    $badgeClass = 'badge-warning';
                                    $estadoTexto = 'Por vencer (' . $doc['dias_para_vencer'] . ' d)';
                                } else {
                                    $estadoTexto = 'Vigente (' . $doc['dias_para_vencer'] . ' d)';
                                }

                                $tieneArchivo = !empty($doc['archivo_digital']) && file_exists(ROOT_PATH . 'subidas/documentos/' . $doc['archivo_digital']);
                                $archivoUrl = $tieneArchivo ? subidaUrl('documentos/' . $doc['archivo_digital']) : '';
                                $esImagen = $tieneArchivo && preg_match('/\.(jpg|jpeg|png|webp)$/i', $doc['archivo_digital']);
                                $remitente = !empty($doc['remitente_nombre']) ? $doc['remitente_nombre'] : $nombreTaxista;
                            ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 7px;">
                                        <?php if (stripos($doc['tipo_documento'], 'Licencia') !== false): ?>
                                            <span style="color: #2563eb; display: inline-flex;">
                                                <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><line x1="15" y1="8" x2="17" y2="8"></line><line x1="15" y1="12" x2="17" y2="12"></line><line x1="7" y1="16" x2="17" y2="16"></line></svg>
                                            </span>
                                        <?php elseif (stripos($doc['tipo_documento'], 'SOAT') !== false): ?>
                                            <span style="color: #059669; display: inline-flex;">
                                                <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                            </span>
                                        <?php elseif (stripos($doc['tipo_documento'], 'Propiedad') !== false): ?>
                                            <span style="color: #d97706; display: inline-flex;">
                                                <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: #64748b; display: inline-flex;">
                                                <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                            </span>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($doc['tipo_documento']) ?>
                                    </div>
                                    <?php if (!empty($doc['observaciones'])): ?>
                                        <div style="font-size: 0.75rem; color: #64748b; max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($doc['observaciones']) ?>">
                                            <?= htmlspecialchars($doc['observaciones']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="font-family: monospace; font-size: 0.85rem; font-weight: 600; color: #334155;"><?= htmlspecialchars($doc['numero_documento']) ?></span>
                                </td>
                                <td>
                                    <div style="font-size: 0.82rem; font-weight: 600; color: #0f172a; display: flex; align-items: center; gap: 4px;">
                                        <svg style="width: 14px; height: 14px; flex-shrink: 0; color: #64748b;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        <span title="<?= htmlspecialchars($remitente) ?>"><?= htmlspecialchars($remitente) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-size: 0.8rem; color: #94a3b8;"><?= htmlspecialchars($doc['entidad_emisora'] ?? '-') ?></span>
                                </td>
                                <td>
                                    <div style="font-weight: 600;"><?= htmlspecialchars(date('d/m/Y', strtotime($doc['fecha_vencimiento']))) ?></div>
                                    <div style="font-size: 0.72rem; color: #64748b;">Emisión: <?= htmlspecialchars(date('d/m/Y', strtotime($doc['fecha_emision']))) ?></div>
                                </td>
                                <td>
                                    <span class="badge <?= $badgeClass ?>" style="white-space: nowrap; font-size: 0.75rem;"><?= htmlspecialchars($estadoTexto) ?></span>
                                </td>
                                <td>
                                    <?php if ($tieneArchivo): ?>
                                        <?php if ($esImagen): ?>
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <img src="<?= $archivoUrl ?>" alt="Foto documento" 
                                                     class="doc-thumb" 
                                                     data-img-src="<?= $archivoUrl ?>"
                                                     data-img-title="<?= htmlspecialchars($doc['tipo_documento'] . ' - ' . $doc['numero_documento']) ?>"
                                                     data-remitente="<?= htmlspecialchars($remitente) ?>"
                                                     style="width: 44px; height: 44px; object-fit: cover; border-radius: 6px; border: 1px solid #334155; cursor: pointer; transition: transform 0.2s;"
                                                     onmouseover="this.style.transform='scale(1.1)';" 
                                                     onmouseout="this.style.transform='scale(1)';"
                                                     title="Haz clic para ampliar la foto">
                                                <a href="<?= $archivoUrl ?>" target="_blank" class="btn btn-outline btn-sm" style="padding: 3px 8px; font-size: 0.75rem;" title="Abrir imagen completa">
                                                    Ver
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <a href="<?= $archivoUrl ?>" target="_blank" class="btn btn-outline btn-sm" style="padding: 4px 10px; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px; color: #ef4444; border-color: #ef4444;">
                                                <svg style="width: 14px; height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                                PDF
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span style="color: #64748b; font-size: 0.78rem;">Sin archivo</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: #94a3b8; padding: 35px 20px;">
                                    <svg style="width: 40px; height: 40px; color: #475569; margin-bottom: 10px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                    <div>No tienes documentos subidos aún.</div>
                                    <div style="font-size: 0.8rem; color: #64748b; margin-top: 5px;">Utiliza el formulario de la izquierda para registrar tu Licencia de Conducir o documentos de tu moto.</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Recomendación de fiscalización -->
            <div style="margin-top: 20px; padding: 15px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; font-size: 0.85rem; color: #475569;">
                <div style="display: flex; gap: 10px; align-items: flex-start;">
                    <svg style="width: 22px; height: 22px; color: #d97706; flex-shrink: 0; margin-top: 2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <div>
                        <strong style="color: #0f172a; display: block; margin-bottom: 3px;">Prevención de Infracciones y Retención de Unidad:</strong>
                        Verifica que los documentos cargados sean legibles y con fecha de vencimiento visible. Conducir con Licencia vencida o sin SOAT en unidades particulares ocasiona <strong>internamiento en depósito municipal y multas graves</strong>.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL VISOR DE FOTOS DE DOCUMENTOS -->
<div id="modalVisorDoc" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #1e293b; border-radius: 12px; max-width: 750px; width: 100%; max-height: 90vh; overflow: hidden; display: flex; flex-direction: column; border: 1px solid #334155; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5);">
        <div style="padding: 15px 20px; border-bottom: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 id="modalDocTitulo" style="margin: 0; font-size: 1.1rem; color: #f1f5f9;">Documento</h3>
                <div id="modalDocRemitente" style="font-size: 0.8rem; color: #38bdf8; margin-top: 2px;">Remitente: Conductor</div>
            </div>
            <button type="button" id="btnCerrarModal" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer; line-height: 1; padding: 5px;">&times;</button>
        </div>
        <div style="padding: 20px; overflow-y: auto; text-align: center; background: #0b1120;">
            <img id="modalDocImg" src="" alt="Documento ampliado" style="max-width: 100%; max-height: 65vh; object-fit: contain; border-radius: 8px; border: 1px solid #334155;">
        </div>
        <div style="padding: 12px 20px; border-top: 1px solid #334155; display: flex; justify-content: flex-end; gap: 10px; background: #1e293b;">
            <a id="modalDocLink" href="#" target="_blank" class="btn btn-primary btn-sm">Abrir en Pestaña Completa</a>
            <button type="button" id="btnCerrarModal2" class="btn btn-outline btn-sm">Cerrar</button>
        </div>
    </div>
</div>

<script>
// Modalidad actual: 'empresa' o 'propia'
let modalidadActual = '<?= $esEmpresaInicial ? 'empresa' : 'propia' ?>';

function seleccionarModalidad(tipo) {
    modalidadActual = tipo;
    const tabEmpresa = document.getElementById('tabEmpresa');
    const tabPropia = document.getElementById('tabPropia');
    const boxEmpresa = document.getElementById('apartadoEmpresaBox');
    const boxPropia = document.getElementById('apartadoPropiaBox');
    const inputTipoPropiedad = document.getElementById('inputTipoPropiedad');
    const contenedorDocEmpresa = document.getElementById('contenedorDocEmpresa');
    const contenedorDocPropia = document.getElementById('contenedorDocPropia');
    const tipoDocFinal = document.getElementById('tipoDocFinal');
    const btnSubmitTexto = document.getElementById('btnSubmitTexto');

    inputTipoPropiedad.value = tipo;

    if (tipo === 'empresa') {
        tabEmpresa.classList.add('active', 'empresa');
        tabPropia.classList.remove('active', 'propia');
        boxEmpresa.style.display = 'block';
        boxPropia.style.display = 'none';
        if (contenedorDocEmpresa) contenedorDocEmpresa.style.display = 'block';
        if (contenedorDocPropia) contenedorDocPropia.style.display = 'none';
        if (tipoDocFinal) tipoDocFinal.value = 'Licencia de Conducir';
        if (btnSubmitTexto) btnSubmitTexto.textContent = 'Subir Licencia de Conducir (Imagen o PDF)';
    } else {
        tabPropia.classList.add('active', 'propia');
        tabEmpresa.classList.remove('active', 'empresa');
        boxEmpresa.style.display = 'none';
        boxPropia.style.display = 'block';
        if (contenedorDocEmpresa) contenedorDocEmpresa.style.display = 'none';
        if (contenedorDocPropia) contenedorDocPropia.style.display = 'block';

        const activeBtn = document.querySelector('.btn-tipo-doc.active');
        const tipoSel = activeBtn ? activeBtn.getAttribute('data-tipo') : 'SOAT';
        if (tipoDocFinal) tipoDocFinal.value = tipoSel;
        if (btnSubmitTexto) btnSubmitTexto.textContent = 'Subir ' + tipoSel + ' (Imagen o PDF)';
    }
}

function elegirDocPropia(tipo, btn) {
    document.querySelectorAll('.btn-tipo-doc').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const tipoDocFinal = document.getElementById('tipoDocFinal');
    const btnSubmitTexto = document.getElementById('btnSubmitTexto');
    if (tipoDocFinal) tipoDocFinal.value = tipo;
    if (btnSubmitTexto) btnSubmitTexto.textContent = 'Subir ' + tipo + ' (Imagen o PDF)';
}

function actualizarVehiculoSeleccionado(select) {
    const opt = select.options[select.selectedIndex];
    if (!opt) return;

    const marca = opt.getAttribute('data-marca') || 'Bajaj RE 205';
    const color = opt.getAttribute('data-color') || 'Azul con Negro';
    const placa = opt.getAttribute('data-placa') || '';
    const padron = opt.getAttribute('data-padron') || '-';
    const mecanico = opt.getAttribute('data-mecanico') || 'Operativo';
    const limpieza = opt.getAttribute('data-limpieza') || 'Limpia';
    const esEmpresa = opt.getAttribute('data-es-empresa') === '1';

    // Actualizar datos visibles en el apartado de moto empresa
    document.getElementById('dispMarcaModelo').textContent = marca;
    document.getElementById('dispColor').textContent = color;
    document.getElementById('dispPlaca').textContent = placa;
    document.getElementById('dispPadron').textContent = padron;

    const elMec = document.getElementById('dispEstadoMecanico');
    if (elMec) {
        elMec.textContent = mecanico;
        elMec.className = (mecanico === 'Operativo') ? 'badge-mecanico-ok' : 'badge-mecanico-obs';
    }

    const elLimp = document.getElementById('dispEstadoLimpieza');
    if (elLimp) {
        elLimp.textContent = limpieza;
    }

    // Cambiar punto de color según el texto del color
    const dot = document.getElementById('dispColorDot');
    if (dot) {
        const cLower = color.toLowerCase();
        if (cLower.includes('azul')) dot.style.background = '#2563eb';
        else if (cLower.includes('rojo')) dot.style.background = '#dc2626';
        else if (cLower.includes('verde')) dot.style.background = '#16a34a';
        else if (cLower.includes('amarillo')) dot.style.background = '#ca8a04';
        else if (cLower.includes('blanco')) dot.style.background = '#f8fafc';
        else dot.style.background = '#64748b';
    }

    // Sincronizar automáticamente la pestaña si la moto seleccionada es de la empresa o no
    if (esEmpresa && modalidadActual !== 'empresa') {
        seleccionarModalidad('empresa');
    } else if (!esEmpresa && modalidadActual !== 'propia') {
        seleccionarModalidad('propia');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Sincronizar estado inicial
    seleccionarModalidad(modalidadActual);

    // Inicializar punto de color
    const selVeh = document.getElementById('idVehiculo');
    if (selVeh) {
        actualizarVehiculoSeleccionado(selVeh);
    }

    // Lógica para dropzone de archivo / foto
    const dropzoneBox = document.getElementById('dropzoneBox');
    const archivoInput = document.getElementById('archivoDoc');
    const dropzonePrompt = document.getElementById('dropzonePrompt');
    const dropzonePreview = document.getElementById('dropzonePreview');
    const previewImg = document.getElementById('previewImg');
    const previewPdf = document.getElementById('previewPdf');
    const pdfTitle = document.getElementById('pdfTitle');
    const previewFileInfo = document.getElementById('previewFileInfo');
    const btnCambiarFile = document.getElementById('btnCambiarFile');

    if (dropzoneBox && archivoInput) {
        dropzoneBox.addEventListener('click', function(e) {
            if (e.target !== btnCambiarFile) {
                archivoInput.click();
            }
        });

        if (btnCambiarFile) {
            btnCambiarFile.addEventListener('click', function(e) {
                e.stopPropagation();
                archivoInput.click();
            });
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropzoneBox.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzoneBox.style.borderColor = '#10b981';
                dropzoneBox.style.background = '#022c22';
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzoneBox.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzoneBox.style.borderColor = '#3b82f6';
                dropzoneBox.style.background = '#0f172a';
            }, false);
        });

        dropzoneBox.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length > 0) {
                archivoInput.files = dt.files;
                procesarArchivo(dt.files[0]);
            }
        });

        archivoInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                procesarArchivo(this.files[0]);
            }
        });
    }

    function procesarArchivo(file) {
        const fileName = file.name;
        const fileSizeKB = (file.size / 1024).toFixed(1);
        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
        const sizeText = file.size > 1024 * 1024 ? fileSizeMB + ' MB' : fileSizeKB + ' KB';
        const fileType = file.type;

        previewFileInfo.textContent = fileName + ' (' + sizeText + ')';
        dropzonePrompt.style.display = 'none';
        dropzonePreview.style.display = 'flex';

        if (fileType.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                previewPdf.style.display = 'none';
            };
            reader.readAsDataURL(file);
        } else if (fileType === 'application/pdf' || fileName.toLowerCase().endsWith('.pdf')) {
            previewImg.style.display = 'none';
            previewPdf.style.display = 'flex';
            pdfTitle.textContent = fileName;
        } else {
            previewImg.style.display = 'none';
            previewPdf.style.display = 'flex';
            pdfTitle.textContent = fileName;
        }
    }

    // Modal para visor de imágenes de documentos
    const modalVisor = document.getElementById('modalVisorDoc');
    const modalTitulo = document.getElementById('modalDocTitulo');
    const modalRemitente = document.getElementById('modalDocRemitente');
    const modalImg = document.getElementById('modalDocImg');
    const modalLink = document.getElementById('modalDocLink');
    const btnCerrar1 = document.getElementById('btnCerrarModal');
    const btnCerrar2 = document.getElementById('btnCerrarModal2');

    document.querySelectorAll('.doc-thumb').forEach(thumb => {
        thumb.addEventListener('click', function(e) {
            e.preventDefault();
            const src = this.getAttribute('data-img-src');
            const title = this.getAttribute('data-img-title') || 'Foto del Documento';
            const remitente = this.getAttribute('data-remitente') || 'Conductor';

            modalTitulo.textContent = title;
            modalRemitente.textContent = 'Remitente: ' + remitente;
            modalImg.src = src;
            modalLink.href = src;
            modalVisor.style.display = 'flex';
        });
    });

    function cerrarModal() {
        modalVisor.style.display = 'none';
        modalImg.src = '';
    }

    if (btnCerrar1) btnCerrar1.addEventListener('click', cerrarModal);
    if (btnCerrar2) btnCerrar2.addEventListener('click', cerrarModal);
    if (modalVisor) {
        modalVisor.addEventListener('click', function(e) {
            if (e.target === modalVisor) {
                cerrarModal();
            }
        });
    }
});
</script>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>