<?php
// ==============================================================
// MODELO: Propietario - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/conexion.php';

class Propietario {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function listarTodos() {
        return $this->db->query("SELECT * FROM propietarios ORDER BY id_propietario DESC")->fetchAll();
    }

    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM propietarios WHERE id_propietario = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function crear($datos) {
        $sql = "INSERT INTO propietarios (tipo_documento, numero_documento, nombres_razon_social, apellidos, telefono, email, direccion, estado) 
                VALUES (:tipo_documento, :numero_documento, :nombres_razon_social, :apellidos, :telefono, :email, :direccion, :estado)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':tipo_documento' => $datos['tipo_documento'] ?? 'DNI',
            ':numero_documento' => trim($datos['numero_documento']),
            ':nombres_razon_social' => $datos['nombres_razon_social'],
            ':apellidos' => $datos['apellidos'] ?? null,
            ':telefono' => $datos['telefono'] ?? null,
            ':email' => $datos['email'] ?? null,
            ':direccion' => $datos['direccion'] ?? null,
            ':estado' => $datos['estado'] ?? 'Activo'
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE propietarios SET tipo_documento = :tipo_documento, numero_documento = :numero_documento, 
                       nombres_razon_social = :nombres_razon_social, apellidos = :apellidos, telefono = :telefono, 
                       email = :email, direccion = :direccion, estado = :estado 
                WHERE id_propietario = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':tipo_documento' => $datos['tipo_documento'] ?? 'DNI',
            ':numero_documento' => trim($datos['numero_documento']),
            ':nombres_razon_social' => $datos['nombres_razon_social'],
            ':apellidos' => $datos['apellidos'] ?? null,
            ':telefono' => $datos['telefono'] ?? null,
            ':email' => $datos['email'] ?? null,
            ':direccion' => $datos['direccion'] ?? null,
            ':estado' => $datos['estado']
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM propietarios WHERE id_propietario = :id");
        return $stmt->execute([':id' => $id]);
    }
}

