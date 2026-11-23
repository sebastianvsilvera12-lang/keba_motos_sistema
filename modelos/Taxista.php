<?php
// ==============================================================
// MODELO: Taxista - KEBA MOTOS
// Operaciones de Perfil, Vehículo Asignado y Documentación
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/conexion.php';

class Taxista {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    /**
     * Obtiene el perfil completo del taxista: Usuario, Conductor, Vehículo asignado y Documentos
     */
    public function obtenerFichaTaxista($idUsuario) {
        // 1. Obtener datos de la cuenta de usuario
        $stmtUser = $this->db->prepare("SELECT * FROM usuarios WHERE id_usuario = :id LIMIT 1");
        $stmtUser->execute([':id' => $idUsuario]);
        $usuario = $stmtUser->fetch();
        if (!$usuario) return null;

        // 2. Buscar si existe en la tabla conductores por DNI
        $stmtCond = $this->db->prepare("SELECT * FROM conductores WHERE dni = :dni LIMIT 1");
        $stmtCond->execute([':dni' => $usuario['dni']]);
        $conductor = $stmtCond->fetch();

        // Si no existe el conductor pero es usuario taxista, auto-crear ficha inicial
        if (!$conductor) {
            $nLicencia = 'Q-' . $usuario['dni'];
            $vencLicencia = date('Y-m-d', strtotime('+3 years'));
            try {
                $stmtIns = $this->db->prepare("
                    INSERT INTO conductores (dni, nombres, apellidos, telefono, numero_licencia, categoria_licencia, fecha_vencimiento_licencia, estado)
                    VALUES (:dni, :nombres, :apellidos, :tel, :lic, 'B-IIc', :venc, 'Habilitado')
                ");
                $stmtIns->execute([
                    ':dni' => $usuario['dni'],
                    ':nombres' => $usuario['nombres'],
                    ':apellidos' => $usuario['apellidos'],
                    ':tel' => $usuario['telefono'] ?? null,
                    ':lic' => $nLicencia,
                    ':venc' => $vencLicencia
                ]);
                $stmtCond->execute([':dni' => $usuario['dni']]);
                $conductor = $stmtCond->fetch();
            } catch (Exception $e) {}
        }

        $vehiculo = null;
        $documentos = [];

        if ($conductor) {
            // 3. Buscar vehículo asignado activo
            $stmtVeh = $this->db->prepare("
                SELECT v.*, a.nombre AS nombre_asociacion, a.color_distintivo,
                       p.nombres_razon_social AS propietario_nombres, p.apellidos AS propietario_apellidos
                FROM vehiculo_conductor vc
                INNER JOIN vehiculos v ON vc.id_vehiculo = v.id_vehiculo
                LEFT JOIN asociaciones a ON v.id_asociacion = a.id_asociacion
                LEFT JOIN propietarios p ON v.id_propietario = p.id_propietario
                WHERE vc.id_conductor = :id_conductor AND vc.estado = 'Activo'
                ORDER BY vc.es_principal DESC, vc.id_asignacion DESC
                LIMIT 1
            ");
            $stmtVeh->execute([':id_conductor' => $conductor['id_conductor']]);
            $vehiculo = $stmtVeh->fetch();

            // Si aún no tiene asignación pero hay vehículos registrados, vincular a la primera unidad activa
            if (!$vehiculo) {
                $stmtPrimerVeh = $this->db->query("
                    SELECT v.*, a.nombre AS nombre_asociacion, a.color_distintivo,
                           p.nombres_razon_social AS propietario_nombres, p.apellidos AS propietario_apellidos
                    FROM vehiculos v
                    LEFT JOIN asociaciones a ON v.id_asociacion = a.id_asociacion
                    LEFT JOIN propietarios p ON v.id_propietario = p.id_propietario
                    WHERE v.estado = 'Activo'
                    ORDER BY v.id_vehiculo ASC
                    LIMIT 1
                ");
                $primerVeh = $stmtPrimerVeh->fetch();
                if ($primerVeh) {
                    try {
                        $stmtAsig = $this->db->prepare("
                            INSERT INTO vehiculo_conductor (id_vehiculo, id_conductor, fecha_asignacion, es_principal, estado)
                            VALUES (:veh, :cond, CURDATE(), 1, 'Activo')
                        ");
                        $stmtAsig->execute([
                            ':veh' => $primerVeh['id_vehiculo'],
                            ':cond' => $conductor['id_conductor']
                        ]);
                        $vehiculo = $primerVeh;
                    } catch (Exception $e) {}
                }
            }

            if ($vehiculo) {
                // 4. Obtener documentos del vehículo asignado o cargados por el usuario
                $stmtDocs = $this->db->prepare("
                    SELECT *, DATEDIFF(fecha_vencimiento, CURDATE()) AS dias_para_vencer
                    FROM documentos_vehiculo
                    WHERE id_vehiculo = :id_vehiculo OR id_usuario = :id_usuario
                    ORDER BY fecha_vencimiento ASC
                ");
                $stmtDocs->execute([
                    ':id_vehiculo' => $vehiculo['id_vehiculo'],
                    ':id_usuario' => $idUsuario
                ]);
                $documentos = $stmtDocs->fetchAll();
            } else {
                // Documentos cargados por este usuario
                $stmtDocs = $this->db->prepare("
                    SELECT *, DATEDIFF(fecha_vencimiento, CURDATE()) AS dias_para_vencer
                    FROM documentos_vehiculo
                    WHERE id_usuario = :id_usuario
                    ORDER BY fecha_vencimiento ASC
                ");
                $stmtDocs->execute([':id_usuario' => $idUsuario]);
                $documentos = $stmtDocs->fetchAll();
            }
        }

        return [
            'usuario' => $usuario,
            'conductor' => $conductor,
            'vehiculo' => $vehiculo,
            'documentos' => $documentos
        ];
    }

    /**
     * Lista todas las mototaxis activas para selector si es necesario
     */
    public function listarVehiculosActivos() {
        $sql = "SELECT v.*, a.nombre AS asociacion
                FROM vehiculos v
                LEFT JOIN asociaciones a ON v.id_asociacion = a.id_asociacion
                WHERE v.estado = 'Activo'
                ORDER BY v.placa ASC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Actualiza la foto de la persona (conductor)
     */
    public function actualizarFotoPersona($idConductor, $nombreArchivo) {
        $stmt = $this->db->prepare("UPDATE conductores SET foto = :foto WHERE id_conductor = :id");
        return $stmt->execute([
            ':foto' => $nombreArchivo,
            ':id' => $idConductor
        ]);
    }

    /**
     * Actualiza la foto de la mototaxi (vehículo)
     */
    public function actualizarFotoMoto($idVehiculo, $nombreArchivo) {
        $stmt = $this->db->prepare("UPDATE vehiculos SET foto = :foto WHERE id_vehiculo = :id");
        return $stmt->execute([
            ':foto' => $nombreArchivo,
            ':id' => $idVehiculo
        ]);
    }

    /**
     * Registra un nuevo documento (SOAT, TUC, Licencia, Revisión Técnica)
     */
    public function registrarDocumento($idVehiculo, $tipo, $numero, $entidad, $emision, $vencimiento, $archivo = null, $observaciones = null, $idUsuario = null, $remitenteNombre = null) {
        // Calcular estado automático según fecha de vencimiento
        $hoy = date('Y-m-d');
        $estado = 'Vigente';
        if ($vencimiento < $hoy) {
            $estado = 'Vencido';
        } else {
            $dias = (strtotime($vencimiento) - strtotime($hoy)) / 86400;
            if ($dias <= 30) {
                $estado = 'Por Vencer';
            }
        }

        $sql = "INSERT INTO documentos_vehiculo (id_vehiculo, id_usuario, remitente_nombre, tipo_documento, numero_documento, entidad_emisora, 
                                                fecha_emision, fecha_vencimiento, archivo_digital, estado, observaciones)
                VALUES (:id_vehiculo, :id_usuario, :remitente, :tipo, :numero, :entidad, :emision, :vencimiento, :archivo, :estado, :obs)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id_vehiculo' => $idVehiculo,
            ':id_usuario' => !empty($idUsuario) ? (int)$idUsuario : null,
            ':remitente' => !empty($remitenteNombre) ? trim($remitenteNombre) : null,
            ':tipo' => $tipo,
            ':numero' => trim($numero),
            ':entidad' => !empty($entidad) ? trim($entidad) : null,
            ':emision' => $emision,
            ':vencimiento' => $vencimiento,
            ':archivo' => $archivo,
            ':estado' => $estado,
            ':obs' => $observaciones
        ]);
    }

    /**
     * Permite asociar o registrar un conductor si el usuario no tiene ficha previa
     */
    public function asegurarFichaConductor($idUsuario, $nLicencia, $catLicencia, $vencLicencia, $direccion = null) {
        $stmtUser = $this->db->prepare("SELECT * FROM usuarios WHERE id_usuario = :id LIMIT 1");
        $stmtUser->execute([':id' => $idUsuario]);
        $u = $stmtUser->fetch();
        if (!$u) return false;

        $stmtCond = $this->db->prepare("SELECT id_conductor FROM conductores WHERE dni = :dni LIMIT 1");
        $stmtCond->execute([':dni' => $u['dni']]);
        $cond = $stmtCond->fetch();

        if ($cond) {
            // Actualizar datos de licencia
            $up = $this->db->prepare("UPDATE conductores SET numero_licencia = :lic, categoria_licencia = :cat, fecha_vencimiento_licencia = :venc, direccion = COALESCE(:dir, direccion) WHERE id_conductor = :id");
            return $up->execute([
                ':lic' => trim($nLicencia),
                ':cat' => $catLicencia ?: 'B-IIc',
                ':venc' => $vencLicencia,
                ':dir' => $direccion,
                ':id' => $cond['id_conductor']
            ]);
        } else {
            // Crear nuevo
            $ins = $this->db->prepare("INSERT INTO conductores (dni, nombres, apellidos, telefono, direccion, numero_licencia, categoria_licencia, fecha_vencimiento_licencia, estado)
                                       VALUES (:dni, :nombres, :apellidos, :tel, :dir, :lic, :cat, :venc, 'Habilitado')");
            return $ins->execute([
                ':dni' => $u['dni'],
                ':nombres' => $u['nombres'],
                ':apellidos' => $u['apellidos'],
                ':tel' => $u['telefono'],
                ':dir' => $direccion,
                ':lic' => trim($nLicencia),
                ':cat' => $catLicencia ?: 'B-IIc',
                ':venc' => $vencLicencia
            ]);
        }
    }
}
