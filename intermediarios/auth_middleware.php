<?php
// ==============================================================
// MIDDLEWARE DE AUTENTICACIÓN - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/session.php';
require_once dirname(__DIR__) . '/configuracion/constantes.php';

if (!estaAutenticado()) {
    setFlash('warning', 'Debes iniciar sesión para acceder al sistema.');
    header('Location: ' . BASE_URL . 'vista/auth/login.php');
    exit;
}

