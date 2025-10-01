<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #e74c3c;
            --success: #27ae60;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Helvetica Neue', Arial, sans-serif;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            display: flex;
        }
        
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 50px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .login-left h1 {
            font-size: 32px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .login-left p {
            font-size: 16px;
            opacity: 0.9;
            line-height: 1.6;
        }
        
        .login-right {
            flex: 1;
            padding: 50px;
        }
        
        .login-title {
            font-size: 28px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 10px;
        }
        
        .login-subtitle {
            color: #6c757d;
            margin-bottom: 30px;
        }
        
        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            font-size: 15px;
        }
        
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.15);
        }
        
        .btn-login {
            background: var(--primary);
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            border: none;
            width: 100%;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background: #1a252f;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 62, 80, 0.3);
        }
        
        .alert {
            border-radius: 8px;
            padding: 12px 15px;
        }
        
        .input-group-text {
            background: transparent;
            border-right: none;
            color: #6c757d;
        }
        
        .input-group .form-control {
            border-left: none;
        }
        
        .input-group .form-control:focus {
            border-left: none;
        }
        
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            
            .login-left {
                padding: 30px;
            }
            
            .login-right {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <h1><i class="fas fa-cheese"></i> QUESOS LESLIE</h1>
            <p>Sistema Integral de Gestión Logística</p>
            <p style="margin-top: 30px; opacity: 0.8;">
                <i class="fas fa-check-circle me-2"></i> Gestión de Producción<br>
                <i class="fas fa-check-circle me-2"></i> Control de Inventario<br>
                <i class="fas fa-check-circle me-2"></i> Optimización de Rutas<br>
                <i class="fas fa-check-circle me-2"></i> Experiencia del Cliente<br>
                <i class="fas fa-check-circle me-2"></i> Reportes y Analítica
            </p>
        </div>
        
        <div class="login-right">
            <h2 class="login-title">Iniciar Sesión</h2>
            <p class="login-subtitle">Ingrese sus credenciales para acceder</p>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php 
                        echo $_SESSION['error']; 
                        unset($_SESSION['error']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php 
                        echo $_SESSION['success']; 
                        unset($_SESSION['success']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo BASE_URL; ?>/index.php?action=login" id="loginForm">
                <div class="mb-3">
                    <label class="form-label">Correo Electrónico</label>
                    <div class="input-group">
                        <span class="input-group-text border-end-0">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" class="form-control" name="email" 
                               placeholder="correo@ejemplo.com" required autocomplete="email">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text border-end-0">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control" name="password" 
                               placeholder="********" required autocomplete="current-password" id="password">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
                </button>
            </form>
            
            <div class="mt-4 text-center">
                <small class="text-muted">
                    <strong>Usuario de prueba:</strong><br>
                    Email: leslie@quesosleslie.com<br>
                    Password: admin123
                </small>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (password.type === 'password') {
                password.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
        
        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.querySelector('input[name="email"]').value;
            const password = document.querySelector('input[name="password"]').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Por favor complete todos los campos');
            }
        });
    </script>
</body>
</html>
