<?php
require_once dirname(dirname(__DIR__)) . '/configuracion/constantes.php';
require_once dirname(dirname(__DIR__)) . '/configuracion/session.php';
require_once dirname(dirname(__DIR__)) . '/ayudantes/funciones.php';

header('Location: ' . vistaUrl('taxista/dashboard.php'));
exit;

