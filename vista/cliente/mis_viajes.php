<?php
// ==============================================================
// HISTORIAL DE VIAJES DEL CLIENTE - KEBA RIDE
// ==============================================================

require_once dirname(dirname(__DIR__)) . '/configuracion/constantes.php';
require_once dirname(dirname(__DIR__)) . '/configuracion/session.php';
require_once dirname(dirname(__DIR__)) . '/ayudantes/funciones.php';
require_once dirname(dirname(__DIR__)) . '/intermediarios/auth_middleware.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Viaje.php';

$usuario = obtenerUsuarioActual();
$idUsuario = $usuario['id_usuario'] ?? $usuario['id'] ?? 1;

$viajeMod = new Viaje();
$viajes = $viajeMod->listarPorUsuario($idUsuario, 50);
$stats = $viajeMod->obtenerEstadisticasCliente($idUsuario);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Viajes - KEBA Ride</title>
    <link rel="stylesheet" href="<?= asset('css/cliente_uber.css') ?>">
</head>
<body style="background: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; min-height: 100vh;">

    <!-- TOPBAR UBER -->
    <header class="uber-navbar">
        <div style="display: flex; align-items: center; gap: 28px;">
            <a href="index.php" class="uber-brand">
                <img src="<?= asset('img/logo_keba.png') ?>" alt="KEBA" style="height: 38px; width: auto; max-width: 170px; object-fit: contain;">
                <span class="uber-brand-text"><span style="font-weight: 300;">Ride</span></span>
            </a>
            <nav class="uber-nav-links">
                <a href="index.php" class="uber-nav-link">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
                    Pedir Auto / Viaje
                </a>
                <a href="mis_viajes.php" class="uber-nav-link active">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Mis Viajes
                </a>
                <a href="perfil.php" class="uber-nav-link">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Mi Perfil
                </a>
            </nav>
        </div>
        <div>
            <a href="index.php" class="btn-uber-primary" style="padding: 8px 16px; font-size: 0.88rem;">
                + Pedir Nuevo Viaje
            </a>
        </div>
    </header>

    <div style="max-width: 1000px; margin: 30px auto; padding: 0 20px;">
        
        <h1 style="font-size: 1.8rem; font-weight: 800; color: #0f172a; margin-bottom: 24px;">
            Actividad y Viajes Anteriores
        </h1>

        <!-- RESUMEN DE ACTIVIDAD -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 30px;">
            <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
                <span style="font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Total de Viajes</span>
                <div style="font-size: 1.8rem; font-weight: 800; color: #0f172a; margin-top: 4px;"><?= $stats['total_viajes'] ?? 0 ?></div>
            </div>
            <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
                <span style="font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Total Invertido</span>
                <div style="font-size: 1.8rem; font-weight: 800; color: #2563eb; margin-top: 4px;">S/ <?= number_format($stats['total_gastado'] ?? 0, 2) ?></div>
            </div>
            <div style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
                <span style="font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Calificación Promedio</span>
                <div style="font-size: 1.8rem; font-weight: 800; color: #f59e0b; margin-top: 4px;">
                    ⭐ <?= number_format($stats['promedio_calificacion'] ?? 5.0, 1) ?>
                </div>
            </div>
        </div>

        <!-- LISTADO DE VIAJES -->
        <?php if (empty($viajes)): ?>
            <div style="background: #fff; padding: 60px 20px; text-align: center; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.04);">
                <div style="font-size: 3rem; margin-bottom: 12px;">🛺</div>
                <h3 style="margin: 0 0 8px 0; color: #0f172a;">Aún no tienes viajes registrados</h3>
                <p style="color: #64748b; margin-bottom: 20px;">Pide tu primer mototaxi en Pisco de forma rápida y segura.</p>
                <a href="index.php" class="btn-uber-primary" style="display: inline-flex; width: auto; padding: 12px 24px;">
                    Pedir un Mototaxi Ahora
                </a>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <?php foreach ($viajes as $v): ?>
                    <div style="background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; gap: 16px; box-shadow: 0 2px 6px rgba(0,0,0,0.03); flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 16px; min-width: 250px;">
                            <div style="width: 48px; height: 48px; border-radius: 12px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                🛺
                            </div>
                            <div>
                                <div style="font-weight: 800; color: #0f172a; font-size: 1.05rem;">
                                    <?= htmlspecialchars($v['destino']) ?>
                                </div>
                                <div style="font-size: 0.85rem; color: #64748b; margin-top: 2px;">
                                    Desde: <?= htmlspecialchars($v['origen']) ?>
                                </div>
                                <div style="font-size: 0.78rem; color: #94a3b8; margin-top: 4px;">
                                    <?= date('d/m/Y H:i', strtotime($v['fecha_solicitud'])) ?> • <?= htmlspecialchars($v['tipo_servicio']) ?>
                                </div>
                            </div>
                        </div>

                        <!-- DATOS DEL CONDUCTOR Y PLACA -->
                        <div style="text-align: center; min-width: 140px;">
                            <div style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">
                                <?= htmlspecialchars(($v['conductor_nombres'] ?? 'Pedro') . ' ' . ($v['conductor_apellidos'] ?? 'Flores')) ?>
                            </div>
                            <span style="font-size: 0.75rem; background: #000; color: #fff; padding: 2px 8px; border-radius: 4px; font-weight: 700; display: inline-block; margin-top: 4px;">
                                Placa <?= htmlspecialchars($v['vehiculo_placa'] ?? '1234-5B') ?>
                            </span>
                        </div>

                        <!-- ESTADO & CALIFICACIÓN -->
                        <div style="text-align: center; min-width: 120px;">
                            <?php 
                            $badgeColor = '#22c55e';
                            $badgeBg = '#dcfce7';
                            if ($v['estado'] === 'Cancelado') { $badgeColor = '#ef4444'; $badgeBg = '#fee2e2'; }
                            elseif ($v['estado'] === 'En Curso' || $v['estado'] === 'Asignado') { $badgeColor = '#2563eb'; $badgeBg = '#dbeafe'; }
                            ?>
                            <span style="background: <?= $badgeBg ?>; color: <?= $badgeColor ?>; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase;">
                                <?= htmlspecialchars($v['estado']) ?>
                            </span>
                            <?php if ($v['calificacion']): ?>
                                <div style="color: #f59e0b; font-size: 0.9rem; margin-top: 6px;">
                                    <?= str_repeat('★', (int)$v['calificacion']) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- PRECIO & PAGO -->
                        <div style="text-align: right; min-width: 100px;">
                            <div style="font-size: 1.3rem; font-weight: 800; color: #0f172a;">
                                S/ <?= number_format($v['precio_final'] ?? $v['precio_estimado'], 2) ?>
                            </div>
                            <div style="font-size: 0.75rem; color: #64748b; font-weight: 600;">
                                <?= htmlspecialchars($v['metodo_pago']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>
