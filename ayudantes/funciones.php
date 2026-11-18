<?php
// ==============================================================
// FUNCIONES AUXILIARES GLOBALES - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/constantes.php';

function limpiarTexto($dato) {
    if (is_null($dato)) return '';
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}

function redireccionar($ruta) {
    if (strpos($ruta, 'http') === 0) {
        header("Location: " . $ruta);
    } else {
        header("Location: " . BASE_URL . ltrim($ruta, '/'));
    }
    exit;
}

function asset($ruta) {
    return BASE_URL . 'assets/' . ltrim($ruta, '/');
}

function vistaUrl($ruta) {
    return BASE_URL . 'vista/' . ltrim($ruta, '/');
}

function controladorUrl($ruta) {
    return BASE_URL . 'controlador/' . ltrim($ruta, '/');
}

function subidaUrl($ruta) {
    return BASE_URL . 'subidas/' . ltrim($ruta, '/');
}

