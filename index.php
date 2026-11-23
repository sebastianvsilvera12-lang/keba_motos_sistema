<?php
// ==============================================================
// PANEL PRINCIPAL DE ENLACES (LAUNCHER) - KEBA MOTOS
// ==============================================================

require_once __DIR__ . '/configuracion/constantes.php';
require_once __DIR__ . '/ayudantes/funciones.php';
require_once __DIR__ . '/configuracion/session.php';
require_once __DIR__ . '/configuracion/permisos.php';
require_once __DIR__ . '/intermediarios/auth_middleware.php';

$usuarioActivo = obtenerUsuarioActual();
$nombreCompleto = trim(($usuarioActivo['nombre'] ?? '') . ' ' . ($usuarioActivo['apellidos'] ?? ''));
if (empty($nombreCompleto)) {
    $nombreCompleto = $usuarioActivo['usuario'] ?? 'Usuario';
}
$inicial = strtoupper(substr($nombreCompleto, 0, 1));
$rolNombre = strtoupper($usuarioActivo['rol_nombre'] ?? 'ADMINISTRADOR');

if (($usuarioActivo['rol_id'] ?? 3) == ROL_CLIENTES) {
    header('Location: vista/cliente/index.php');
    exit;
}

$dashUrl = 'vista/dashboard/dashboard_admin.php';
if (($usuarioActivo['rol_id'] ?? 3) == ROL_TAXISTAS || ($usuarioActivo['rol_id'] ?? 3) == ROL_CONSULTA) {
    $dashUrl = 'vista/taxista/perfil.php';
} elseif (($usuarioActivo['rol_id'] ?? 3) == ROL_ANALISTA || ($usuarioActivo['rol_id'] ?? 3) == ROL_PERSONAL) {
    $dashUrl = 'vista/dashboard/dashboard_personal.php';
}
$perfilDestino = (($usuarioActivo['rol_id'] ?? 3) == ROL_TAXISTAS || ($usuarioActivo['rol_id'] ?? 3) == ROL_CONSULTA) ? 'vista/taxista/perfil.php' : $dashUrl;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KEBA MOTOS - Panel Principal</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body class="index-body">

    <!-- BARRA SUPERIOR -->
    <header class="launcher-navbar">
        <a href="index.php" class="launcher-brand" style="display: flex; align-items: center;">
            <img src="assets/img/logo_keba.png" alt="KEBA MOTOS" style="height: 48px; max-width: 230px; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));">
        </a>

        <div class="launcher-center-info">
            <span>SEDE:</span>
            <strong>CENTRAL</strong>
            <span style="font-size: 0.7rem;">▼</span>
        </div>

        <div class="navbar-user">
            <a href="<?= $perfilDestino ?>" title="Ver Mi Perfil de <?= htmlspecialchars($usuarioActivo['rol_nombre'] ?? 'Taxistas') ?>" style="text-decoration: none;">
                <span class="user-badge" style="cursor: pointer;"><?= htmlspecialchars($usuarioActivo['rol_nombre'] ?? 'Taxistas') ?></span>
            </a>

            <a href="<?= $perfilDestino ?>" class="user-name" title="Ver Mi Perfil y Panel de Datos" style="color: #ffffff; font-weight: 600; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; cursor: pointer;">
                <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                <?= htmlspecialchars($nombreCompleto) ?>
            </a>

            <a href="controlador/auth/logout_controlador.php" class="btn btn-outline btn-sm" style="color: #ffffff; border-color: #475569; background-color: #1e293b;">Salir</a>
        </div>
    </header>

    <!-- CONTENEDOR CENTRAL -->
    <main class="launcher-container">

        <!-- ALERTA FLASH SI EXISTE -->
        <?php 
        $flash = getFlash();
        if ($flash): 
        ?>
        <div class="alert alert-<?= htmlspecialchars($flash['tipo']) ?>" style="margin-bottom: 20px;">
            <svg style="width: 18px; height: 18px; flex-shrink: 0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
            <div><?= htmlspecialchars($flash['mensaje']) ?></div>
        </div>
        <?php endif; ?>

        <!-- BANNER DE BIENVENIDA CON EL NOMBRE DEL USUARIO AUTENTICADO -->
        <section class="launcher-welcome-banner">
            <div class="welcome-info">
                <h2>
                    Bienvenido(a), <?= htmlspecialchars($nombreCompleto) ?>
                    <span class="role-badge"><?= htmlspecialchars($rolNombre) ?></span>
                </h2>
                <p>
                    <svg style="width: 16px; height: 16px; color: #2563eb;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="9" y1="6" x2="9" y2="6.01"></line><line x1="15" y1="6" x2="15" y2="6.01"></line><line x1="9" y1="10" x2="9" y2="10.01"></line><line x1="15" y1="10" x2="15" y2="10.01"></line><line x1="9" y1="14" x2="9" y2="14.01"></line><line x1="15" y1="14" x2="15" y2="14.01"></line><line x1="9" y1="18" x2="15" y2="18"></line></svg>
                    KEBA MOTOS • Sistema Integrado de Control, Gestión y Fiscalización
                </p>
            </div>

            <div class="welcome-meta">
                <div class="meta-pill">
                    <svg style="width: 16px; height: 16px; color: #64748b;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <span id="fechaActual"><?= date('d/m/Y') ?></span>
                </div>
                <div class="meta-pill online">
                    <span class="dot"></span>
                    <span>Sistema Operativo</span>
                </div>
            </div>
        </section>

        <!-- CUADRÍCULA DE BOTONES DE NAVEGACIÓN (TILES) -->
        <section class="launcher-grid">

            <?php if (($usuarioActivo['rol_id'] ?? 3) == ROL_TAXISTAS || ($usuarioActivo['rol_id'] ?? 3) == ROL_CONSULTA): ?>
            <!-- ============================================== -->
            <!-- ACCESOS DIRECTOS EXCLUSIVOS PARA ROL TAXISTAS  -->
            <!-- ============================================== -->
            <!-- 1. REGISTRAR ASISTENCIA -->
            <a href="vista/taxista/asistencia.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <div class="tile-label">Mi Asistencia</div>
            </a>

            <!-- 2. SUBIR DOCUMENTOS -->
            <a href="vista/taxista/documentos.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                </div>
                <div class="tile-label">Subir Documentos</div>
            </a>

            <!-- 3. FOTOS PERSONA Y MOTO -->
            <a href="vista/taxista/fotos.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="13" r="4"></circle><path d="M5 7h1a2 2 0 0 0 2-2 1 1 0 0 1 1-1h6a1 1 0 0 1 1 1 2 2 0 0 0 2 2h1a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2z"></path></svg>
                </div>
                <div class="tile-label">Fotos Persona y Moto</div>
            </a>

            <!-- 4. DAÑOS Y REVISIÓN DE MOTO -->
            <a href="vista/taxista/reportar_estado.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <div class="tile-label">Daños y Revisión</div>
            </a>

            <!-- 5. SUBIR CUOTA MENSUAL (YAPE) -->
            <a href="vista/taxista/cuotas.php" class="launcher-tile" style="border-color: #d8b4fe;">
                <div class="tile-icon-wrapper" style="padding: 0; background: transparent; display: flex; align-items: center; justify-content: center; box-shadow: none;">
                    <img src="assets/img/yape_app.png" alt="Yape" style="width: 48px; height: 48px; border-radius: 12px; object-fit: cover; display: block; box-shadow: 0 4px 10px rgba(116, 34, 132, 0.35);">
                </div>
                <div class="tile-label" style="color: #6b21a8; font-weight: 800;">Cuotas Yape</div>
            </a>

            <!-- 6. CONSULTAR PLACA -->
            <a href="vista/fiscalizacion/historial_vehiculo.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <div class="tile-label">Consultar Placa</div>
            </a>

            <!-- 6.1 PEDIR MOTOTAXI / CLIENTE UBER -->
            <a href="vista/taxista/viajes.php" class="launcher-tile" style="border-color: #0f172a;">
                <div class="tile-icon-wrapper" style="background: #000000; color: #ffffff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/>
                    </svg>
                </div>
                <div class="tile-label" style="color: #000000; font-weight: 800;">Keba Ride (Uber)</div>
            </a>

            <!-- 7. CERRAR SESIÓN -->
            <a href="controlador/auth/logout_controlador.php" class="launcher-tile" style="border-color: #fca5a5;">
                <div class="tile-icon-wrapper danger">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                </div>
                <div class="tile-label" style="color: #b91c1c;">Cerrar Sesión</div>
            </a>

            <?php else: ?>
            <!-- ============================================== -->
            <!-- ACCESOS PARA ADMINISTRADOR Y ANALISTA         -->
            <!-- ============================================== -->
            <!-- 1. ESTADÍSTICAS -->
            <?php if (tienePermiso('dashboard', 'ver')): ?>
            <a href="<?= $dashUrl ?>" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                </div>
                <div class="tile-label">Estadísticas</div>
            </a>
            <?php endif; ?>

            <!-- 2. REPORTES -->
            <?php if (tienePermiso('reportes', 'ver')): ?>
            <a href="vista/reportes/reporte_general.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </div>
                <div class="tile-label">Reportes</div>
            </a>
            <?php endif; ?>

            <!-- 3. USUARIOS -->
            <?php if (tienePermiso('usuarios', 'ver')): ?>
            <a href="vista/usuarios/listar_usuarios.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <div class="tile-label">Usuarios</div>
            </a>
            <?php endif; ?>

            <!-- 4. MOTOTAXIS -->
            <?php if (tienePermiso('vehiculos', 'ver')): ?>
            <a href="vista/vehiculos/listar_vehiculos.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="17" r="3"></circle><circle cx="19" cy="17" r="3"></circle><path d="M5 17h14"></path><path d="M9 17l2-7h4l2 7"></path><path d="M11 10h4"></path><circle cx="12" cy="6" r="1"></circle></svg>
                </div>
                <div class="tile-label">Mototaxis</div>
            </a>

            <!-- 4.1 AVISOS DE MOTOS (DAÑOS/REVISIÓN) -->
            <a href="vista/vehiculos/reportes_taxistas.php" class="launcher-tile">
                <div class="tile-icon-wrapper warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <div class="tile-label">Avisos Daños Moto</div>
            </a>

            <!-- 4.2 CUOTAS TAXISTAS (YAPE) -->
            <a href="vista/vehiculos/cuotas_taxistas.php" class="launcher-tile" style="border-color: #d8b4fe;">
                <div class="tile-icon-wrapper" style="padding: 0; background: transparent; display: flex; align-items: center; justify-content: center; box-shadow: none;">
                    <img src="assets/img/yape_app.png" alt="Yape" style="width: 48px; height: 48px; border-radius: 12px; object-fit: cover; display: block; box-shadow: 0 4px 10px rgba(116, 34, 132, 0.35);">
                </div>
                <div class="tile-label" style="color: #6b21a8; font-weight: 800;">Cuotas Yape</div>
            </a>
            <?php endif; ?>

            <!-- 5. CONDUCTORES -->
            <?php if (tienePermiso('conductores', 'ver')): ?>
            <a href="vista/conductores/listar_conductores.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><line x1="15" y1="8" x2="17" y2="8"></line><line x1="15" y1="12" x2="17" y2="12"></line><line x1="7" y1="16" x2="17" y2="16"></line></svg>
                </div>
                <div class="tile-label">Conductores</div>
            </a>
            <?php endif; ?>

            <!-- 6. PROPIETARIOS -->
            <?php if (tienePermiso('propietarios', 'ver')): ?>
            <a href="vista/propietarios/listar_propietarios.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
                <div class="tile-label">Propietarios</div>
            </a>
            <?php endif; ?>

            <!-- 7. ASOCIACIONES -->
            <?php if (tienePermiso('asociaciones', 'ver')): ?>
            <a href="vista/asociaciones/listar_asociaciones.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="9" y1="6" x2="9" y2="6.01"></line><line x1="15" y1="6" x2="15" y2="6.01"></line><line x1="9" y1="10" x2="9" y2="10.01"></line><line x1="15" y1="10" x2="15" y2="10.01"></line><line x1="9" y1="14" x2="9" y2="14.01"></line><line x1="15" y1="14" x2="15" y2="14.01"></line><line x1="9" y1="18" x2="15" y2="18"></line></svg>
                </div>
                <div class="tile-label">Asociaciones</div>
            </a>
            <?php endif; ?>

            <!-- 8. DOCUMENTOS -->
            <?php if (tienePermiso('documentos', 'ver')): ?>
            <a href="vista/documentos/listar_documentos.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><polyline points="9 15 11 17 15 13"></polyline></svg>
                </div>
                <div class="tile-label">Documentos</div>
            </a>
            <?php endif; ?>

            <!-- 9. VENCIMIENTOS -->
            <?php if (tienePermiso('documentos', 'ver')): ?>
            <a href="vista/documentos/documentos_por_vencer.php" class="launcher-tile">
                <div class="tile-icon-wrapper warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <div class="tile-label">Vencimientos</div>
            </a>
            <?php endif; ?>

            <!-- 10. FISCALIZACIÓN -->
            <?php if (tienePermiso('fiscalizacion', 'ver')): ?>
            <a href="vista/fiscalizacion/listar_infracciones.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                </div>
                <div class="tile-label">Fiscalización</div>
            </a>
            <?php endif; ?>

            <!-- 11. MULTAS PENDIENTES -->
            <?php if (tienePermiso('fiscalizacion', 'ver')): ?>
            <a href="vista/fiscalizacion/infracciones_pendientes.php" class="launcher-tile">
                <div class="tile-icon-wrapper warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line><line x1="12" y1="14" x2="12" y2="18"></line><line x1="8" y1="16" x2="16" y2="16"></line></svg>
                </div>
                <div class="tile-label">Multas Pendientes</div>
            </a>
            <?php endif; ?>

            <!-- 12. HISTORIAL PLACA -->
            <?php if (tienePermiso('fiscalizacion', 'ver') || tienePermiso('vehiculos', 'ver')): ?>
            <a href="vista/fiscalizacion/historial_vehiculo.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <div class="tile-label">Historial Placa</div>
            </a>
            <?php endif; ?>

            <!-- 13. HISTORIAL CHOFER -->
            <?php if (tienePermiso('conductores', 'ver')): ?>
            <a href="vista/fiscalizacion/historial_conductor.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="18" y1="8" x2="23" y2="13"></line><line x1="23" y1="8" x2="18" y2="13"></line></svg>
                </div>
                <div class="tile-label">Historial Chofer</div>
            </a>
            <?php endif; ?>

            <!-- 14. REGISTRO VEHÍCULO -->
            <?php if (tienePermiso('vehiculos', 'crear')): ?>
            <a href="vista/vehiculos/registrar_vehiculo.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                </div>
                <div class="tile-label">Nuevo Vehículo</div>
            </a>
            <?php endif; ?>

            <!-- 14.1 PORTAL CLIENTE (UBER MOTO) -->
            <a href="vista/cliente/index.php" class="launcher-tile" style="border-color: #0f172a;">
                <div class="tile-icon-wrapper" style="background: #000000; color: #ffffff; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/>
                    </svg>
                </div>
                <div class="tile-label" style="color: #000000; font-weight: 800;">Keba Ride (Uber)</div>
            </a>

            <!-- 15. CERRAR SESIÓN -->
            <a href="controlador/auth/logout_controlador.php" class="launcher-tile" style="border-color: #fca5a5;">
                <div class="tile-icon-wrapper danger">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                </div>
                <div class="tile-label" style="color: #b91c1c;">Cerrar Sesión</div>
            </a>
            <?php endif; ?>

        </section>

    </main>

    <!-- PIE DE PÁGINA -->
    <footer class="launcher-footer">
        <div>Sistema de Gestión y Fiscalización <strong>KEBA MOTOS</strong> &copy; <?= date('Y') ?></div>
        <div>Versión 1.0</div>
    </footer>

    <!-- SCRIPT FECHA AUTOMÁTICA -->
    <script>
        const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const fecha = new Date().toLocaleDateString('es-ES', opciones);
        document.getElementById('fechaActual').innerText = fecha.charAt(0).toUpperCase() + fecha.slice(1);
    </script>

</body>
</html>
