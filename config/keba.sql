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
  `tipo_documento` ENUM('SOAT', 'Tarjeta de Propiedad', 'Tarjeta de Circulacion', 'Revision Tecnica', 'Seguro Municipal', 'Otro') NOT NULL,
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

SET FOREIGN_KEY_CHECKS = 1;


-- ==============================================================
-- INSERCIÓN DE DATOS INICIALES (SEED DATA)
-- ==============================================================

-- 1. Roles del sistema
INSERT INTO `roles` (`id_rol`, `nombre_rol`, `descripcion`) VALUES
(1, 'Administrador', 'Control total de usuarios, configuraciones y módulos'),
(2, 'Personal Administrativo', 'Gestión operativa de mototaxis, conductores y actas'),
(3, 'Usuario de Consulta', 'Acceso de solo lectura para fiscalizadores y reportes');

-- 2. Usuarios Principales del Sistema
-- Usuario 1: Sebastian alfredo Vargas silvera (DNI: 61205626, Clave: kirishima12)
-- Usuario 2: Administrador General (admin / admin123)
-- Usuario 3: Personal Administrativo (personal / admin123)
-- Usuario 4: Inspector de Campo (consulta / admin123)
INSERT INTO `usuarios` (`id_usuario`, `id_rol`, `dni`, `nombres`, `apellidos`, `usuario`, `password`, `email`, `telefono`, `estado`) VALUES
(1, 1, '61205626', 'Sebastian alfredo', 'Vargas silvera', 'sebastian', '$2y$12$AI04WyBqxUNrTxxSfpQKhuAS5.FyApLLfFShNujUpXyXtxdLEU3qe', 'sebastian@kebamotos.pe', '984512301', 'Activo'),
(2, 1, '70001122', 'Administrador', 'KEBA MOTOS', 'admin', '$2y$12$PM6OYDugwuPdMN9m34fWN.XOPwZJsxiMT4V3uZsey2PMRsGJqeZfy', 'admin@kebamotos.pe', '987654321', 'Activo'),
(3, 2, '71223344', 'Carlos', 'Mendoza Quispe', 'personal', '$2y$12$PM6OYDugwuPdMN9m34fWN.XOPwZJsxiMT4V3uZsey2PMRsGJqeZfy', 'personal@kebamotos.pe', '912345678', 'Activo'),
(4, 3, '73445566', 'Inspector', 'Fiscalización', 'consulta', '$2y$12$PM6OYDugwuPdMN9m34fWN.XOPwZJsxiMT4V3uZsey2PMRsGJqeZfy', 'consulta@kebamotos.pe', '923456789', 'Activo');

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
INSERT INTO `vehiculos` (`id_vehiculo`, `placa`, `numero_padron`, `marca`, `modelo`, `color`, `anio_fabricacion`, `numero_motor`, `numero_chasis`, `id_propietario`, `id_asociacion`, `estado`) VALUES
(1, '1234-5B', 'PAD-010', 'Bajaj', 'RE 205', 'Azul con Negro', 2021, 'BJ205-985412', 'CHAS-9874561230', 1, 1, 'Activo'),
(2, '5678-3A', 'PAD-025', 'TVS', 'King Deluxe', 'Azul y Blanco', 2022, 'TVS-451298', 'CHAS-1245896325', 2, 2, 'Activo');

-- 8. Documentos Vehiculares (SOAT, TUC)
INSERT INTO `documentos_vehiculo` (`id_documento`, `id_vehiculo`, `tipo_documento`, `numero_documento`, `entidad_emisora`, `fecha_emision`, `fecha_vencimiento`, `estado`) VALUES
(1, 1, 'SOAT', 'SOAT-2026-98124', 'La Positiva Seguros', '2026-01-15', '2027-01-15', 'Vigente'),
(2, 1, 'Tarjeta de Circulacion', 'TUC-MUNI-2025-08', 'Municipalidad Provincial', '2025-05-10', '2026-05-10', 'Vigente'),
(3, 2, 'SOAT', 'SOAT-2025-33214', 'Pacífico Seguros', '2025-11-20', '2026-11-20', 'Por Vencer');

-- 9. Asignación de Conductores a Unidades
INSERT INTO `vehiculo_conductor` (`id_asignacion`, `id_vehiculo`, `id_conductor`, `fecha_asignacion`, `es_principal`, `estado`) VALUES
(1, 1, 1, '2025-01-10', 1, 'Activo'),
(2, 2, 2, '2025-02-01', 1, 'Activo');
