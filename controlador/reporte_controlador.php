<?php
// ==============================================================
// CONTROLADOR: Reportes - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/constantes.php';
require_once dirname(__DIR__) . '/configuracion/session.php';
require_once dirname(__DIR__) . '/ayudantes/funciones.php';
require_once dirname(__DIR__) . '/modelos/Reporte.php';

$tipo = $_GET['tipo'] ?? 'general';
redireccionar('vista/reportes/reporte_' . $tipo . '.php');

