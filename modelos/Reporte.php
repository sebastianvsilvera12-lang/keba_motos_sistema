<?php
// ==============================================================
// MODELO: Reporte - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/conexion.php';

class Reporte {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function obtenerMetricasGenerales() {
        return [
            'total_vehiculos' => (int)$this->db->query("SELECT COUNT(*) FROM vehiculos")->fetchColumn(),
            'total_conductores' => (int)$this->db->query("SELECT COUNT(*) FROM conductores")->fetchColumn(),
            'total_asociaciones' => (int)$this->db->query("SELECT COUNT(*) FROM asociaciones")->fetchColumn(),
            'infracciones_pendientes' => (int)$this->db->query("SELECT COUNT(*) FROM infracciones WHERE estado_pago = 'Pendiente'")->fetchColumn(),
            'documentos_por_vencer' => (int)$this->db->query("SELECT COUNT(*) FROM documentos_vehiculo WHERE DATEDIFF(fecha_vencimiento, CURDATE()) <= 30")->fetchColumn()
        ];
    }

    public function reporteInfraccionesPorMes() {
        $sql = "SELECT MONTHNAME(fecha_infraccion) as mes, COUNT(*) as cantidad, SUM(monto) as total_monto 
                FROM infracciones 
                GROUP BY MONTH(fecha_infraccion) 
                LIMIT 6";
        return $this->db->query($sql)->fetchAll();
    }
}

