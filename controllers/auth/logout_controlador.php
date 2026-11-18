<?php
// ==============================================================
// CONTROLADOR: Cierre de Sesión - KEBA MOTOS
// ==============================================================

require_once dirname(dirname(__DIR__)) . '/config/constantes.php';
require_once dirname(dirname(__DIR__)) . '/config/session.php';
require_once dirname(dirname(__DIR__)) . '/helpers/funciones.php';

cerrarSesion();
session_start();
setFlash('info', 'Has cerrado sesión exitosamente.');
redireccionar('views/auth/login.php');

