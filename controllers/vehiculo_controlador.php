<?php
// ==============================================================
// CONTROLADOR: Vehículos (Mototaxis) - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/config/constantes.php';
require_once dirname(__DIR__) . '/config/session.php';
require_once dirname(__DIR__) . '/helpers/funciones.php';
require_once dirname(__DIR__) . '/models/Vehiculo.php';

$modelo = new Vehiculo();
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($accion === 'crear') {
        if ($modelo->crear($_POST)) {
            setFlash('success', 'Vehículo registrado correctamente.');
        } else {
            setFlash('danger', 'Error al guardar el vehículo. Revisa la placa duplicada.');
        }
    } elseif ($accion === 'editar') {
        $id = (int)$_POST['id_vehiculo'];
        if ($modelo->actualizar($id, $_POST)) {
            setFlash('success', 'Vehículo actualizado con éxito.');
        } else {
            setFlash('danger', 'Error al actualizar el vehículo.');
        }
    }
    redireccionar('views/vehiculos/listar_vehiculos.php');
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $accion === 'eliminar') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0 && $modelo->eliminar($id)) {
        setFlash('success', 'Vehículo eliminado correctamente.');
    } else {
        setFlash('danger', 'No se pudo eliminar el vehículo.');
    }
    redireccionar('views/vehiculos/listar_vehiculos.php');
}

