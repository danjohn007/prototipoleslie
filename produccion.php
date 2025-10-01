<?php
/**
 * Módulo de Producción
 * Gestión de producción e inventario
 */

// Cargar configuración
require_once __DIR__ . '/app/config/config.php';

// Verificar autenticación
$authController = new AuthController();
$authController->checkAuth();

// Obtener usuario actual
$userModel = new User();
$currentUser = $userModel->getCurrentUser();

// Obtener datos de producción
$productionModel = new Production();
$productModel = new Product();

$productions = $productionModel->getAll();
$stats = $productionModel->getStats();
$recentProductions = $productionModel->getRecent(5);
$productStats = $productModel->getStats();
$lowStock = $productModel->getLowStock();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producción e Inventario - Quesos Leslie</title>
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
        
        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
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
        
        .user-table {
            width: 100%;
            font-size: 14px;
        }
        
        .user-table th {
            text-align: left;
            padding: 10px;
            background-color: var(--light-gray);
            font-weight: 500;
            color: var(--dark-gray);
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }
        
        .user-table td {
            padding: 12px 10px;
            border-bottom: 1px solid var(--medium-gray);
        }
        
        .user-table tr:last-child td {
            border-bottom: none;
        }
        
        .badge-status {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }
        
        .status-active {
            background-color: rgba(40, 167, 69, 0.2);
            color: #155724;
        }
        
        .status-expiring {
            background-color: rgba(255, 193, 7, 0.2);
            color: #856404;
        }
        
        .status-expired {
            background-color: rgba(220, 53, 69, 0.2);
            color: #721c24;
        }
        
        .product-icon {
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            margin-right: 8px;
            color: white;
            font-size: 12px;
        }
        
        .product-cheese {
            background-color: #e67e22;
        }
        
        .product-yogurt {
            background-color: #3498db;
        }
        
        .product-cream {
            background-color: #9b59b6;
        }
        
        .product-butter {
            background-color: #f39c12;
        }
        
        .user-profile {
            position: sticky;
            bottom: 0;
            background: white;
            border-top: 1px solid var(--medium-gray);
            padding: 15px 0;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-success {
            background-color: var(--environment);
            border-color: var(--environment);
        }
        
        .btn-warning {
            background-color: var(--education);
            border-color: var(--education);
        }
        
        .progress {
            height: 8px;
            margin-top: 5px;
        }
        
        .alert-warning {
            background-color: rgba(255, 193, 7, 0.1);
            border-color: rgba(255, 193, 7, 0.3);
            color: #856404;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border-color: rgba(220, 53, 69, 0.3);
            color: #721c24;
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
            <div class="brand-subtitle">PRODUCCIÓN</div>
        </div>
        
                <!-- MÓDULOS DEL SISTEMA -->
        <div class="nav-section">
            <div class="nav-section-title">MÓDULOS</div>
            <a href="dashboard.php" class="nav-link">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
            <a href="produccion.php" class="nav-link active">
                <i class="fas fa-industry"></i> Producción
                <span class="nav-badge"><?php echo $stats['total_lotes'] ?? 0; ?></span>
            </a>
            <a href="nuevo-lote.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Lote
            </a>
            <a href="inventario.php" class="nav-link">
                <i class="fas fa-boxes"></i> Gestión de Inventario
                <span class="nav-badge"><?php echo count($lowStock); ?></span>
            </a>
            <a href="nuevo-producto.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Producto
            </a>
            <a href="<?php echo BASE_URL; ?>/registro-produccion.php" class="nav-link">
                <i class="fas fa-clipboard-list"></i> Registro de Producción
                <span class="nav-badge">3</span>
            </a>
            <a href="pedidos.php" class="nav-link">
                <i class="fas fa-shopping-cart"></i> Pedidos
            </a>
            <a href="nuevo-pedido.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Pedido
            </a>
            <a href="ventas-punto.php" class="nav-link">
                <i class="fas fa-store"></i> Ventas
            </a>
            <a href="optimizacion-logistica.php" class="nav-link">
                <i class="fas fa-route"></i> Logística
            </a>
            <a href="nueva-ruta.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nueva Ruta
            </a>
            <a href="control-retornos.php" class="nav-link">
                <i class="fas fa-undo-alt"></i> Retornos
            </a>
            <a href="registrar-retorno.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Registrar Retorno
            </a>
            <a href="experiencia-cliente.php" class="nav-link">
                <i class="fas fa-users"></i> Clientes
            </a>
            <a href="analitica-reportes.php" class="nav-link">
                <i class="fas fa-chart-bar"></i> Analítica
            </a>
            <a href="<?php echo BASE_URL; ?>/gestion-clientes.php" class="nav-link">
                <i class="fas fa-address-book"></i> Gestión Clientes
            </a>
            <a href="<?php echo BASE_URL; ?>/nuevo-cliente.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Cliente
            </a>
            <a href="<?php echo BASE_URL; ?>/administracion-financiera.php" class="nav-link">
                <i class="fas fa-dollar-sign"></i> Admin. Financiera
            </a>
            <a href="<?php echo BASE_URL; ?>/nueva-transaccion.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nueva Transacción
            </a>
        </div>
        
        <!-- User Profile -->
        <div class="user-profile">
            <a href="#" class="nav-link">
                <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($currentUser['nombre']); ?>
            </a>
            <a href="index.php?action=logout" class="nav-link" id="logout-btn" onclick="return confirm('¿Está seguro que desea cerrar sesión?')">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>
    </div>
    
    <!-- Main Content Area -->
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Producción e Inventario</h1>
            <div>
                <a href="nuevo-lote.php" class="btn btn-primary me-2">
                    <i class="fas fa-plus"></i> Nuevo Lote
                </a>
                <button class="btn btn-success">
                    <i class="fas fa-file-export"></i> Exportar Reporte
                </button>
            </div>
        </div>
        
        <!-- KPI Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Producción Total</div>
                    <div class="kpi-value" style="color: var(--environment);"><?php echo number_format($stats['total_produccion'] ?? 0); ?> kg</div>
                    <div class="kpi-trend up">
                        <i class="fas fa-arrow-up"></i> Total acumulado
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Inventario Total</div>
                    <div class="kpi-value" style="color: var(--human-rights);"><?php echo format_currency($productStats['valor_inventario'] ?? 0); ?></div>
                    <div class="kpi-trend up">
                        <i class="fas fa-arrow-up"></i> Valor de inventario
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Lotes Activos</div>
                    <div class="kpi-value" style="color: var(--equity);"><?php echo $stats['total_lotes'] ?? 0; ?></div>
                    <div class="kpi-trend up">
                        <i class="fas fa-check"></i> <?php echo $stats['lotes_completados'] ?? 0; ?> completados
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Stock Bajo</div>
                    <div class="kpi-value" style="color: var(--education);"><?php echo count($lowStock); ?></div>
                    <div class="kpi-trend <?php echo count($lowStock) > 0 ? 'down' : ''; ?>">
                        <i class="fas fa-<?php echo count($lowStock) > 0 ? 'exclamation-triangle' : 'check'; ?>"></i> Productos
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertas -->
        <?php if (count($lowStock) > 0): ?>
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                    <div>
                        <strong><?php echo count($lowStock); ?> productos con stock bajo</strong> - Revisar inventario y planificar reabastecimiento
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php 
                        echo $_SESSION['success']; 
                        unset($_SESSION['success']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Charts Row -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Producción Mensual por Producto</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="productionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Estado del Inventario</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="inventoryStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Lotes Activos -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Lotes Activos en Inventario</div>
                        <button class="btn btn-sm btn-primary">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="user-table">
                            <thead>
                                <tr>
                                    <th>Lote ID</th>
                                    <th>Producto</th>
                                    <th>Fecha Producción</th>
                                    <th>Fecha Vencimiento</th>
                                    <th>Cantidad</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($productions)): ?>
                                    <?php foreach ($productions as $production): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($production['numero_lote']); ?></td>
                                        <td>
                                            <span class="product-icon product-cheese"><i class="fas fa-cheese"></i></span> 
                                            <?php echo htmlspecialchars($production['producto_nombre']); ?>
                                        </td>
                                        <td><?php echo format_date($production['fecha_produccion']); ?></td>
                                        <td><?php echo format_date($production['fecha_vencimiento'] ?? ''); ?></td>
                                        <td><?php echo number_format($production['cantidad_producida']); ?> kg</td>
                                        <td>
                                            <?php 
                                            $statusClass = 'status-active';
                                            $statusText = ucfirst(str_replace('_', ' ', $production['estado']));
                                            if ($production['estado'] == 'en_proceso') {
                                                $statusClass = 'status-expiring';
                                            } elseif ($production['estado'] == 'completado' || $production['estado'] == 'aprobado') {
                                                $statusClass = 'status-active';
                                            }
                                            ?>
                                            <span class="badge-status <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning" title="Historial">
                                                <i class="fas fa-history"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No hay lotes de producción registrados</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Producción Reciente y Niveles de Stock -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Producción Reciente</div>
                    </div>
                    <div class="card-body">
                        <table class="user-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Lote</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>10/11/2023</td>
                                    <td><span class="product-icon product-cheese"><i class="fas fa-cheese"></i></span> Queso Gouda</td>
                                    <td>50 kg</td>
                                    <td>#L-23018</td>
                                </tr>
                                <tr>
                                    <td>09/11/2023</td>
                                    <td><span class="product-icon product-cheese"><i class="fas fa-cheese"></i></span> Queso Manchego</td>
                                    <td>45 kg</td>
                                    <td>#L-23019</td>
                                </tr>
                                <tr>
                                    <td>08/11/2023</td>
                                    <td><span class="product-icon product-yogurt"><i class="fas fa-wine-bottle"></i></span> Yogurt Natural</td>
                                    <td>30 kg</td>
                                    <td>#L-23020</td>
                                </tr>
                                <tr>
                                    <td>07/11/2023</td>
                                    <td><span class="product-icon product-cream"><i class="fas fa-wine-bottle"></i></span> Crema Fresca</td>
                                    <td>25 kg</td>
                                    <td>#L-23021</td>
                                </tr>
                                <tr>
                                    <td>06/11/2023</td>
                                    <td><span class="product-icon product-butter"><i class="fas fa-cube"></i></span> Mantequilla</td>
                                    <td>20 kg</td>
                                    <td>#L-23022</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Niveles de Stock por Producto</div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Queso Gouda</span>
                                <span>320/400 kg</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: 80%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Queso Manchego</span>
                                <span>280/350 kg</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: 80%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Yogurt Natural</span>
                                <span>150/200 kg</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-warning" style="width: 75%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Crema Fresca</span>
                                <span>90/150 kg</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-warning" style="width: 60%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Mantequilla</span>
                                <span>65/100 kg</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-danger" style="width: 65%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script>
        // Production Chart
        const productionCtx = document.getElementById('productionChart').getContext('2d');
        const productionChart = new Chart(productionCtx, {
            type: 'bar',
            data: {
                labels: ['Queso Gouda', 'Queso Manchego', 'Yogurt Natural', 'Crema Fresca', 'Mantequilla'],
                datasets: [{
                    label: 'Producción Mensual (kg)',
                    data: [320, 280, 150, 90, 65],
                    backgroundColor: [
                        'rgba(39, 174, 96, 0.7)',
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(155, 89, 182, 0.7)',
                        'rgba(243, 156, 18, 0.7)',
                        'rgba(230, 126, 34, 0.7)'
                    ],
                    borderColor: [
                        'rgba(39, 174, 96, 1)',
                        'rgba(52, 152, 219, 1)',
                        'rgba(155, 89, 182, 1)',
                        'rgba(243, 156, 18, 1)',
                        'rgba(230, 126, 34, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' kg';
                            }
                        }
                    }
                }
            }
        });

        // Inventory Status Chart
        const inventoryStatusCtx = document.getElementById('inventoryStatusChart').getContext('2d');
        const inventoryStatusChart = new Chart(inventoryStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Activo', 'Por Caducar', 'Caducado'],
                datasets: [{
                    data: [12, 3, 0],
                    backgroundColor: [
                        '#28a745',
                        '#ffc107',
                        '#dc3545'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw + ' lotes';
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
        
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