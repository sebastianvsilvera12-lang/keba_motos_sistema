<?php
// ==============================================================
// MODELO: CuotaMensual - KEBA MOTOS
// Gestión de Cuotas Mensuales de Taxistas (Exclusivo por Yape)
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/conexion.php';

class CuotaMensual {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    /**
     * Registra un nuevo comprobante de pago de cuota mensual vía Yape (guardando la imagen en BD como BLOB y en disco)
     */
    public function registrarCuota($idUsuario, $idConductor, $idVehiculo, $remitenteNombre, $mesPeriodo, $monto, $numeroOperacion, $comprobanteImg, $blobData = null, $mimeType = 'image/jpeg', $observaciones = null) {
        $sql = "INSERT INTO cuotas_mensuales 
                (id_usuario, id_conductor, id_vehiculo, remitente_nombre, mes_periodo, monto, metodo_pago, numero_operacion, comprobante_img, comprobante_blob, comprobante_mime, estado, observaciones)
                VALUES 
                (:id_usuario, :id_conductor, :id_vehiculo, :remitente_nombre, :mes_periodo, :monto, 'Yape', :numero_operacion, :comprobante_img, :comprobante_blob, :comprobante_mime, 'Pendiente', :observaciones)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_usuario', (int)$idUsuario, PDO::PARAM_INT);
        $stmt->bindValue(':id_conductor', !empty($idConductor) ? (int)$idConductor : null, PDO::PARAM_INT);
        $stmt->bindValue(':id_vehiculo', !empty($idVehiculo) ? (int)$idVehiculo : null, PDO::PARAM_INT);
        $stmt->bindValue(':remitente_nombre', trim($remitenteNombre), PDO::PARAM_STR);
        $stmt->bindValue(':mes_periodo', trim($mesPeriodo), PDO::PARAM_STR);
        $stmt->bindValue(':monto', (float)$monto);
        $stmt->bindValue(':numero_operacion', !empty($numeroOperacion) ? trim($numeroOperacion) : null, PDO::PARAM_STR);
        $stmt->bindValue(':comprobante_img', $comprobanteImg, PDO::PARAM_STR);
        $stmt->bindValue(':comprobante_blob', $blobData, PDO::PARAM_LOB);
        $stmt->bindValue(':comprobante_mime', $mimeType, PDO::PARAM_STR);
        $stmt->bindValue(':observaciones', !empty($observaciones) ? trim($observaciones) : null, PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    /**
     * Cuenta la cantidad de cuotas Yape pendientes de validación
     */
    public function contarPendientes() {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM cuotas_mensuales WHERE estado = 'Pendiente'");
        return (int)($stmt->fetch()['total'] ?? 0);
    }

    /**
     * Suma el total recaudado en cuotas aprobadas
     */
    public function sumarTotalAprobado() {
        $stmt = $this->db->query("SELECT COALESCE(SUM(monto), 0) AS total FROM cuotas_mensuales WHERE estado = 'Aprobado'");
        return (float)($stmt->fetch()['total'] ?? 0);
    }

    /**
     * Retorna la imagen del comprobante (ya sea desde disco o directamente de la BD en base64)
     */
    public static function obtenerSrcComprobante($cuota) {
        if (!empty($cuota['comprobante_img']) && file_exists(ROOT_PATH . 'subidas/cuotas_yape/' . $cuota['comprobante_img'])) {
            return subidaUrl('cuotas_yape/' . $cuota['comprobante_img']);
        }
        if (!empty($cuota['comprobante_blob'])) {
            $mime = !empty($cuota['comprobante_mime']) ? $cuota['comprobante_mime'] : 'image/jpeg';
            return 'data:' . $mime . ';base64,' . base64_encode($cuota['comprobante_blob']);
        }
        return '';
    }

    /**
     * Obtiene todas las cuotas enviadas por un usuario taxista específico
     */
    public function listarPorUsuario($idUsuario) {
        $sql = "SELECT c.*, v.placa, v.marca, v.modelo, v.numero_padron,
                       u_adm.nombres AS admin_nombre, u_adm.apellidos AS admin_apellidos
                FROM cuotas_mensuales c
                LEFT JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
                LEFT JOIN usuarios u_adm ON c.id_admin_valida = u_adm.id_usuario
                WHERE c.id_usuario = :id_usuario
                ORDER BY c.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_usuario' => (int)$idUsuario]);
        return $stmt->fetchAll();
    }

    /**
     * Obtiene todas las cuotas registradas para la administración
     */
    public function listarTodas($filtroEstado = null) {
        $sql = "SELECT c.*, v.placa, v.marca, v.modelo, v.numero_padron,
                       u.usuario, u.dni AS usuario_dni,
                       u_adm.nombres AS admin_nombre, u_adm.apellidos AS admin_apellidos
                FROM cuotas_mensuales c
                INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
                LEFT JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
                LEFT JOIN usuarios u_adm ON c.id_admin_valida = u_adm.id_usuario";
        
        if ($filtroEstado && in_array($filtroEstado, ['Pendiente', 'Aprobado', 'Rechazado'])) {
            $sql .= " WHERE c.estado = :estado";
            $sql .= " ORDER BY c.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':estado' => $filtroEstado]);
            return $stmt->fetchAll();
        } else {
            $sql .= " ORDER BY FIELD(c.estado, 'Pendiente', 'Aprobado', 'Rechazado'), c.created_at DESC";
            return $this->db->query($sql)->fetchAll();
        }
    }

    /**
     * Obtiene una cuota por su ID
     */
    public function obtenerPorId($idCuota) {
        $sql = "SELECT c.*, v.placa, v.marca, v.modelo, v.numero_padron,
                       u.usuario, u.dni AS usuario_dni, u.telefono AS usuario_telefono,
                       cond.numero_licencia,
                       u_adm.nombres AS admin_nombre, u_adm.apellidos AS admin_apellidos
                FROM cuotas_mensuales c
                INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
                LEFT JOIN conductores cond ON c.id_conductor = cond.id_conductor
                LEFT JOIN vehiculos v ON c.id_vehiculo = v.id_vehiculo
                LEFT JOIN usuarios u_adm ON c.id_admin_valida = u_adm.id_usuario
                WHERE c.id_cuota = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => (int)$idCuota]);
        return $stmt->fetch();
    }

