<?php
require_once dirname(dirname(__DIR__)) . '/config/constantes.php';
require_once dirname(dirname(__DIR__)) . '/config/session.php';
require_once dirname(dirname(__DIR__)) . '/middleware/auth_middleware.php';
require_once dirname(dirname(__DIR__)) . '/helpers/funciones.php';
$usuarioActivo = obtenerUsuarioActual();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloPagina ?? 'Panel de Control') ?> - KEBA MOTOS</title>
    <!-- Estilos en la paleta: Negro, Blanco y Azul -->
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/estilos.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/tablas.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/formularios.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/reportes.css') ?>">
</head>
<body>
<div class="app-container">

