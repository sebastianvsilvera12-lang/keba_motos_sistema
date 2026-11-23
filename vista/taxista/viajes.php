<?php
$tituloPagina = 'Pedidos de Pasajeros - Keba Ride';
$subtituloNavbar = 'Solicitudes de Viajes en Tiempo Real';
$menuActivo = 'keba_ride';

require_once dirname(__DIR__) . '/plantillas/header.php';
require_once dirname(__DIR__) . '/plantillas/sidebar.php';
require_once dirname(__DIR__) . '/plantillas/navbar.php';

require_once dirname(dirname(__DIR__)) . '/modelos/Taxista.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Viaje.php';

$usuario = obtenerUsuarioActual();
$taxistaMod = new Taxista();
$viajeMod = new Viaje();

$ficha = $taxistaMod->obtenerFichaTaxista($usuario['id']);
$conductor = $ficha['conductor'] ?? null;
$vehiculo = $ficha['vehiculo'] ?? null;

$idConductor = (int)($conductor['id_conductor'] ?? 0);
$viajes = $viajeMod->listarSolicitudesParaTaxista($idConductor);

// Separar viajes activos de completados
$solicitudesActivas = [];
$historialHoy = [];

foreach ($viajes as $v) {
    if (in_array($v['estado'], ['Solicitado', 'Asignado', 'En Curso'])) {
        $solicitudesActivas[] = $v;
    } else {
        $historialHoy[] = $v;
    }
}
?>

<style>
    .ride-radar-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 12px;
        padding: 14px 20px;
        color: #ffffff;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .radar-indicator {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
    }
    .radar-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 8px #10b981;
        animation: radarPulse 1.6s infinite;
    }
    @keyframes radarPulse {
        0% { transform: scale(0.9); opacity: 0.7; }
        50% { transform: scale(1.3); opacity: 1; }
        100% { transform: scale(0.9); opacity: 0.7; }
    }
    .ride-request-card {
        background: #ffffff;
        border: 2px solid #2563eb;
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 20px rgba(37, 99, 235, 0.12);
        position: relative;
    }
    .ride-request-card.solicitado {
        border-color: #10b981;
        box-shadow: 0 4px 20px rgba(16, 185, 129, 0.15);
    }
    .ride-request-card.en-curso {
        border-color: #f59e0b;
        box-shadow: 0 4px 20px rgba(245, 158, 11, 0.15);
    }
    .route-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 12px 14px;
        border-radius: 10px;
        margin-bottom: 12px;
    }
    .route-origen {
        background: #f0fdf4;
        border: 1.5px solid #86efac;
    }
    .route-destino {
        background: #eff6ff;
        border: 1.5px solid #93c5fd;
    }
    .phone-contact-box {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        padding: 14px;
        margin-bottom: 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .call-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #10b981;
        color: #ffffff;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.88rem;
        text-decoration: none;
        transition: background 0.15s;
    }
    .call-btn:hover {
        background: #059669;
        color: #ffffff;
    }
    .whatsapp-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #25d366;
        color: #ffffff;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.88rem;
        text-decoration: none;
        transition: background 0.15s;
    }
    .whatsapp-btn:hover {
        background: #128c7e;
        color: #ffffff;
    }
</style>

<!-- ENCABEZADO DE RADAR -->
<div class="ride-radar-header">
    <div>
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
            <span class="radar-indicator">
                <span class="radar-dot"></span>
                Radar Keba Ride Activo
            </span>
            <small style="color: #38bdf8; font-size: 0.8rem; font-weight: 700;">Huanta &bull; VRAEM &bull; Interprovincial</small>
        </div>
        <h1 style="margin: 0; font-size: 1.35rem; font-weight: 800;">Solicitudes de Pasajeros y Viajes</h1>
        <p style="margin: 2px 0 0 0; color: #94a3b8; font-size: 0.85rem;">
            Rutas en auto desde Huanta hacia Pichari, VRAEM, Sivia, Ayacucho, Lima, Cusco, Ica y Pangoa
        </p>
    </div>

    <div style="display: flex; align-items: center; gap: 10px;">
        <button type="button" class="btn btn-outline btn-sm" onclick="window.location.reload();" style="color: #ffffff; border-color: rgba(255,255,255,0.25); display: inline-flex; align-items: center; gap: 6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/></svg>
            <span>Actualizar</span>
        </button>
        <a href="<?= vistaUrl('taxista/dashboard.php') ?>" class="btn btn-outline btn-sm" style="color: #ffffff; border-color: rgba(255,255,255,0.25); display: inline-flex; align-items: center; gap: 6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 18-6-6 6-6"/></svg>
            <span>Volver</span>
        </a>
    </div>
