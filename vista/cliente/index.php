<?php
// ==============================================================
// PORTAL DE CLIENTES ESTILO UBER - KEBA MOTOS / KEBA S.A.C.
// ==============================================================

require_once dirname(dirname(__DIR__)) . '/configuracion/constantes.php';
require_once dirname(dirname(__DIR__)) . '/configuracion/session.php';
require_once dirname(dirname(__DIR__)) . '/ayudantes/funciones.php';
require_once dirname(dirname(__DIR__)) . '/intermediarios/auth_middleware.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Viaje.php';

$usuario = obtenerUsuarioActual();
$nombreUsuario = trim(($usuario['nombres'] ?? $usuario['nombre'] ?? '') . ' ' . ($usuario['apellidos'] ?? ''));
if (empty($nombreUsuario)) {
    $nombreUsuario = $usuario['usuario'] ?? 'Cliente';
}

$viajeMod = new Viaje();
$viajeActivo = $viajeMod->obtenerViajeActivo($usuario['id_usuario'] ?? $usuario['id'] ?? 1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KEBA Ride - Viajes y Autos desde Huanta (Pichari, VRAEM, Cusco, Lima)</title>
    
    <!-- CSS Estilos Uber Client -->
    <link rel="stylesheet" href="<?= asset('css/cliente_uber.css') ?>">
    
    <!-- Leaflet Map CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
</head>
<body class="uber-body">

    <!-- 1. BARRA SUPERIOR UBER -->
    <header class="uber-navbar">
        <div style="display: flex; align-items: center; gap: 28px;">
            <a href="index.php" class="uber-brand">
                <img src="<?= asset('img/logo_keba.png') ?>" alt="KEBA" style="height: 38px; width: auto; max-width: 170px; object-fit: contain;">
                <span class="uber-brand-text">
                    <span style="font-weight: 300;">Ride</span>
                    <span class="badge-client">PASAJEROS</span>
                </span>
            </a>

            <nav class="uber-nav-links">
                <a href="index.php" class="uber-nav-link active">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
                    Pedir Auto / Viaje
                </a>
                <a href="mis_viajes.php" class="uber-nav-link">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Mis Viajes
                </a>
                <a href="perfil.php" class="uber-nav-link">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Mi Perfil
                </a>
            </nav>
        </div>

        <div class="uber-nav-user">
            <div class="user-pill">
                <div style="width: 8px; height: 8px; border-radius: 50%; background: #22c55e;"></div>
                <span><?= htmlspecialchars($nombreUsuario) ?></span>
            </div>
            <a href="../../index.php" class="uber-nav-link" title="Volver al Panel Principal de KEBA" style="font-size: 0.85rem; border: 1px solid #444;">
                Panel Principal &rarr;
            </a>
            <a href="../../controlador/auth/logout_controlador.php" class="uber-nav-link" style="color: #f87171; font-size: 0.85rem;">
                Salir
            </a>
        </div>
    </header>

    <!-- 2. CONTENEDOR PRINCIPAL TIPO SPLIT SCREEN -->
    <main class="uber-main-layout">

        <!-- PANEL IZQUIERDO: FORMULARIO DE RESERVA / ESTADO DEL VIAJE -->
        <section class="uber-panel-left" id="panelIzquierdo">

            <!-- CASO A: CUANDO NO HAY VIAJE ACTIVO -> FORMULARIO UBER -->
            <div id="seccionReserva" style="<?= $viajeActivo ? 'display: none;' : '' ?>">
                
                <div class="uber-booking-header">
                    <h1 class="uber-title">Viajes y Autos KEBA - Huanta</h1>
                    <p class="uber-subtitle">Autos y camionetas desde Huanta hacia Pichari, VRAEM, Sivia, Ayacucho, Lima, Cusco, Ica y Pangoa</p>
                </div>

                <!-- INPUTS DE RUTA -->
                <div class="uber-inputs-container">
                    <div class="inputs-timeline"></div>

                    <!-- ORIGEN -->
                    <div class="input-box">
                        <div class="input-icon-pickup"></div>
                        <input type="text" id="inputOrigen" placeholder="Punto de partida (Recogida)" value="Plaza de Armas de Huanta">
                        <button type="button" class="btn-loc-quick" id="btnMiUbicacion" title="Usar mi ubicación GPS">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="8"/></svg>
                        </button>
                    </div>

                    <!-- DESTINO -->
                    <div class="input-box">
                        <div class="input-icon-dropoff"></div>
                        <input type="text" id="inputDestino" placeholder="¿A dónde viajas? (Pichari, VRAEM, Ayacucho, Cusco...)" autofocus>
                    </div>
                </div>

                <!-- ACCESOS RÁPIDOS DE DESTINOS DESDE HUANTA -->
                <div class="quick-places-row">
                    <button type="button" class="place-pill" onclick="seleccionarDestino('Pichari (VRAEM)', -12.5225, -73.8315, 185, 45.00)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg> Pichari (VRAEM)
                    </button>
                    <button type="button" class="place-pill" onclick="seleccionarDestino('VRAEM (Kimbiri / San Francisco)', -12.6200, -73.7800, 165, 40.00)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg> VRAEM (Kimbiri)
                    </button>
                    <button type="button" class="place-pill" onclick="seleccionarDestino('Sivia (VRAEM)', -12.5050, -73.8600, 140, 35.00)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg> Sivia (VRAEM)
                    </button>
                    <button type="button" class="place-pill" onclick="seleccionarDestino('Ayacucho (Huamanga)', -13.1588, -74.2236, 48, 15.00)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="8"/></svg> Ayacucho (Huamanga)
                    </button>
                    <button type="button" class="place-pill" onclick="seleccionarDestino('Lima Metropolitana', -12.0464, -77.0428, 560, 90.00)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="8"/></svg> Lima
                    </button>
                    <button type="button" class="place-pill" onclick="seleccionarDestino('Ica Centro', -14.0678, -75.7286, 380, 70.00)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="8"/></svg> Ica
                    </button>
                    <button type="button" class="place-pill" onclick="seleccionarDestino('Cusco Imperial', -13.5319, -71.9675, 540, 85.00)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="12" r="8"/></svg> Cusco
                    </button>
                    <button type="button" class="place-pill" onclick="seleccionarDestino('Pangoa (Satipo / Selva)', -11.4333, -74.4833, 220, 60.00)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg> Pangoa (Satipo)
                    </button>
                    <button type="button" class="place-pill" onclick="seleccionarDestino('Terminal Terrestre de Huanta', -12.9380, -74.2460, 2, 4.00)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg> Terminal Huanta
                    </button>
                    <button type="button" class="place-pill" onclick="seleccionarDestino('Plaza de Armas de Huanta', -12.9416, -74.2494, 1, 3.50)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg> Plaza de Armas Huanta
                    </button>
                </div>

                <!-- SELECTOR DE SERVICIOS (AUTOS, CAMIONETAS Y COLECTIVOS) -->
                <div class="service-cards-container">
                    
                    <!-- 1. AUTO COLECTIVO -->
                    <div class="service-card selected" data-servicio="Auto Colectivo" data-precio-base="15.00" onclick="seleccionarServicio(this, 'Auto Colectivo', 15.00)">
                        <div class="service-left">
                            <div class="service-icon-wrap" style="background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #2563eb;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"></path>
                                    <circle cx="7" cy="17" r="2"></circle>
                                    <circle cx="17" cy="17" r="2"></circle>
                                </svg>
                            </div>
                            <div class="service-info">
                                <h4>Auto Colectivo / Sedán</h4>
                                <div class="service-details">
                                    <span class="time">Salidas continuas</span> • <span>4 pasajeros • Rápido</span>
                                </div>
                            </div>
                        </div>
                        <div class="service-price" id="precioEstandar">S/ 15.00</div>
                    </div>

                    <!-- 2. CAMIONETA 4X4 VRAEM EXPRESS -->
                    <div class="service-card" data-servicio="Camioneta 4x4 VRAEM" data-precio-base="40.00" onclick="seleccionarServicio(this, 'Camioneta 4x4 VRAEM', 40.00)">
                        <div class="service-left">
                            <div class="service-icon-wrap" style="background: #ecfdf5; display: flex; align-items: center; justify-content: center; color: #059669;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="8" width="20" height="9" rx="2"></rect>
                                    <path d="M5 8V5a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3"></path>
                                    <circle cx="6" cy="17" r="2.5"></circle>
                                    <circle cx="18" cy="17" r="2.5"></circle>
                                </svg>
                            </div>
                            <div class="service-info">
                                <h4>Camioneta 4x4 (Ruta VRAEM)</h4>
                                <div class="service-details">
                                    <span class="time">Pichari • Sivia • Kimbiri • Pangoa</span> • <span>Hilux 4x4</span>
                                </div>
                            </div>
                        </div>
                        <div class="service-price" id="precioExpress">S/ 40.00</div>
                    </div>

                    <!-- 3. AUTO EXPRESO INTERPROVINCIAL -->
                    <div class="service-card" data-servicio="Auto Expreso" data-precio-base="80.00" onclick="seleccionarServicio(this, 'Auto Expreso', 80.00)">
                        <div class="service-left">
                            <div class="service-icon-wrap" style="background: #faf5ff; display: flex; align-items: center; justify-content: center; color: #7c3aed;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="5" width="20" height="12" rx="2"></rect>
                                    <path d="M7 17v2"></path>
                                    <path d="M17 17v2"></path>
                                    <circle cx="7" cy="13" r="1.5"></circle>
                                    <circle cx="17" cy="13" r="1.5"></circle>
                                </svg>
                            </div>
                            <div class="service-info">
                                <h4>Expreso Interprovincial</h4>
                                <div class="service-details">
                                    <span class="time">Puerta a Puerta</span> • <span>Lima • Ica • Cusco</span>
                                </div>
                            </div>
                        </div>
                        <div class="service-price" id="precioVip">S/ 80.00</div>
                    </div>

                    <!-- 4. KEBA MOTO LOCAL HUANTA -->
                    <div class="service-card" data-servicio="Keba Moto Local" data-precio-base="3.50" onclick="seleccionarServicio(this, 'Keba Moto Local', 3.50)">
                        <div class="service-left">
                            <div class="service-icon-wrap" style="background: #fffbeb; display: flex; align-items: center; justify-content: center; color: #d97706;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="18.5" cy="17.5" r="3.5"></circle>
                                    <circle cx="5.5" cy="17.5" r="3.5"></circle>
                                    <circle cx="15" cy="5" r="1"></circle>
                                    <path d="M12 17.5V14l-3-3 4-3 2 3h2"></path>
                                </svg>
                            </div>
                            <div class="service-info">
                                <h4>Keba Moto Urbana (Huanta)</h4>
                                <div class="service-details">
                                    <span class="time">Llega en 3 min</span> • <span>Viajes dentro de la ciudad</span>
                                </div>
                            </div>
                        </div>
                        <div class="service-price" id="precioMoto">S/ 3.50</div>
                    </div>

                </div>

                <!-- ACCIONES INFERIORES: PAGO Y BOTÓN SOLICITAR -->
                <div class="uber-footer-actions">
                    <div class="payment-method-selector" id="paymentSelector" onclick="togglePaymentModal()">
                        <div class="payment-left" id="pagoSeleccionado">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg>
                            <span>Efectivo (Pago al llegar)</span>
                        </div>
                        <span style="color: #64748b; font-size: 0.8rem;">Cambiar &rarr;</span>
                    </div>

                    <button type="button" class="btn-uber-primary" id="btnPedirMoto" onclick="solicitarViaje()">
                        <span>Pedir Auto / Viaje Keba</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </div>

            </div>

            <!-- CASO B: VIAJE EN CURSO O ASIGNADO (LIVE TRIP DRAWER) -->
            <div id="seccionViajeActivo" style="<?= $viajeActivo ? '' : 'display: none;' ?>">
                
                <div class="live-trip-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div class="radar-dot"></div>
                            <strong id="liveEstadoTitulo" style="font-size: 1.1rem; color: #0f172a;">
                                <?= htmlspecialchars($viajeActivo['estado'] ?? 'Asignado') ?>
                            </strong>
                        </div>
                        <div class="pin-security-badge">
                            PIN DE VIAJE
                            <span id="livePin"><?= htmlspecialchars($viajeActivo['codigo_pin'] ?? '4821') ?></span>
                        </div>
                    </div>

                    <!-- CONDUCTOR & VEHÍCULO -->
                    <div class="trip-driver-header">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="driver-avatar" style="width: 44px; height: 44px; border-radius: 50%; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #2563eb; border: 1.5px solid #bfdbfe; flex-shrink: 0;">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <div>
                                <h3 id="liveConductorNombre" style="margin: 0; font-size: 1.05rem;">
                                    <?= htmlspecialchars(($viajeActivo['conductor_nombres'] ?? 'Pedro Luis') . ' ' . ($viajeActivo['conductor_apellidos'] ?? 'Flores')) ?>
                                </h3>
                                <div style="font-size: 0.85rem; color: #475569; display: flex; align-items: center; gap: 4px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#f59e0b" stroke="#f59e0b" stroke-width="1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    <span>4.9 (124 viajes)</span>
                                </div>
                            </div>
                        </div>

                        <div style="text-align: right;">
                            <div id="livePlaca" style="font-size: 1.15rem; font-weight: 800; background: #000; color: #fff; padding: 4px 10px; border-radius: 6px; letter-spacing: 1px;">
                                <?= htmlspecialchars($viajeActivo['vehiculo_placa'] ?? '1234-5B') ?>
                            </div>
                            <div id="liveVehiculoDesc" style="font-size: 0.8rem; color: #64748b; margin-top: 4px;">
                                <?= htmlspecialchars(($viajeActivo['vehiculo_marca'] ?? 'Bajaj RE') . ' • ' . ($viajeActivo['vehiculo_color'] ?? 'Azul')) ?>
                            </div>
                        </div>
                    </div>

                    <!-- RUTA -->
                    <div style="background: #f8fafc; border-radius: 10px; padding: 12px; margin-bottom: 16px; font-size: 0.9rem;">
                        <div style="margin-bottom: 8px; display: flex; align-items: center; gap: 10px;">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981; display: inline-block; box-shadow: 0 0 6px rgba(16,185,129,0.5);"></span>
                            <strong id="liveOrigen"><?= htmlspecialchars($viajeActivo['origen'] ?? 'Plaza de Armas') ?></strong>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="width: 10px; height: 10px; border-radius: 2px; background: #2563eb; display: inline-block; box-shadow: 0 0 6px rgba(37,99,235,0.5);"></span>
                            <strong id="liveDestino"><?= htmlspecialchars($viajeActivo['destino'] ?? 'Hospital San Juan de Dios') ?></strong>
                        </div>
                    </div>

                    <!-- TARIFA Y PAGO -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <div>
                            <span style="font-size: 0.8rem; color: #64748b;">MÉTODO DE PAGO</span>
                            <div id="liveMetodoPago" style="font-weight: 700; color: #0f172a;">
                                <?= htmlspecialchars($viajeActivo['metodo_pago'] ?? 'Efectivo') ?>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <span style="font-size: 0.8rem; color: #64748b;">TARIFA FINAL</span>
                            <div id="livePrecio" style="font-size: 1.4rem; font-weight: 800; color: #0f172a;">
                                S/ <?= number_format($viajeActivo['precio_estimado'] ?? 4.00, 2) ?>
                            </div>
                        </div>
                    </div>

                    <!-- BOTONES DE ACCIÓN DEL VIAJE -->
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <div style="display: flex; gap: 10px;">
                            <button type="button" class="btn-uber-primary" style="flex: 1; background: #2563eb; padding: 12px; font-size: 0.95rem;" onclick="simularAvanceViaje()">
                                Simular Avance (Demo)
                            </button>
                            <button type="button" class="btn-uber-primary" style="flex: 1; background: #059669; padding: 12px; font-size: 0.95rem;" onclick="abrirModalCalificar()">
                                Completar & Calificar
                            </button>
                        </div>
                        <button type="button" style="width: 100%; background: #fee2e2; color: #b91c1c; border: none; padding: 12px; border-radius: 8px; font-weight: 700; cursor: pointer;" onclick="cancelarViaje()">
                            Cancelar Viaje
                        </button>
                    </div>

                </div>

            </div>

        </section>

        <!-- PANEL DERECHO: MAPA INTERACTIVO LEAFLET DE PISCO -->
        <section class="uber-panel-right">
            <div id="mapaPisco"></div>

            <!-- OVERLAY DE AUTOS EN VIVO -->
            <div class="map-status-overlay">
                <div class="radar-dot"></div>
                <div>
                    <div style="font-weight: 800; font-size: 0.9rem; color: #0f172a;">Autos y Camionetas Keba Activos</div>
                    <div style="font-size: 0.75rem; color: #64748b;">Circulando en Huanta y rutas al VRAEM</div>
                </div>
            </div>
        </section>

    </main>

    <!-- MODAL DE SELECCIÓN DE MÉTODO DE PAGO (EFECTIVO O YAPE) -->
    <div id="modalPago" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 2000; align-items: center; justify-content: center;">
        <div style="background: #fff; width: 380px; border-radius: 16px; padding: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
            <h3 style="margin-top: 0; margin-bottom: 16px;">Selecciona método de pago</h3>
            
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <!-- EFECTIVO -->
                <div style="display: flex; align-items: center; gap: 12px; padding: 14px; border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer;" onclick="cambiarMetodoPago('Efectivo')">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg>
                    <div>
                        <strong>Efectivo</strong>
                        <div style="font-size: 0.8rem; color: #64748b;">Paga directamente al conductor</div>
                    </div>
                </div>

                <!-- YAPE -->
                <div style="display: flex; align-items: center; gap: 12px; padding: 14px; border: 2px solid #d8b4fe; background: #fbf0fd; border-radius: 12px; cursor: pointer;" onclick="cambiarMetodoPago('Yape')">
                    <img src="<?= asset('img/yape_app.png') ?>" alt="Yape" style="width: 34px; height: 34px; border-radius: 8px;">
                    <div>
                        <strong style="color: #742284;">Yape</strong>
                        <div style="font-size: 0.8rem; color: #6b21a8;">Paga con código QR o número del chofer</div>
                    </div>
                </div>
            </div>

            <button type="button" style="width: 100%; margin-top: 20px; padding: 12px; background: #000; color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;" onclick="togglePaymentModal()">
                Listo
            </button>
        </div>
    </div>

    <!-- MODAL DE CALIFICACIÓN CON ESTRELLAS -->
    <div id="modalCalificar" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 2000; align-items: center; justify-content: center;">
        <div style="background: #fff; width: 400px; border-radius: 16px; padding: 28px; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px auto; border: 2px solid #a7f3d0;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <h2 style="margin: 0 0 8px 0;">¡Viaje Completado!</h2>
            <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px;">¿Qué tal fue tu experiencia con el conductor?</p>

            <!-- ESTRELLAS -->
            <div style="display: flex; justify-content: center; gap: 8px; margin-bottom: 18px; font-size: 2rem; cursor: pointer;" id="starRating">
                <span onclick="setEstrellas(1)" style="color: #f59e0b;">★</span>
                <span onclick="setEstrellas(2)" style="color: #f59e0b;">★</span>
                <span onclick="setEstrellas(3)" style="color: #f59e0b;">★</span>
                <span onclick="setEstrellas(4)" style="color: #f59e0b;">★</span>
                <span onclick="setEstrellas(5)" style="color: #f59e0b;">★</span>
            </div>

            <textarea id="comentarioCalificacion" placeholder="Deja un comentario o felicitación (opcional)..." style="width: 100%; box-sizing: border-box; height: 80px; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; margin-bottom: 20px; font-family: inherit; font-size: 0.9rem;"></textarea>

            <button type="button" class="btn-uber-primary" onclick="enviarCalificacion()">
                Enviar Calificación
            </button>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        // Variables globales
        let map;
        let markerOrigen = null;
        let markerDestino = null;
        let rutaPolyline = null;
        let mototaxiMarkers = [];
        let idViajeActivo = <?= intval($viajeActivo['id_viaje'] ?? 0) ?>;
        
        let servicioSeleccionado = 'Keba Estándar';
        let precioBase = 3.50;
        let distanciaActual = 2.5;
        let metodoPagoActual = 'Efectivo';
        let estrellasCalificacion = 5;

        // Coordenadas iniciales: Plaza de Armas de Huanta
        const COORD_HUANTA = [-12.9416, -74.2494];

        // 1. INICIALIZAR MAPA INTERACTIVO ESTILO UBER EN HUANTA
        function initMap() {
            map = L.map('mapaPisco', { zoomControl: false }).setView(COORD_HUANTA, 14);
            L.control.zoom({ position: 'bottomright' }).addTo(map);

            // Capa de mapa limpia estilo Uber
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap & KEBA RIDE'
            }).addTo(map);

            // Marcador por defecto en Origen (Plaza de Armas de Huanta)
            crearMarcadorOrigen(COORD_HUANTA[0], COORD_HUANTA[1], 'Plaza de Armas de Huanta');

            // Cargar autos y camionetas en tiempo real en el mapa
            cargarMototaxisCercanos();

            // Clic en el mapa para fijar destino
            map.on('click', function(e) {
                if (idViajeActivo > 0) return;
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                seleccionarDestino('Punto marcado en el mapa (' + lat.toFixed(4) + ', ' + lng.toFixed(4) + ')', lat, lng, 15, 15.00);
            });
        }

        // 2. CARGAR AUTOS Y CAMIONETAS EN HUANTA & VRAEM
        function cargarMototaxisCercanos() {
            const iconoAuto = L.divIcon({
                className: 'moto-marker-icon',
                html: '<div style="background:#0f172a; color:#fff; padding:4px 10px; border-radius:18px; font-size:11px; font-weight:800; display:flex; align-items:center; gap:6px; box-shadow:0 3px 10px rgba(0,0,0,0.3); border:1.5px solid #38bdf8;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg> <span>Keba Auto</span></div>',
                iconSize: [95, 28],
                iconAnchor: [47, 14]
            });

            fetch('../../controlador/cliente_controlador.php?accion=obtener_mototaxis')
                .then(r => r.json())
                .then(motos => {
                    motos.forEach(m => {
                        const marker = L.marker([m.lat, m.lng], { icon: iconoAuto }).addTo(map);
                        marker.bindPopup('<b>Vehículo KEBA</b><br>Unidad: ' + m.tipo + '<br>Placa: ' + m.placa + '<br>Chofer: ' + m.conductor + '<br>Estado: ' + m.estado);
                        mototaxiMarkers.push(marker);
                    });
                })
                .catch(() => {});
        }

        function crearMarcadorOrigen(lat, lng, texto) {
            if (markerOrigen) map.removeLayer(markerOrigen);
            const iconoOrigen = L.divIcon({
                className: 'pin-origen',
                html: '<div style="width:14px; height:14px; background:#000; border-radius:50%; border:3px solid #fff; box-shadow:0 2px 6px rgba(0,0,0,0.4);"></div>',
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });
            markerOrigen = L.marker([lat, lng], { icon: iconoOrigen }).addTo(map);
            markerOrigen.bindPopup('<b>Punto de recogida:</b><br>' + texto);
        }

        function crearMarcadorDestino(lat, lng, texto) {
            if (markerDestino) map.removeLayer(markerDestino);
            const iconoDestino = L.divIcon({
                className: 'pin-destino',
                html: '<div style="width:14px; height:14px; background:#2563eb; border-radius:2px; border:3px solid #fff; box-shadow:0 2px 6px rgba(37,99,235,0.4);"></div>',
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });
            markerDestino = L.marker([lat, lng], { icon: iconoDestino }).addTo(map);
            markerDestino.bindPopup('<b>Destino:</b><br>' + texto);

            // Trazar línea de ruta estilo Uber
            if (markerOrigen) {
                if (rutaPolyline) map.removeLayer(rutaPolyline);
                const latlngs = [markerOrigen.getLatLng(), [lat, lng]];
                rutaPolyline = L.polyline(latlngs, {
                    color: '#000000',
                    weight: 5,
                    dashArray: '8, 8',
                    opacity: 0.85
                }).addTo(map);
                map.fitBounds(rutaPolyline.getBounds(), { padding: [50, 50] });
            }
        }

        // 3. SELECCIÓN DE DESTINO RÁPIDO
        function seleccionarDestino(nombre, lat, lng, distanciaKm, precio) {
            document.getElementById('inputDestino').value = nombre;
            distanciaActual = distanciaKm;
            crearMarcadorDestino(lat, lng, nombre);
            actualizarPrecios(distanciaKm);
        }

        function actualizarPrecios(km) {
            let pCol, pCam, pExp, pMot;
            if (km >= 100) {
                // Rutas interprovinciales / VRAEM / Lima / Cusco / Pangoa / Ica
                pCol = Math.round(km * 0.22).toFixed(2);
                pCam = Math.round(km * 0.25).toFixed(2);
                pExp = Math.round(km * 0.35).toFixed(2);
                pMot = 'No aplica';
            } else if (km > 20) {
                // Ruta Huanta - Ayacucho (Huamanga 48 km)
                pCol = '15.00';
                pCam = '20.00';
                pExp = '45.00';
                pMot = 'No aplica';
            } else {
                // Rutas locales Huanta ciudad
                pCol = '5.00';
                pCam = '8.00';
                pExp = '15.00';
                pMot = Math.max(3.50, (km * 1.50)).toFixed(2);
            }

            const elEst = document.getElementById('precioEstandar');
            if (elEst) elEst.textContent = 'S/ ' + pCol;

            const elExp = document.getElementById('precioExpress');
            if (elExp) elExp.textContent = 'S/ ' + pCam;

            const elVip = document.getElementById('precioVip');
            if (elVip) elVip.textContent = 'S/ ' + pExp;

            const elMoto = document.getElementById('precioMoto');
            if (elMoto) elMoto.textContent = pMot === 'No aplica' ? 'Ruta larga' : ('S/ ' + pMot);
        }

        // 4. SELECCIÓN DE SERVICIO
        function seleccionarServicio(card, servicio, base) {
            document.querySelectorAll('.service-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            servicioSeleccionado = servicio;
            precioBase = base;
        }

        // 5. CAMBIO DE MÉTODO DE PAGO
        function togglePaymentModal() {
            const modal = document.getElementById('modalPago');
            modal.style.display = modal.style.display === 'none' ? 'flex' : 'none';
        }

        function cambiarMetodoPago(metodo) {
            metodoPagoActual = metodo;
            const container = document.getElementById('pagoSeleccionado');
            if (metodo === 'Yape') {
                container.innerHTML = '<img src="../../assets/img/yape_app.png" alt="Yape" style="width:22px; height:22px; border-radius:4px;"><span style="color:#742284; font-weight:800;">Yape (Billetera Digital)</span>';
            } else {
                container.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/></svg><span>Efectivo (Pago al llegar)</span>';
            }
            togglePaymentModal();
        }

        // 6. SOLICITAR VIAJE POR AJAX
        function solicitarViaje() {
            const origen = document.getElementById('inputOrigen').value.trim();
            const destino = document.getElementById('inputDestino').value.trim();

            if (!destino) {
                alert('Por favor indica a dónde vas (escribe o pulsa un lugar rápido).');
                document.getElementById('inputDestino').focus();
                return;
            }

            const btn = document.getElementById('btnPedirMoto');
            btn.innerHTML = '<div class="radar-dot"></div> Buscando mototaxi cercano...';
            btn.disabled = true;

            const cardSeleccionada = document.querySelector('.service-card.selected');
            const precioTexto = cardSeleccionada.querySelector('.service-price').textContent.replace('S/ ', '');
            const precioEstimado = parseFloat(precioTexto) || 4.00;

            const latDest = markerDestino ? markerDestino.getLatLng().lat : -13.7145;
            const lngDest = markerDestino ? markerDestino.getLatLng().lng : -76.2085;

            const formData = new FormData();
            formData.append('accion', 'solicitar_viaje');
            formData.append('origen', origen);
            formData.append('destino', destino);
            formData.append('tipo_servicio', servicioSeleccionado);
            formData.append('metodo_pago', metodoPagoActual);
            formData.append('precio_estimado', precioEstimado);
            formData.append('distancia_km', distanciaActual);
            formData.append('lat_destino', latDest);
            formData.append('lng_destino', lngDest);

            fetch('../../controlador/cliente_controlador.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.exito && data.viaje) {
                    idViajeActivo = data.viaje.id_viaje;
                    mostrarViajeActivo(data.viaje);
                } else {
                    alert(data.mensaje || 'Error al solicitar el viaje.');
                    btn.innerHTML = '<span>Pedir Keba Moto</span>';
                    btn.disabled = false;
                }
            })
            .catch(err => {
                alert('Error de conexión con el servidor.');
                btn.innerHTML = '<span>Pedir Keba Moto</span>';
                btn.disabled = false;
            });
        }

        function mostrarViajeActivo(v) {
            document.getElementById('seccionReserva').style.display = 'none';
            document.getElementById('seccionViajeActivo').style.display = 'block';

            document.getElementById('liveEstadoTitulo').textContent = v.estado || 'Asignado';
            document.getElementById('livePin').textContent = v.codigo_pin || '4821';
            document.getElementById('liveConductorNombre').textContent = (v.conductor_nombres || 'Pedro Luis') + ' ' + (v.conductor_apellidos || 'Flores');
            document.getElementById('livePlaca').textContent = v.vehiculo_placa || '1234-5B';
            document.getElementById('liveVehiculoDesc').textContent = (v.vehiculo_marca || 'Bajaj RE') + ' • ' + (v.vehiculo_color || 'Azul');
            document.getElementById('liveOrigen').textContent = v.origen;
            document.getElementById('liveDestino').textContent = v.destino;
            document.getElementById('liveMetodoPago').textContent = v.metodo_pago;
            document.getElementById('livePrecio').textContent = 'S/ ' + parseFloat(v.precio_estimado).toFixed(2);
        }

        // 7. SIMULAR AVANCE / COMPLETAR
        function simularAvanceViaje() {
            if (!idViajeActivo) return;
            const formData = new FormData();
            formData.append('accion', 'simular_estado');
            formData.append('id_viaje', idViajeActivo);
            formData.append('nuevo_estado', 'En Curso');

            fetch('../../controlador/cliente_controlador.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(d => {
                document.getElementById('liveEstadoTitulo').textContent = 'En Curso (Viajando)';
                alert('¡El mototaxi ha iniciado el viaje rumbo a tu destino!');
            });
        }

        function cancelarViaje() {
            if (!idViajeActivo) return;
            if (!confirm('¿Seguro que deseas cancelar el viaje?')) return;

            const formData = new FormData();
            formData.append('accion', 'cancelar_viaje');
            formData.append('id_viaje', idViajeActivo);

            fetch('../../controlador/cliente_controlador.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(d => {
                alert('Viaje cancelado.');
                idViajeActivo = 0;
                document.getElementById('seccionViajeActivo').style.display = 'none';
                document.getElementById('seccionReserva').style.display = 'block';
                const btn = document.getElementById('btnPedirMoto');
                btn.innerHTML = '<span>Pedir Keba Moto</span>';
                btn.disabled = false;
            });
        }

        // 8. CALIFICAR VIAJE
        function abrirModalCalificar() {
            document.getElementById('modalCalificar').style.display = 'flex';
        }

        function setEstrellas(n) {
            estrellasCalificacion = n;
            const spans = document.getElementById('starRating').querySelectorAll('span');
            spans.forEach((s, idx) => {
                s.style.color = (idx < n) ? '#f59e0b' : '#cbd5e1';
            });
        }

        function enviarCalificacion() {
            const com = document.getElementById('comentarioCalificacion').value.trim();
            const formData = new FormData();
            formData.append('accion', 'calificar_viaje');
            formData.append('id_viaje', idViajeActivo);
            formData.append('calificacion', estrellasCalificacion);
            formData.append('comentario', com);

            fetch('../../controlador/cliente_controlador.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(d => {
                alert(d.mensaje || '¡Gracias por viajar con KEBA MOTOS!');
                window.location.href = 'mis_viajes.php';
            });
        }

        // Iniciar al cargar
        document.addEventListener('DOMContentLoaded', initMap);
    </script>

</body>
</html>
