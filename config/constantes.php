<?php
// ==============================================================
// CONSTANTES GLOBALES DEL SISTEMA - KEBA MOTOS
// ==============================================================

if (!defined('APP_NAME')) {
    define('APP_NAME', 'KEBA MOTOS');
    define('APP_VERSION', '1.0.0');
    
    // Rutas absolutas del servidor
    define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
    define('CONFIG_PATH', ROOT_PATH . 'config' . DIRECTORY_SEPARATOR);
    define('MODELS_PATH', ROOT_PATH . 'models' . DIRECTORY_SEPARATOR);
    define('CONTROLLERS_PATH', ROOT_PATH . 'controllers' . DIRECTORY_SEPARATOR);
    define('VIEWS_PATH', ROOT_PATH . 'views' . DIRECTORY_SEPARATOR);
    define('HELPERS_PATH', ROOT_PATH . 'helpers' . DIRECTORY_SEPARATOR);
    define('UPLOADS_PATH', ROOT_PATH . 'uploads' . DIRECTORY_SEPARATOR);

    // URL Base dinámica
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    
    // Detectar carpeta base (/keba_motos_sistema)
    $baseFolder = '/keba_motos_sistema/';
    define('BASE_URL', $protocol . $host . $baseFolder);

    // Roles del Sistema
    define('ROL_ADMIN', 1);
    define('ROL_PERSONAL', 2);
    define('ROL_CONSULTA', 3);

    // Zona Horaria
    date_default_timezone_set('America/Lima');
}

