<?php
// ==============================================================
// MATRIZ DE PERMISOS POR ROL - KEBA MOTOS
// ==============================================================

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/constantes.php';

// Definición de permisos por módulo
$permisos_rol = [
    ROL_ADMIN => [
        'dashboard' => ['ver', 'admin'],
        'vehiculos' => ['ver', 'crear', 'editar', 'eliminar'],
        'conductores' => ['ver', 'crear', 'editar', 'eliminar'],
        'propietarios' => ['ver', 'crear', 'editar', 'eliminar'],
        'asociaciones' => ['ver', 'crear', 'editar', 'eliminar'],
        'documentos' => ['ver', 'crear', 'editar', 'eliminar'],
        'fiscalizacion' => ['ver', 'crear', 'editar', 'anular', 'cobrar'],
        'reportes' => ['ver', 'exportar_pdf', 'exportar_excel'],
        'usuarios' => ['ver', 'crear', 'editar', 'eliminar']
    ],
    ROL_PERSONAL => [
        'dashboard' => ['ver', 'personal'],
        'vehiculos' => ['ver', 'crear', 'editar'],
        'conductores' => ['ver', 'crear', 'editar'],
        'propietarios' => ['ver', 'crear', 'editar'],
        'asociaciones' => ['ver', 'crear', 'editar'],
        'documentos' => ['ver', 'crear', 'editar'],
        'fiscalizacion' => ['ver', 'crear', 'cobrar'],
        'reportes' => ['ver', 'exportar_pdf'],
        'usuarios' => []
    ],
    ROL_CONSULTA => [
        'dashboard' => ['ver', 'consulta'],
        'vehiculos' => ['ver'],
        'conductores' => ['ver'],
        'propietarios' => ['ver'],
        'asociaciones' => ['ver'],
        'documentos' => ['ver'],
        'fiscalizacion' => ['ver'],
        'reportes' => ['ver'],
        'usuarios' => []
    ]
];

function tienePermiso($modulo, $accion = 'ver') {
    global $permisos_rol;
    $usuario = obtenerUsuarioActual();
    if (!$usuario) return false;

    $rol_id = (int)$usuario['rol_id'];
    if (!isset($permisos_rol[$rol_id][$modulo])) {
        return false;
    }

    return in_array($accion, $permisos_rol[$rol_id][$modulo]);
}

function requerirRol($rolesPermitidos = []) {
    $usuario = obtenerUsuarioActual();
    if (!$usuario || !in_array((int)$usuario['rol_id'], (array)$rolesPermitidos)) {
        setFlash('danger', 'Acceso denegado: No tienes permisos suficientes para realizar esta acción.');
        header('Location: ' . BASE_URL . 'vista/dashboard/dashboard_admin.php');
        exit;
    }
}

