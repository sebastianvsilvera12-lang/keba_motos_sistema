<?php
// ==============================================================
// CONEXIÓN A LA BASE DE DATOS (PDO) - KEBA MOTOS
// AUTO-INSTALACIÓN Y CREACIÓN AUTOMÁTICA DE TABLAS
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
                $opciones = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => true,
                    PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ];

                // 1. Intentar conectar a la base de datos 'keba'
                try {
                    $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8mb4";
                    self::$conexion = new PDO($dsn, self::$username, self::$password, $opciones);
                } catch (PDOException $e) {
                    // Si la base de datos no existe (código 1049), crearla automáticamente
                    if ($e->getCode() == 1049 || stripos($e->getMessage(), 'Unknown database') !== false) {
                        $dsnRoot = "mysql:host=" . self::$host . ";charset=utf8mb4";
                        $pdoRoot = new PDO($dsnRoot, self::$username, self::$password, $opciones);
                        $pdoRoot->exec("CREATE DATABASE IF NOT EXISTS `" . self::$db_name . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                        $pdoRoot = null;

                        // Conectar ahora a la base de datos recién creada
                        $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8mb4";
                        self::$conexion = new PDO($dsn, self::$username, self::$password, $opciones);
                    } else {
                        throw $e;
                    }
                }

                // 2. Verificar si la tabla 'usuarios' existe. Si no existe, auto-importar keba.sql
                $check = self::$conexion->query("SHOW TABLES LIKE 'usuarios'");
                if ($check->rowCount() === 0) {
                    $sqlFile = __DIR__ . '/keba.sql';
                    if (file_exists($sqlFile)) {
                        $sql = file_get_contents($sqlFile);
                        self::$conexion->exec($sql);
                    }
                }

            } catch (PDOException $e) {
                die("<div style='padding:20px; font-family:sans-serif; background:#fee2e2; color:#991b1b; border:1px solid #f87171; border-radius:8px; margin:20px;'>
                        <strong>Error al conectar con la Base de Datos:</strong> " . htmlspecialchars($e->getMessage()) . "<br><br>
                        <em>Asegúrate de que MySQL esté activo en el Panel de Control de XAMPP.</em>
                     </div>");
            }
        }
        return self::$conexion;
    }
}

function getConexion() {
    return Database::conectar();
}