    /**
     * Validación del administrador: Aprobar o Rechazar el comprobante Yape
     */
    public function responderCuota($idCuota, $estado, $respuestaAdmin, $idAdmin) {
        if (!in_array($estado, ['Aprobado', 'Rechazado'])) {
            return false;
        }

        $sql = "UPDATE cuotas_mensuales 
                SET estado = :estado, 
                    respuesta_admin = :respuesta, 
                    id_admin_valida = :admin, 
                    fecha_validacion = NOW() 
                WHERE id_cuota = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':estado' => $estado,
            ':respuesta' => trim($respuestaAdmin),
            ':admin' => (int)$idAdmin,
            ':id' => (int)$idCuota
        ]);
    }

    /**
     * Resumen de cuotas de un taxista
     */
    public function obtenerResumenTaxista($idUsuario) {
        $sql = "SELECT 
                    COUNT(*) AS total,
                    SUM(CASE WHEN estado = 'Aprobado' THEN 1 ELSE 0 END) AS aprobados,
                    SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) AS pendientes,
                    SUM(CASE WHEN estado = 'Rechazado' THEN 1 ELSE 0 END) AS rechazados,
                    COALESCE(SUM(CASE WHEN estado = 'Aprobado' THEN monto ELSE 0 END), 0) AS monto_total_aprobado
                FROM cuotas_mensuales
                WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_usuario' => (int)$idUsuario]);
        return $stmt->fetch();
    }
}

