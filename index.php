<?php
// ==============================================================
// PANEL PRINCIPAL DE ENLACES (LAUNCHER) - KEBA MOTOS
// ==============================================================

require_once __DIR__ . '/configuracion/constantes.php';
require_once __DIR__ . '/configuracion/session.php';
require_once __DIR__ . '/intermediarios/auth_middleware.php';

$usuarioActivo = obtenerUsuarioActual();
$nombreCompleto = trim(($usuarioActivo['nombre'] ?? '') . ' ' . ($usuarioActivo['apellidos'] ?? ''));
if (empty($nombreCompleto)) {
    $nombreCompleto = $usuarioActivo['usuario'] ?? 'Usuario';
}
$inicial = strtoupper(substr($nombreCompleto, 0, 1));
$rolNombre = strtoupper($usuarioActivo['rol_nombre'] ?? 'ADMINISTRADOR');
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
            <span class="user-badge"><?= htmlspecialchars($usuarioActivo['rol_nombre'] ?? 'Administrador') ?></span>
            <span class="user-name" style="color: #ffffff; font-weight: 600; font-size: 0.95rem; display: inline-flex; align-items: center; gap: 6px;">
                <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                <?= htmlspecialchars($nombreCompleto) ?>
            </span>
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

            <!-- 1. ESTADÍSTICAS -->
            <a href="vista/dashboard/dashboard_admin.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                </div>
                <div class="tile-label">Estadísticas</div>
            </a>

            <!-- 2. REPORTES -->
            <a href="vista/reportes/reporte_general.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </div>
                <div class="tile-label">Reportes</div>
            </a>

            <!-- 3. USUARIOS -->
            <a href="vista/usuarios/listar_usuarios.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <div class="tile-label">Usuarios</div>
            </a>

            <!-- 4. MOTOTAXIS -->
            <a href="vista/vehiculos/listar_vehiculos.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="17" r="3"></circle><circle cx="19" cy="17" r="3"></circle><path d="M5 17h14"></path><path d="M9 17l2-7h4l2 7"></path><path d="M11 10h4"></path><circle cx="12" cy="6" r="1"></circle></svg>
                </div>
                <div class="tile-label">Mototaxis</div>
            </a>

            <!-- 5. CONDUCTORES -->
            <a href="vista/conductores/listar_conductores.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"></rect><circle cx="9" cy="10" r="2"></circle><line x1="15" y1="8" x2="17" y2="8"></line><line x1="15" y1="12" x2="17" y2="12"></line><line x1="7" y1="16" x2="17" y2="16"></line></svg>
                </div>
                <div class="tile-label">Conductores</div>
            </a>

            <!-- 6. PROPIETARIOS -->
            <a href="vista/propietarios/listar_propietarios.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
                <div class="tile-label">Propietarios</div>
            </a>

            <!-- 7. ASOCIACIONES -->
            <a href="vista/asociaciones/listar_asociaciones.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="9" y1="6" x2="9" y2="6.01"></line><line x1="15" y1="6" x2="15" y2="6.01"></line><line x1="9" y1="10" x2="9" y2="10.01"></line><line x1="15" y1="10" x2="15" y2="10.01"></line><line x1="9" y1="14" x2="9" y2="14.01"></line><line x1="15" y1="14" x2="15" y2="14.01"></line><line x1="9" y1="18" x2="15" y2="18"></line></svg>
                </div>
                <div class="tile-label">Asociaciones</div>
            </a>

            <!-- 8. DOCUMENTOS -->
            <a href="vista/documentos/listar_documentos.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><polyline points="9 15 11 17 15 13"></polyline></svg>
                </div>
                <div class="tile-label">Documentos</div>
            </a>

            <!-- 9. VENCIMIENTOS -->
            <a href="vista/documentos/documentos_por_vencer.php" class="launcher-tile">
                <div class="tile-icon-wrapper warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <div class="tile-label">Vencimientos</div>
            </a>

            <!-- 10. FISCALIZACIÓN -->
            <a href="vista/fiscalizacion/listar_infracciones.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                </div>
                <div class="tile-label">Fiscalización</div>
            </a>

            <!-- 11. MULTAS PENDIENTES -->
            <a href="vista/fiscalizacion/infracciones_pendientes.php" class="launcher-tile">
                <div class="tile-icon-wrapper warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line><line x1="12" y1="14" x2="12" y2="18"></line><line x1="8" y1="16" x2="16" y2="16"></line></svg>
                </div>
                <div class="tile-label">Multas Pendientes</div>
            </a>

            <!-- 12. HISTORIAL PLACA -->
            <a href="vista/fiscalizacion/historial_vehiculo.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <div class="tile-label">Historial Placa</div>
            </a>

            <!-- 13. HISTORIAL CHOFER -->
            <a href="vista/fiscalizacion/historial_conductor.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="18" y1="8" x2="23" y2="13"></line><line x1="23" y1="8" x2="18" y2="13"></line></svg>
                </div>
                <div class="tile-label">Historial Chofer</div>
            </a>

            <!-- 14. REGISTRO VEHÍCULO -->
            <a href="vista/vehiculos/registrar_vehiculo.php" class="launcher-tile">
                <div class="tile-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                </div>
                <div class="tile-label">Nuevo Vehículo</div>
            </a>

            <!-- 15. CERRAR SESIÓN -->
            <a href="controlador/auth/logout_controlador.php" class="launcher-tile" style="border-color: #fca5a5;">
                <div class="tile-icon-wrapper danger">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                </div>
                <div class="tile-label" style="color: #b91c1c;">Cerrar Sesión</div>
            </a>

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

