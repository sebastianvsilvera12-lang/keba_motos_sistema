<?php
// ==============================================================
// FORMATEO DE FECHAS - KEBA MOTOS
// ==============================================================

function formatearFechaPeru($fecha) {
    if (!$fecha || $fecha === '0000-00-00') return '-';
    $timestamp = strtotime($fecha);
    return date('d/m/Y', $timestamp);
}

function formatearFechaHoraPeru($fechaHora) {
    if (!$fechaHora || $fechaHora === '0000-00-00 00:00:00') return '-';
    $timestamp = strtotime($fechaHora);
    return date('d/m/Y h:i A', $timestamp);
}

function calcularDiasRestantes($fechaVencimiento) {
    if (!$fechaVencimiento) return null;
    $hoy = new DateTime();
    $vence = new DateTime($fechaVencimiento);
    $diferencia = $hoy->diff($vence);
    return (int)$diferencia->format('%r%a');
}

