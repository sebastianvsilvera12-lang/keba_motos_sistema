<?php
// ==============================================================
// CONTROLADOR: Documentos de Vehículos - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/constantes.php';
require_once dirname(__DIR__) . '/configuracion/session.php';
require_once dirname(__DIR__) . '/ayudantes/funciones.php';
require_once dirname(__DIR__) . '/modelos/Documento.php';

$modelo = new Documento();
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($accion === 'crear') {
        if ($modelo->crear($_POST)) {
            setFlash('success', 'Documento vehicular registrado.');
        } else {
            setFlash('danger', 'Error al registrar el documento.');
        }
    } elseif ($accion === 'editar') {
        $id = (int)$_POST['id_documento'];
        if ($modelo->actualizar($id, $_POST)) {
            setFlash('success', 'Documento actualizado con éxito.');
        } else {
            setFlash('danger', 'Error al actualizar el documento.');
        }
    }
    redireccionar('vista/documentos/listar_documentos.php');
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $accion === 'eliminar') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id > 0 && $modelo->eliminar($id)) {
        setFlash('success', 'Documento eliminado.');
    }
    redireccionar('vista/documentos/listar_documentos.php');
}

