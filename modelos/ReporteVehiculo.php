<?php
// ==============================================================
// MODELO: ReporteVehiculo - KEBA MOTOS
// Gestión de Daños, Revisiones, Limpieza y Novedades de Unidades
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/conexion.php';

class ReporteVehiculo {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    /**
     * Registra un nuevo reporte/mensaje enviado por el taxista
     */
    public function crearReporte($idVehiculo, $idUsuario, $idConductor, $tipoAsunto, $descripcionDanos, $estadoLimpieza, $fechaRevisionSugerida = null, $prioridad = 'Normal', $fotoEvidencia = null) {
        $sql = "INSERT INTO reportes_vehiculo (id_vehiculo, id_usuario, id_conductor, tipo_asunto, descripcion_danos, 
                                              estado_limpieza, fecha_revision_sugerida, prioridad, foto_evidencia, estado)
                VALUES (:id_vehiculo, :id_usuario, :id_conductor, :tipo_asunto, :descripcion_danos, 
                        :estado_limpieza, :fecha_revision, :prioridad, :foto, 'Pendiente')";
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            ':id_vehiculo' => $idVehiculo,
            ':id_usuario' => $idUsuario,
            ':id_conductor' => !empty($idConductor) ? $idConductor : null,
            ':tipo_asunto' => $tipoAsunto,
            ':descripcion_danos' => $descripcionDanos,
            ':estado_limpieza' => $estadoLimpieza,
            ':fecha_revision' => !empty($fechaRevisionSugerida) ? $fechaRevisionSugerida : null,
            ':prioridad' => $prioridad,
            ':foto' => $fotoEvidencia
        ]);

        if ($ok) {
            // Actualizar estado general en tabla vehiculos
            try {
                $upVeh = $this->db->prepare("
                    UPDATE vehiculos 
                    SET estado_limpieza = CASE 
                            WHEN :limpieza = 'Requiere Lavado Urgente' THEN 'Requiere Lavado'
                            WHEN :limpieza = 'Regular / Aceptable' THEN 'Regular'
                            ELSE 'Limpia'
                        END,
                        observaciones_danos = :danos
                    WHERE id_vehiculo = :id_vehiculo
                ");
                $upVeh->execute([
                    ':limpieza' => $estadoLimpieza,
                    ':danos' => mb_substr($descripcionDanos, 0, 250),
                    ':id_vehiculo' => $idVehiculo
                ]);
            } catch (Exception $e) {}
        }

        return $ok;
    }

    /**
     * Lista los reportes enviados por un taxista específico
     */
    public function listarPorUsuario($idUsuario) {
        $sql = "SELECT r.*, v.placa, v.marca, v.modelo, v.numero_padron, v.es_empresa,
                       u_admin.nombres AS admin_nombres, u_admin.apellidos AS admin_apellidos
                FROM reportes_vehiculo r
                INNER JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
                LEFT JOIN usuarios u_admin ON r.id_usuario_admin = u_admin.id_usuario
                WHERE r.id_usuario = :id_usuario
                ORDER BY r.id_reporte DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_usuario' => $idUsuario]);
        return $stmt->fetchAll();
    }

    /**
     * Lista todos los reportes para la bandeja del Administrador
     */
    public function listarTodos($estadoFiltro = null) {
        $sql = "SELECT r.*, v.placa, v.marca, v.modelo, v.numero_padron, v.color, v.es_empresa,
                       u.usuario, u.nombres AS conductor_nombres, u.apellidos AS conductor_apellidos, u.telefono AS conductor_telefono,
                       u_admin.nombres AS admin_nombres, u_admin.apellidos AS admin_apellidos
                FROM reportes_vehiculo r
                INNER JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
                INNER JOIN usuarios u ON r.id_usuario = u.id_usuario
                LEFT JOIN usuarios u_admin ON r.id_usuario_admin = u_admin.id_usuario";
        
        if ($estadoFiltro) {
            $sql .= " WHERE r.estado = :estado";
        }
        $sql .= " ORDER BY FIELD(r.estado, 'Pendiente', 'En Revisión', 'Atendido', 'Resuelto'), r.created_at DESC";

        $stmt = $this->db->prepare($sql);
        if ($estadoFiltro) {
            $stmt->execute([':estado' => $estadoFiltro]);
        } else {
            $stmt->execute();
        }
        return $stmt->fetchAll();
    }

    /**
     * Cuenta los reportes pendientes de atención para el administrador
     */
    public function contarPendientes() {
        $sql = "SELECT COUNT(*) FROM reportes_vehiculo WHERE estado = 'Pendiente'";
        return (int)$this->db->query($sql)->fetchColumn();
    }

    /**
     * Obtiene un reporte por su ID
     */
    public function obtenerPorId($idReporte) {
        $sql = "SELECT r.*, v.placa, v.marca, v.modelo, v.numero_padron, v.color,
                       u.usuario, u.nombres AS conductor_nombres, u.apellidos AS conductor_apellidos, u.telefono AS conductor_telefono
                FROM reportes_vehiculo r
                INNER JOIN vehiculos v ON r.id_vehiculo = v.id_vehiculo
                INNER JOIN usuarios u ON r.id_usuario = u.id_usuario
                WHERE r.id_reporte = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idReporte]);
        return $stmt->fetch();
    }

    /**
     * El Administrador responde al taxista y actualiza el estado del reporte y del vehículo
     */
    public function responderReporte($idReporte, $idAdmin, $respuestaAdmin, $nuevoEstado, $fechaRevisionDefinitiva = null, $estadoMecanico = null) {
        $sql = "UPDATE reportes_vehiculo 
                SET respuesta_admin = :respuesta,
                    id_usuario_admin = :id_admin,
                    estado = :estado,
                    fecha_respuesta = NOW()
                WHERE id_reporte = :id";
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute([
            ':respuesta' => trim($respuestaAdmin),
            ':id_admin' => $idAdmin,
            ':estado' => $nuevoEstado,
            ':id' => $idReporte
        ]);

        if ($ok) {
            // Actualizar fecha próxima de revisión y estado mecánico en vehiculos si fue indicado
            $rep = $this->obtenerPorId($idReporte);
            if ($rep && (!empty($fechaRevisionDefinitiva) || !empty($estadoMecanico))) {
                try {
                    $sqlUp = "UPDATE vehiculos SET ";
                    $params = [':id' => $rep['id_vehiculo']];
                    $updates = [];
                    if (!empty($fechaRevisionDefinitiva)) {
                        $updates[] = "fecha_proxima_revision = :rev";
                        $params[':rev'] = $fechaRevisionDefinitiva;
                    }
                    if (!empty($estadoMecanico)) {
                        $updates[] = "estado_mecanico = :mec";
                        $params[':mec'] = $estadoMecanico;
                    }
                    $sqlUp .= implode(', ', $updates) . " WHERE id_vehiculo = :id";
                    $stmtVeh = $this->db->prepare($sqlUp);
                    $stmtVeh->execute($params);
                } catch (Exception $e) {}
            }
        }

        return $ok;
    }
}

