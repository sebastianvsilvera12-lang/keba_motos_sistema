<?php
// ==============================================================
// MODELO: Asociacion - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/conexion.php';

class Asociacion {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function listarTodos() {
        $sql = "SELECT a.*, COUNT(v.id_vehiculo) as total_vehiculos 
                FROM asociaciones a 
                LEFT JOIN vehiculos v ON a.id_asociacion = v.id_asociacion 
                GROUP BY a.id_asociacion 
                ORDER BY a.id_asociacion DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM asociaciones WHERE id_asociacion = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function crear($datos) {
        $sql = "INSERT INTO asociaciones (nombre, ruc, resolucion_municipal, direccion, telefono, presidente, color_distintivo, estado) 
                VALUES (:nombre, :ruc, :resolucion_municipal, :direccion, :telefono, :presidente, :color_distintivo, :estado)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => trim($datos['nombre']),
            ':ruc' => !empty($datos['ruc']) ? trim($datos['ruc']) : null,
            ':resolucion_municipal' => $datos['resolucion_municipal'] ?? null,
            ':direccion' => $datos['direccion'] ?? null,
            ':telefono' => $datos['telefono'] ?? null,
            ':presidente' => $datos['presidente'] ?? null,
            ':color_distintivo' => $datos['color_distintivo'] ?? null,
            ':estado' => $datos['estado'] ?? 'Activo'
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE asociaciones SET nombre = :nombre, ruc = :ruc, resolucion_municipal = :resolucion_municipal, 
                       direccion = :direccion, telefono = :telefono, presidente = :presidente, 
                       color_distintivo = :color_distintivo, estado = :estado 
                WHERE id_asociacion = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nombre' => trim($datos['nombre']),
            ':ruc' => !empty($datos['ruc']) ? trim($datos['ruc']) : null,
            ':resolucion_municipal' => $datos['resolucion_municipal'] ?? null,
            ':direccion' => $datos['direccion'] ?? null,
            ':telefono' => $datos['telefono'] ?? null,
            ':presidente' => $datos['presidente'] ?? null,
            ':color_distintivo' => $datos['color_distintivo'] ?? null,
            ':estado' => $datos['estado']
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM asociaciones WHERE id_asociacion = :id");
        return $stmt->execute([':id' => $id]);
    }
}

