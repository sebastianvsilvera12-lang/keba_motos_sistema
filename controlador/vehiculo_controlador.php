<?php
// ==============================================================
// CONTROLADOR: Vehículos (Mototaxis) - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/constantes.php';
require_once dirname(__DIR__) . '/configuracion/session.php';
require_once dirname(__DIR__) . '/ayudantes/funciones.php';
require_once dirname(__DIR__) . '/modelos/Vehiculo.php';

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
    } elseif ($accion === 'responder_reporte_taxista') {
        require_once dirname(__DIR__) . '/modelos/ReporteVehiculo.php';
        $repMod = new ReporteVehiculo();
        $idReporte = (int)$_POST['id_reporte'];
        $respuesta = trim($_POST['respuesta_admin'] ?? '');
        $nuevoEstado = $_POST['estado'] ?? 'Atendido';
        $fechaRevision = !empty($_POST['fecha_proxima_revision']) ? $_POST['fecha_proxima_revision'] : null;
        $estadoMecanico = !empty($_POST['estado_mecanico']) ? $_POST['estado_mecanico'] : null;

        $userActual = obtenerUsuarioActual();
        $idAdmin = (int)($userActual['id'] ?? 1);

        if ($repMod->responderReporte($idReporte, $idAdmin, $respuesta, $nuevoEstado, $fechaRevision, $estadoMecanico)) {
            setFlash('success', '¡Respuesta enviada al taxista y estado de la unidad actualizado!');
        } else {
            setFlash('danger', 'Error al guardar la respuesta.');
        }
        redireccionar('vista/vehiculos/reportes_taxistas.php');
    }
    redireccionar('vista/vehiculos/listar_vehiculos.php');
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $accion === 'eliminar') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0 && $modelo->eliminar($id)) {
        setFlash('success', 'Vehículo eliminado correctamente.');
    } else {
        setFlash('danger', 'No se pudo eliminar el vehículo.');
    }
    redireccionar('vista/vehiculos/listar_vehiculos.php');
}

