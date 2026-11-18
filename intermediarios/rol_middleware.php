<?php
// ==============================================================
// MIDDLEWARE DE CONTROL DE ROLES - KEBA MOTOS
// ==============================================================

require_once dirname(__DIR__) . '/configuracion/permisos.php';

function verificarAccesoModulo($modulo, $accion = 'ver') {
    if (!tienePermiso($modulo, $accion)) {
        setFlash('danger', 'No cuentas con los permisos requeridos para esta sección.');
        header('Location: ' . BASE_URL . 'vista/dashboard/dashboard_admin.php');
        exit;
    }
}

