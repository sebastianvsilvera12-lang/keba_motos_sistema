<?php
// ==============================================================
// MODELO: Usuario - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/config/conexion.php';

class Usuario {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function login($usuario, $password) {
        $sql = "SELECT u.*, r.nombre_rol 
                FROM usuarios u 
                INNER JOIN roles r ON u.id_rol = r.id_rol 
                WHERE (u.usuario = :usuario OR u.email = :email OR u.dni = :dni) AND u.estado = 'Activo' 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario' => $usuario,
            ':email' => $usuario,
            ':dni' => $usuario
        ]);
        $user = $stmt->fetch();

        if ($user) {
            // Soporta hash BCRYPT y contraseñas ingresadas directamente en phpMyAdmin
            $passwordValida = password_verify($password, $user['password']) || ($password === $user['password']);

            if ($passwordValida) {
                // Si estaba en texto plano, migrar automáticamente a hash seguro
                if ($password === $user['password']) {
                    $nuevoHash = password_hash($password, PASSWORD_BCRYPT);
                    $upPass = $this->db->prepare("UPDATE usuarios SET password = :p WHERE id_usuario = :id");
                    $upPass->execute([':p' => $nuevoHash, ':id' => $user['id_usuario']]);
                }

                // Actualizar último login
                $update = $this->db->prepare("UPDATE usuarios SET ultimo_login = NOW() WHERE id_usuario = :id");
                $update->execute([':id' => $user['id_usuario']]);
                return $user;
            }
        }
        return false;
    }

    public function listarTodos() {
        $sql = "SELECT u.*, r.nombre_rol 
                FROM usuarios u 
                INNER JOIN roles r ON u.id_rol = r.id_rol 
                ORDER BY u.id_usuario DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id_usuario = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function crear($datos) {
        $sql = "INSERT INTO usuarios (id_rol, dni, nombres, apellidos, usuario, password, email, telefono, estado) 
                VALUES (:id_rol, :dni, :nombres, :apellidos, :usuario, :password, :email, :telefono, :estado)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_rol' => $datos['id_rol'],
            ':dni' => $datos['dni'],
            ':nombres' => $datos['nombres'],
            ':apellidos' => $datos['apellidos'],
            ':usuario' => $datos['usuario'],
            ':password' => password_hash($datos['password'], PASSWORD_BCRYPT),
            ':email' => $datos['email'],
            ':telefono' => $datos['telefono'],
            ':estado' => $datos['estado'] ?? 'Activo'
        ]);
    }

    public function actualizar($id, $datos) {
        if (!empty($datos['password'])) {
            $sql = "UPDATE usuarios SET id_rol = :id_rol, dni = :dni, nombres = :nombres, apellidos = :apellidos, 
                    usuario = :usuario, password = :password, email = :email, telefono = :telefono, estado = :estado 
                    WHERE id_usuario = :id";
            $params = [
                ':id' => $id,
                ':id_rol' => $datos['id_rol'],
                ':dni' => $datos['dni'],
                ':nombres' => $datos['nombres'],
                ':apellidos' => $datos['apellidos'],
                ':usuario' => $datos['usuario'],
                ':password' => password_hash($datos['password'], PASSWORD_BCRYPT),
                ':email' => $datos['email'],
                ':telefono' => $datos['telefono'],
                ':estado' => $datos['estado']
            ];
        } else {
            $sql = "UPDATE usuarios SET id_rol = :id_rol, dni = :dni, nombres = :nombres, apellidos = :apellidos, 
                    usuario = :usuario, email = :email, telefono = :telefono, estado = :estado 
                    WHERE id_usuario = :id";
            $params = [
                ':id' => $id,
                ':id_rol' => $datos['id_rol'],
                ':dni' => $datos['dni'],
                ':nombres' => $datos['nombres'],
                ':apellidos' => $datos['apellidos'],
                ':usuario' => $datos['usuario'],
                ':email' => $datos['email'],
                ':telefono' => $datos['telefono'],
                ':estado' => $datos['estado']
            ];
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function cambiarEstado($id, $estado) {
        $stmt = $this->db->prepare("UPDATE usuarios SET estado = :estado WHERE id_usuario = :id");
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id_usuario = :id");
        return $stmt->execute([':id' => $id]);
    }
}

