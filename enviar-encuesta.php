<?php
/**
 * Enviar Encuesta
 * Módulo con conexión a base de datos
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

// Obtener datos desde base de datos
$clientModel = new Client();
$items = $clientModel->getAll();

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
    <title>Enviar Encuesta - Quesos Leslie</title>
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
            transform: translateX(-280px);
            transition: transform 0.3s ease-in-out;
        }
        
        .sidebar.active {
            transform: translateX(0);
        }
        
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0,0,0,0.5);
            z-index: 999;
            display: none;
        }
        
        .sidebar-overlay.active {
            display: block;
        }
        
        .brand-header {
            padding: 25px;
            border-bottom: 1px solid var(--medium-gray);
            position: sticky;
            top: 0;
            background: white;
            z-index: 100;
        }
        
        .brand-title {
            font-size: 22px;
            font-weight: 400;
            color: var(--primary);
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .brand-subtitle {
            font-size: 12px;
            color: var(--dark-gray);
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        
        .nav-section {
            padding: 15px 0;
            border-bottom: 1px solid var(--medium-gray);
        }
        
        .nav-section-title {
            font-size: 11px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 25px 10px 25px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: var(--dark-gray);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
            position: relative;
        }
        
        .nav-link:hover {
            background-color: var(--light-gray);
            color: var(--primary);
        }
        
        .nav-link.active {
            color: var(--primary);
            border-left: 3px solid var(--secondary);
            background-color: rgba(44, 62, 80, 0.05);
        }
        
        .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 16px;
        }
        
        .nav-badge {
            position: absolute;
            right: 25px;
            background-color: var(--secondary);
            color: white;
            border-radius: 10px;
            padding: 2px 8px;
            font-size: 10px;
        }
        
        .user-profile {
            position: sticky;
            bottom: 0;
            background: white;
            border-top: 1px solid var(--medium-gray);
            padding: 15px 0;
        }
        
        .main-content {
            margin-left: 0;
            padding: 30px;
            transition: margin-left 0.3s ease-in-out;
        }
        
        @media (min-width: 992px) {
            .sidebar {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 280px;
            }
            .sidebar-overlay {
                display: none !important;
            }
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 400;
            color: var(--primary);
            margin: 0;
        }
        
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 24px;
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid var(--medium-gray);
            padding: 16px 20px;
            font-weight: 500;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-title {
            font-size: 16px;
            margin: 0;
            color: var(--primary);
        }
        
        .card-body {
            padding: 20px;
        }
        
        .kpi-card {
            text-align: center;
            padding: 20px;
        }
        
        .kpi-value {
            font-size: 28px;
            font-weight: 500;
            margin: 10px 0;
        }
        
        .kpi-label {
            font-size: 12px;
            color: var(--dark-gray);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .kpi-trend {
            font-size: 12px;
            margin-top: 5px;
        }
        
        .kpi-trend.up {
            color: var(--success);
        }
        
        .kpi-trend.down {
            color: var(--danger);
        }
        
        .hamburger-btn {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--primary);
            cursor: pointer;
            padding: 10px;
            margin-right: 15px;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
        }
        
        @media (max-width: 991px) {
            .hamburger-btn {
                display: block;
            }
        }
        
        .rating-stars {
            color: var(--education);
            font-size: 18px;
        }
        
        .feedback-card {
            padding: 15px;
            border-left: 4px solid var(--human-rights);
            background-color: rgba(52, 152, 219, 0.05);
            margin-bottom: 15px;
        }
        
        .sentiment-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .sentiment-positive {
            background-color: rgba(39, 174, 96, 0.1);
            color: var(--success);
        }
        
        .sentiment-neutral {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning);
        }
        
        .sentiment-negative {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }
    </style>
</head>
<body>
    <!-- Overlay para menú móvil -->
    <div class="sidebar-overlay"></div>
    
    <!-- Botón hamburguesa para móvil -->
    <button class="hamburger-btn">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="brand-header">
            <div class="brand-title">QUESOS LESLIE</div>
            <div class="brand-subtitle">EXPERIENCIA</div>
        </div>
        
                <!-- MÓDULOS DEL SISTEMA -->
        <div class="nav-section">
            <div class="nav-section-title">MÓDULOS</div>
            <a href="<?php echo BASE_URL; ?>/dashboard.php" class="nav-link">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
            <a href="<?php echo BASE_URL; ?>/produccion.php" class="nav-link">
                <i class="fas fa-industry"></i> Producción
                <span class="nav-badge">15</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/nuevo-lote.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Lote
            </a>
            <a href="<?php echo BASE_URL; ?>/inventario.php" class="nav-link">
                <i class="fas fa-boxes"></i> Gestión de Inventario
                <span class="nav-badge">8</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/nuevo-producto.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Producto
            </a>
            <a href="<?php echo BASE_URL; ?>/registro-produccion.php" class="nav-link">
                <i class="fas fa-clipboard-list"></i> Registro de Producción
                <span class="nav-badge">3</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/pedidos.php" class="nav-link">
                <i class="fas fa-shopping-cart"></i> Gestión de Pedidos
                <span class="nav-badge">47</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/nuevo-pedido.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Pedido
            </a>
            <a href="<?php echo BASE_URL; ?>/ventas-punto.php" class="nav-link">
                <i class="fas fa-store"></i> Ventas en Punto
                <span class="nav-badge">12</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/optimizacion-logistica.php" class="nav-link">
                <i class="fas fa-route"></i> Optimización Logística
                <span class="nav-badge">5</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/nueva-ruta.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nueva Ruta
            </a>
            <a href="<?php echo BASE_URL; ?>/control-retornos.php" class="nav-link">
                <i class="fas fa-undo-alt"></i> Control de Retornos
                <span class="nav-badge">7</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/registrar-retorno.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Registrar Retorno
            </a>
            <a href="<?php echo BASE_URL; ?>/experiencia-cliente.php" class="nav-link">
                <i class="fas fa-smile"></i> Experiencia del Cliente
            </a>
            <a href="<?php echo BASE_URL; ?>/enviar-encuesta.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-envelope"></i> Enviar Encuesta
            </a>
            <a href="<?php echo BASE_URL; ?>/analitica-reportes.php" class="nav-link">
                <i class="fas fa-chart-bar"></i> Analítica y Reportes
            </a>
            <a href="<?php echo BASE_URL; ?>/nuevo-reporte.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Reporte
            </a>
            <a href="<?php echo BASE_URL; ?>/gestion-clientes.php" class="nav-link">
                <i class="fas fa-users"></i> Gestión de Clientes
                <span class="nav-badge">234</span>
            </a>
            <a href="<?php echo BASE_URL; ?>/nuevo-cliente.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Cliente
            </a>
            <a href="<?php echo BASE_URL; ?>/administracion-financiera.php" class="nav-link">
                <i class="fas fa-dollar-sign"></i> Administración Financiera
            </a>
            <a href="<?php echo BASE_URL; ?>/nueva-transaccion.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nueva Transacción
            </a>
        </div>
        
                <!-- User Profile -->
        <div class="user-profile">
            <div class="user-info">
                <div class="user-name"><i class="fas fa-user-circle me-2"></i> <?php echo htmlspecialchars($currentUser['nombre']); ?></div>
                <div class="user-role"><?php echo htmlspecialchars($currentUser['rol']); ?></div>
            </div>
            <a href="<?php echo BASE_URL; ?>/index.php?action=logout" class="nav-link" onclick="return confirm('¿Está seguro que desea cerrar sesión?')">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>
    </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">NPS Score</div>
                    <div class="kpi-value" style="color: var(--human-rights);">72</div>
                    <div class="kpi-trend up">
                        <i class="fas fa-arrow-up"></i> 5 puntos más
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Encuestas Este Mes</div>
                    <div class="kpi-value" style="color: var(--equity);">156</div>
                    <div class="kpi-trend up">
                        <i class="fas fa-arrow-up"></i> 23% más vs mes pasado
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Tiempo Respuesta Promedio</div>
                    <div class="kpi-value" style="color: var(--environment);">4.2h</div>
                    <div class="kpi-trend down">
                        <i class="fas fa-arrow-down"></i> 1.5h menos
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Feedback Reciente -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Feedback Reciente de Clientes</div>
                        <div>
                            <select class="form-select form-select-sm" style="width: 150px;">
                                <option>Todos</option>
                                <option>Positivo</option>
                                <option>Neutral</option>
                                <option>Negativo</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="feedback-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">Supermercado La Esperanza</h6>
                                    <div class="rating-stars mb-1">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <span class="sentiment-badge sentiment-positive">Positivo</span>
                            </div>
                            <p class="mb-2">"Excelente calidad de productos y entrega siempre puntual. Los quesos frescos son los mejores de la zona."</p>
                            <small class="text-muted">Hace 2 horas</small>
                        </div>
                        
                        <div class="feedback-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">Tienda Don Pepe</h6>
                                    <div class="rating-stars mb-1">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                </div>
                                <span class="sentiment-badge sentiment-positive">Positivo</span>
                            </div>
                            <p class="mb-2">"Buenos productos, aunque me gustaría tener más variedad en presentaciones pequeñas."</p>
                            <small class="text-muted">Hace 5 horas</small>
                        </div>
                        
                        <div class="feedback-card" style="border-left-color: var(--warning);">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">Minimarket El Trébol</h6>
                                    <div class="rating-stars mb-1">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                </div>
                                <span class="sentiment-badge sentiment-neutral">Neutral</span>
                            </div>
                            <p class="mb-2">"La calidad es buena pero tuvieron un retraso en la entrega de esta semana."</p>
                            <small class="text-muted">Hace 1 día</small>
                        </div>
                        
                        <div class="feedback-card" style="border-left-color: var(--success);">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">Bodega Central</h6>
                                    <div class="rating-stars mb-1">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <span class="sentiment-badge sentiment-positive">Positivo</span>
                            </div>
                            <p class="mb-2">"Mis clientes están muy contentos con los quesos. La relación calidad-precio es excelente."</p>
                            <small class="text-muted">Hace 1 día</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Distribución de Sentimientos</div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small>😊 Positivo</small>
                                <small class="fw-bold">78%</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: 78%"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small>😐 Neutral</small>
                                <small class="fw-bold">15%</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: 15%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <small>😞 Negativo</small>
                                <small class="fw-bold">7%</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-danger" style="width: 7%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Aspectos Mejor Valorados</div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <small>Calidad del Producto</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: 95%"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <small>Atención al Cliente</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: 88%"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <small>Puntualidad</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: 82%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <small>Precio</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: 75%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Áreas de Mejora</div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-2 small">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Variedad de productos</strong>
                            <p class="mb-0 mt-1">15 clientes solicitaron más opciones</p>
                        </div>
                        <div class="alert alert-info mb-0 small">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Horarios de entrega</strong>
                            <p class="mb-0 mt-1">8 clientes pidieron flexibilidad</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.querySelector('.hamburger-btn').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        });
        
        // Close sidebar when clicking on overlay
        document.querySelector('.sidebar-overlay').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.remove('active');
            this.classList.remove('active');
        });
        
        // Close sidebar when clicking on menu items
        document.querySelectorAll('.sidebar .nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                document.querySelector('.sidebar').classList.remove('active');
                document.querySelector('.sidebar-overlay').classList.remove('active');
            });
        });
        
        // Logout simulation
        document.getElementById('logout-btn').addEventListener('click', function(e) {
            e.preventDefault();
            if(confirm('¿Está seguro que desea cerrar sesión?')) {
                alert('Sesión cerrada. Redirigiendo al login...');
                // In a real application, redirect to login page
                // window.location.href = 'login.html';
            }
        });
    </script>
</body>
</html>
