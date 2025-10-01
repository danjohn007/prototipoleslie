<?php
/**
 * Analítica y Reportes
 * Gestión de reportes y análisis de datos
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

// Obtener datos para reportes
$saleModel = new Sale();
$productionModel = new Production();
$orderModel = new Order();
$productModel = new Product();

// Estadísticas generales
$salesStats = $saleModel->getStats();
$productionStats = $productionModel->getStats();
$orderStats = $orderModel->getStats();
$productStats = $productModel->getStats();

// Calcular métricas combinadas
$totalRevenue = $salesStats['total_ventas'] ?? 0;
$totalOrders = $orderStats['total_pedidos'] ?? 0;
$totalProduction = $productionStats['total_lotes'] ?? 0;
$totalProducts = $productStats['total_productos'] ?? 0;

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
    <title>Analítica y Reportes - Quesos Leslie</title>
    <link href="https://fonts.googleapis.com/css2?family=Helvetica+Neue:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #e74c3c;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark-gray: #495057;
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
        
        @media (min-width: 992px) {
            .sidebar {
                transform: translateX(0);
            }
        }
        
        .sidebar.active {
            transform: translateX(0);
        }
        
        .brand-header {
            padding: 30px 20px;
            text-align: center;
            border-bottom: 1px solid var(--medium-gray);
        }
        
        .brand-title {
            font-size: 18px;
            font-weight: 500;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .brand-subtitle {
            font-size: 12px;
            color: var(--dark-gray);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .nav-section {
            padding: 10px 0;
        }
        
        .nav-section-title {
            padding: 15px 20px 10px;
            font-size: 11px;
            font-weight: 500;
            color: var(--dark-gray);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
            transition: background-color 0.2s;
            font-size: 14px;
        }
        
        .nav-link:hover {
            background-color: var(--light-gray);
            color: var(--primary);
        }
        
        .nav-link.active {
            background-color: var(--primary);
            color: white;
            border-left: 3px solid var(--secondary);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 12px;
            font-size: 16px;
        }
        
        .nav-badge {
            margin-left: auto;
            background-color: var(--secondary);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 500;
        }
        
        .user-profile {
            padding: 20px;
            border-top: 1px solid var(--medium-gray);
            margin-top: auto;
        }
        
        .user-info {
            padding: 10px 0;
            border-bottom: 1px solid var(--medium-gray);
            margin-bottom: 10px;
        }
        
        .user-name {
            font-size: 14px;
            font-weight: 500;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .user-role {
            font-size: 12px;
            color: var(--dark-gray);
        }
        
        .main-content {
            margin-left: 0;
            padding: 30px;
            transition: margin-left 0.3s;
        }
        
        @media (min-width: 992px) {
            .main-content {
                margin-left: 280px;
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
            background-color: white;
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
        
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover {
            background-color: #1a252f;
            border-color: #1a252f;
        }
        
        .btn-success {
            background-color: var(--success);
            border-color: var(--success);
        }
        
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }
        
        .sidebar-overlay.active {
            display: block;
        }
        
        .hamburger-btn {
            display: none;
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
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
        
        .report-card {
            padding: 20px;
            border-left: 4px solid;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .report-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .report-card.sales {
            border-left-color: var(--success);
        }
        
        .report-card.production {
            border-left-color: var(--info);
        }
        
        .report-card.inventory {
            border-left-color: var(--warning);
        }
        
        .report-card.financial {
            border-left-color: var(--danger);
        }
        
        .report-icon {
            font-size: 36px;
            margin-bottom: 10px;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
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
            <div class="brand-subtitle">ANALÍTICA</div>
        </div>
        
        <!-- MÓDULOS DEL SISTEMA -->
        <div class="nav-section">
            <div class="nav-section-title">MÓDULOS</div>
            <a href="<?php echo BASE_URL; ?>/dashboard.php" class="nav-link">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
            <a href="<?php echo BASE_URL; ?>/produccion.php" class="nav-link">
                <i class="fas fa-industry"></i> Producción
            </a>
            <a href="<?php echo BASE_URL; ?>/nuevo-lote.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Lote
            </a>
            <a href="<?php echo BASE_URL; ?>/inventario.php" class="nav-link">
                <i class="fas fa-boxes"></i> Inventario
            </a>
            <a href="<?php echo BASE_URL; ?>/nuevo-producto.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Producto
            </a>
            <a href="<?php echo BASE_URL; ?>/pedidos.php" class="nav-link">
                <i class="fas fa-shopping-cart"></i> Pedidos
            </a>
            <a href="<?php echo BASE_URL; ?>/nuevo-pedido.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Pedido
            </a>
            <a href="<?php echo BASE_URL; ?>/ventas-punto.php" class="nav-link">
                <i class="fas fa-store"></i> Ventas
            </a>
            <a href="<?php echo BASE_URL; ?>/optimizacion-logistica.php" class="nav-link">
                <i class="fas fa-route"></i> Logística
            </a>
            <a href="<?php echo BASE_URL; ?>/nueva-ruta.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nueva Ruta
            </a>
            <a href="<?php echo BASE_URL; ?>/control-retornos.php" class="nav-link">
                <i class="fas fa-undo-alt"></i> Retornos
            </a>
            <a href="<?php echo BASE_URL; ?>/registrar-retorno.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Registrar Retorno
            </a>
            <a href="<?php echo BASE_URL; ?>/experiencia-cliente.php" class="nav-link">
                <i class="fas fa-users"></i> Clientes
            </a>
            <a href="<?php echo BASE_URL; ?>/analitica-reportes.php" class="nav-link active">
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
            <h1 class="page-title">Analítica y Reportes</h1>
            <div>
                <button class="btn btn-primary me-2">
                    <i class="fas fa-plus"></i> Nuevo Reporte
                </button>
                <button class="btn btn-success">
                    <i class="fas fa-file-export"></i> Exportar Todo
                </button>
            </div>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($success); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- KPI Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Ventas Totales</div>
                    <div class="kpi-value" style="color: var(--success);">S/. <?php echo number_format($totalRevenue, 2); ?></div>
                    <div class="kpi-trend up">
                        <i class="fas fa-arrow-up"></i> +12% este mes
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Pedidos</div>
                    <div class="kpi-value" style="color: var(--info);"><?php echo $totalOrders; ?></div>
                    <div class="kpi-trend up">
                        <i class="fas fa-arrow-up"></i> +8%
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Lotes Producidos</div>
                    <div class="kpi-value" style="color: var(--primary);"><?php echo $totalProduction; ?></div>
                    <div class="kpi-trend">
                        <i class="fas fa-minus"></i> Estable
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Productos Activos</div>
                    <div class="kpi-value" style="color: var(--warning);"><?php echo $totalProducts; ?></div>
                    <div class="kpi-trend">
                        <i class="fas fa-minus"></i> Sin cambios
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Reportes Disponibles -->
        <div class="row">
            <div class="col-md-6">
                <div class="card report-card sales">
                    <div class="report-icon text-success">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h5>Reporte de Ventas</h5>
                    <p class="text-muted">Análisis detallado de ventas por período, producto y cliente</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="text-muted">Última actualización: <?php echo date('d/m/Y H:i'); ?></span>
                        <button class="btn btn-sm btn-success">
                            <i class="fas fa-download"></i> Descargar
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card report-card production">
                    <div class="report-icon text-info">
                        <i class="fas fa-industry"></i>
                    </div>
                    <h5>Reporte de Producción</h5>
                    <p class="text-muted">Análisis de lotes producidos, eficiencia y rendimiento</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="text-muted">Última actualización: <?php echo date('d/m/Y H:i'); ?></span>
                        <button class="btn btn-sm btn-info">
                            <i class="fas fa-download"></i> Descargar
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card report-card inventory">
                    <div class="report-icon text-warning">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h5>Reporte de Inventario</h5>
                    <p class="text-muted">Estado del inventario, rotación y alertas de stock</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="text-muted">Última actualización: <?php echo date('d/m/Y H:i'); ?></span>
                        <button class="btn btn-sm btn-warning">
                            <i class="fas fa-download"></i> Descargar
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card report-card financial">
                    <div class="report-icon text-danger">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h5>Reporte Financiero</h5>
                    <p class="text-muted">Estado financiero, ingresos, gastos y rentabilidad</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="text-muted">Última actualización: <?php echo date('d/m/Y H:i'); ?></span>
                        <button class="btn btn-sm btn-danger">
                            <i class="fas fa-download"></i> Descargar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gráficas -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Tendencia de Ventas (Últimos 6 Meses)</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="salesTrendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Productos Más Vendidos</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="topProductsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
                if (window.innerWidth < 992) {
                    document.querySelector('.sidebar').classList.remove('active');
                    document.querySelector('.sidebar-overlay').classList.remove('active');
                }
            });
        });
        
        // Sales Trend Chart
        const salesCtx = document.getElementById('salesTrendChart');
        if (salesCtx) {
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Ventas (S/.)',
                        data: [12000, 15000, 13500, 16800, 18200, 19500],
                        borderColor: 'rgb(40, 167, 69)',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
        // Top Products Chart
        const productsCtx = document.getElementById('topProductsChart');
        if (productsCtx) {
            new Chart(productsCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Queso Fresco', 'Yogurt', 'Mantequilla', 'Crema', 'Otros'],
                    datasets: [{
                        data: [35, 25, 15, 15, 10],
                        backgroundColor: [
                            'rgb(40, 167, 69)',
                            'rgb(23, 162, 184)',
                            'rgb(255, 193, 7)',
                            'rgb(220, 53, 69)',
                            'rgb(108, 117, 125)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
