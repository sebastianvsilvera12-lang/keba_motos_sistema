<?php
// ==============================================================
// MODELO: Documento - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/conexion.php';

class Documento {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function listarTodos() {
        $sql = "SELECT d.*, v.placa, v.marca, v.modelo,
                       DATEDIFF(d.fecha_vencimiento, CURDATE()) AS dias_para_vencer
                FROM documentos_vehiculo d
                INNER JOIN vehiculos v ON d.id_vehiculo = v.id_vehiculo
                ORDER BY d.fecha_vencimiento ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function listarPorVencer($dias = 30) {
        $sql = "SELECT d.*, v.placa, v.marca, v.modelo,
                       DATEDIFF(d.fecha_vencimiento, CURDATE()) AS dias_para_vencer
                FROM documentos_vehiculo d
                INNER JOIN vehiculos v ON d.id_vehiculo = v.id_vehiculo
                WHERE DATEDIFF(d.fecha_vencimiento, CURDATE()) <= :dias
                ORDER BY d.fecha_vencimiento ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':dias' => $dias]);
        return $stmt->fetchAll();
    }

    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM documentos_vehiculo WHERE id_documento = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function crear($datos) {
        $sql = "INSERT INTO documentos_vehiculo (id_vehiculo, tipo_documento, numero_documento, entidad_emisora, 
                                                fecha_emision, fecha_vencimiento, archivo_digital, estado, observaciones) 
                VALUES (:id_vehiculo, :tipo_documento, :numero_documento, :entidad_emisora, 
                        :fecha_emision, :fecha_vencimiento, :archivo_digital, :estado, :observaciones)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_vehiculo' => $datos['id_vehiculo'],
            ':tipo_documento' => $datos['tipo_documento'],
            ':numero_documento' => trim($datos['numero_documento']),
            ':entidad_emisora' => $datos['entidad_emisora'] ?? null,
            ':fecha_emision' => $datos['fecha_emision'],
            ':fecha_vencimiento' => $datos['fecha_vencimiento'],
            ':archivo_digital' => $datos['archivo_digital'] ?? null,
            ':estado' => $datos['estado'] ?? 'Vigente',
            ':observaciones' => $datos['observaciones'] ?? null
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE documentos_vehiculo SET id_vehiculo = :id_vehiculo, tipo_documento = :tipo_documento, 
                       numero_documento = :numero_documento, entidad_emisora = :entidad_emisora, 
                       fecha_emision = :fecha_emision, fecha_vencimiento = :fecha_vencimiento, 
                       estado = :estado, observaciones = :observaciones 
                WHERE id_documento = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':id_vehiculo' => $datos['id_vehiculo'],
            ':tipo_documento' => $datos['tipo_documento'],
            ':numero_documento' => trim($datos['numero_documento']),
            ':entidad_emisora' => $datos['entidad_emisora'] ?? null,
            ':fecha_emision' => $datos['fecha_emision'],
            ':fecha_vencimiento' => $datos['fecha_vencimiento'],
            ':estado' => $datos['estado'],
            ':observaciones' => $datos['observaciones'] ?? null
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM documentos_vehiculo WHERE id_documento = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function contarPorVencer($dias = 30) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM documentos_vehiculo WHERE DATEDIFF(fecha_vencimiento, CURDATE()) <= :dias");
        $stmt->execute([':dias' => $dias]);
        return (int)$stmt->fetchColumn();
    }
}

