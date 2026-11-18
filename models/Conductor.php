<?php
// ==============================================================
// MODELO: Conductor - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/config/conexion.php';

class Conductor {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function listarTodos() {
        $sql = "SELECT * FROM conductores ORDER BY id_conductor DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM conductores WHERE id_conductor = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function crear($datos) {
        $sql = "INSERT INTO conductores (dni, nombres, apellidos, fecha_nacimiento, telefono, direccion, 
                                        numero_licencia, categoria_licencia, fecha_emision_licencia, 
                                        fecha_vencimiento_licencia, estado) 
                VALUES (:dni, :nombres, :apellidos, :fecha_nacimiento, :telefono, :direccion, 
                        :numero_licencia, :categoria_licencia, :fecha_emision_licencia, 
                        :fecha_vencimiento_licencia, :estado)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':dni' => trim($datos['dni']),
            ':nombres' => $datos['nombres'],
            ':apellidos' => $datos['apellidos'],
            ':fecha_nacimiento' => !empty($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : null,
            ':telefono' => $datos['telefono'] ?? null,
            ':direccion' => $datos['direccion'] ?? null,
            ':numero_licencia' => strtoupper(trim($datos['numero_licencia'])),
            ':categoria_licencia' => $datos['categoria_licencia'] ?? 'B-IIc',
            ':fecha_emision_licencia' => !empty($datos['fecha_emision_licencia']) ? $datos['fecha_emision_licencia'] : null,
            ':fecha_vencimiento_licencia' => $datos['fecha_vencimiento_licencia'],
            ':estado' => $datos['estado'] ?? 'Habilitado'
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE conductores SET dni = :dni, nombres = :nombres, apellidos = :apellidos, 
                       fecha_nacimiento = :fecha_nacimiento, telefono = :telefono, direccion = :direccion, 
                       numero_licencia = :numero_licencia, categoria_licencia = :categoria_licencia, 
                       fecha_emision_licencia = :fecha_emision_licencia, 
                       fecha_vencimiento_licencia = :fecha_vencimiento_licencia, estado = :estado 
                WHERE id_conductor = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':dni' => trim($datos['dni']),
            ':nombres' => $datos['nombres'],
            ':apellidos' => $datos['apellidos'],
            ':fecha_nacimiento' => !empty($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : null,
            ':telefono' => $datos['telefono'] ?? null,
            ':direccion' => $datos['direccion'] ?? null,
            ':numero_licencia' => strtoupper(trim($datos['numero_licencia'])),
            ':categoria_licencia' => $datos['categoria_licencia'],
            ':fecha_emision_licencia' => !empty($datos['fecha_emision_licencia']) ? $datos['fecha_emision_licencia'] : null,
            ':fecha_vencimiento_licencia' => $datos['fecha_vencimiento_licencia'],
            ':estado' => $datos['estado']
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM conductores WHERE id_conductor = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function contarTotal() {
        return (int)$this->db->query("SELECT COUNT(*) FROM conductores WHERE estado = 'Habilitado'")->fetchColumn();
    }
}

