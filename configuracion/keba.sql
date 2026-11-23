-- ==============================================================
-- SISTEMA DE GESTIÓN Y FISCALIZACIÓN KEBA MOTOS
-- ARCHIVO DE TABLAS Y ESTRUCTURA: keba.sql
-- ==============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- -------------------------------------------------------------
-- 1. TABLA: roles
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id_rol` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre_rol` VARCHAR(50) NOT NULL UNIQUE,
  `descripcion` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 2. TABLA: usuarios
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id_usuario` INT AUTO_INCREMENT PRIMARY KEY,
  `id_rol` INT NOT NULL,
  `dni` VARCHAR(8) NOT NULL UNIQUE,
  `nombres` VARCHAR(100) NOT NULL,
  `apellidos` VARCHAR(100) NOT NULL,
  `usuario` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(100) UNIQUE NULL,
  `telefono` VARCHAR(20) NULL,
  `estado` ENUM('Activo', 'Inactivo') DEFAULT 'Activo',
  `ultimo_login` DATETIME NULL,
  `token_recuperacion` VARCHAR(100) NULL,
  `token_expira` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 3. TABLA: asociaciones (Empresas y Paraderos de Mototaxis)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `asociaciones`;
CREATE TABLE `asociaciones` (
  `id_asociacion` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(150) NOT NULL,
  `ruc` VARCHAR(11) UNIQUE NULL,
  `resolucion_municipal` VARCHAR(100) NULL,
  `direccion` VARCHAR(200) NULL,
  `telefono` VARCHAR(20) NULL,
  `presidente` VARCHAR(150) NULL,
  `color_distintivo` VARCHAR(50) NULL,
  `cantidad_paraderos` INT DEFAULT 1,
  `estado` ENUM('Activo', 'Inactivo') DEFAULT 'Activo',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 4. TABLA: propietarios (Dueños de las unidades)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `propietarios`;
CREATE TABLE `propietarios` (
  `id_propietario` INT AUTO_INCREMENT PRIMARY KEY,
  `tipo_documento` ENUM('DNI', 'RUC', 'Carnet Extranjeria') DEFAULT 'DNI',
  `numero_documento` VARCHAR(20) NOT NULL UNIQUE,
  `nombres_razon_social` VARCHAR(150) NOT NULL,
  `apellidos` VARCHAR(150) NULL,
  `telefono` VARCHAR(20) NULL,
  `email` VARCHAR(100) NULL,
  `direccion` VARCHAR(255) NULL,
  `estado` ENUM('Activo', 'Inactivo') DEFAULT 'Activo',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 5. TABLA: conductores (Choferes habilitados)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `conductores`;
CREATE TABLE `conductores` (
  `id_conductor` INT AUTO_INCREMENT PRIMARY KEY,
  `dni` VARCHAR(8) NOT NULL UNIQUE,
  `nombres` VARCHAR(100) NOT NULL,
  `apellidos` VARCHAR(100) NOT NULL,
  `fecha_nacimiento` DATE NULL,
  `telefono` VARCHAR(20) NULL,
  `direccion` VARCHAR(255) NULL,
  `numero_licencia` VARCHAR(20) NOT NULL UNIQUE,
  `categoria_licencia` VARCHAR(10) NOT NULL DEFAULT 'B-IIc',
  `fecha_emision_licencia` DATE NULL,
  `fecha_vencimiento_licencia` DATE NOT NULL,
  `foto` VARCHAR(255) NULL,
  `estado` ENUM('Habilitado', 'Inhabilitado', 'Suspendido') DEFAULT 'Habilitado',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 6. TABLA: vehiculos (Mototaxis registradas)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `vehiculos`;
CREATE TABLE `vehiculos` (
  `id_vehiculo` INT AUTO_INCREMENT PRIMARY KEY,
  `placa` VARCHAR(10) NOT NULL UNIQUE,
  `numero_padron` VARCHAR(20) NULL,
  `marca` VARCHAR(50) NOT NULL,
  `modelo` VARCHAR(50) NULL,
  `color` VARCHAR(50) NOT NULL,
  `anio_fabricacion` YEAR NULL,
  `numero_motor` VARCHAR(50) NULL,
  `numero_chasis` VARCHAR(50) NULL,
  `id_propietario` INT NOT NULL,
  `id_asociacion` INT NULL,
  `foto` VARCHAR(255) NULL,
  `es_empresa` TINYINT(1) DEFAULT 1,
  `fecha_proxima_revision` DATE NULL,
  `estado_limpieza` ENUM('Excelente', 'Limpia', 'Regular', 'Requiere Lavado') DEFAULT 'Limpia',
  `estado_mecanico` ENUM('Operativo', 'Con Observaciones', 'En Mantenimiento', 'Taller') DEFAULT 'Operativo',
  `observaciones_danos` TEXT NULL,
  `estado` ENUM('Activo', 'Inactivo', 'De Baja', 'Observado') DEFAULT 'Activo',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_vehiculos_propietarios` FOREIGN KEY (`id_propietario`) REFERENCES `propietarios` (`id_propietario`) ON UPDATE CASCADE,
  CONSTRAINT `fk_vehiculos_asociaciones` FOREIGN KEY (`id_asociacion`) REFERENCES `asociaciones` (`id_asociacion`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 7. TABLA: vehiculo_conductor (Relación Mototaxi - Chofer)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `vehiculo_conductor`;
CREATE TABLE `vehiculo_conductor` (
  `id_asignacion` INT AUTO_INCREMENT PRIMARY KEY,
  `id_vehiculo` INT NOT NULL,
  `id_conductor` INT NOT NULL,
  `fecha_asignacion` DATE NOT NULL,
  `fecha_cese` DATE NULL,
  `es_principal` TINYINT(1) DEFAULT 1,
  `estado` ENUM('Activo', 'Inactivo') DEFAULT 'Activo',
  `observaciones` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_asig_vehiculo` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_asig_conductor` FOREIGN KEY (`id_conductor`) REFERENCES `conductores` (`id_conductor`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 8. TABLA: documentos_vehiculo (SOAT, TUC, Revisión Técnica)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `documentos_vehiculo`;
CREATE TABLE `documentos_vehiculo` (
  `id_documento` INT AUTO_INCREMENT PRIMARY KEY,
  `id_vehiculo` INT NOT NULL,
  `id_usuario` INT NULL,
  `remitente_nombre` VARCHAR(150) NULL,
  `tipo_documento` ENUM('SOAT', 'Tarjeta de Propiedad', 'Tarjeta de Circulacion', 'Licencia de Conducir', 'Revision Tecnica', 'Seguro Municipal', 'Otro') NOT NULL,
  `numero_documento` VARCHAR(50) NOT NULL,
  `entidad_emisora` VARCHAR(100) NULL,
  `fecha_emision` DATE NOT NULL,
  `fecha_vencimiento` DATE NOT NULL,
  `archivo_digital` VARCHAR(255) NULL,
  `estado` ENUM('Vigente', 'Por Vencer', 'Vencido') DEFAULT 'Vigente',
  `observaciones` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_docs_vehiculo` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 9. TABLA: tipos_infraccion (Catálogo oficial de multas)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `tipos_infraccion`;
CREATE TABLE `tipos_infraccion` (
  `id_tipo_infraccion` INT AUTO_INCREMENT PRIMARY KEY,
  `codigo` VARCHAR(20) NOT NULL UNIQUE,
  `descripcion` TEXT NOT NULL,
  `gravedad` ENUM('Leve', 'Grave', 'Muy Grave') NOT NULL,
  `monto_sancion` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `medida_preventiva` VARCHAR(150) NULL,
  `estado` ENUM('Activo', 'Inactivo') DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 10. TABLA: infracciones (Actas de fiscalización)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `infracciones`;
CREATE TABLE `infracciones` (
  `id_infraccion` INT AUTO_INCREMENT PRIMARY KEY,
  `numero_acta` VARCHAR(30) NOT NULL UNIQUE,
  `id_tipo_infraccion` INT NOT NULL,
  `id_vehiculo` INT NOT NULL,
  `id_conductor` INT NULL,
  `id_usuario_fiscalizador` INT NOT NULL,
  `fecha_infraccion` DATETIME NOT NULL,
  `lugar_infraccion` VARCHAR(255) NOT NULL,
  `monto` DECIMAL(10,2) NOT NULL,
  `observacion` TEXT NULL,
  `estado_pago` ENUM('Pendiente', 'Pagado', 'Anulado', 'En Reclamo') DEFAULT 'Pendiente',
  `fecha_pago` DATETIME NULL,
  `comprobante_pago` VARCHAR(50) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_infraccion_tipo` FOREIGN KEY (`id_tipo_infraccion`) REFERENCES `tipos_infraccion` (`id_tipo_infraccion`) ON UPDATE CASCADE,
  CONSTRAINT `fk_infraccion_vehiculo` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON UPDATE CASCADE,
  CONSTRAINT `fk_infraccion_conductor` FOREIGN KEY (`id_conductor`) REFERENCES `conductores` (`id_conductor`) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT `fk_infraccion_fiscalizador` FOREIGN KEY (`id_usuario_fiscalizador`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 11. TABLA: auditoria (Bitácora de seguridad)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `auditoria`;
CREATE TABLE `auditoria` (
  `id_auditoria` INT AUTO_INCREMENT PRIMARY KEY,
  `id_usuario` INT NULL,
  `accion` VARCHAR(100) NOT NULL,
  `modulo` VARCHAR(50) NOT NULL,
  `detalle` TEXT NULL,
  `ip_address` VARCHAR(45) NULL,
  `fecha_hora` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 12. TABLA: asistencia (Control de asistencia de taxistas)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `asistencia`;
CREATE TABLE `asistencia` (
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
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_asistencia_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_asistencia_conductor` FOREIGN KEY (`id_conductor`) REFERENCES `conductores` (`id_conductor`) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT `fk_asistencia_vehiculo` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 13. TABLA: reportes_vehiculo (Novedades, daños, limpieza y revisión de motos de la empresa)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `reportes_vehiculo`;
CREATE TABLE `reportes_vehiculo` (
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
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_repveh_vehiculo` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_repveh_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_repveh_conductor` FOREIGN KEY (`id_conductor`) REFERENCES `conductores` (`id_conductor`) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT `fk_repveh_admin` FOREIGN KEY (`id_usuario_admin`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 14. TABLA: cuotas_mensuales (Pago exclusivo por Yape de taxistas)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `cuotas_mensuales`;
CREATE TABLE `cuotas_mensuales` (
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
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_cuotas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_cuotas_vehiculo` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT `fk_cuotas_conductor` FOREIGN KEY (`id_conductor`) REFERENCES `conductores` (`id_conductor`) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT `fk_cuotas_admin` FOREIGN KEY (`id_admin_valida`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 15. TABLA: clientes (Clientes y Compradores de Motos / Repuestos)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- 16. TABLA: viajes (Solicitudes de Viajes estilo Uber)
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `viajes`;
CREATE TABLE `viajes` (
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
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_viajes_conductor` FOREIGN KEY (`id_conductor`) REFERENCES `conductores` (`id_conductor`) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT `fk_viajes_vehiculo` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;


-- ==============================================================
-- INSERCIÓN DE DATOS INICIALES (SEED DATA)
-- ==============================================================

-- 1. Roles del sistema
INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`) VALUES
(1, 'Administrador', 'Control total de usuarios, configuraciones y módulos'),
(2, 'Analista', 'Gestión y análisis operativo de mototaxis, conductores y actas'),
(3, 'Taxistas', 'Acceso para taxistas y consulta de unidades, licencias y documentos'),
(4, 'Clientes', 'Portal de clientes para solicitar viajes y mototaxis');

-- 2. Usuarios Principales del Sistema
-- Usuario 1: Sebastian alfredo Vargas silvera (sebastian)
-- Usuario 2: Administrador General (admin)
-- Usuario 3: Analista (analista)
-- Usuario 4: Taxista (taxista)
INSERT INTO `usuarios` (`id_usuario`, `id_rol`, `dni`, `nombres`, `apellidos`, `usuario`, `password`, `email`, `telefono`, `estado`) VALUES
(1, 1, '61205626', 'Sebastian alfredo', 'Vargas silvera', 'sebastian', '$2y$12$AI04WyBqxUNrTxxSfpQKhuAS5.FyApLLfFShNujUpXyXtxdLEU3qe', 'sebastian@kebamotos.pe', '984512301', 'Activo'),
(2, 1, '70001122', 'Administrador', 'KEBA MOTOS', 'admin', '$2y$12$PM6OYDugwuPdMN9m34fWN.XOPwZJsxiMT4V3uZsey2PMRsGJqeZfy', 'admin@kebamotos.pe', '987654321', 'Activo'),
(3, 2, '71223344', 'Carlos', 'Mendoza Quispe', 'analista', '$2y$12$PM6OYDugwuPdMN9m34fWN.XOPwZJsxiMT4V3uZsey2PMRsGJqeZfy', 'analista@kebamotos.pe', '912345678', 'Activo'),
(4, 3, '73445566', 'Conductor', 'Taxista Municipal', 'taxista', '$2y$12$PM6OYDugwuPdMN9m34fWN.XOPwZJsxiMT4V3uZsey2PMRsGJqeZfy', 'taxista@kebamotos.pe', '923456789', 'Activo');

-- 3. Catálogo de Infracciones de Mototaxis (Ordenanza Municipal)
INSERT INTO `tipos_infraccion` (`codigo`, `descripcion`, `gravedad`, `monto_sancion`, `medida_preventiva`, `estado`) VALUES
('INF-001', 'Prestar servicio de mototaxi sin contar con SOAT vigente o no portarlo.', 'Muy Grave', 350.00, 'Internamiento del vehículo', 'Activo'),
('INF-002', 'Circular sin Tarjeta Única de Circulación (TUC) o con documento vencido.', 'Grave', 200.00, 'Retención preventiva del vehículo', 'Activo'),
('INF-003', 'Conducir el mototaxi sin contar con Licencia de Conducir o teniéndola suspendida.', 'Muy Grave', 400.00, 'Retención del vehículo y sanción al conductor', 'Activo'),
('INF-004', 'No contar con el número de padrón o sticker oficial visible en la unidad.', 'Leve', 80.00, 'Notificación preventiva', 'Activo'),
('INF-005', 'Prestar servicio sin estar afiliado a una empresa o asociación autorizada.', 'Grave', 250.00, 'Internamiento del vehículo', 'Activo'),
('INF-006', 'Transportar más de 3 pasajeros o carga excesiva no permitida.', 'Grave', 150.00, 'Multa administrativa', 'Activo');

-- 4. Asociaciones y Paraderos de Mototaxis
INSERT INTO `asociaciones` (`id_asociacion`, `nombre`, `ruc`, `resolucion_municipal`, `direccion`, `telefono`, `presidente`, `color_distintivo`, `estado`) VALUES
(1, 'Asociación de Mototaxis Los Halcones', '20485961234', 'RES-MUN-2023-045', 'Av. Central 450, Paradero 3', '984512301', 'Juan Pérez Morales', 'Azul con Blanco', 'Activo'),
(2, 'Empresa de Transportes Rápido Keba S.A.', '20601248791', 'RES-MUN-2022-112', 'Jr. Las Palmeras 120', '971203498', 'Roberto García Torres', 'Azul Marino', 'Activo');

-- 5. Propietarios de Unidades
INSERT INTO `propietarios` (`id_propietario`, `tipo_documento`, `numero_documento`, `nombres_razon_social`, `apellidos`, `telefono`, `email`, `direccion`, `estado`) VALUES
(1, 'DNI', '45892147', 'Mario Alberto', 'Gómez Sánchez', '998124578', 'mario.gomez@gmail.com', 'Urb. Los Álamos Mz B Lote 4', 'Activo'),
(2, 'DNI', '42189654', 'Elena Sofía', 'Ramírez Castro', '985471203', 'elena.ramirez@gmail.com', 'Av. El Sol 890', 'Activo');

-- 6. Conductores Habilitados
INSERT INTO `conductores` (`id_conductor`, `dni`, `nombres`, `apellidos`, `fecha_nacimiento`, `telefono`, `direccion`, `numero_licencia`, `categoria_licencia`, `fecha_emision_licencia`, `fecha_vencimiento_licencia`, `estado`) VALUES
(1, '72458963', 'Pedro Luis', 'Flores Miranda', '1995-04-12', '974125896', 'Calle Las Flores 230', 'Q-72458963', 'B-IIc', '2021-03-10', '2026-03-10', 'Habilitado'),
(2, '75896321', 'Jorge David', 'Vargas Huamán', '1998-08-25', '965823147', 'Av. Grau 145', 'Q-75896321', 'B-IIc', '2022-07-15', '2027-07-15', 'Habilitado');

-- 7. Mototaxis Empadronadas
INSERT INTO `vehiculos` (`id_vehiculo`, `placa`, `numero_padron`, `marca`, `modelo`, `color`, `anio_fabricacion`, `numero_motor`, `numero_chasis`, `id_propietario`, `id_asociacion`, `es_empresa`, `fecha_proxima_revision`, `estado_limpieza`, `estado_mecanico`, `estado`) VALUES
(1, '1234-5B', 'PAD-010', 'Bajaj', 'RE 205', 'Azul con Negro', 2021, 'BJ205-985412', 'CHAS-9874561230', 1, 1, 1, '2026-12-15', 'Limpia', 'Operativo', 'Activo'),
(2, '5678-3A', 'PAD-025', 'TVS', 'King Deluxe', 'Azul y Blanco', 2022, 'TVS-451298', 'CHAS-1245896325', 2, 2, 1, '2026-11-30', 'Regular', 'Operativo', 'Activo');

-- 8. Documentos Vehiculares (SOAT, TUC)
INSERT INTO `documentos_vehiculo` (`id_documento`, `id_vehiculo`, `tipo_documento`, `numero_documento`, `entidad_emisora`, `fecha_emision`, `fecha_vencimiento`, `estado`) VALUES
(1, 1, 'SOAT', 'SOAT-2026-98124', 'La Positiva Seguros', '2026-01-15', '2027-01-15', 'Vigente'),
(2, 1, 'Tarjeta de Circulacion', 'TUC-MUNI-2025-08', 'Municipalidad Provincial', '2025-05-10', '2026-05-10', 'Vigente'),
(3, 2, 'SOAT', 'SOAT-2025-33214', 'Pacífico Seguros', '2025-11-20', '2026-11-20', 'Por Vencer');

-- 9. Asignación de Conductores a Unidades
INSERT INTO `vehiculo_conductor` (`id_asignacion`, `id_vehiculo`, `id_conductor`, `fecha_asignacion`, `es_principal`, `estado`) VALUES
(1, 1, 1, '2025-01-10', 1, 'Activo'),
(2, 2, 2, '2025-02-01', 1, 'Activo');

-- 10. Reportes de Estado, Daños y Revisión de Unidades de la Empresa
INSERT INTO `reportes_vehiculo` (`id_reporte`, `id_vehiculo`, `id_usuario`, `id_conductor`, `tipo_asunto`, `descripcion_danos`, `estado_limpieza`, `fecha_revision_sugerida`, `prioridad`, `estado`, `respuesta_admin`, `id_usuario_admin`, `fecha_respuesta`) VALUES
(1, 1, 4, 1, 'Daños y Desperfectos', 'Presenta leve holgura en el cable de embrague y raspón superficial en el tapabarro delantero derecho.', 'Regular / Aceptable', '2026-11-25', 'Normal', 'Atendido', 'Se autorizó el pase a taller para calibración de embrague este jueves a las 09:00 AM.', 1, '2026-11-19 10:30:00');
