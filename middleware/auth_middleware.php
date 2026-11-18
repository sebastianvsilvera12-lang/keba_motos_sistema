<?php
// ==============================================================
// MIDDLEWARE DE AUTENTICACIÓN - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/config/session.php';
require_once dirname(__DIR__) . '/config/constantes.php';

if (!estaAutenticado()) {
    setFlash('warning', 'Debes iniciar sesión para acceder al sistema.');
    header('Location: ' . BASE_URL . 'views/auth/login.php');
    exit;
}

