<?php
// ==============================================================
// CONTROLADOR: Cierre de Sesión - KEBA MOTOS
// ==============================================================

require_once dirname(dirname(__DIR__)) . '/configuracion/constantes.php';
require_once dirname(dirname(__DIR__)) . '/configuracion/session.php';
require_once dirname(dirname(__DIR__)) . '/ayudantes/funciones.php';

cerrarSesion();
session_start();
setFlash('info', 'Has cerrado sesión exitosamente.');
redireccionar('vista/auth/login.php');

