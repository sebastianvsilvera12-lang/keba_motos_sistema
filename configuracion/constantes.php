<?php
// ==============================================================
// CONSTANTES GLOBALES DEL SISTEMA - KEBA MOTOS
// ==============================================================

if (!defined('APP_NAME')) {
    define('APP_NAME', 'KEBA MOTOS');
    define('APP_VERSION', '1.0.0');
    
    // Rutas absolutas del servidor
    define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
    define('CONFIG_PATH', ROOT_PATH . 'configuracion' . DIRECTORY_SEPARATOR);
    define('MODELS_PATH', ROOT_PATH . 'modelos' . DIRECTORY_SEPARATOR);
    define('CONTROLLERS_PATH', ROOT_PATH . 'controlador' . DIRECTORY_SEPARATOR);
    define('VIEWS_PATH', ROOT_PATH . 'vista' . DIRECTORY_SEPARATOR);
    define('HELPERS_PATH', ROOT_PATH . 'ayudantes' . DIRECTORY_SEPARATOR);
    define('INTERMEDIARIOS_PATH', ROOT_PATH . 'intermediarios' . DIRECTORY_SEPARATOR);
    define('SUBIDAS_PATH', ROOT_PATH . 'subidas' . DIRECTORY_SEPARATOR);
    define('UPLOADS_PATH', SUBIDAS_PATH);

    // URL Base dinámica
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    
    // Detectar carpeta base (/keba_motos_sistema)
    $baseFolder = '/keba_motos_sistema/';
    define('BASE_URL', $protocol . $host . $baseFolder);

    // Roles del Sistema (1: Administrador, 2: Analista, 3: Taxistas, 4: Clientes)
    define('ROL_ADMIN', 1);
    define('ROL_ADMINISTRADOR', 1);
    define('ROL_ANALISTA', 2);
    define('ROL_PERSONAL', 2);
    define('ROL_TAXISTAS', 3);
    define('ROL_TAXISTA', 3);
    define('ROL_CONSULTA', 3);
    define('ROL_CONDUCTORES', 3);
    define('ROL_CLIENTES', 4);
    define('ROL_CLIENTE', 4);

    // Zona Horaria
    date_default_timezone_set('America/Lima');
}

