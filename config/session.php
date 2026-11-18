<?php
// ==============================================================
// GESTIÓN DE SESIONES - KEBA MOTOS
// ==============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function estaAutenticado() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

function obtenerUsuarioActual() {
    if (!estaAutenticado()) {
        return null;
    }
    return [
        'id' => $_SESSION['usuario_id'] ?? null,
        'nombre' => $_SESSION['usuario_nombre'] ?? '',
        'apellidos' => $_SESSION['usuario_apellidos'] ?? '',
        'usuario' => $_SESSION['usuario_login'] ?? '',
        'rol_id' => $_SESSION['usuario_rol_id'] ?? null,
        'rol_nombre' => $_SESSION['usuario_rol_nombre'] ?? 'Invitado'
    ];
}

function iniciarSesionUsuario($usuario) {
    $_SESSION['usuario_id'] = $usuario['id_usuario'];
    $_SESSION['usuario_nombre'] = $usuario['nombres'];
    $_SESSION['usuario_apellidos'] = $usuario['apellidos'];
    $_SESSION['usuario_login'] = $usuario['usuario'];
    $_SESSION['usuario_rol_id'] = $usuario['id_rol'];
    $_SESSION['usuario_rol_nombre'] = $usuario['nombre_rol'] ?? 'Usuario';
    $_SESSION['ultimo_acceso'] = time();
}

function cerrarSesion() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

function setFlash($tipo, $mensaje) {
    $_SESSION['flash_message'] = [
        'tipo' => $tipo, // 'success', 'danger', 'warning', 'info'
        'mensaje' => $mensaje
    ];
}

function getFlash() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

