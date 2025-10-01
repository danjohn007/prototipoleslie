<?php
/**
 * Controlador de Autenticación
 */

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Muestra el formulario de login
     */
    public function showLogin() {
        // Si ya está logueado, redirigir al dashboard
        if ($this->userModel->isLoggedIn()) {
            $this->redirect('dashboard.php');
        }
        
        include APP_PATH . '/views/auth/login.php';
    }
    
    /**
     * Procesa el login
     */
    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'Por favor complete todos los campos';
                $this->redirect('index.php');
                return;
            }
            
            if ($this->userModel->login($email, $password)) {
                $_SESSION['success'] = 'Bienvenido al sistema';
                $this->redirect('dashboard.php');
            } else {
                $_SESSION['error'] = 'Credenciales inválidas';
                $this->redirect('index.php');
            }
        }
    }
    
    /**
     * Procesa el logout
     */
    public function logout() {
        $this->userModel->logout();
        $_SESSION['success'] = 'Sesión cerrada exitosamente';
        $this->redirect('index.php');
    }
    
    /**
     * Verifica autenticación
     */
    public function checkAuth() {
        if (!$this->userModel->isLoggedIn()) {
            $_SESSION['error'] = 'Debe iniciar sesión para acceder';
            $this->redirect('index.php');
            exit;
        }
    }
    
    /**
     * Redirección helper
     */
    private function redirect($url) {
        header('Location: ' . BASE_URL . '/' . $url);
        exit;
    }
}
