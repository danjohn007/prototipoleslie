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

// Obtener datos para el dashboard
$orderModel = new Order();
$productionModel = new Production();
$productModel = new Product();
$inventoryModel = new Inventory();

// Estadísticas
$orderStats = $orderModel->getStats();
$productionStats = $productionModel->getStats();
$productStats = $productModel->getStats();
$inventoryStats = $inventoryModel->getStats();

// Datos para gráficas
$recentOrders = $orderModel->getRecent(7);
$recentProduction = $productionModel->getRecent(5);
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
    <?php include __DIR__ . '/app/includes/sidebar-styles.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/app/includes/sidebar.php'; ?>
    
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
                    <div class="kpi-label">Total Pedidos</div>
                    <div class="kpi-value" style="color: var(--primary);"><?php echo $orderStats['total'] ?? 0; ?></div>
                    <div class="kpi-trend up">
                        <i class="fas fa-shopping-cart"></i> Gestión de Pedidos
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">En Producción</div>
                    <div class="kpi-value" style="color: var(--education);"><?php echo $productionStats['en_proceso'] ?? 0; ?></div>
                    <div class="kpi-trend up">
                        <i class="fas fa-industry"></i> Lotes Activos
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Productos</div>
                    <div class="kpi-value" style="color: var(--transport);"><?php echo $productStats['total'] ?? 0; ?></div>
                    <div class="kpi-trend">
                        <i class="fas fa-boxes"></i> En Inventario
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Stock Bajo</div>
                    <div class="kpi-value" style="color: var(--danger);"><?php echo $inventoryStats['bajo_stock'] ?? 0; ?></div>
                    <div class="kpi-trend down">
                        <i class="fas fa-exclamation-triangle"></i> Requieren atención
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charts Section -->
        <div class="row">
            <!-- Chart 1: Pedidos por Estado -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-chart-pie me-2"></i> Pedidos por Estado
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="ordersStatusChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Chart 2: Producción Reciente -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-chart-bar me-2"></i> Producción Reciente
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="productionChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Chart 3: Estado del Inventario -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-chart-doughnut me-2"></i> Estado del Inventario
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="inventoryChart" style="max-height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Access -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-bolt me-2"></i> Accesos Rápidos
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <a href="<?php echo BASE_URL; ?>/nuevo-pedido.php" class="btn btn-primary btn-lg w-100 mb-2">
                                    <i class="fas fa-plus-circle me-2"></i> Nuevo Pedido
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="<?php echo BASE_URL; ?>/nuevo-lote.php" class="btn btn-success btn-lg w-100 mb-2">
                                    <i class="fas fa-industry me-2"></i> Nuevo Lote
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="<?php echo BASE_URL; ?>/nuevo-producto.php" class="btn btn-info btn-lg w-100 mb-2">
                                    <i class="fas fa-box me-2"></i> Nuevo Producto
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="<?php echo BASE_URL; ?>/analitica-reportes.php" class="btn btn-warning btn-lg w-100 mb-2">
                                    <i class="fas fa-chart-line me-2"></i> Ver Reportes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Chart 1: Pedidos por Estado
        const ordersCtx = document.getElementById('ordersStatusChart').getContext('2d');
        const ordersChart = new Chart(ordersCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pendiente', 'Confirmado', 'En Preparación', 'En Ruta', 'Entregado'],
                datasets: [{
                    data: [
                        <?php echo $orderStats['pendientes'] ?? 0; ?>,
                        <?php echo $orderStats['confirmados'] ?? 0; ?>,
                        <?php echo $orderStats['en_preparacion'] ?? 0; ?>,
                        <?php echo $orderStats['en_ruta'] ?? 0; ?>,
                        <?php echo $orderStats['entregados'] ?? 0; ?>
                    ],
                    backgroundColor: [
                        '#ffc107',
                        '#3498db',
                        '#9b59b6',
                        '#e67e22',
                        '#27ae60'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });

        // Chart 2: Producción Reciente
        const productionCtx = document.getElementById('productionChart').getContext('2d');
        const productionChart = new Chart(productionCtx, {
            type: 'bar',
            data: {
                labels: [<?php 
                    $labels = [];
                    foreach ($recentProduction as $prod) {
                        $labels[] = "'" . substr($prod['producto_nombre'], 0, 15) . "'";
                    }
                    echo implode(',', $labels);
                ?>],
                datasets: [{
                    label: 'Cantidad Producida',
                    data: [<?php 
                        $quantities = [];
                        foreach ($recentProduction as $prod) {
                            $quantities[] = $prod['cantidad_producida'];
                        }
                        echo implode(',', $quantities);
                    ?>],
                    backgroundColor: '#27ae60',
                    borderColor: '#229954',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });

        // Chart 3: Estado del Inventario
        const inventoryCtx = document.getElementById('inventoryChart').getContext('2d');
        const inventoryChart = new Chart(inventoryCtx, {
            type: 'pie',
            data: {
                labels: ['Stock Óptimo', 'Stock Bajo', 'Sin Stock'],
                datasets: [{
                    data: [
                        <?php echo $inventoryStats['optimo'] ?? 0; ?>,
                        <?php echo $inventoryStats['bajo_stock'] ?? 0; ?>,
                        <?php echo $inventoryStats['sin_stock'] ?? 0; ?>
                    ],
                    backgroundColor: [
                        '#27ae60',
                        '#ffc107',
                        '#dc3545'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
