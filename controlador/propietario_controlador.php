<?php
// ==============================================================
// CONTROLADOR: Propietarios - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/constantes.php';
require_once dirname(__DIR__) . '/configuracion/session.php';
require_once dirname(__DIR__) . '/ayudantes/funciones.php';
require_once dirname(__DIR__) . '/modelos/Propietario.php';

$modelo = new Propietario();
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($accion === 'crear') {
        if ($modelo->crear($_POST)) {
            setFlash('success', 'Propietario guardado exitosamente.');
        } else {
            setFlash('danger', 'Error al registrar propietario.');
        }
    } elseif ($accion === 'editar') {
        $id = (int)$_POST['id_propietario'];
        if ($modelo->actualizar($id, $_POST)) {
            setFlash('success', 'Propietario actualizado.');
        } else {
            setFlash('danger', 'Error al actualizar.');
        }
    }
    redireccionar('vista/propietarios/listar_propietarios.php');
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $accion === 'eliminar') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0 && $modelo->eliminar($id)) {
        setFlash('success', 'Propietario eliminado.');
    } else {
        setFlash('danger', 'No se pudo eliminar el propietario.');
    }
    redireccionar('vista/propietarios/listar_propietarios.php');
}

