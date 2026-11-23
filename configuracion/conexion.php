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
                } else {
                    // Asegurar que la tabla 'asistencia' exista
                    try {
                        self::$conexion->exec("CREATE TABLE IF NOT EXISTS `asistencia` (
                          `id_asistencia` INT AUTO_INCREMENT PRIMARY KEY,
                          `id_usuario` INT NOT NULL,
                          `id_conductor` INT NULL,
                          `id_vehiculo` INT NULL,
                          `fecha` DATE NOT NULL,
                          `hora_ingreso` TIME NOT NULL,
                          `hora_salida` TIME NULL,
                          `turno` ENUM('Mañana', 'Tarde', 'Noche', 'Completo') DEFAULT 'Mañana',
                          `estado` ENUM('En Turno', 'Finalizado') DEFAULT 'En Turno',
                          `kilometraje_inicial` INT NULL,
                          `kilometraje_final` INT NULL,
                          `observaciones` TEXT NULL,
                          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
                    } catch (Exception $e) {}

                    // Asegurar tabla viajes (solicitudes estilo Uber)
                    try {
                        self::$conexion->exec("CREATE TABLE IF NOT EXISTS `viajes` (
                          `id_viaje` INT AUTO_INCREMENT PRIMARY KEY,
                          `id_cliente` INT NULL,
                          `id_usuario` INT NULL,
                          `id_conductor` INT NULL,
                          `id_vehiculo` INT NULL,
                          `origen` VARCHAR(255) NOT NULL,
                          `lat_origen` DECIMAL(10, 7) NULL,
                          `lng_origen` DECIMAL(10, 7) NULL,
                          `destino` VARCHAR(255) NOT NULL,
                          `lat_destino` DECIMAL(10, 7) NULL,
                          `lng_destino` DECIMAL(10, 7) NULL,
                          `tipo_servicio` ENUM('Keba Estándar', 'Keba Express', 'Keba Carga') DEFAULT 'Keba Estándar',
                          `precio_estimado` DECIMAL(10,2) NOT NULL DEFAULT 4.00,
                          `precio_final` DECIMAL(10,2) NULL,
                          `metodo_pago` ENUM('Efectivo', 'Yape', 'Plin', 'Tarjeta') DEFAULT 'Efectivo',
                          `distancia_km` DECIMAL(5,2) DEFAULT 2.50,
                          `tiempo_minutos` INT DEFAULT 8,
                          `estado` ENUM('Solicitado', 'Asignado', 'En Curso', 'Completado', 'Cancelado') DEFAULT 'Solicitado',
                          `codigo_pin` VARCHAR(4) DEFAULT '1234',
                          `calificacion` INT NULL,
                          `comentario_calificacion` TEXT NULL,
                          `fecha_solicitud` DATETIME DEFAULT CURRENT_TIMESTAMP,
                          `fecha_finalizacion` DATETIME NULL,
                          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
                    } catch (Exception $e) {}

                    // Asegurar rol Clientes
                    try {
                        self::$conexion->exec("INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`) VALUES (4, 'Clientes', 'Portal de clientes para solicitar viajes y mototaxis') ON DUPLICATE KEY UPDATE `nombre_rol`=VALUES(`nombre_rol`);");
                    } catch (Exception $e) {}

                    // Asegurar columnas foto en conductores y vehiculos
                    try { self::$conexion->exec("ALTER TABLE `conductores` ADD COLUMN `foto` VARCHAR(255) NULL AFTER `fecha_vencimiento_licencia`"); } catch (Exception $e) {}
                    try { self::$conexion->exec("ALTER TABLE `vehiculos` ADD COLUMN `foto` VARCHAR(255) NULL AFTER `id_asociacion`"); } catch (Exception $e) {}
                    try { self::$conexion->exec("ALTER TABLE `vehiculos` ADD COLUMN `es_empresa` TINYINT(1) DEFAULT 1 AFTER `foto`"); } catch (Exception $e) {}
                    try { self::$conexion->exec("ALTER TABLE `vehiculos` ADD COLUMN `fecha_proxima_revision` DATE NULL AFTER `es_empresa`"); } catch (Exception $e) {}
                    try { self::$conexion->exec("ALTER TABLE `vehiculos` ADD COLUMN `estado_limpieza` ENUM('Excelente', 'Limpia', 'Regular', 'Requiere Lavado') DEFAULT 'Limpia' AFTER `fecha_proxima_revision`"); } catch (Exception $e) {}
                    try { self::$conexion->exec("ALTER TABLE `vehiculos` ADD COLUMN `estado_mecanico` ENUM('Operativo', 'Con Observaciones', 'En Mantenimiento', 'Taller') DEFAULT 'Operativo' AFTER `estado_limpieza`"); } catch (Exception $e) {}
                    try { self::$conexion->exec("ALTER TABLE `vehiculos` ADD COLUMN `observaciones_danos` TEXT NULL AFTER `estado_mecanico`"); } catch (Exception $e) {}

                    // Asegurar columnas id_usuario y remitente_nombre en documentos_vehiculo
                    try { self::$conexion->exec("ALTER TABLE `documentos_vehiculo` ADD COLUMN `id_usuario` INT NULL AFTER `id_vehiculo`"); } catch (Exception $e) {}
                    try { self::$conexion->exec("ALTER TABLE `documentos_vehiculo` ADD COLUMN `remitente_nombre` VARCHAR(150) NULL AFTER `id_usuario`"); } catch (Exception $e) {}
                    try { self::$conexion->exec("ALTER TABLE `documentos_vehiculo` MODIFY COLUMN `tipo_documento` ENUM('SOAT','Tarjeta de Propiedad','Tarjeta de Circulacion','Licencia de Conducir','Revision Tecnica','Seguro Municipal','Otro') NOT NULL"); } catch (Exception $e) {}
                    try { self::$conexion->exec("ALTER TABLE `documentos_vehiculo` MODIFY COLUMN `numero_documento` VARCHAR(50) NULL DEFAULT 'POR REVISAR', MODIFY COLUMN `fecha_emision` DATE NULL, MODIFY COLUMN `fecha_vencimiento` DATE NULL"); } catch (Exception $e) {}

                    // Asegurar tabla reportes_vehiculo (Daños, revisiones, limpieza)
                    try {
                        self::$conexion->exec("CREATE TABLE IF NOT EXISTS `reportes_vehiculo` (
                          `id_reporte` INT AUTO_INCREMENT PRIMARY KEY,
                          `id_vehiculo` INT NOT NULL,
                          `id_usuario` INT NOT NULL,
                          `id_conductor` INT NULL,
                          `tipo_asunto` ENUM('Daños y Desperfectos', 'Programación de Revisión', 'Estado de Limpieza', 'Mantenimiento General', 'Otro') NOT NULL,
                          `descripcion_danos` TEXT NOT NULL,
                          `estado_limpieza` ENUM('Excelente / Limpia', 'Regular / Aceptable', 'Requiere Lavado Urgente') DEFAULT 'Regular / Aceptable',
                          `fecha_revision_sugerida` DATE NULL,
                          `prioridad` ENUM('Baja', 'Normal', 'Alta', 'Urgente') DEFAULT 'Normal',
                          `foto_evidencia` VARCHAR(255) NULL,
                          `estado` ENUM('Pendiente', 'En Revisión', 'Atendido', 'Resuelto') DEFAULT 'Pendiente',
                          `respuesta_admin` TEXT NULL,
                          `id_usuario_admin` INT NULL,
                          `fecha_respuesta` DATETIME NULL,
                          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
                    } catch (Exception $e) {}

                    // Asegurar tabla cuotas_mensuales (Pago exclusivo por Yape de taxistas con imagen en BD)
                    try {
                        self::$conexion->exec("CREATE TABLE IF NOT EXISTS `cuotas_mensuales` (
                          `id_cuota` INT AUTO_INCREMENT PRIMARY KEY,
                          `id_usuario` INT NOT NULL,
                          `id_conductor` INT NULL,
                          `id_vehiculo` INT NULL,
                          `remitente_nombre` VARCHAR(150) NOT NULL,
                          `mes_periodo` VARCHAR(50) NOT NULL,
                          `monto` DECIMAL(10,2) NOT NULL DEFAULT 30.00,
                          `metodo_pago` VARCHAR(50) NOT NULL DEFAULT 'Yape',
                          `numero_operacion` VARCHAR(50) NULL,
                          `comprobante_img` VARCHAR(255) NULL,
                          `comprobante_blob` LONGBLOB NULL,
                          `comprobante_mime` VARCHAR(50) DEFAULT 'image/jpeg',
                          `estado` ENUM('Pendiente', 'Aprobado', 'Rechazado') DEFAULT 'Pendiente',
                          `observaciones` TEXT NULL,
                          `respuesta_admin` TEXT NULL,
                          `id_admin_valida` INT NULL,
                          `fecha_validacion` DATETIME NULL,
                          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
                    } catch (Exception $e) {}

                    // Asegurar que existan las columnas para guardar la imagen en la base de datos
                    try { self::$conexion->exec("ALTER TABLE `cuotas_mensuales` ADD COLUMN `comprobante_blob` LONGBLOB NULL AFTER `comprobante_img`"); } catch (Exception $e) {}
                    try { self::$conexion->exec("ALTER TABLE `cuotas_mensuales` ADD COLUMN `comprobante_mime` VARCHAR(50) DEFAULT 'image/jpeg' AFTER `comprobante_blob`"); } catch (Exception $e) {}

                    // Asegurar tabla clientes
                    try {
                        self::$conexion->exec("CREATE TABLE IF NOT EXISTS `clientes` (
                          `id_cliente` INT AUTO_INCREMENT PRIMARY KEY,
                          `tipo_documento` ENUM('DNI', 'RUC', 'Carnet Extranjeria', 'Pasaporte') DEFAULT 'DNI',
                          `numero_documento` VARCHAR(20) NOT NULL UNIQUE,
                          `nombres` VARCHAR(100) NOT NULL,
                          `apellidos` VARCHAR(100) NULL,
                          `telefono` VARCHAR(20) NULL,
                          `telefono_alternativo` VARCHAR(20) NULL,
                          `email` VARCHAR(100) NULL,
                          `direccion` VARCHAR(255) NULL,
                          `ciudad` VARCHAR(100) DEFAULT 'Pisco',
                          `tipo_cliente` ENUM('Natural', 'Jurídico') DEFAULT 'Natural',
                          `estado` ENUM('Activo', 'Inactivo', 'Bloqueado') DEFAULT 'Activo',
                          `observaciones` TEXT NULL,
                          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
                    } catch (Exception $e) {}

                    // Asegurar tabla viajes (solicitudes estilo Uber)
                    try {
                        self::$conexion->exec("CREATE TABLE IF NOT EXISTS `viajes` (
                          `id_viaje` INT AUTO_INCREMENT PRIMARY KEY,
                          `id_cliente` INT NULL,
                          `id_usuario` INT NULL,
                          `id_conductor` INT NULL,
                          `id_vehiculo` INT NULL,
                          `origen` VARCHAR(255) NOT NULL,
                          `lat_origen` DECIMAL(10, 7) NULL,
                          `lng_origen` DECIMAL(10, 7) NULL,
                          `destino` VARCHAR(255) NOT NULL,
                          `lat_destino` DECIMAL(10, 7) NULL,
                          `lng_destino` DECIMAL(10, 7) NULL,
                          `tipo_servicio` ENUM('Keba Estándar', 'Keba Express', 'Keba Carga') DEFAULT 'Keba Estándar',
                          `precio_estimado` DECIMAL(10,2) NOT NULL DEFAULT 4.00,
                          `precio_final` DECIMAL(10,2) NULL,
                          `metodo_pago` ENUM('Efectivo', 'Yape', 'Plin', 'Tarjeta') DEFAULT 'Efectivo',
                          `distancia_km` DECIMAL(5,2) DEFAULT 2.50,
                          `tiempo_minutos` INT DEFAULT 8,
                          `estado` ENUM('Solicitado', 'Asignado', 'En Curso', 'Completado', 'Cancelado') DEFAULT 'Solicitado',
                          `codigo_pin` VARCHAR(4) DEFAULT '1234',
                          `calificacion` INT NULL,
                          `comentario_calificacion` TEXT NULL,
                          `fecha_solicitud` DATETIME DEFAULT CURRENT_TIMESTAMP,
                          `fecha_finalizacion` DATETIME NULL,
                          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                          `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
                    } catch (Exception $e) {}

                    // Asegurar rol Clientes
                    try {
                        self::$conexion->exec("INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`) VALUES (4, 'Clientes', 'Portal de clientes para solicitar viajes y mototaxis') ON DUPLICATE KEY UPDATE `nombre_rol`=VALUES(`nombre_rol`);");
                    } catch (Exception $e) {}
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
