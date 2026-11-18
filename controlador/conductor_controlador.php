<?php
// ==============================================================
// CONTROLADOR: Conductores - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/constantes.php';
require_once dirname(__DIR__) . '/configuracion/session.php';
require_once dirname(__DIR__) . '/ayudantes/funciones.php';
require_once dirname(__DIR__) . '/modelos/Conductor.php';

$modelo = new Conductor();
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($accion === 'crear') {
        if ($modelo->crear($_POST)) {
            setFlash('success', 'Conductor registrado satisfactoriamente.');
        } else {
            setFlash('danger', 'Error al registrar conductor. Verifica DNI o Licencia.');
        }
    } elseif ($accion === 'editar') {
        $id = (int)$_POST['id_conductor'];
        if ($modelo->actualizar($id, $_POST)) {
            setFlash('success', 'Datos del conductor actualizados.');
        } else {
            setFlash('danger', 'Error al actualizar conductor.');
        }
    }
    redireccionar('vista/conductores/listar_conductores.php');
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $accion === 'eliminar') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0 && $modelo->eliminar($id)) {
        setFlash('success', 'Conductor eliminado.');
    } else {
        setFlash('danger', 'No se pudo eliminar el conductor.');
    }
    redireccionar('vista/conductores/listar_conductores.php');
}

