<?php
// ==============================================================
// PERFIL DEL CLIENTE - KEBA RIDE
// ==============================================================

require_once dirname(dirname(__DIR__)) . '/configuracion/constantes.php';
require_once dirname(dirname(__DIR__)) . '/configuracion/session.php';
require_once dirname(dirname(__DIR__)) . '/ayudantes/funciones.php';
require_once dirname(dirname(__DIR__)) . '/intermediarios/auth_middleware.php';

$usuario = obtenerUsuarioActual();
$mensajeExito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevoTelefono = trim($_POST['telefono'] ?? '');
    $nuevaDireccion = trim($_POST['direccion'] ?? '');
    
    // Actualizar en base de datos
    $db = Database::conectar();
    $stmt = $db->prepare("UPDATE usuarios SET telefono = :tel WHERE id_usuario = :id");
    $stmt->execute([':tel' => $nuevoTelefono, ':id' => $usuario['id_usuario']]);
    $_SESSION['telefono'] = $nuevoTelefono;
    $mensajeExito = '¡Datos de perfil actualizados con éxito!';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - KEBA Ride</title>
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
                <a href="mis_viajes.php" class="uber-nav-link">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Mis Viajes
                </a>
                <a href="perfil.php" class="uber-nav-link active">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Mi Perfil
                </a>
            </nav>
        </div>
    </header>

    <div style="max-width: 650px; margin: 40px auto; padding: 0 20px;">
        
        <?php if ($mensajeExito): ?>
            <div style="background: #dcfce7; color: #15803d; padding: 14px 20px; border-radius: 10px; margin-bottom: 20px; font-weight: 600;">
                ✓ <?= htmlspecialchars($mensajeExito) ?>
            </div>
        <?php endif; ?>

        <div style="background: #fff; border-radius: 16px; padding: 32px; border: 1px solid #e2e8f0; box-shadow: 0 4px 14px rgba(0,0,0,0.04);">
            
            <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 28px; padding-bottom: 20px; border-bottom: 1px solid #f1f5f9;">
                <div style="width: 72px; height: 72px; border-radius: 50%; background: #000; color: #fff; font-size: 2rem; font-weight: 800; display: flex; align-items: center; justify-content: center;">
                    <?= strtoupper(substr($usuario['nombres'] ?? $usuario['nombre'] ?? 'C', 0, 1)) ?>
                </div>
                <div>
                    <h2 style="margin: 0; font-size: 1.4rem; color: #0f172a;">
                        <?= htmlspecialchars(trim(($usuario['nombres'] ?? '') . ' ' . ($usuario['apellidos'] ?? ''))) ?>
                    </h2>
                    <div style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">
                        Usuario: <strong><?= htmlspecialchars($usuario['usuario']) ?></strong> • Rol: <strong><?= htmlspecialchars($usuario['rol_nombre'] ?? 'Cliente') ?></strong>
                    </div>
                </div>
            </div>

            <form method="POST" style="display: flex; flex-direction: column; gap: 20px;">
                <div>
                    <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                        Número de Teléfono / WhatsApp:
                    </label>
                    <input type="text" name="telefono" value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>" placeholder="Ej: 956123456" style="width: 100%; box-sizing: border-box; padding: 12px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 0.95rem;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                        Lugar Frecuente (Casa):
                    </label>
                    <input type="text" name="direccion" value="Plaza de Armas de Pisco" placeholder="Ej: Calle Comercio 124" style="width: 100%; box-sizing: border-box; padding: 12px; border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 0.95rem;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.88rem; font-weight: 700; color: #1e293b; margin-bottom: 6px;">
                        Método de Pago Favorito:
                    </label>
                    <div style="display: flex; gap: 12px;">
                        <label style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer;">
                            <input type="radio" name="fav_pago" checked>
                            <span>Efectivo</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer;">
                            <input type="radio" name="fav_pago">
                            <span style="color: #742284; font-weight: 700;">Yape</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-uber-primary" style="margin-top: 10px;">
                    Guardar Cambios
                </button>
            </form>

        </div>

    </div>

</body>
</html>
