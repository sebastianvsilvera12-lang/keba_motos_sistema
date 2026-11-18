<?php
// ==============================================================
// CONTROLADOR: Inicio de Sesión - KEBA MOTOS
// ==============================================================

require_once dirname(dirname(__DIR__)) . '/configuracion/constantes.php';
require_once dirname(dirname(__DIR__)) . '/configuracion/session.php';
require_once dirname(dirname(__DIR__)) . '/ayudantes/funciones.php';
require_once dirname(dirname(__DIR__)) . '/modelos/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($usuario) || empty($password)) {
        setFlash('danger', 'Por favor ingresa tu DNI o usuario y contraseña.');
        redireccionar('vista/auth/login.php');
    }

    $modeloUsuario = new Usuario();
    $usuarioValido = $modeloUsuario->login($usuario, $password);

    if ($usuarioValido) {
        iniciarSesionUsuario($usuarioValido);
        setFlash('success', '¡Bienvenido(a) al sistema, ' . htmlspecialchars($usuarioValido['nombres']) . '!');
        redireccionar('index.php');
    } else {
        setFlash('danger', 'Usuario o contraseña incorrectos, o cuenta inactiva.');
        redireccionar('vista/auth/login.php');
    }
} else {
    redireccionar('vista/auth/login.php');
}

