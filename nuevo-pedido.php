<?php
/**
 * Nuevo Pedido
 * Formulario para crear un nuevo pedido
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

// Obtener clientes y productos para el formulario
$clientModel = new Client();
$clients = $clientModel->getAll();

$productModel = new Product();
$products = $productModel->getAll();

// Generar número de pedido
$numero_pedido = generate_order_number('PED');

// Procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderController = new OrderController();
    $orderController->createOrder();
    exit;
}

// Mensajes de sesión
$error = $_SESSION['error'] ?? null;
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['error'], $_SESSION['errors']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Pedido - <?php echo APP_NAME; ?></title>
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
        
        .status-pending {
            background-color: rgba(255, 193, 7, 0.2);
            color: #856404;
        }
        
        .status-confirmed {
            background-color: rgba(0, 123, 255, 0.2);
            color: #004085;
        }
        
        .status-preparing {
            background-color: rgba(111, 66, 193, 0.2);
            color: #542c85;
        }
        
        .status-shipped {
            background-color: rgba(32, 201, 151, 0.2);
            color: #0a5448;
        }
        
        .status-delivered {
            background-color: rgba(40, 167, 69, 0.2);
            color: #155724;
        }
        
        .status-cancelled {
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
        
        .btn-info {
            background-color: var(--human-rights);
            border-color: var(--human-rights);
        }
        
        .channel-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            background-color: #e9ecef;
            color: #495057;
        }
        
        .channel-web {
            background-color: rgba(52, 152, 219, 0.1);
            color: #004085;
        }
        
        .channel-whatsapp {
            background-color: rgba(37, 211, 102, 0.1);
            color: #0a5448;
        }
        
        .channel-phone {
            background-color: rgba(108, 117, 125, 0.1);
            color: #383d41;
        }
        
        .search-box {
            max-width: 300px;
        }
        
        .order-timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .order-timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #e9ecef;
        }
        
        .timeline-step {
            position: relative;
            margin-bottom: 20px;
        }
        
        .timeline-step::before {
            content: '';
            position: absolute;
            left: -25px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #6c757d;
            border: 2px solid white;
        }
        
        .timeline-step.active::before {
            background-color: var(--success);
        }
        
        .timeline-step.completed::before {
            background-color: var(--success);
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
            <div class="brand-subtitle">PEDIDOS</div>
        </div>
        
                <!-- MÓDULOS DEL SISTEMA -->
        <div class="nav-section">
            <div class="nav-section-title">MÓDULOS</div>
            <a href="dashboard.php" class="nav-link">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
            <a href="produccion.php" class="nav-link">
                <i class="fas fa-industry"></i> Producción
                <span class="nav-badge">15</span>
            </a>
            <a href="nuevo-lote.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Lote
            </a>
            <a href="inventario.php" class="nav-link">
                <i class="fas fa-boxes"></i> Gestión de Inventario
                <span class="nav-badge">8</span>
            </a>
            <a href="nuevo-producto.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Producto
            </a>
            <a href="registro-produccion.html" class="nav-link">
                <i class="fas fa-clipboard-list"></i> Registro de Producción
                <span class="nav-badge">3</span>
            </a>
            <a href="pedidos.php" class="nav-link">
                <i class="fas fa-shopping-cart"></i> Gestión de Pedidos
                <span class="nav-badge">47</span>
            </a>
            <a href="nuevo-pedido.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Pedido
            </a>
            <a href="ventas-punto.php" class="nav-link">
                <i class="fas fa-store"></i> Ventas en Punto
                <span class="nav-badge">12</span>
            </a>
            <a href="optimizacion-logistica.php" class="nav-link">
                <i class="fas fa-route"></i> Optimización Logística
                <span class="nav-badge">5</span>
            </a>
            <a href="nueva-ruta.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nueva Ruta
            </a>
            <a href="control-retornos.php" class="nav-link">
                <i class="fas fa-undo-alt"></i> Control de Retornos
                <span class="nav-badge">7</span>
            </a>
            <a href="registrar-retorno.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Registrar Retorno
            </a>
            <a href="experiencia-cliente.php" class="nav-link">
                <i class="fas fa-smile"></i> Experiencia del Cliente
            </a>
            <a href="enviar-encuesta.html" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-envelope"></i> Enviar Encuesta
            </a>
            <a href="analitica-reportes.php" class="nav-link">
                <i class="fas fa-chart-bar"></i> Analítica y Reportes
            </a>
            <a href="nuevo-reporte.html" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Reporte
            </a>
            <a href="gestion-clientes.html" class="nav-link">
                <i class="fas fa-users"></i> Gestión de Clientes
                <span class="nav-badge">234</span>
            </a>
            <a href="nuevo-cliente.html" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Cliente
            </a>
            <a href="administracion-financiera.html" class="nav-link">
                <i class="fas fa-dollar-sign"></i> Administración Financiera
            </a>
            <a href="nueva-transaccion.html" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nueva Transacción
            </a>
        </div>
        
        <!-- User Profile -->
        <div class="user-profile">
            <a href="#" class="nav-link">
                <i class="fas fa-user-circle"></i> Leslie Lugo
            </a>
            <a href="#" class="nav-link" id="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>
    </div>
    
    <!-- Main Content Area -->
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Nuevo Pedido</h1>
            <div>
                <button class="btn btn-primary me-2">
                    <i class="fas fa-save"></i> Guardar Pedido
                </button>
                <button class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </button>
            </div>
        </div>
        
        <!-- KPI Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Pedidos Pendientes</div>
                    <div class="kpi-value" style="color: var(--warning);">24</div>
                    <div class="kpi-trend up">
                        <i class="fas fa-arrow-up"></i> 8% vs semana anterior
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">En Preparación</div>
                    <div class="kpi-value" style="color: var(--human-rights);">15</div>
                    <div class="kpi-trend up">
                        <i class="fas fa-arrow-up"></i> 12% vs semana anterior
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">En Ruta</div>
                    <div class="kpi-value" style="color: var(--equity);">8</div>
                    <div class="kpi-trend down">
                        <i class="fas fa-arrow-down"></i> 5% vs semana anterior
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Entregados Hoy</div>
                    <div class="kpi-value" style="color: var(--environment);">32</div>
                    <div class="kpi-trend up">
                        <i class="fas fa-arrow-up"></i> 15% vs semana anterior
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros y Búsqueda -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" class="form-control search-box" placeholder="Buscar pedido...">
                            </div>
                            <div class="col-md-2">
                                <select class="form-select">
                                    <option>Todos los estados</option>
                                    <option>Pendiente</option>
                                    <option>Confirmado</option>
                                    <option>En preparación</option>
                                    <option>En ruta</option>
                                    <option>Entregado</option>
                                    <option>Cancelado</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select">
                                    <option>Todos los canales</option>
                                    <option>Web</option>
                                    <option>WhatsApp</option>
                                    <option>Teléfono</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary w-100">
                                    <i class="fas fa-filter"></i> Aplicar Filtros
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charts Row -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Pedidos por Estado</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="ordersStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Pedidos por Canal</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="ordersChannelChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Lista de Pedidos -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Pedidos Recientes</div>
                        <div>
                            <button class="btn btn-sm btn-success me-2">
                                <i class="fas fa-file-export"></i> Exportar
                            </button>
                            <button class="btn btn-sm btn-primary">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="user-table">
                            <thead>
                                <tr>
                                    <th>Pedido ID</th>
                                    <th>Cliente</th>
                                    <th>Productos</th>
                                    <th>Canal</th>
                                    <th>Fecha Entrega</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#QL-23101</td>
                                    <td>Restaurante La Parrilla</td>
                                    <td>
                                        <span class="product-icon product-cheese"><i class="fas fa-cheese"></i></span> Gouda (15kg)<br>
                                        <span class="product-icon product-butter"><i class="fas fa-cube"></i></span> Mantequilla (5kg)
                                    </td>
                                    <td><span class="channel-badge channel-web">Web</span></td>
                                    <td>15/11/2023</td>
                                    <td>$1,250.00</td>
                                    <td><span class="badge-status status-delivered">Entregado</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info me-1" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" title="Reimprimir">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#QL-23102</td>
                                    <td>Supermercado Central</td>
                                    <td>
                                        <span class="product-icon product-cheese"><i class="fas fa-cheese"></i></span> Manchego (25kg)<br>
                                        <span class="product-icon product-yogurt"><i class="fas fa-wine-bottle"></i></span> Yogurt (10kg)
                                    </td>
                                    <td><span class="channel-badge channel-whatsapp">WhatsApp</span></td>
                                    <td>15/11/2023</td>
                                    <td>$2,150.00</td>
                                    <td><span class="badge-status status-shipped">En Ruta</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info me-1" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success" title="Marcar entregado">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#QL-23103</td>
                                    <td>Cafetería El Aroma</td>
                                    <td>
                                        <span class="product-icon product-yogurt"><i class="fas fa-wine-bottle"></i></span> Yogurt (8kg)<br>
                                        <span class="product-icon product-cream"><i class="fas fa-wine-bottle"></i></span> Crema (4kg)
                                    </td>
                                    <td><span class="channel-badge channel-phone">Teléfono</span></td>
                                    <td>16/11/2023</td>
                                    <td>$680.00</td>
                                    <td><span class="badge-status status-preparing">En Preparación</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info me-1" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-primary" title="Asignar ruta">
                                            <i class="fas fa-truck"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#QL-23104</td>
                                    <td>Tienda Gourmet</td>
                                    <td>
                                        <span class="product-icon product-cheese"><i class="fas fa-cheese"></i></span> Gouda (5kg)<br>
                                        <span class="product-icon product-cream"><i class="fas fa-wine-bottle"></i></span> Crema (3kg)
                                    </td>
                                    <td><span class="channel-badge channel-web">Web</span></td>
                                    <td>16/11/2023</td>
                                    <td>$450.00</td>
                                    <td><span class="badge-status status-confirmed">Confirmado</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info me-1" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" title="Preparar pedido">
                                            <i class="fas fa-box"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#QL-23105</td>
                                    <td>Hotel Plaza</td>
                                    <td>
                                        <span class="product-icon product-cheese"><i class="fas fa-cheese"></i></span> Variedad (32kg)<br>
                                        <span class="product-icon product-butter"><i class="fas fa-cube"></i></span> Mantequilla (8kg)
                                    </td>
                                    <td><span class="channel-badge channel-whatsapp">WhatsApp</span></td>
                                    <td>17/11/2023</td>
                                    <td>$3,200.00</td>
                                    <td><span class="badge-status status-pending">Pendiente</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info me-1" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success" title="Confirmar pedido">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Detalles del Pedido y Seguimiento -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Detalles del Pedido #QL-23102</div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Cliente:</strong> Supermercado Central<br>
                            <strong>Dirección:</strong> Av. Principal #123, Centro<br>
                            <strong>Teléfono:</strong> +52 55 1234 5678<br>
                            <strong>Email:</strong> contacto@supercentral.com
                        </div>
                        <div class="mb-3">
                            <strong>Productos:</strong>
                            <ul class="mt-2">
                                <li>Queso Manchego - 25kg - $1,875.00</li>
                                <li>Yogurt Natural - 10kg - $275.00</li>
                            </ul>
                        </div>
                        <div class="mb-3">
                            <strong>Total:</strong> $2,150.00<br>
                            <strong>Método de Pago:</strong> Transferencia<br>
                            <strong>Vendedor:</strong> Ana Pérez
                        </div>
                        <button class="btn btn-primary w-100">
                            <i class="fas fa-edit"></i> Editar Pedido
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Seguimiento del Pedido</div>
                    </div>
                    <div class="card-body">
                        <div class="order-timeline">
                            <div class="timeline-step completed">
                                <strong>Pedido Recibido</strong>
                                <div class="text-muted small">15/11/2023 09:30 AM</div>
                                <div class="text-muted">Pedido creado vía WhatsApp</div>
                            </div>
                            <div class="timeline-step completed">
                                <strong>Confirmado</strong>
                                <div class="text-muted small">15/11/2023 10:15 AM</div>
                                <div class="text-muted">Pedido confirmado por Ana Pérez</div>
                            </div>
                            <div class="timeline-step completed">
                                <strong>En Preparación</strong>
                                <div class="text-muted small">15/11/2023 11:00 AM</div>
                                <div class="text-muted">Productos asignados del inventario</div>
                            </div>
                            <div class="timeline-step active">
                                <strong>En Ruta</strong>
                                <div class="text-muted small">15/11/2023 02:30 PM</div>
                                <div class="text-muted">Asignado a Luis Gómez - Ruta Norte</div>
                            </div>
                            <div class="timeline-step">
                                <strong>Entregado</strong>
                                <div class="text-muted small">Pendiente</div>
                                <div class="text-muted">Esperando confirmación de entrega</div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-success w-100">
                                <i class="fas fa-qrcode"></i> Generar Código QR
                            </button>
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
        // Orders Status Chart
        const ordersStatusCtx = document.getElementById('ordersStatusChart').getContext('2d');
        const ordersStatusChart = new Chart(ordersStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pendiente', 'Confirmado', 'En Preparación', 'En Ruta', 'Entregado', 'Cancelado'],
                datasets: [{
                    data: [24, 15, 12, 8, 32, 3],
                    backgroundColor: [
                        '#ffc107',
                        '#007bff',
                        '#6f42c1',
                        '#20c997',
                        '#28a745',
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
                                return context.label + ': ' + context.raw + ' pedidos';
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });

        // Orders Channel Chart
        const ordersChannelCtx = document.getElementById('ordersChannelChart').getContext('2d');
        const ordersChannelChart = new Chart(ordersChannelCtx, {
            type: 'bar',
            data: {
                labels: ['Web', 'WhatsApp', 'Teléfono'],
                datasets: [{
                    label: 'Pedidos por Canal',
                    data: [35, 45, 20],
                    backgroundColor: [
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(37, 211, 102, 0.7)',
                        'rgba(108, 117, 125, 0.7)'
                    ],
                    borderColor: [
                        'rgba(52, 152, 219, 1)',
                        'rgba(37, 211, 102, 1)',
                        'rgba(108, 117, 125, 1)'
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
                                return value + '%';
                            }
                        }
                    }
                }
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
