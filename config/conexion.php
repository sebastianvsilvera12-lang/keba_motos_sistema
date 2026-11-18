<?php
// ==============================================================
// CONEXIÓN A LA BASE DE DATOS (PDO) - KEBA MOTOS
// ==============================================================

require_once __DIR__ . '/constantes.php';

class Database {
    private static $host = "localhost";
    private static $db_name = "keba";
    private static $username = "root";
    private static $password = "";
    private static $conexion = null;

    public static function conectar() {
        if (self::$conexion === null) {
            try {
                $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8mb4";
                $opciones = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => true,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ];
                self::$conexion = new PDO($dsn, self::$username, self::$password, $opciones);
            } catch (PDOException $e) {
                // Registrar o mostrar error
                die("<div style='padding:20px; font-family:sans-serif; background:#fee2e2; color:#991b1b; border:1px solid #f87171; border-radius:8px; margin:20px;'>
                        <strong>Error al conectar con la Base de Datos:</strong> " . htmlspecialchars($e->getMessage()) . "<br><br>
                        <em>Asegúrate de que MySQL esté activo en XAMPP y hayas importado el archivo <code>config/keba.sql</code>.</em>
                     </div>");
            }
        }
        return self::$conexion;
    }
}

function getConexion() {
    return Database::conectar();
}