</div>

<!-- SOLICITUDES ACTIVAS DE VIAJE -->
<div class="row">
    <div class="col-12 col-lg-8" style="margin: 0 auto;">

        <?php if (!empty($solicitudesActivas)): ?>
            <?php foreach ($solicitudesActivas as $viaje): 
                $telCliente = preg_replace('/[^0-9]/', '', $viaje['cliente_telefono'] ?? '');
                $nombrePasajero = trim($viaje['cliente_nombres'] . ' ' . $viaje['cliente_apellidos']);
                $esSolicitado = ($viaje['estado'] === 'Solicitado');
                $esAsignado = ($viaje['estado'] === 'Asignado');
                $esEnCurso = ($viaje['estado'] === 'En Curso');
            ?>
                <div class="ride-request-card <?= $esSolicitado ? 'solicitado' : ($esEnCurso ? 'en-curso' : '') ?>">
                    
                    <!-- 1. MENSAJE / ESTADO DE SOLICITUD -->
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
                        <div>
                            <?php if ($esSolicitado): ?>
                                <span class="badge badge-success" style="font-size: 0.8rem; padding: 4px 10px; display: inline-flex; align-items: center; gap: 6px; font-weight: 700;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                                    NUEVO PEDIDO DE PASAJERO
                                </span>
                            <?php elseif ($esAsignado): ?>
                                <span class="badge badge-info" style="font-size: 0.8rem; padding: 4px 10px; display: inline-flex; align-items: center; gap: 6px; font-weight: 700;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
                                    EN CAMINO A RECOGER AL CLIENTE
                                </span>
                            <?php elseif ($esEnCurso): ?>
                                <span class="badge badge-warning" style="font-size: 0.8rem; padding: 4px 10px; display: inline-flex; align-items: center; gap: 6px; font-weight: 700;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    VIAJE EN CURSO HACIA EL DESTINO
                                </span>
                            <?php endif; ?>
                            <div style="font-size: 0.95rem; font-weight: 700; color: #0f172a; margin-top: 6px;">
                                Pasajero: <?= htmlspecialchars($nombrePasajero) ?>
                            </div>
                        </div>

                        <div style="text-align: right;">
                            <span style="font-size: 0.75rem; color: #64748b; font-weight: 600;">Tarifa Estimada</span>
                            <div style="font-size: 1.25rem; font-weight: 800; color: #059669;">
                                S/ <?= number_format($viaje['precio_estimado'], 2) ?>
                            </div>
                            <small style="color: #64748b; font-size: 0.72rem;"><?= htmlspecialchars($viaje['metodo_pago'] ?? 'Efectivo') ?></small>
                        </div>
                    </div>

                    <!-- 2. DIRECCIÓN A DÓNDE ESTÁ EL CLIENTE (ORIGEN / RECOGIDA) -->
                    <div class="route-item route-origen">
                        <div style="width: 36px; height: 36px; border-radius: 8px; background: #dcfce7; color: #15803d; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1.5px solid #86efac;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-size: 0.75rem; font-weight: 800; color: #047857; text-transform: uppercase; letter-spacing: 0.5px;">
                                DÓNDE ESTÁ EL CLIENTE (PUNTO DE RECOGIDA):
                            </div>
                            <div style="font-size: 1.15rem; font-weight: 800; color: #064e3b; margin-top: 2px;">
                                <?= htmlspecialchars($viaje['origen']) ?>
                            </div>
                            <small style="color: #047857; font-size: 0.78rem;">Acude a este punto para recoger al pasajero</small>
                        </div>
                    </div>

                    <!-- 3. DÓNDE ES SU DESTINO (PUNTO DE LLEGADA) -->
                    <div class="route-item route-destino">
                        <div style="width: 36px; height: 36px; border-radius: 8px; background: #dbeafe; color: #1d4ed8; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1.5px solid #93c5fd;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="3 11 22 2 13 21 11 13 3 11"></polygon>
                            </svg>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-size: 0.75rem; font-weight: 800; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px;">
                                DÓNDE ES SU DESTINO (PUNTO DE LLEGADA):
                            </div>
                            <div style="font-size: 1.15rem; font-weight: 800; color: #1e3a8a; margin-top: 2px;">
                                <?= htmlspecialchars($viaje['destino']) ?>
                            </div>
                            <small style="color: #1e40af; font-size: 0.78rem;">Destino final solicitado por el pasajero</small>
                        </div>
                    </div>

                    <!-- 4. EL NÚMERO DEL CLIENTE (CON BOTONES DIRECTOS) -->
                    <div class="phone-contact-box">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 36px; height: 36px; border-radius: 8px; background: #f1f5f9; color: #0284c7; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1.5px solid #cbd5e1;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </div>
                            <div>
                                <div style="font-size: 0.72rem; color: #64748b; font-weight: 800; text-transform: uppercase;">
                                    NÚMERO DEL CLIENTE:
                                </div>
                                <div style="font-size: 1.25rem; font-weight: 900; color: #0f172a; font-family: monospace;">
                                    <?= !empty($viaje['cliente_telefono']) && $viaje['cliente_telefono'] !== 'No registrado' ? htmlspecialchars($viaje['cliente_telefono']) : '987 654 321' ?>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <?php $numLlamar = !empty($telCliente) ? $telCliente : '987654321'; ?>
                            <a href="tel:<?= $numLlamar ?>" class="call-btn">
                                <svg style="width: 16px; height: 16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                <span>Llamar al Cliente</span>
                            </a>
                            <a href="https://wa.me/51<?= $numLlamar ?>?text=Hola,%20soy%20su%20conductor%20de%20KEBA%20MOTOS,%20estoy%20en%20camino%20a%20recogerlo." target="_blank" class="whatsapp-btn">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.694.06-2.146-.541-1.859-.77-3.048-2.67-3.14-2.794-.094-.124-.76-1.012-.76-1.931 0-.918.481-1.369.653-1.554.172-.185.376-.231.501-.231.125 0 .25 0 .36.006.115.006.269-.044.421.32.156.375.532 1.297.579 1.391.047.094.078.204.016.329-.063.125-.094.204-.188.313-.094.11-.198.245-.282.329-.094.094-.192.196-.083.383.11.188.487.804 1.044 1.301.718.641 1.323.84 1.511.933.188.094.298.078.407-.047.11-.125.469-.547.594-.735.125-.188.25-.156.422-.094.172.062 1.094.516 1.282.609.188.094.313.141.359.219.047.078.047.453-.097.858z"/></svg>
                                <span>WhatsApp</span>
                            </a>
                        </div>
                    </div>

                    <!-- 5. ACCIONES DE LA CARRERA -->
                    <div style="display: flex; gap: 10px; justify-content: flex-end; align-items: center; flex-wrap: wrap; border-top: 1px solid #f1f5f9; padding-top: 14px;">
                        <?php if ($esSolicitado): ?>
                            <form action="<?= controladorUrl('taxista_controlador.php') ?>" method="POST" style="margin: 0;">
                                <input type="hidden" name="accion" value="aceptar_viaje">
                                <input type="hidden" name="id_viaje" value="<?= (int)$viaje['id_viaje'] ?>">
                                <button type="submit" class="btn btn-success" style="font-weight: 800; padding: 10px 24px; font-size: 0.95rem; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span>ACEPTAR ESTE VIAJE</span>
                                </button>
                            </form>
                        <?php elseif ($esAsignado): ?>
                            <form action="<?= controladorUrl('taxista_controlador.php') ?>" method="POST" style="margin: 0;">
                                <input type="hidden" name="accion" value="iniciar_viaje">
                                <input type="hidden" name="id_viaje" value="<?= (int)$viaje['id_viaje'] ?>">
                                <button type="submit" class="btn btn-primary" style="font-weight: 800; padding: 10px 24px; font-size: 0.95rem; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                    <span>CLIENTE A BORDO (INICIAR)</span>
                                </button>
                            </form>
                        <?php elseif ($esEnCurso): ?>
                            <form action="<?= controladorUrl('taxista_controlador.php') ?>" method="POST" style="margin: 0;">
                                <input type="hidden" name="accion" value="finalizar_viaje">
                                <input type="hidden" name="id_viaje" value="<?= (int)$viaje['id_viaje'] ?>">
                                <button type="submit" class="btn btn-success" style="font-weight: 800; padding: 10px 24px; font-size: 0.95rem; border-radius: 8px; background: #059669; display: inline-flex; align-items: center; gap: 8px;">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
                                    <span>FINALIZAR VIAJE (DESTINO)</span>
                                </button>
                            </form>
                        <?php endif; ?>

                        <form action="<?= controladorUrl('taxista_controlador.php') ?>" method="POST" style="margin: 0;" onsubmit="return confirm('¿Seguro que deseas cancelar este pedido?');">
                            <input type="hidden" name="accion" value="cancelar_viaje">
                            <input type="hidden" name="id_viaje" value="<?= (int)$viaje['id_viaje'] ?>">
                            <button type="submit" class="btn btn-outline btn-sm" style="color: #ef4444; border-color: #fca5a5;">
                                Cancelar
                            </button>
                        </form>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- ESTADO VACÍO (ESPERANDO NUEVOS PEDIDOS) -->
            <div class="card" style="text-align: center; padding: 45px 25px; border: 1.5px dashed #cbd5e1; border-radius: 14px;">
                <div style="width: 70px; height: 70px; border-radius: 50%; background: #eff6ff; border: 2px solid #bfdbfe; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto; color: #2563eb;">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4.9 19.1C1 15.2 1 8.8 4.9 4.9"></path>
                        <path d="M7.8 16.2c-2.3-2.3-2.3-6.1 0-8.5"></path>
                        <circle cx="12" cy="12" r="2"></circle>
                        <path d="M16.2 7.8c2.3 2.3 2.3 6.1 0 8.5"></path>
                        <path d="M19.1 4.9C23 8.8 23 15.1 19.1 19"></path>
                    </svg>
                </div>
                <h3 style="margin: 0 0 8px 0; color: #0f172a; font-size: 1.25rem; font-weight: 800;">
                    Esperando nuevas solicitudes de pasajeros...
                </h3>
                <p style="margin: 0 auto 20px auto; max-width: 480px; color: #64748b; font-size: 0.9rem; line-height: 1.5;">
                    El radar está activo. En cuanto un cliente solicite un viaje en auto o camioneta desde Huanta, aparecerá en esta pantalla con su <strong>dirección de recogida</strong>, su <strong>destino</strong> (Pichari, VRAEM, Sivia, Ayacucho, Lima, Cusco...) y su <strong>número de teléfono</strong>.
                </p>
                <div style="display: inline-flex; align-items: center; gap: 8px; background: #f1f5f9; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; color: #475569;">
                    <span class="radar-dot"></span>
                    Buscando solicitudes en Huanta y rutas interprovinciales
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
    // Auto-refrescar cada 8 segundos para detectar nuevos pedidos de pasajeros
    setTimeout(function() {
        window.location.reload();
    }, 8000);
</script>

<?php require_once dirname(__DIR__) . '/plantillas/footer.php'; ?>
