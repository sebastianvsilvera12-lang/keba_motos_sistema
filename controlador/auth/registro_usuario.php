<?php
// ==============================================================
// CONTROLADOR: Registro / Gestión de Usuarios - KEBA MOTOS
// ==============================================================

require_once dirname(dirname(__DIR__)) . '/configuracion/constantes.php';
require_once dirname(dirname(__DIR__)) . '/configuracion/session.php';
require_once dirname(dirname(__DIR__)) . '/ayudantes/funciones.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelo = new Usuario();
    $accion = $_POST['accion'] ?? 'crear';

    if ($accion === 'crear') {
        $resultado = $modelo->crear($_POST);
        if ($resultado) {
            setFlash('success', 'Usuario registrado correctamente.');
        } else {
            setFlash('danger', 'Error al registrar el usuario.');
        }
    } elseif ($accion === 'editar') {
        $id = (int)$_POST['id_usuario'];
        $resultado = $modelo->actualizar($id, $_POST);
        if ($resultado) {
            setFlash('success', 'Usuario actualizado correctamente.');
        } else {
            setFlash('danger', 'Error al actualizar el usuario.');
        }
    }
    redireccionar('vista/usuarios/listar_usuarios.php');
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && (($_GET['accion'] ?? '') === 'eliminar')) {
    $id = (int)($_GET['id'] ?? 0);
    $usuarioActual = obtenerUsuarioActual();

    // Solo el administrador puede eliminar
    if (($usuarioActual['rol_id'] ?? 0) != ROL_ADMIN) {
        setFlash('danger', 'Acceso denegado: Solo el Administrador puede eliminar usuarios.');
        redireccionar('vista/usuarios/listar_usuarios.php');
    }

    // Evitar que el administrador se elimine a sí mismo
    if ($id === (int)($usuarioActual['id'] ?? 0)) {
        setFlash('warning', 'No puedes eliminar tu propia cuenta de Administrador mientras estás en sesión.');
        redireccionar('vista/usuarios/listar_usuarios.php');
    }

    $modelo = new Usuario();
    if ($id > 0 && $modelo->eliminar($id)) {
        setFlash('success', 'Usuario eliminado correctamente del sistema.');
    } else {
        setFlash('danger', 'No se pudo eliminar el usuario.');
    }
    redireccionar('vista/usuarios/listar_usuarios.php');
}

