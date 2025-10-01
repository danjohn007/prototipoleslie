<?php
/**
 * Dashboard Principal
 * Requiere autenticación
 */

// Cargar configuración
require_once __DIR__ . '/app/config/config.php';

// Verificar autenticación
$authController = new AuthController();
$authController->checkAuth();

// Obtener usuario actual
$userModel = new User();
$currentUser = $userModel->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
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
            color: var(--dark-gray);
            text-decoration: none;
            transition: all 0.3s;
            font-size: 14px;
        }
        
        .nav-link:hover {
            background-color: var(--light-gray);
            color: var(--primary);
            padding-left: 25px;
        }
        
        .nav-link i {
            width: 25px;
            margin-right: 10px;
        }
        
        .main-content {
            margin-left: 280px;
            padding: 30px;
            min-height: 100vh;
            width: calc(100% - 280px);
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
        
        .user-profile {
            border-top: 1px solid var(--medium-gray);
            padding: 20px;
            position: absolute;
            bottom: 0;
            width: 100%;
            background: white;
        }
        
        .user-info {
            padding: 10px 20px;
            background: var(--light-gray);
            border-radius: 8px;
            margin-bottom: 10px;
        }
        
        .user-name {
            font-weight: 600;
            color: var(--primary);
        }
        
        .user-role {
            font-size: 12px;
            color: var(--dark-gray);
            text-transform: capitalize;
        }
        
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="brand-header">
            <div class="brand-title">QUESOS LESLIE</div>
            <div class="brand-subtitle">SISTEMA</div>
        </div>
        
        <!-- MÓDULOS DEL SISTEMA -->
        <div class="nav-section">
            <div class="nav-section-title">MÓDULOS</div>
            <a href="<?php echo BASE_URL; ?>/dashboard.php" class="nav-link">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
            <a href="<?php echo BASE_URL; ?>/produccion.html" class="nav-link">
                <i class="fas fa-industry"></i> Producción
            </a>
            <a href="<?php echo BASE_URL; ?>/inventario.html" class="nav-link">
                <i class="fas fa-boxes"></i> Inventario
            </a>
            <a href="<?php echo BASE_URL; ?>/pedidos.html" class="nav-link">
                <i class="fas fa-shopping-cart"></i> Pedidos
            </a>
            <a href="<?php echo BASE_URL; ?>/optimizacion-logistica.html" class="nav-link">
                <i class="fas fa-route"></i> Logística
            </a>
            <a href="<?php echo BASE_URL; ?>/experiencia-cliente.html" class="nav-link">
                <i class="fas fa-users"></i> Clientes
            </a>
            <a href="<?php echo BASE_URL; ?>/analitica-reportes.html" class="nav-link">
                <i class="fas fa-chart-bar"></i> Analítica
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
    
    <!-- Main Content Area -->
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Dashboard Principal</h1>
            <div>
                <span class="text-muted">
                    <i class="fas fa-calendar me-1"></i> <?php echo date('d/m/Y'); ?>
                </span>
            </div>
        </div>
        
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
        
        <!-- KPI Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Pedidos Hoy</div>
                    <div class="kpi-value" style="color: var(--primary);">23</div>
                    <div class="kpi-trend up">
                        <i class="fas fa-arrow-up"></i> 12% vs ayer
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">En Producción</div>
                    <div class="kpi-value" style="color: var(--education);">8</div>
                    <div class="kpi-trend up">
                        <i class="fas fa-arrow-up"></i> 2 lotes nuevos
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Rutas Activas</div>
                    <div class="kpi-value" style="color: var(--transport);">5</div>
                    <div class="kpi-trend">
                        <i class="fas fa-minus"></i> Sin cambios
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Satisfacción</div>
                    <div class="kpi-value" style="color: var(--success);">4.8/5</div>
                    <div class="kpi-trend up">
                        <i class="fas fa-arrow-up"></i> +0.2 pts
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Welcome Message -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <h2 class="mb-3">
                            <i class="fas fa-cheese text-primary me-2"></i>
                            Bienvenido al Sistema de Logística Quesos Leslie
                        </h2>
                        <p class="lead text-muted mb-4">
                            Sistema completo de gestión logística desarrollado con PHP puro y MySQL
                        </p>
                        <div class="row text-start">
                            <div class="col-md-6">
                                <h5><i class="fas fa-check-circle text-success me-2"></i> Características Implementadas:</h5>
                                <ul class="list-unstyled ms-4">
                                    <li><i class="fas fa-chevron-right text-primary me-2"></i> Arquitectura MVC</li>
                                    <li><i class="fas fa-chevron-right text-primary me-2"></i> Autenticación con password_hash()</li>
                                    <li><i class="fas fa-chevron-right text-primary me-2"></i> Base de datos MySQL con datos de ejemplo</li>
                                    <li><i class="fas fa-chevron-right text-primary me-2"></i> URL Base auto-configurable</li>
                                    <li><i class="fas fa-chevron-right text-primary me-2"></i> Bootstrap 5 para diseño responsivo</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="fas fa-cog text-info me-2"></i> Módulos del Sistema:</h5>
                                <ul class="list-unstyled ms-4">
                                    <li><i class="fas fa-chevron-right text-primary me-2"></i> Gestión de Producción e Inventario</li>
                                    <li><i class="fas fa-chevron-right text-primary me-2"></i> Control de Pedidos y Ventas</li>
                                    <li><i class="fas fa-chevron-right text-primary me-2"></i> Optimización Logística</li>
                                    <li><i class="fas fa-chevron-right text-primary me-2"></i> Experiencia del Cliente</li>
                                    <li><i class="fas fa-chevron-right text-primary me-2"></i> Analítica y Reportes</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="<?php echo BASE_URL; ?>/test-connection.php" class="btn btn-primary btn-lg me-2">
                                <i class="fas fa-vial me-2"></i> Test de Conexión
                            </a>
                            <a href="<?php echo BASE_URL; ?>/produccion.html" class="btn btn-success btn-lg">
                                <i class="fas fa-industry me-2"></i> Ver Producción
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
