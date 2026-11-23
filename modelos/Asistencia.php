<?php
// ==============================================================
// MODELO: Asistencia - KEBA MOTOS
// Gestión de Asistencia y Turnos de Taxistas / Uso de Mototaxis
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/conexion.php';

class Asistencia {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    /**
     * Obtiene el registro de asistencia del usuario para el día de hoy
     */
    public function obtenerAsistenciaHoy($idUsuario) {
        $sql = "SELECT a.*, v.placa, v.marca, v.modelo, v.numero_padron, v.color,
                       c.nombres AS conductor_nombres, c.apellidos AS conductor_apellidos, c.foto AS conductor_foto, c.numero_licencia
                FROM asistencia a
                LEFT JOIN vehiculos v ON a.id_vehiculo = v.id_vehiculo
                LEFT JOIN conductores c ON a.id_conductor = c.id_conductor
                WHERE a.id_usuario = :id_usuario AND a.fecha = CURDATE()
                ORDER BY a.id_asistencia DESC
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_usuario' => $idUsuario]);
        return $stmt->fetch();
    }

    /**
     * Determina el turno según la hora actual
     */
    public static function detectarTurnoActual() {
        $hora = (int)date('G');
        if ($hora >= 6 && $hora < 14) {
            return 'Mañana';
        } elseif ($hora >= 14 && $hora < 22) {
            return 'Tarde';
        } else {
            return 'Noche';
        }
    }

    /**
     * Registra la entrada / inicio de turno de un taxista
     */
    public function registrarEntrada($idUsuario, $idConductor, $idVehiculo, $turno = null, $kmInicial = null, $observaciones = null) {
        // Verificar si ya tiene un registro hoy que esté "En Turno"
        $asistenciaActiva = $this->obtenerAsistenciaHoy($idUsuario);
        if ($asistenciaActiva && $asistenciaActiva['estado'] === 'En Turno') {
            return false; // Ya tiene turno abierto hoy
        }

        if (empty($turno)) {
            $turno = self::detectarTurnoActual();
        }

        $sql = "INSERT INTO asistencia (id_usuario, id_conductor, id_vehiculo, fecha, hora_ingreso, turno, estado, kilometraje_inicial, observaciones)
                VALUES (:id_usuario, :id_conductor, :id_vehiculo, CURDATE(), CURTIME(), :turno, 'En Turno', :km_inicial, :observaciones)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_usuario' => $idUsuario,
            ':id_conductor' => !empty($idConductor) ? $idConductor : null,
            ':id_vehiculo' => !empty($idVehiculo) ? $idVehiculo : null,
            ':turno' => $turno,
            ':km_inicial' => !empty($kmInicial) ? (int)$kmInicial : null,
            ':observaciones' => !empty($observaciones) ? $observaciones : null
        ]);
    }

    /**
     * Registra la salida / cierre de turno de un taxista
     */
    public function registrarSalida($idAsistencia, $kmFinal = null, $observaciones = null) {
        $sql = "UPDATE asistencia 
                SET hora_salida = CURTIME(), 
                    estado = 'Finalizado', 
                    kilometraje_final = :km_final,
                    observaciones = CASE 
                        WHEN :observaciones IS NOT NULL AND :observaciones != '' THEN CONCAT(COALESCE(observaciones, ''), ' | Salida: ', :observaciones)
                        ELSE observaciones 
                    END
                WHERE id_asistencia = :id AND estado = 'En Turno'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $idAsistencia,
            ':km_final' => !empty($kmFinal) ? (int)$kmFinal : null,
            ':observaciones' => !empty($observaciones) ? $observaciones : null
        ]);
    }

    /**
     * Lista el historial de asistencias de un taxista
     */
    public function listarPorUsuario($idUsuario, $limite = 30) {
        $sql = "SELECT a.*, v.placa, v.marca, v.modelo, v.numero_padron, v.color
                FROM asistencia a
                LEFT JOIN vehiculos v ON a.id_vehiculo = v.id_vehiculo
                WHERE a.id_usuario = :id_usuario
                ORDER BY a.fecha DESC, a.hora_ingreso DESC
                LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_usuario', (int)$idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lista general de asistencias para el Administrador con soporte de filtros
     */
    public function listarTodas($filtros = [], $limite = 100) {
        $condiciones = [];
        $params = [];

        if (!empty($filtros['fecha'])) {
            $condiciones[] = "a.fecha = :fecha";
            $params[':fecha'] = $filtros['fecha'];
        }

        if (!empty($filtros['estado'])) {
            $condiciones[] = "a.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        if (!empty($filtros['buscar'])) {
            $condiciones[] = "(u.nombres LIKE :buscar OR u.apellidos LIKE :buscar OR u.dni LIKE :buscar 
                              OR c.nombres LIKE :buscar OR c.apellidos LIKE :buscar OR c.numero_licencia LIKE :buscar
                              OR v.placa LIKE :buscar OR v.numero_padron LIKE :buscar)";
            $params[':buscar'] = '%' . $filtros['buscar'] . '%';
        }

        $where = !empty($condiciones) ? 'WHERE ' . implode(' AND ', $condiciones) : '';

        $sql = "SELECT a.*, 
                       u.usuario, u.dni AS usuario_dni, u.nombres AS usuario_nombre, u.apellidos AS usuario_apellidos,
                       c.nombres AS conductor_nombres, c.apellidos AS conductor_apellidos, c.foto AS conductor_foto, c.numero_licencia,
                       v.placa, v.numero_padron, v.marca, v.modelo, v.color,
                       TIMEDIFF(COALESCE(a.hora_salida, CURTIME()), a.hora_ingreso) AS tiempo_servicio
                FROM asistencia a
                INNER JOIN usuarios u ON a.id_usuario = u.id_usuario
                LEFT JOIN conductores c ON a.id_conductor = c.id_conductor
                LEFT JOIN vehiculos v ON a.id_vehiculo = v.id_vehiculo
                {$where}
                ORDER BY a.fecha DESC, a.hora_ingreso DESC
                LIMIT :limite";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Cuenta cuántos taxistas están en turno actualmente
     */
    public function contarEnTurnoHoy() {
        $sql = "SELECT COUNT(*) FROM asistencia WHERE fecha = CURDATE() AND estado = 'En Turno'";
        return (int)$this->db->query($sql)->fetchColumn();
    }

    /**
     * Cuenta total de asistencias iniciadas hoy
     */
    public function contarHoy() {
        $sql = "SELECT COUNT(*) FROM asistencia WHERE fecha = CURDATE()";
        return (int)$this->db->query($sql)->fetchColumn();
    }

    /**
     * Cuenta total de turnos finalizados hoy
     */
    public function contarFinalizadosHoy() {
        $sql = "SELECT COUNT(*) FROM asistencia WHERE fecha = CURDATE() AND estado = 'Finalizado'";
        return (int)$this->db->query($sql)->fetchColumn();
    }
}
