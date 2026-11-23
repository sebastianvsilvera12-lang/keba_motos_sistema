# Estructura de Base de Datos - KEBA MOTOS / KEBA S.A.C.

**Motor:** MySQL / MariaDB (InnoDB)  
**Codificación:** `utf8mb4_unicode_ci`  
**Base de datos:** `keba`

---

## 📋 Lista de Tablas

1. **`roles`**: Perfiles del sistema (1: Administrador, 2: Analista, 3: Taxistas).
2. **`usuarios`**: Cuentas de acceso, credenciales cifradas y tokens de sesión.
3. **`asociaciones`**: Empresas y paraderos autorizados de mototaxis.
4. **`propietarios`**: Dueños registrados de las unidades.
5. **`conductores`**: Choferes habilitados con licencias y datos personales.
6. **`vehiculos`**: Flota de mototaxis con placa, motor, chasis, estado mecánico y fotos.
7. **`vehiculo_conductor`**: Historial de asignaciones moto-conductor con turnos.
8. **`documentos_vehiculo`**: Documentación obligatoria (SOAT, Tarjeta de Propiedad, Inspección Técnica).
9. **`tipos_infraccion`**: Catálogo de multas e infracciones de tránsito.
10. **`infracciones`**: Actas de fiscalización y multas aplicadas.
11. **`auditoria`**: Trazabilidad y bitácora de eventos del sistema.
12. **`asistencia`**: Registro de entrada/salida diario de los conductores.
13. **`reportes_vehiculo`**: Notificaciones de averías, daños y revisiones de las motos.
14. **`cuotas_mensuales`**: Pagos de cuotas mediante Yape con soporte de comprobante.
15. **`clientes`**: Registro y gestión de clientes (personas naturales o jurídicas).

---

## 📌 Detalle de la Tabla: `clientes`

| Campo | Tipo | Nulo | Clave | Predeterminado | Descripción |
| :--- | :--- | :---: | :---: | :--- | :--- |
| `id_cliente` | `INT` | NO | **PRI** | Auto-increment | Identificador único del cliente |
| `tipo_documento` | `ENUM('DNI','RUC','Carnet Extranjeria','Pasaporte')` | YES | | `'DNI'` | Tipo de documento de identidad |
| `numero_documento` | `VARCHAR(20)` | NO | **UNI** | | Número de documento (único) |
| `nombres` | `VARCHAR(100)` | NO | | | Nombres o Razón Social |
| `apellidos` | `VARCHAR(100)` | YES | | `NULL` | Apellidos (para personas naturales) |
| `telefono` | `VARCHAR(20)` | YES | | `NULL` | Teléfono / Celular principal |
| `telefono_alternativo` | `VARCHAR(20)` | YES | | `NULL` | Teléfono secundario o de referencia |
| `email` | `VARCHAR(100)` | YES | | `NULL` | Correo electrónico |
| `direccion` | `VARCHAR(255)` | YES | | `NULL` | Dirección o domicilio |
| `ciudad` | `VARCHAR(100)` | YES | | `'Pisco'` | Ciudad o distrito |
| `tipo_cliente` | `ENUM('Natural','Jurídico')` | YES | | `'Natural'` | Clasificación del cliente |
| `estado` | `ENUM('Activo','Inactivo','Bloqueado')` | YES | | `'Activo'` | Estado operativo del cliente |
| `observaciones` | `TEXT` | YES | | `NULL` | Notas u observaciones adicionales |
| `created_at` | `TIMESTAMP` | YES | | `CURRENT_TIMESTAMP` | Fecha y hora de creación |
| `updated_at` | `TIMESTAMP` | YES | | `CURRENT_TIMESTAMP ON UPDATE` | Fecha y hora de última modificación |
