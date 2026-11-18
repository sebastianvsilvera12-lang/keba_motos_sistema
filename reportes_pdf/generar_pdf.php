<?php
// ==============================================================
// GENERADOR DE REPORTES PDF / IMPRESIÓN DIRECTA
// ==============================================================

require_once __DIR__ . '/plantilla_pdf.php';
require_once dirname(__DIR__) . '/config/constantes.php';

$tipo = $_GET['tipo'] ?? 'vehiculos';
header("Location: " . BASE_URL . "views/reportes/reporte_" . $tipo . ".php");
exit;

