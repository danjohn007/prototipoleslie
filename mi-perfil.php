<?php
/**
 * Mi Perfil - User Profile Page
 * Permite al usuario actualizar su información y cambiar contraseña
 */

// Cargar configuración
require_once __DIR__ . '/app/config/config.php';

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $authController = new AuthController();
    $authController->logout();
}

// Verificar autenticación
$authController = new AuthController();
$authController->checkAuth();

// Obtener usuario actual
$userModel = new User();
$currentUser = $userModel->getCurrentUser();

// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_profile') {
            // Actualizar información del perfil
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            
            if (empty($nombre) || empty($email)) {
                $_SESSION['error'] = 'Nombre y email son requeridos';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = 'Email inválido';
            } else {
                if ($userModel->updateProfile($currentUser['id'], $nombre, $email)) {
                    $_SESSION['success'] = 'Perfil actualizado correctamente';
                    // Actualizar sesión
                    $_SESSION['user_name'] = $nombre;
                    $_SESSION['user_email'] = $email;
                    header('Location: mi-perfil.php');
                    exit;
                } else {
                    $_SESSION['error'] = 'Error al actualizar perfil. El email podría estar en uso.';
                }
            }
        } elseif ($_POST['action'] === 'change_password') {
            // Cambiar contraseña
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $_SESSION['error'] = 'Todos los campos de contraseña son requeridos';
            } elseif ($new_password !== $confirm_password) {
                $_SESSION['error'] = 'Las nuevas contraseñas no coinciden';
            } elseif (strlen($new_password) < 6) {
                $_SESSION['error'] = 'La nueva contraseña debe tener al menos 6 caracteres';
            } else {
                if ($userModel->changePassword($currentUser['id'], $current_password, $new_password)) {
                    $_SESSION['success'] = 'Contraseña cambiada correctamente';
                    header('Location: mi-perfil.php');
                    exit;
                } else {
                    $_SESSION['error'] = 'Contraseña actual incorrecta';
                }
            }
        }
    }
}

// Mensajes de sesión
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - <?php echo APP_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Helvetica+Neue:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #e74c3c;
            --environment: #27ae60;
            --human-rights: #3498db;
            --equity: #9b59b6;
            --education: #f39c12;
            --energy: #e67e22;
            --transport: #1abc9c;
            --water: #2980b9;
            --government: #34495e;
            --security: #c0392b;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark-gray: #495057;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
        }
        
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-weight: 400;
            color: #333;
            background-color: var(--light-gray);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        .sidebar {
            background-color: white;
            height: 100vh;
            width: 280px;
            position: fixed;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            border-right: 1px solid var(--medium-gray);
            z-index: 1000;
            overflow-y: auto;
        }
        
        @media (min-width: 992px) {
            .sidebar {
                transform: translateX(0);
            }
        }
        
        .brand-header {
            padding: 30px 20px;
            border-bottom: 1px solid var(--medium-gray);
            text-align: center;
        }
        
        .brand-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--primary);
            letter-spacing: 2px;
        }
        
        .brand-subtitle {
            font-size: 12px;
            color: var(--dark-gray);
            letter-spacing: 3px;
            margin-top: 5px;
        }
        
        .nav-section {
            padding: 20px 0;
            border-bottom: 1px solid var(--medium-gray);
        }
        
        .nav-section-title {
            padding: 10px 20px;
            font-size: 11px;
            color: var(--dark-gray);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            background-color: var(--light-gray);
            color: var(--primary);
            padding-left: 25px;
        }
        
        .nav-link.active {
            background-color: var(--primary);
            color: white;
            border-left: 4px solid var(--secondary);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 12px;
        }
        
        .user-profile {
            position: sticky;
            bottom: 0;
            background: white;
            border-top: 1px solid var(--medium-gray);
            padding: 15px 0;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
        }
        
        @media (max-width: 991px) {
            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--medium-gray);
        }
        
        .page-title {
            font-size: 28px;
            font-weight: 500;
            color: var(--primary);
            margin: 0;
        }
        
        .card {
            background: white;
            border: 1px solid var(--medium-gray);
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }
        
        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--medium-gray);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-title {
            font-size: 18px;
            font-weight: 500;
            color: var(--primary);
            margin: 0;
        }
        
        .card-body {
            padding: 25px;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--primary);
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border: 1px solid var(--medium-gray);
            border-radius: 6px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
        }
        
        .btn {
            padding: 10px 24px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #1a252f;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.3);
        }
        
        .btn-secondary {
            background-color: var(--dark-gray);
            color: white;
        }
        
        .btn-danger {
            background-color: var(--danger);
            color: white;
        }
        
        .alert {
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--human-rights));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
            margin: 0 auto 20px;
        }
        
        .profile-info {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .profile-name {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .profile-role {
            font-size: 14px;
            color: var(--dark-gray);
            text-transform: capitalize;
        }
        
        .profile-email {
            font-size: 14px;
            color: var(--dark-gray);
        }
    </style>
    <?php include __DIR__ . '/app/includes/sidebar-styles.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/app/includes/sidebar.php'; ?>
    
    <!-- Main Content Area -->
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Mi Perfil</h1>
        </div>
        
        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="profile-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="profile-info">
                            <div class="profile-name"><?php echo htmlspecialchars($currentUser['nombre']); ?></div>
                            <div class="profile-role"><?php echo htmlspecialchars($currentUser['rol']); ?></div>
                            <div class="profile-email"><?php echo htmlspecialchars($currentUser['email']); ?></div>
                        </div>
                        <div class="text-center text-muted">
                            <small><i class="fas fa-calendar me-1"></i> Miembro desde <?php echo date('d/m/Y', strtotime($currentUser['fecha_creacion'])); ?></small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <!-- Actualizar Perfil -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-user-edit me-2"></i> Información del Perfil
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_profile">
                            
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre Completo</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" 
                                       value="<?php echo htmlspecialchars($currentUser['nombre']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($currentUser['email']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Rol</label>
                                <input type="text" class="form-control" 
                                       value="<?php echo htmlspecialchars($currentUser['rol']); ?>" disabled>
                                <small class="text-muted">El rol solo puede ser modificado por un administrador</small>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i> Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Cambiar Contraseña -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-key me-2"></i> Cambiar Contraseña
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="change_password">
                            
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Contraseña Actual</label>
                                <input type="password" class="form-control" id="current_password" 
                                       name="current_password" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="new_password" 
                                       name="new_password" minlength="6" required>
                                <small class="text-muted">Mínimo 6 caracteres</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirmar Nueva Contraseña</label>
                                <input type="password" class="form-control" id="confirm_password" 
                                       name="confirm_password" minlength="6" required>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-lock me-2"></i> Cambiar Contraseña
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
