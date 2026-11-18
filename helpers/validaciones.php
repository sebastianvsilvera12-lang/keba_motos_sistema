<?php
// ==============================================================
// VALIDACIONES AUXILIARES - KEBA MOTOS
// ==============================================================

function validarDNI($dni) {
    return preg_match('/^[0-9]{8}$/', trim($dni));
}

function validarRUC($ruc) {
    return preg_match('/^[0-9]{11}$/', trim($ruc));
}

function validarPlaca($placa) {
    // Formato común mototaxis Perú: ej. 1234-5B o 4 dígitos y letras
    return preg_match('/^[A-Z0-9-]{5,10}$/i', trim($placa));
}

function validarEmail($email) {
    return filter_var(trim($email), FILTER_VALIDATE_EMAIL);
}

function validarTelefono($telefono) {
    return preg_match('/^[0-9]{7,12}$/', trim($telefono));
}

