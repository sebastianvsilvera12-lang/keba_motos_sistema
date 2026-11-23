<?php
// ==============================================================
// MODELO: Viaje - KEBA MOTOS / KEBA S.A.C.
// Lógica de Viajes y Solicitudes de Mototaxis estilo Uber
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/conexion.php';

class Viaje {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    /**
     * Crear una nueva solicitud de viaje
     */
    public function solicitarViaje($datos) {
        // Generar PIN de seguridad de 4 dígitos (estilo Uber)
        $pin = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);

        // Si no se asigna conductor de inmediato, buscar el primer conductor activo disponible
        $idConductor = $datos['id_conductor'] ?? null;
        $idVehiculo = $datos['id_vehiculo'] ?? null;
        $estado = 'Solicitado';

        if (!$idConductor) {
            $conductorDisp = $this->obtenerConductorAleatorio();
            if ($conductorDisp) {
                $idConductor = $conductorDisp['id_conductor'];
                $idVehiculo = $conductorDisp['id_vehiculo'];
                $estado = 'Asignado';
            }
        }

        $sql = "INSERT INTO viajes (
                    id_cliente, id_usuario, id_conductor, id_vehiculo,
                    origen, lat_origen, lng_origen,
                    destino, lat_destino, lng_destino,
                    tipo_servicio, precio_estimado, precio_final,
                    metodo_pago, distancia_km, tiempo_minutos,
                    estado, codigo_pin
                ) VALUES (
                    :id_cliente, :id_usuario, :id_conductor, :id_vehiculo,
                    :origen, :lat_origen, :lng_origen,
                    :destino, :lat_destino, :lng_destino,
                    :tipo_servicio, :precio_estimado, :precio_final,
                    :metodo_pago, :distancia_km, :tiempo_minutos,
                    :estado, :codigo_pin
                )";

        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            ':id_cliente' => $datos['id_cliente'] ?? null,
            ':id_usuario' => $datos['id_usuario'] ?? null,
            ':id_conductor' => $idConductor,
            ':id_vehiculo' => $idVehiculo,
            ':origen' => $datos['origen'],
            ':lat_origen' => $datos['lat_origen'] ?? -12.9416,
            ':lng_origen' => $datos['lng_origen'] ?? -74.2494,
            ':destino' => $datos['destino'],
            ':lat_destino' => $datos['lat_destino'] ?? -12.5225,
            ':lng_destino' => $datos['lng_destino'] ?? -73.8315,
            ':tipo_servicio' => $datos['tipo_servicio'] ?? 'Auto Colectivo',
            ':precio_estimado' => $datos['precio_estimado'] ?? 15.00,
            ':precio_final' => $datos['precio_estimado'] ?? 15.00,
            ':metodo_pago' => $datos['metodo_pago'] ?? 'Efectivo',
            ':distancia_km' => $datos['distancia_km'] ?? 2.50,
            ':tiempo_minutos' => $datos['tiempo_minutos'] ?? 8,
            ':estado' => $estado,
            ':codigo_pin' => $pin
        ]);

        if ($ok) {
            $idNuevo = (int)$this->db->lastInsertId();
            return $this->obtenerPorId($idNuevo);
        }
        return false;
    }

    /**
     * Obtener detalle completo de un viaje con conductor y vehículo
     */
    public function obtenerPorId($idViaje) {
        $sql = "SELECT v.*, 
                       c.nombres AS conductor_nombres, c.apellidos AS conductor_apellidos, 
                       c.telefono AS conductor_telefono, c.foto AS conductor_foto,
                       m.placa AS vehiculo_placa, m.marca AS vehiculo_marca, 
                       m.modelo AS vehiculo_modelo, m.color AS vehiculo_color, m.foto AS vehiculo_foto,
                       u.nombres AS cliente_nombres, u.apellidos AS cliente_apellidos, u.telefono AS cliente_telefono
                FROM viajes v
                LEFT JOIN conductores c ON v.id_conductor = c.id_conductor
                LEFT JOIN vehiculos m ON v.id_vehiculo = m.id_vehiculo
                LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
                WHERE v.id_viaje = :id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idViaje]);
        return $stmt->fetch();
    }

    /**
     * Obtener viaje activo (Solicitado, Asignado o En Curso) de un usuario
     */
    public function obtenerViajeActivo($idUsuario) {
        $sql = "SELECT v.*, 
                       c.nombres AS conductor_nombres, c.apellidos AS conductor_apellidos, 
                       c.telefono AS conductor_telefono, c.foto AS conductor_foto,
                       m.placa AS vehiculo_placa, m.marca AS vehiculo_marca, 
                       m.modelo AS vehiculo_modelo, m.color AS vehiculo_color,
                       TIMESTAMPDIFF(MINUTE, v.fecha_solicitud, NOW()) AS minutos_transcurridos
                FROM viajes v
                LEFT JOIN conductores c ON v.id_conductor = c.id_conductor
                LEFT JOIN vehiculos m ON v.id_vehiculo = m.id_vehiculo
                WHERE v.id_usuario = :id_usuario 
                  AND v.estado IN ('Solicitado', 'Asignado', 'En Curso')
                ORDER BY v.id_viaje DESC
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_usuario' => $idUsuario]);
        return $stmt->fetch();
    }

    /**
     * Listar historial de viajes de un usuario
     */
    public function listarPorUsuario($idUsuario, $limite = 30) {
        $sql = "SELECT v.*, 
                       c.nombres AS conductor_nombres, c.apellidos AS conductor_apellidos,
                       m.placa AS vehiculo_placa, m.marca AS vehiculo_marca, m.modelo AS vehiculo_modelo
                FROM viajes v
                LEFT JOIN conductores c ON v.id_conductor = c.id_conductor
                LEFT JOIN vehiculos m ON v.id_vehiculo = m.id_vehiculo
                WHERE v.id_usuario = :id_usuario
                ORDER BY v.id_viaje DESC
                LIMIT " . (int)$limite;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_usuario' => $idUsuario]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener solicitudes de viaje para taxistas (pendientes o asignadas)
     */
    public function listarSolicitudesParaTaxista($idConductor = null) {
        $condicionConductor = "";
        $params = [];
        if (!empty($idConductor)) {
            $condicionConductor = "OR (v.id_conductor = :id_conductor AND v.estado IN ('Asignado', 'En Curso'))";
            $params[':id_conductor'] = (int)$idConductor;
        }

        $sql = "SELECT v.*, 
                       COALESCE(cl.nombres, u.nombres, 'Cliente Particular') AS cliente_nombres,
                       COALESCE(cl.apellidos, u.apellidos, '') AS cliente_apellidos,
                       COALESCE(cl.telefono, u.telefono, 'No registrado') AS cliente_telefono,
                       TIMESTAMPDIFF(MINUTE, v.fecha_solicitud, NOW()) AS minutos_transcurridos
                FROM viajes v
                LEFT JOIN clientes cl ON v.id_cliente = cl.id_cliente
                LEFT JOIN usuarios u ON v.id_usuario = u.id_usuario
                WHERE v.estado = 'Solicitado'
                   {$condicionConductor}
                   OR (v.fecha_solicitud >= CURDATE() AND v.estado IN ('Completado', 'Cancelado'))
                ORDER BY 
                    CASE 
                        WHEN v.estado = 'Solicitado' THEN 1
                        WHEN v.estado = 'Asignado' THEN 2
                        WHEN v.estado = 'En Curso' THEN 3
                        ELSE 4
                    END,
                    v.id_viaje DESC
                LIMIT 30";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Aceptar viaje por parte del taxista
     */
    public function aceptarViajePorTaxista($idViaje, $idConductor, $idVehiculo) {
        $sql = "UPDATE viajes 
                SET id_conductor = :id_conductor, 
                    id_vehiculo = :id_vehiculo, 
                    estado = 'Asignado' 
                WHERE id_viaje = :id AND estado = 'Solicitado'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $idViaje,
            ':id_conductor' => !empty($idConductor) ? $idConductor : null,
            ':id_vehiculo' => !empty($idVehiculo) ? $idVehiculo : null
        ]);
    }

    /**
     * Cambiar estado de un viaje
     */
    public function cambiarEstado($idViaje, $nuevoEstado, $precioFinal = null) {
        $extra = "";
        $params = [':estado' => $nuevoEstado, ':id' => $idViaje];
        if ($nuevoEstado === 'Completado') {
            $extra = ", fecha_finalizacion = NOW()";
            if ($precioFinal !== null) {
                $extra .= ", precio_final = :precio_final";
                $params[':precio_final'] = $precioFinal;
            }
        }
        $stmt = $this->db->prepare("UPDATE viajes SET estado = :estado {$extra} WHERE id_viaje = :id");
        return $stmt->execute($params);
    }

    /**
     * Calificar viaje con estrellas (1 - 5)
     */
    public function calificarViaje($idViaje, $calificacion, $comentario = '') {
        $stmt = $this->db->prepare("UPDATE viajes SET calificacion = :cal, comentario_calificacion = :com WHERE id_viaje = :id");
        return $stmt->execute([
            ':cal' => (int)$calificacion,
            ':com' => trim($comentario),
            ':id' => $idViaje
        ]);
    }

    /**
     * Cancelar viaje
     */
    public function cancelarViaje($idViaje) {
        return $this->cambiarEstado($idViaje, 'Cancelado');
    }

    /**
     * Estadísticas del cliente (total viajes, gasto, calificación promedio)
     */
    public function obtenerEstadisticasCliente($idUsuario) {
        $sql = "SELECT 
                    COUNT(*) AS total_viajes,
                    COALESCE(SUM(CASE WHEN estado = 'Completado' THEN precio_final ELSE 0 END), 0) AS total_gastado,
                    COALESCE(AVG(CASE WHEN calificacion IS NOT NULL THEN calificacion END), 5.0) AS promedio_calificacion,
                    COUNT(CASE WHEN estado = 'Completado' THEN 1 END) AS viajes_completados
                FROM viajes
                WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_usuario' => $idUsuario]);
        return $stmt->fetch();
    }

    /**
     * Obtener un conductor y vehículo disponible al azar para asignación
     */
    private function obtenerConductorAleatorio() {
        $sql = "SELECT c.id_conductor, v.id_vehiculo 
                FROM conductores c
                JOIN vehiculo_conductor vc ON c.id_conductor = vc.id_conductor AND vc.estado = 'Activo'
                JOIN vehiculos v ON vc.id_vehiculo = v.id_vehiculo
                WHERE c.estado = 'Habilitado' AND v.estado = 'Activo'
                ORDER BY RAND()
                LIMIT 1";
        $res = $this->db->query($sql)->fetch();
        if (!$res) {
            // Fallback si no hay asignación en vehiculo_conductor
            $c = $this->db->query("SELECT id_conductor FROM conductores WHERE estado = 'Habilitado' LIMIT 1")->fetch();
            $v = $this->db->query("SELECT id_vehiculo FROM vehiculos WHERE estado = 'Activo' LIMIT 1")->fetch();
            if ($c && $v) {
                return ['id_conductor' => $c['id_conductor'], 'id_vehiculo' => $v['id_vehiculo']];
            }
        }
        return $res;
    }

    /**
     * Obtener flota de autos y camionetas disponibles en Huanta y rutas al VRAEM
     */
    public function obtenerMototaxisEnMapa() {
        return [
            [
                'id' => 1,
                'placa' => 'V4X-120',
                'conductor' => 'Pedro Flores (Toyota Yaris)',
                'lat' => -12.9410,
                'lng' => -74.2485,
                'estado' => 'Disponible',
                'tipo' => '🚗 Auto Sedán - Huanta Centro'
            ],
            [
                'id' => 2,
                'placa' => 'W2B-890',
                'conductor' => 'Jorge Huamán (Hilux 4x4)',
                'lat' => -12.9385,
                'lng' => -74.2460,
                'estado' => 'Disponible',
                'tipo' => '🚙 Camioneta 4x4 - Ruta Pichari / VRAEM'
            ],
            [
                'id' => 3,
                'placa' => 'A1F-432',
                'conductor' => 'Carlos Mendoza (Auto Colectivo)',
                'lat' => -12.9430,
                'lng' => -74.2510,
                'estado' => 'Disponible',
                'tipo' => '🚗 Auto Colectivo - Ruta Ayacucho'
            ],
            [
                'id' => 4,
                'placa' => 'C9T-765',
                'conductor' => 'Luis Peña (Camioneta 4x4)',
                'lat' => -12.9360,
                'lng' => -74.2440,
                'estado' => 'En Viaje',
                'tipo' => '🚙 Camioneta 4x4 - Ruta Sivia / VRAEM'
            ],
            [
                'id' => 5,
                'placa' => 'X3E-512',
                'conductor' => 'Raúl Quispe (Expreso Interprovincial)',
                'lat' => -12.9450,
                'lng' => -74.2490,
                'estado' => 'Disponible',
                'tipo' => '🚐 Auto Expreso - Lima / Ica / Cusco'
            ]
        ];
    }
}
