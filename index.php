<?php
// ==============================================================
// PANEL PRINCIPAL DE ENLACES (LAUNCHER) - KEBA MOTOS
// ==============================================================

require_once __DIR__ . '/config/constantes.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/permisos.php';
require_once __DIR__ . '/middleware/auth_middleware.php';

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
            <span class="user-name" style="color: #ffffff; font-weight: 600; font-size: 0.95rem;">👤 <?= htmlspecialchars($nombreCompleto) ?></span>
            <a href="controllers/auth/logout_controlador.php" class="btn btn-outline btn-sm" style="color: #ffffff; border-color: #475569; background-color: #1e293b;">Salir</a>
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
            <span>🔔</span>
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
                    <span>🏛️</span>
                    KEBA MOTOS • Sistema Integrado de Control, Gestión y Fiscalización
                </p>
            </div>

            <div class="welcome-meta">
                <div class="meta-pill">
                    <span>📅</span>
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

            <?php
            $dashUrl = 'views/dashboard/dashboard_admin.php';
            if ($usuarioActivo['rol_id'] == ROL_CONSULTA) {
                $dashUrl = 'views/dashboard/dashboard_consulta.php';
            } elseif ($usuarioActivo['rol_id'] == ROL_ANALISTA || $usuarioActivo['rol_id'] == ROL_PERSONAL) {
                $dashUrl = 'views/dashboard/dashboard_personal.php';
            }
            ?>
            <!-- 1. ESTADÍSTICAS / DASHBOARD -->
            <?php if (tienePermiso('dashboard', 'ver')): ?>
            <a href="<?= $dashUrl ?>" class="launcher-tile">
                <div class="tile-icon">📈</div>
                <div class="tile-label">Estadísticas</div>
            </a>
            <?php endif; ?>

            <!-- 2. REPORTES -->
            <?php if (tienePermiso('reportes', 'ver')): ?>
            <a href="views/reportes/reporte_general.php" class="launcher-tile">
                <div class="tile-icon">📑</div>
                <div class="tile-label">Reportes</div>
            </a>
            <?php endif; ?>

            <!-- 3. USUARIOS -->
            <?php if (tienePermiso('usuarios', 'ver')): ?>
            <a href="views/usuarios/listar_usuarios.php" class="launcher-tile">
                <div class="tile-icon">⚙️</div>
                <div class="tile-label">Usuarios</div>
            </a>
            <?php endif; ?>

            <!-- 4. MOTOTAXIS -->
            <?php if (tienePermiso('vehiculos', 'ver')): ?>
            <a href="views/vehiculos/listar_vehiculos.php" class="launcher-tile">
                <div class="tile-icon">🛵</div>
                <div class="tile-label">Mototaxis</div>
            </a>
            <?php endif; ?>

            <!-- 5. CONDUCTORES -->
            <?php if (tienePermiso('conductores', 'ver')): ?>
            <a href="views/conductores/listar_conductores.php" class="launcher-tile">
                <div class="tile-icon">👨‍✈️</div>
                <div class="tile-label">Conductores</div>
            </a>
            <?php endif; ?>

            <!-- 6. PROPIETARIOS -->
            <?php if (tienePermiso('propietarios', 'ver')): ?>
            <a href="views/propietarios/listar_propietarios.php" class="launcher-tile">
                <div class="tile-icon">👤</div>
                <div class="tile-label">Propietarios</div>
            </a>
            <?php endif; ?>

            <!-- 7. ASOCIACIONES -->
            <?php if (tienePermiso('asociaciones', 'ver')): ?>
            <a href="views/asociaciones/listar_asociaciones.php" class="launcher-tile">
                <div class="tile-icon">🏢</div>
                <div class="tile-label">Asociaciones</div>
            </a>
            <?php endif; ?>

            <!-- 8. DOCUMENTOS -->
            <?php if (tienePermiso('documentos', 'ver')): ?>
            <a href="views/documentos/listar_documentos.php" class="launcher-tile">
                <div class="tile-icon">📄</div>
                <div class="tile-label">Documentos</div>
            </a>
            <?php endif; ?>

            <!-- 9. VENCIMIENTOS -->
            <?php if (tienePermiso('documentos', 'ver')): ?>
            <a href="views/documentos/documentos_por_vencer.php" class="launcher-tile">
                <div class="tile-icon">⚠️</div>
                <div class="tile-label">Vencimientos</div>
            </a>
            <?php endif; ?>

            <!-- 10. FISCALIZACIÓN -->
            <?php if (tienePermiso('fiscalizacion', 'ver')): ?>
            <a href="views/fiscalizacion/listar_infracciones.php" class="launcher-tile">
                <div class="tile-icon">👮</div>
                <div class="tile-label">Fiscalización</div>
            </a>
            <?php endif; ?>

            <!-- 11. MULTAS PENDIENTES -->
            <?php if (tienePermiso('fiscalizacion', 'ver')): ?>
            <a href="views/fiscalizacion/infracciones_pendientes.php" class="launcher-tile">
                <div class="tile-icon">💰</div>
                <div class="tile-label">Multas Pendientes</div>
            </a>
            <?php endif; ?>

            <!-- 12. HISTORIAL PLACA -->
            <?php if (tienePermiso('fiscalizacion', 'ver') || tienePermiso('vehiculos', 'ver')): ?>
            <a href="views/fiscalizacion/historial_vehiculo.php" class="launcher-tile">
                <div class="tile-icon">🔍</div>
                <div class="tile-label">Historial Placa</div>
            </a>
            <?php endif; ?>

            <!-- 13. HISTORIAL CHOFER -->
            <?php if (tienePermiso('conductores', 'ver')): ?>
            <a href="views/fiscalizacion/historial_conductor.php" class="launcher-tile">
                <div class="tile-icon">📋</div>
                <div class="tile-label">Historial Chofer</div>
            </a>
            <?php endif; ?>

            <!-- 14. REGISTRO VEHÍCULO -->
            <?php if (tienePermiso('vehiculos', 'crear')): ?>
            <a href="views/vehiculos/registrar_vehiculo.php" class="launcher-tile">
                <div class="tile-icon">➕</div>
                <div class="tile-label">Nuevo Vehículo</div>
            </a>
            <?php endif; ?>

            <!-- 15. CERRAR SESIÓN -->
            <a href="controllers/auth/logout_controlador.php" class="launcher-tile" style="border-color: #fca5a5;">
                <div class="tile-icon" style="color: #ef4444;">🚪</div>
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

