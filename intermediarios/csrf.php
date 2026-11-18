<?php
// ==============================================================
// MIDDLEWARE DE PROTECCIÓN CSRF - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/ayudantes/seguridad.php';
require_once dirname(__DIR__) . '/configuracion/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verificarTokenCSRF($token)) {
        setFlash('danger', 'Error de seguridad: Solicitud no válida o token expirado.');
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? BASE_URL . 'vista/auth/login.php'));
        exit;
    }
}

