<?php
// ==============================================================
// MODELO: Vehiculo - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/config/conexion.php';

class Vehiculo {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function listarTodos() {
        $sql = "SELECT v.*, p.nombres_razon_social AS propietario_nombre, p.numero_documento AS propietario_doc,
                       a.nombre AS asociacion_nombre
                FROM vehiculos v
                LEFT JOIN propietarios p ON v.id_propietario = p.id_propietario
                LEFT JOIN asociaciones a ON v.id_asociacion = a.id_asociacion
                ORDER BY v.id_vehiculo DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM vehiculos WHERE id_vehiculo = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function crear($datos) {
        $sql = "INSERT INTO vehiculos (placa, numero_padron, marca, modelo, color, anio_fabricacion, 
                                      numero_motor, numero_chasis, id_propietario, id_asociacion, estado) 
                VALUES (:placa, :numero_padron, :marca, :modelo, :color, :anio_fabricacion, 
                        :numero_motor, :numero_chasis, :id_propietario, :id_asociacion, :estado)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':placa' => strtoupper(trim($datos['placa'])),
            ':numero_padron' => $datos['numero_padron'] ?? null,
            ':marca' => $datos['marca'],
            ':modelo' => $datos['modelo'],
            ':color' => $datos['color'],
            ':anio_fabricacion' => $datos['anio_fabricacion'] ?? null,
            ':numero_motor' => $datos['numero_motor'] ?? null,
            ':numero_chasis' => $datos['numero_chasis'] ?? null,
            ':id_propietario' => $datos['id_propietario'],
            ':id_asociacion' => !empty($datos['id_asociacion']) ? $datos['id_asociacion'] : null,
            ':estado' => $datos['estado'] ?? 'Activo'
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE vehiculos SET placa = :placa, numero_padron = :numero_padron, marca = :marca, 
                       modelo = :modelo, color = :color, anio_fabricacion = :anio_fabricacion, 
                       numero_motor = :numero_motor, numero_chasis = :numero_chasis, 
                       id_propietario = :id_propietario, id_asociacion = :id_asociacion, estado = :estado 
                WHERE id_vehiculo = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':placa' => strtoupper(trim($datos['placa'])),
            ':numero_padron' => $datos['numero_padron'] ?? null,
            ':marca' => $datos['marca'],
            ':modelo' => $datos['modelo'],
            ':color' => $datos['color'],
            ':anio_fabricacion' => $datos['anio_fabricacion'] ?? null,
            ':numero_motor' => $datos['numero_motor'] ?? null,
            ':numero_chasis' => $datos['numero_chasis'] ?? null,
            ':id_propietario' => $datos['id_propietario'],
            ':id_asociacion' => !empty($datos['id_asociacion']) ? $datos['id_asociacion'] : null,
            ':estado' => $datos['estado']
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM vehiculos WHERE id_vehiculo = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function contarTotal() {
        return (int)$this->db->query("SELECT COUNT(*) FROM vehiculos WHERE estado = 'Activo'")->fetchColumn();
    }
}

