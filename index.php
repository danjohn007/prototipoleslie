<?php
/**
 * Front Controller - Punto de entrada principal
 * Sistema de Logística Quesos Leslie
 */

// Cargar configuración
require_once __DIR__ . '/app/config/config.php';

// Crear instancia del controlador de autenticación
$authController = new AuthController();

// Obtener la acción de la URL
$action = $_GET['action'] ?? 'showLogin';

// Enrutamiento simple
switch ($action) {
    case 'login':
        $authController->processLogin();
        break;
    
    case 'logout':
        $authController->logout();
        break;
    
    case 'showLogin':
    default:
        $authController->showLogin();
        break;
}
