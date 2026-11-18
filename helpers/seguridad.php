<?php
// ==============================================================
// SEGURIDAD, HASHING Y CSRF - KEBA MOTOS
// ==============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function generarHashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verificarPassword($password, $hash) {
    return password_verify($password, $hash);
}

function generarTokenCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verificarTokenCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

