<?php
// ==============================================================
// MODELO: Cliente - KEBA MOTOS / KEBA S.A.C.
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/conexion.php';

class Cliente {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function listarTodos($filtroEstado = null) {
        if ($filtroEstado) {
            $stmt = $this->db->prepare("SELECT * FROM clientes WHERE estado = :estado ORDER BY id_cliente DESC");
            $stmt->execute([':estado' => $filtroEstado]);
            return $stmt->fetchAll();
        }
        return $this->db->query("SELECT * FROM clientes ORDER BY id_cliente DESC")->fetchAll();
    }

    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM clientes WHERE id_cliente = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function obtenerPorDocumento($numero_documento) {
        $stmt = $this->db->prepare("SELECT * FROM clientes WHERE numero_documento = :num LIMIT 1");
        $stmt->execute([':num' => trim($numero_documento)]);
        return $stmt->fetch();
    }

    public function crear($datos) {
        $sql = "INSERT INTO clientes (tipo_documento, numero_documento, nombres, apellidos, telefono, telefono_alternativo, email, direccion, ciudad, tipo_cliente, estado, observaciones) 
                VALUES (:tipo_documento, :numero_documento, :nombres, :apellidos, :telefono, :telefono_alternativo, :email, :direccion, :ciudad, :tipo_cliente, :estado, :observaciones)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':tipo_documento' => $datos['tipo_documento'] ?? 'DNI',
            ':numero_documento' => trim($datos['numero_documento']),
            ':nombres' => trim($datos['nombres']),
            ':apellidos' => !empty($datos['apellidos']) ? trim($datos['apellidos']) : null,
            ':telefono' => $datos['telefono'] ?? null,
            ':telefono_alternativo' => $datos['telefono_alternativo'] ?? null,
            ':email' => $datos['email'] ?? null,
            ':direccion' => $datos['direccion'] ?? null,
            ':ciudad' => $datos['ciudad'] ?? 'Pisco',
            ':tipo_cliente' => $datos['tipo_cliente'] ?? 'Natural',
            ':estado' => $datos['estado'] ?? 'Activo',
            ':observaciones' => $datos['observaciones'] ?? null
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE clientes SET tipo_documento = :tipo_documento, numero_documento = :numero_documento, 
                       nombres = :nombres, apellidos = :apellidos, telefono = :telefono, 
                       telefono_alternativo = :telefono_alternativo, email = :email, direccion = :direccion, 
                       ciudad = :ciudad, tipo_cliente = :tipo_cliente, estado = :estado, observaciones = :observaciones 
                WHERE id_cliente = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':tipo_documento' => $datos['tipo_documento'] ?? 'DNI',
            ':numero_documento' => trim($datos['numero_documento']),
            ':nombres' => trim($datos['nombres']),
            ':apellidos' => !empty($datos['apellidos']) ? trim($datos['apellidos']) : null,
            ':telefono' => $datos['telefono'] ?? null,
            ':telefono_alternativo' => $datos['telefono_alternativo'] ?? null,
            ':email' => $datos['email'] ?? null,
            ':direccion' => $datos['direccion'] ?? null,
            ':ciudad' => $datos['ciudad'] ?? 'Pisco',
            ':tipo_cliente' => $datos['tipo_cliente'] ?? 'Natural',
            ':estado' => $datos['estado'] ?? 'Activo',
            ':observaciones' => $datos['observaciones'] ?? null
        ]);
    }

    public function cambiarEstado($id, $nuevoEstado) {
        $stmt = $this->db->prepare("UPDATE clientes SET estado = :estado WHERE id_cliente = :id");
        return $stmt->execute([':estado' => $nuevoEstado, ':id' => $id]);
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM clientes WHERE id_cliente = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function buscar($termino) {
        $stmt = $this->db->prepare("SELECT * FROM clientes 
                                   WHERE nombres LIKE :t 
                                      OR apellidos LIKE :t 
                                      OR numero_documento LIKE :t 
                                      OR telefono LIKE :t 
                                   ORDER BY id_cliente DESC");
        $stmt->execute([':t' => "%$termino%"]);
        return $stmt->fetchAll();
    }

    public function contarTotal() {
        return (int)$this->db->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
    }
}
