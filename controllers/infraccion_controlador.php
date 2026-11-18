<?php
// ==============================================================
// CONTROLADOR: Infracciones / Fiscalización - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/config/constantes.php';
require_once dirname(__DIR__) . '/config/session.php';
require_once dirname(__DIR__) . '/helpers/funciones.php';
require_once dirname(__DIR__) . '/models/Infraccion.php';

$modelo = new Infraccion();
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($accion === 'crear') {
        $usuario = obtenerUsuarioActual();
        $_POST['id_usuario_fiscalizador'] = $usuario['id'] ?? 1;

        if ($modelo->crear($_POST)) {
            setFlash('success', 'Acta de infracción registrada correctamente.');
        } else {
            setFlash('danger', 'Error al registrar el acta de infracción.');
        }
    } elseif ($accion === 'pagar') {
        $id = (int)$_POST['id_infraccion'];
        $comprobante = trim($_POST['comprobante_pago'] ?? 'REC-001');
        if ($modelo->registrarPago($id, $comprobante)) {
            setFlash('success', 'Pago de infracción procesado exitosamente.');
        } else {
            setFlash('danger', 'Error al procesar el pago.');
        }
    }
    redireccionar('views/fiscalizacion/listar_infracciones.php');
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $accion === 'anular') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0 && $modelo->anular($id)) {
        setFlash('info', 'Infracción anulada.');
    }
    redireccionar('views/fiscalizacion/listar_infracciones.php');
}

