<?php
// ==============================================================
// MODELO: Infraccion - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/conexion.php';

class Infraccion {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function listarTodos() {
        $sql = "SELECT i.*, ti.codigo AS codigo_infraccion, ti.descripcion AS descripcion_infraccion, ti.gravedad,
                       v.placa, c.nombres AS conductor_nombres, c.apellidos AS conductor_apellidos, c.dni AS conductor_dni,
                       u.nombres AS fiscalizador_nombres
                FROM infracciones i
                INNER JOIN tipos_infraccion ti ON i.id_tipo_infraccion = ti.id_tipo_infraccion
                INNER JOIN vehiculos v ON i.id_vehiculo = v.id_vehiculo
                LEFT JOIN conductores c ON i.id_conductor = c.id_conductor
                INNER JOIN usuarios u ON i.id_usuario_fiscalizador = u.id_usuario
                ORDER BY i.fecha_infraccion DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function listarPendientes() {
        $sql = "SELECT i.*, ti.codigo AS codigo_infraccion, ti.descripcion AS descripcion_infraccion,
                       v.placa, c.nombres AS conductor_nombres, c.apellidos AS conductor_apellidos
                FROM infracciones i
                INNER JOIN tipos_infraccion ti ON i.id_tipo_infraccion = ti.id_tipo_infraccion
                INNER JOIN vehiculos v ON i.id_vehiculo = v.id_vehiculo
                LEFT JOIN conductores c ON i.id_conductor = c.id_conductor
                WHERE i.estado_pago = 'Pendiente'
                ORDER BY i.fecha_infraccion DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM infracciones WHERE id_infraccion = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function listarTipos() {
        return $this->db->query("SELECT * FROM tipos_infraccion WHERE estado = 'Activo'")->fetchAll();
    }

    public function crear($datos) {
        $sql = "INSERT INTO infracciones (numero_acta, id_tipo_infraccion, id_vehiculo, id_conductor, 
                                         id_usuario_fiscalizador, fecha_infraccion, lugar_infraccion, monto, observacion, estado_pago) 
                VALUES (:numero_acta, :id_tipo_infraccion, :id_vehiculo, :id_conductor, 
                        :id_usuario_fiscalizador, :fecha_infraccion, :lugar_infraccion, :monto, :observacion, :estado_pago)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':numero_acta' => trim($datos['numero_acta']),
            ':id_tipo_infraccion' => $datos['id_tipo_infraccion'],
            ':id_vehiculo' => $datos['id_vehiculo'],
            ':id_conductor' => !empty($datos['id_conductor']) ? $datos['id_conductor'] : null,
            ':id_usuario_fiscalizador' => $datos['id_usuario_fiscalizador'],
            ':fecha_infraccion' => $datos['fecha_infraccion'],
            ':lugar_infraccion' => $datos['lugar_infraccion'],
            ':monto' => $datos['monto'],
            ':observacion' => $datos['observacion'] ?? null,
            ':estado_pago' => $datos['estado_pago'] ?? 'Pendiente'
        ]);
    }

    public function registrarPago($id, $comprobante) {
        $sql = "UPDATE infracciones SET estado_pago = 'Pagado', fecha_pago = NOW(), comprobante_pago = :comp WHERE id_infraccion = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':comp' => $comprobante, ':id' => $id]);
    }

    public function anular($id) {
        $stmt = $this->db->prepare("UPDATE infracciones SET estado_pago = 'Anulado' WHERE id_infraccion = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function contarPendientes() {
        return (int)$this->db->query("SELECT COUNT(*) FROM infracciones WHERE estado_pago = 'Pendiente'")->fetchColumn();
    }
}

