<?php
// ==============================================================
// CONTROLADOR: Cliente / Viajes estilo Uber - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/constantes.php';
require_once dirname(__DIR__) . '/configuracion/session.php';
require_once dirname(__DIR__) . '/ayudantes/funciones.php';
require_once dirname(__DIR__) . '/modelos/Viaje.php';

$usuario = obtenerUsuarioActual();
if (!$usuario) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['exito' => false, 'mensaje' => 'Sesión no válida o expirada.']);
        exit;
    }
    redireccionar('vista/auth/login.php');
}

$viajeMod = new Viaje();
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

// Manejo de peticiones AJAX o Form POST
switch ($accion) {

    case 'solicitar_viaje':
        header('Content-Type: application/json');
        $origen = trim($_POST['origen'] ?? '');
        $destino = trim($_POST['destino'] ?? '');
        $tipoServicio = trim($_POST['tipo_servicio'] ?? 'Keba Estándar');
        $metodoPago = trim($_POST['metodo_pago'] ?? 'Efectivo');
        $distanciaKm = floatval($_POST['distancia_km'] ?? 2.5);
        $precioEstimado = floatval($_POST['precio_estimado'] ?? 4.0);
        $tiempoMinutos = intval($_POST['tiempo_minutos'] ?? 8);
        $latOrigen = floatval($_POST['lat_origen'] ?? -13.7103);
        $lngOrigen = floatval($_POST['lng_origen'] ?? -76.2054);
        $latDestino = floatval($_POST['lat_destino'] ?? -13.7150);
        $lngDestino = floatval($_POST['lng_destino'] ?? -76.2080);

        if (empty($origen) || empty($destino)) {
            echo json_encode(['exito' => false, 'mensaje' => 'Debes ingresar un punto de partida y un destino.']);
            exit;
        }

        $datos = [
            'id_usuario' => $usuario['id_usuario'],
            'id_cliente' => $usuario['id_cliente'] ?? null,
            'origen' => $origen,
            'destino' => $destino,
            'lat_origen' => $latOrigen,
            'lng_origen' => $lngOrigen,
            'lat_destino' => $latDestino,
            'lng_destino' => $lngDestino,
            'tipo_servicio' => $tipoServicio,
            'precio_estimado' => $precioEstimado,
            'metodo_pago' => $metodoPago,
            'distancia_km' => $distanciaKm,
            'tiempo_minutos' => $tiempoMinutos
        ];

        $viaje = $viajeMod->solicitarViaje($datos);
        if ($viaje) {
            echo json_encode([
                'exito' => true,
                'mensaje' => '¡Mototaxi solicitado con éxito!',
                'viaje' => $viaje
            ]);
        } else {
            echo json_encode(['exito' => false, 'mensaje' => 'Error al registrar la solicitud de viaje.']);
        }
        exit;

    case 'cancelar_viaje':
        header('Content-Type: application/json');
        $idViaje = intval($_POST['id_viaje'] ?? 0);
        if ($idViaje > 0) {
            $ok = $viajeMod->cancelarViaje($idViaje);
            echo json_encode(['exito' => $ok, 'mensaje' => $ok ? 'Viaje cancelado.' : 'No se pudo cancelar el viaje.']);
        } else {
            echo json_encode(['exito' => false, 'mensaje' => 'ID de viaje no válido.']);
        }
        exit;

    case 'calificar_viaje':
        header('Content-Type: application/json');
        $idViaje = intval($_POST['id_viaje'] ?? 0);
        $estrellas = intval($_POST['calificacion'] ?? 5);
        $comentario = trim($_POST['comentario'] ?? '');

        if ($idViaje > 0) {
            $ok = $viajeMod->calificarViaje($idViaje, $estrellas, $comentario);
            echo json_encode(['exito' => $ok, 'mensaje' => $ok ? '¡Gracias por calificar tu viaje!' : 'Error al guardar la calificación.']);
        } else {
            echo json_encode(['exito' => false, 'mensaje' => 'ID de viaje no válido.']);
        }
        exit;

    case 'simular_estado':
        // Simulación para demo interactiva (pasar a 'En Curso' o 'Completado')
        header('Content-Type: application/json');
        $idViaje = intval($_POST['id_viaje'] ?? 0);
        $nuevoEstado = trim($_POST['nuevo_estado'] ?? 'En Curso');
        $ok = $viajeMod->cambiarEstado($idViaje, $nuevoEstado);
        echo json_encode(['exito' => $ok, 'estado' => $nuevoEstado]);
        exit;

    case 'obtener_mototaxis':
        header('Content-Type: application/json');
        echo json_encode($viajeMod->obtenerMototaxisEnMapa());
        exit;

    case 'obtener_viaje_activo':
        header('Content-Type: application/json');
        $activo = $viajeMod->obtenerViajeActivo($usuario['id_usuario']);
        echo json_encode(['activo' => (bool)$activo, 'viaje' => $activo]);
        exit;

    default:
        redireccionar('vista/cliente/index.php');
        break;
}
