<?php
// ==============================================================
// CONTROLADOR: Reportes - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/config/constantes.php';
require_once dirname(__DIR__) . '/config/session.php';
require_once dirname(__DIR__) . '/helpers/funciones.php';
require_once dirname(__DIR__) . '/models/Reporte.php';

$tipo = $_GET['tipo'] ?? 'general';
redireccionar('views/reportes/reporte_' . $tipo . '.php');

