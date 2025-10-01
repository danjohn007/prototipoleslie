<?php
/**
 * Modelo User
 * Gestiona la autenticación y usuarios del sistema
 */

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Autentica un usuario
     */
    public function login($email, $password) {
        $sql = "SELECT * FROM usuarios WHERE email = ? AND activo = 1";
        $user = $this->db->queryOne($sql, [$email]);
        
        if ($user && password_verify($password, $user['password'])) {
            // Actualizar última sesión
            $this->updateLastLogin($user['id']);
            
            // Guardar datos en sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_rol'] = $user['rol'];
            $_SESSION['logged_in'] = true;
            $_SESSION['last_activity'] = time();
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Cierra sesión del usuario
     */
    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }
    
    /**
     * Verifica si el usuario está autenticado
     */
    public function isLoggedIn() {
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            // Verificar timeout de sesión
            if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
                $this->logout();
                return false;
            }
            $_SESSION['last_activity'] = time();
            return true;
        }
        return false;
    }
    
    /**
     * Obtiene el usuario actual
     */
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'nombre' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email'],
                'rol' => $_SESSION['user_rol']
            ];
        }
        return null;
    }
    
    /**
     * Registra un nuevo usuario
     */
    public function register($nombre, $email, $password, $rol = 'operador') {
        // Verificar si el email ya existe
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        $existing = $this->db->queryOne($sql, [$email]);
        
        if ($existing) {
            return ['success' => false, 'message' => 'El email ya está registrado'];
        }
        
        // Hash del password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insertar usuario
        $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
        $result = $this->db->execute($sql, [$nombre, $email, $hashedPassword, $rol]);
        
        if ($result) {
            return ['success' => true, 'message' => 'Usuario registrado exitosamente'];
        }
        
        return ['success' => false, 'message' => 'Error al registrar el usuario'];
    }
    
    /**
     * Actualiza la última fecha de inicio de sesión
     */
    private function updateLastLogin($userId) {
        $sql = "UPDATE usuarios SET ultima_sesion = NOW() WHERE id = ?";
        $this->db->execute($sql, [$userId]);
    }
    
    /**
     * Obtiene todos los usuarios
     */
    public function getAll() {
        $sql = "SELECT id, nombre, email, rol, activo, fecha_creacion, ultima_sesion 
                FROM usuarios ORDER BY nombre ASC";
        return $this->db->query($sql);
    }
    
    /**
     * Obtiene un usuario por ID
     */
    public function getById($id) {
        $sql = "SELECT id, nombre, email, rol, activo, fecha_creacion, ultima_sesion 
                FROM usuarios WHERE id = ?";
        return $this->db->queryOne($sql, [$id]);
    }
    
    /**
     * Cambia el password de un usuario
     */
    public function changePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET password = ? WHERE id = ?";
        return $this->db->execute($sql, [$hashedPassword, $userId]);
    }
}
