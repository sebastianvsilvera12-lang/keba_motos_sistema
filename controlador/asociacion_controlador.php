<?php
// ==============================================================
// CONTROLADOR: Asociaciones / Paraderos - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/constantes.php';
require_once dirname(__DIR__) . '/configuracion/session.php';
require_once dirname(__DIR__) . '/ayudantes/funciones.php';
require_once dirname(__DIR__) . '/modelos/Asociacion.php';

$modelo = new Asociacion();
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($accion === 'crear') {
        if ($modelo->crear($_POST)) {
            setFlash('success', 'Asociación registrada con éxito.');
        } else {
            setFlash('danger', 'Error al registrar la asociación.');
        }
    } elseif ($accion === 'editar') {
        $id = (int)$_POST['id_asociacion'];
        if ($modelo->actualizar($id, $_POST)) {
            setFlash('success', 'Asociación actualizada.');
        } else {
            setFlash('danger', 'Error al actualizar.');
        }
    }
    redireccionar('vista/asociaciones/listar_asociaciones.php');
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $accion === 'eliminar') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0 && $modelo->eliminar($id)) {
        setFlash('success', 'Asociación eliminada.');
    } else {
        setFlash('danger', 'No se pudo eliminar la asociación.');
    }
    redireccionar('vista/asociaciones/listar_asociaciones.php');
}

