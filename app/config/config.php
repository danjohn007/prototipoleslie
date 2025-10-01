<?php
/**
 * Archivo de Configuración Principal
 * Sistema de Logística Quesos Leslie
 */

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// CONFIGURACIÓN DE BASE DE DATOS
// ============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'talentos_leslie');
define('DB_USER', 'talentos_leslie');
define('DB_PASS', 'Danjohn007!');
define('DB_CHARSET', 'utf8mb4');

// ============================================
// CONFIGURACIÓN DE URL BASE (Auto-detección)
// ============================================
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $path = str_replace('\\', '/', dirname($script));
    
    // Si está en el directorio raíz
    if ($path == '/') {
        $path = '';
    }
    
    return $protocol . '://' . $host . $path;
}

define('BASE_URL', getBaseUrl());

// ============================================
// CONFIGURACIÓN DE RUTAS
// ============================================
define('ROOT_PATH', dirname(dirname(__DIR__)));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// ============================================
// CONFIGURACIÓN DE LA APLICACIÓN
// ============================================
define('APP_NAME', 'Quesos Leslie - Sistema de Logística');
define('APP_VERSION', '1.0.0');
define('APP_AUTHOR', 'Leslie Lugo');

// ============================================
// CONFIGURACIÓN DE SEGURIDAD
// ============================================
define('SESSION_TIMEOUT', 3600); // 1 hora en segundos
define('PASSWORD_MIN_LENGTH', 6);

// ============================================
// CONFIGURACIÓN DE ZONA HORARIA
// ============================================
date_default_timezone_set('America/Lima');

// ============================================
// CONFIGURACIÓN DE ERRORES (Desarrollo)
// ============================================
// En producción cambiar a 0
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ============================================
// AUTOLOAD DE CLASES
// ============================================
spl_autoload_register(function($class) {
    $paths = [
        APP_PATH . '/models/' . $class . '.php',
        APP_PATH . '/controllers/' . $class . '.php',
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// ============================================
// CARGAR FUNCIONES HELPER
// ============================================
require_once APP_PATH . '/config/helpers.php';
