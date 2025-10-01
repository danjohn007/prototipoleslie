<?php
/**
 * Módulo de Nuevo Lote
 * Formulario para crear un nuevo lote de producción
 */

// Cargar configuración
require_once __DIR__ . '/app/config/config.php';

// Verificar autenticación
$authController = new AuthController();
$authController->checkAuth();

// Obtener usuario actual
$userModel = new User();
$currentUser = $userModel->getCurrentUser();

// Obtener productos para el formulario
$productModel = new Product();
$products = $productModel->getAll();

// Generar número de lote
$numero_lote = generate_batch_number('LOTE');

// Procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productionController = new ProductionController();
    $productionController->createBatch();
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Lote - Quesos Leslie</title>
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
        
        .status-completed {
            background-color: rgba(40, 167, 69, 0.2);
            color: #155724;
        }
        
        .status-in-progress {
            background-color: rgba(0, 123, 255, 0.2);
            color: #004085;
        }
        
        .status-pending {
            background-color: rgba(255, 193, 7, 0.2);
            color: #856404;
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
        
        .production-type {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .type-card {
            border: 2px solid var(--medium-gray);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .type-card:hover {
            border-color: var(--primary);
        }
        
        .type-card.selected {
            border-color: var(--environment);
            background-color: rgba(39, 174, 96, 0.05);
        }
        
        .type-icon {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--primary);
        }
        
        .production-form {
            background-color: var(--light-gray);
            border-radius: 8px;
            padding: 20px;
        }
        
        .form-section {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--medium-gray);
        }
        
        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .batch-info {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid var(--environment);
        }
        
        .ingredient-item {
            display: flex;
            justify-content: between;
            align-items: center;
            padding: 10px;
            border: 1px solid var(--medium-gray);
            border-radius: 6px;
            margin-bottom: 10px;
        }
        
        .step-indicator {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .step {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            background-color: white;
            border: 1px solid var(--medium-gray);
            border-radius: 20px;
            margin-right: 10px;
        }
        
        .step.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .step.completed {
            background-color: var(--environment);
            color: white;
            border-color: var(--environment);
        }
        
        .step-number {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: var(--medium-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .step.active .step-number {
            background-color: white;
            color: var(--primary);
        }
        
        .step.completed .step-number {
            background-color: white;
            color: var(--environment);
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
            <a href="produccion.php" class="nav-link">
                <i class="fas fa-industry"></i> Producción
                <span class="nav-badge">15</span>
            </a>
            <a href="nuevo-lote.php" class="nav-link active" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Lote
            </a>
            <a href="inventario.php" class="nav-link">
                <i class="fas fa-boxes"></i> Gestión de Inventario
                <span class="nav-badge">8</span>
            </a>
            <a href="nuevo-producto.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Producto
            </a>
            <a href="<?php echo BASE_URL; ?>/registro-produccion.php" class="nav-link">
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
            <a href="<?php echo BASE_URL; ?>/enviar-encuesta.php" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-envelope"></i> Enviar Encuesta
            </a>
            <a href="analitica-reportes.php" class="nav-link">
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
            <a href="<?php echo BASE_URL; ?>/mi-perfil.php" class="nav-link">
                <i class="fas fa-user-cog"></i> Mi Perfil
            </a>
            <a href="index.php?action=logout" class="nav-link" id="logout-btn" onclick="return confirm('¿Está seguro que desea cerrar sesión?')">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>
    </div>
    
    <!-- Main Content Area -->
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Nuevo Lote de Producción</h1>
            <div>
                <a href="produccion.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        
        <?php if (isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <ul class="mb-0">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
            <?php unset($_SESSION['errors']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
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
        
        <!-- KPI Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Lotes Hoy</div>
                    <div class="kpi-value" style="color: var(--environment);">8</div>
                    <div class="kpi-trend up">
                        <i class="fas fa-arrow-up"></i> 2 vs ayer
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Producción Total</div>
                    <div class="kpi-value" style="color: var(--human-rights);">245 kg</div>
                    <div class="kpi-trend up">
                        <i class="fas fa-arrow-up"></i> 15% vs ayer
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Eficiencia</div>
                    <div class="kpi-value" style="color: var(--equity);">94%</div>
                    <div class="kpi-trend up">
                        <i class="fas fa-arrow-up"></i> 3% vs ayer
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Tiempo Promedio</div>
                    <div class="kpi-value" style="color: var(--education);">3.2h</div>
                    <div class="kpi-trend down">
                        <i class="fas fa-arrow-down"></i> 0.4h vs ayer
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicador de Pasos -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="step-indicator">
                    <div class="step completed">
                        <div class="step-number">1</div>
                        <span>Tipo de Producción</span>
                    </div>
                    <div class="step active">
                        <div class="step-number">2</div>
                        <span>Datos del Lote</span>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <span>Ingredientes</span>
                    </div>
                    <div class="step">
                        <div class="step-number">4</div>
                        <span>Control de Calidad</span>
                    </div>
                    <div class="step">
                        <div class="step-number">5</div>
                        <span>Confirmación</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Formulario de Registro -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Nuevo Registro de Producción</div>
                        <span class="text-muted">Lote <?php echo $numero_lote; ?></span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="nuevo-lote.php" id="batch-form">
                        <!-- Campo oculto para tipo de producción -->
                        <input type="hidden" name="tipo_produccion" id="tipo_produccion" value="granel">
                        
                        <!-- Tipo de Producción -->
                        <div class="form-section">
                            <h6 class="mb-3">Tipo de Producción</h6>
                            <div class="production-type">
                                <div class="type-card selected" data-type="granel">
                                    <div class="type-icon">
                                        <i class="fas fa-weight"></i>
                                    </div>
                                    <h6>A Granel</h6>
                                    <small class="text-muted">Por peso total</small>
                                </div>
                                <div class="type-card" data-type="pieza">
                                    <div class="type-icon">
                                        <i class="fas fa-cube"></i>
                                    </div>
                                    <h6>Por Pieza</h6>
                                    <small class="text-muted">Unidades individuales</small>
                                </div>
                                <div class="type-card" data-type="paquete">
                                    <div class="type-icon">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <h6>Por Paquete</h6>
                                    <small class="text-muted">Cajas/empaques</small>
                                </div>
                            </div>
                        </div>

                        <!-- Datos del Producto -->
                        <div class="form-section">
                            <h6 class="mb-3">Datos del Producto</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Producto *</label>
                                    <select class="form-select" name="producto_id" id="producto_id" required>
                                        <option value="">Seleccionar producto...</option>
                                        <?php foreach ($products as $product): ?>
                                        <option value="<?php echo $product['id']; ?>" 
                                                data-nombre="<?php echo htmlspecialchars($product['nombre']); ?>"
                                                data-precio="<?php echo $product['precio_unitario']; ?>"
                                                data-categoria="<?php echo $product['categoria']; ?>">
                                            <?php echo htmlspecialchars($product['nombre']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Cantidad *</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="cantidad_producida" id="cantidad_producida" 
                                               required min="1" step="0.01" placeholder="0">
                                        <span class="input-group-text" id="unidad_medida">kg</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fecha Producción *</label>
                                    <input type="date" class="form-control" name="fecha_produccion" id="fecha_produccion" 
                                           value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fecha Vencimiento</label>
                                    <input type="date" class="form-control" name="fecha_vencimiento" id="fecha_vencimiento">
                                </div>
                            </div>
                        </div>

                        <!-- Ingredientes -->
                        <div class="form-section">
                            <h6 class="mb-3">Ingredientes Utilizados</h6>
                            <div class="ingredient-item">
                                <div class="flex-grow-1">
                                    <strong>Leche Entera</strong>
                                    <div class="text-muted small">Lote #LT-231114</div>
                                </div>
                                <div class="text-end">
                                    <strong>500 L</strong>
                                    <div class="text-muted small">$1,250.00</div>
                                </div>
                            </div>
                            <div class="ingredient-item">
                                <div class="flex-grow-1">
                                    <strong>Cuajo</strong>
                                    <div class="text-muted small">Lote #CJ-231110</div>
                                </div>
                                <div class="text-end">
                                    <strong>2 L</strong>
                                    <div class="text-muted small">$180.00</div>
                                </div>
                            </div>
                            <div class="ingredient-item">
                                <div class="flex-grow-1">
                                    <strong>Sal</strong>
                                    <div class="text-muted small">Lote #SL-231108</div>
                                </div>
                                <div class="text-end">
                                    <strong>5 kg</strong>
                                    <div class="text-muted small">$75.00</div>
                                </div>
                            </div>
                            <button class="btn btn-outline-primary btn-sm mt-2">
                                <i class="fas fa-plus"></i> Agregar Ingrediente
                            </button>
                        </div>

                        <!-- Observaciones -->
                        <div class="form-section">
                            <h6 class="mb-3">Estado y Observaciones</h6>
                            <div class="mb-3">
                                <label class="form-label">Estado del Lote</label>
                                <select class="form-select" name="estado">
                                    <option value="en_proceso">En Proceso</option>
                                    <option value="completado">Completado</option>
                                    <option value="inspeccion">En Inspección</option>
                                    <option value="aprobado">Aprobado</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Observaciones</label>
                                <textarea class="form-control" name="observaciones" rows="3" placeholder="Notas sobre el proceso de producción..."></textarea>
                            </div>
                        </div>
                        
                        <!-- Botones de acción -->
                        <div class="form-section text-end">
                            <a href="produccion.php" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Lote
                            </button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Información del Lote -->
                <div class="card batch-info">
                    <div class="card-header">
                        <div class="card-title">Resumen del Lote</div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3" id="batch-summary">
                            <strong>Lote ID:</strong> <span id="display-lote-id"><?php echo $numero_lote; ?></span><br>
                            <strong>Producto:</strong> <span id="display-producto">No seleccionado</span><br>
                            <strong>Cantidad:</strong> <span id="display-cantidad">0</span> <span id="display-unidad">kg</span><br>
                            <strong>Fecha Prod:</strong> <span id="display-fecha-prod"><?php echo date('d/m/Y'); ?></span><br>
                            <strong>Fecha Cad:</strong> <span id="display-fecha-cad">No especificada</span>
                        </div>
                        
                        <div class="mb-3" id="cost-breakdown">
                            <strong>Costos Estimados</strong>
                            <div class="d-flex justify-content-between mt-2">
                                <span>Materias Primas:</span>
                                <span id="display-costo-materias">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Mano de Obra:</span>
                                <span id="display-costo-mano-obra">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Otros Costos:</span>
                                <span id="display-otros-costos">$0.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Costo Total:</strong>
                                <strong id="display-costo-total">$0.00</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <strong>Costo por <span id="display-unidad-costo">kg</span>:</strong>
                                <strong id="display-costo-unitario">$0.00</strong>
                            </div>
                        </div>

                        <!-- Responsables -->
                        <div class="mb-3">
                            <strong>Responsables</strong>
                            <div class="mt-2">
                                <div class="d-flex justify-content-between">
                                    <span>Supervisor:</span>
                                    <span><?php echo htmlspecialchars($currentUser['nombre']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Turno:</span>
                                    <span id="display-turno"><?php 
                                        $hora = date('H');
                                        if ($hora >= 6 && $hora < 14) echo 'Matutino';
                                        elseif ($hora >= 14 && $hora < 22) echo 'Vespertino';
                                        else echo 'Nocturno';
                                    ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Estado:</span>
                                    <span id="display-estado" class="badge-status status-pending">Borrador</span>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success" id="save-batch-btn" disabled>
                                <i class="fas fa-save"></i> Guardar Lote
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="printLabels()">
                                <i class="fas fa-print"></i> Vista Previa
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Producción en Proceso -->
                <div class="card mt-4">
                    <div class="card-header">
                        <div class="card-title">Producción en Proceso</div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <strong>Queso Manchego</strong>
                                <div class="text-muted small">Lote #PR-231114</div>
                            </div>
                            <span class="badge-status status-in-progress">En Proceso</span>
                        </div>
                        <div class="progress mb-4">
                            <div class="progress-bar" style="width: 75%">75%</div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Yogurt Natural</strong>
                                <div class="text-muted small">Lote #PR-231113</div>
                            </div>
                            <span class="badge-status status-in-progress">En Proceso</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 40%">40%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Lotes Recientes -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Lotes Registrados Hoy</div>
                        <button class="btn btn-sm btn-primary">
                            <i class="fas fa-redo"></i> Actualizar
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="user-table">
                            <thead>
                                <tr>
                                    <th>Lote ID</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Fecha Prod.</th>
                                    <th>Fecha Cad.</th>
                                    <th>Responsable</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#PR-231114</td>
                                    <td><span class="product-icon product-cheese"><i class="fas fa-cheese"></i></span> Queso Manchego</td>
                                    <td>45 kg</td>
                                    <td>15/11/2023</td>
                                    <td>15/02/2024</td>
                                    <td>Carlos Rodríguez</td>
                                    <td><span class="badge-status status-in-progress">En Proceso</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info me-1">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#PR-231113</td>
                                    <td><span class="product-icon product-yogurt"><i class="fas fa-wine-bottle"></i></span> Yogurt Natural</td>
                                    <td>60 L</td>
                                    <td>15/11/2023</td>
                                    <td>30/11/2023</td>
                                    <td>María González</td>
                                    <td><span class="badge-status status-in-progress">En Proceso</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info me-1">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#PR-231112</td>
                                    <td><span class="product-icon product-cream"><i class="fas fa-wine-bottle"></i></span> Crema Fresca</td>
                                    <td>35 kg</td>
                                    <td>14/11/2023</td>
                                    <td>28/11/2023</td>
                                    <td>Pedro Hernández</td>
                                    <td><span class="badge-status status-completed">Completado</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info me-1">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success">
                                            <i class="fas fa-box"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#PR-231111</td>
                                    <td><span class="product-icon product-butter"><i class="fas fa-cube"></i></span> Mantequilla</td>
                                    <td>25 kg</td>
                                    <td>14/11/2023</td>
                                    <td>14/12/2023</td>
                                    <td>Ana López</td>
                                    <td><span class="badge-status status-completed">Completado</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info me-1">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success">
                                            <i class="fas fa-box"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Métricas de Producción -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Producción por Producto (Hoy)</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="productionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Eficiencia por Turno</div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="efficiencyChart"></canvas>
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
                    label: 'Producción (kg)',
                    data: [50, 45, 60, 35, 25],
                    backgroundColor: [
                        'rgba(230, 126, 34, 0.7)',
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(155, 89, 182, 0.7)',
                        'rgba(243, 156, 18, 0.7)',
                        'rgba(39, 174, 96, 0.7)'
                    ],
                    borderColor: [
                        'rgba(230, 126, 34, 1)',
                        'rgba(52, 152, 219, 1)',
                        'rgba(155, 89, 182, 1)',
                        'rgba(243, 156, 18, 1)',
                        'rgba(39, 174, 96, 1)'
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

        // Efficiency Chart
        const efficiencyCtx = document.getElementById('efficiencyChart').getContext('2d');
        const efficiencyChart = new Chart(efficiencyCtx, {
            type: 'line',
            data: {
                labels: ['Matutino', 'Vespertino', 'Nocturno'],
                datasets: [{
                    label: 'Eficiencia (%)',
                    data: [92, 94, 89],
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    tension: 0.4,
                    fill: true
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
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });

        // Production type selection
        document.querySelectorAll('.type-card').forEach(card => {
            card.addEventListener('click', function() {
                document.querySelectorAll('.type-card').forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                
                // Actualizar tipo de producción
                const tipo = this.dataset.type;
                document.getElementById('tipo_produccion').value = tipo;
                
                // Actualizar unidad de medida según el tipo
                const unidadSpan = document.getElementById('unidad_medida');
                const displayUnidad = document.getElementById('display-unidad');
                const displayUnidadCosto = document.getElementById('display-unidad-costo');
                
                let unidad = 'kg';
                if (tipo === 'pieza') unidad = 'unid';
                else if (tipo === 'paquete') unidad = 'cajas';
                
                unidadSpan.textContent = unidad;
                displayUnidad.textContent = unidad;
                displayUnidadCosto.textContent = unidad;
                
                updateBatchSummary();
            });
        });

        // Actualizar resumen cuando cambian los campos
        function updateBatchSummary() {
            const productoSelect = document.getElementById('producto_id');
            const cantidadInput = document.getElementById('cantidad_producida');
            const fechaProdInput = document.getElementById('fecha_produccion');
            const fechaCadInput = document.getElementById('fecha_vencimiento');
            
            // Actualizar producto
            const productoOption = productoSelect.options[productoSelect.selectedIndex];
            const nombreProducto = productoOption.dataset.nombre || 'No seleccionado';
            const precioUnitario = parseFloat(productoOption.dataset.precio) || 0;
            
            document.getElementById('display-producto').textContent = nombreProducto;
            
            // Actualizar cantidad
            const cantidad = parseFloat(cantidadInput.value) || 0;
            document.getElementById('display-cantidad').textContent = cantidad;
            
            // Actualizar fechas
            if (fechaProdInput.value) {
                const fechaProd = new Date(fechaProdInput.value);
                document.getElementById('display-fecha-prod').textContent = fechaProd.toLocaleDateString('es-ES');
            }
            
            if (fechaCadInput.value) {
                const fechaCad = new Date(fechaCadInput.value);
                document.getElementById('display-fecha-cad').textContent = fechaCad.toLocaleDateString('es-ES');
            } else {
                document.getElementById('display-fecha-cad').textContent = 'No especificada';
            }
            
            // Calcular costos estimados
            if (cantidad > 0 && precioUnitario > 0) {
                const costoMateriasPrimas = cantidad * precioUnitario * 0.6; // 60% del precio son materias primas
                const costoManoObra = cantidad * 8; // $8 por unidad de mano de obra
                const otrosCostos = cantidad * 2; // $2 por unidad otros costos
                const costoTotal = costoMateriasPrimas + costoManoObra + otrosCostos;
                const costoUnitario = costoTotal / cantidad;
                
                document.getElementById('display-costo-materias').textContent = '$' + costoMateriasPrimas.toFixed(2);
                document.getElementById('display-costo-mano-obra').textContent = '$' + costoManoObra.toFixed(2);
                document.getElementById('display-otros-costos').textContent = '$' + otrosCostos.toFixed(2);
                document.getElementById('display-costo-total').textContent = '$' + costoTotal.toFixed(2);
                document.getElementById('display-costo-unitario').textContent = '$' + costoUnitario.toFixed(2);
            } else {
                // Resetear costos
                document.getElementById('display-costo-materias').textContent = '$0.00';
                document.getElementById('display-costo-mano-obra').textContent = '$0.00';
                document.getElementById('display-otros-costos').textContent = '$0.00';
                document.getElementById('display-costo-total').textContent = '$0.00';
                document.getElementById('display-costo-unitario').textContent = '$0.00';
            }
            
            // Habilitar/deshabilitar botón de guardar
            const saveBtn = document.getElementById('save-batch-btn');
            const isValid = productoSelect.value && cantidad > 0 && fechaProdInput.value;
            saveBtn.disabled = !isValid;
            
            if (isValid) {
                document.getElementById('display-estado').textContent = 'Listo para guardar';
                document.getElementById('display-estado').className = 'badge-status status-in-progress';
            } else {
                document.getElementById('display-estado').textContent = 'Borrador';
                document.getElementById('display-estado').className = 'badge-status status-pending';
            }
        }
        
        // Event listeners para actualizar el resumen
        document.getElementById('producto_id').addEventListener('change', updateBatchSummary);
        document.getElementById('cantidad_producida').addEventListener('input', updateBatchSummary);
        document.getElementById('fecha_produccion').addEventListener('change', updateBatchSummary);
        document.getElementById('fecha_vencimiento').addEventListener('change', updateBatchSummary);
        
        // Función para imprimir etiquetas (vista previa)
        function printLabels() {
            const productoSelect = document.getElementById('producto_id');
            const cantidad = document.getElementById('cantidad_producida').value;
            const loteId = document.getElementById('display-lote-id').textContent;
            
            if (!productoSelect.value || !cantidad) {
                alert('Por favor complete los datos del producto y cantidad antes de generar la vista previa.');
                return;
            }
            
            // Abrir ventana con vista previa de etiquetas
            const ventana = window.open('', '_blank', 'width=800,height=600');
            ventana.document.write(`
                <html>
                <head>
                    <title>Vista Previa - Etiquetas de Lote</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        .etiqueta { border: 2px solid #333; padding: 15px; margin: 10px; width: 300px; display: inline-block; }
                        .lote-id { font-size: 18px; font-weight: bold; }
                        .producto { font-size: 16px; margin: 5px 0; }
                        .fecha { font-size: 12px; color: #666; }
                    </style>
                </head>
                <body>
                    <h2>Vista Previa - Etiquetas de Lote</h2>
                    <div class="etiqueta">
                        <div class="lote-id">LOTE: ${loteId}</div>
                        <div class="producto">${document.getElementById('display-producto').textContent}</div>
                        <div>Cantidad: ${cantidad} ${document.getElementById('display-unidad').textContent}</div>
                        <div class="fecha">Producción: ${document.getElementById('display-fecha-prod').textContent}</div>
                        <div class="fecha">Vencimiento: ${document.getElementById('display-fecha-cad').textContent}</div>
                    </div>
                    <p><button onclick="window.print()">Imprimir</button> <button onclick="window.close()">Cerrar</button></p>
                </body>
                </html>
            `);
        }
        
        // Inicializar resumen
        updateBatchSummary();

        // Step navigation simulation
        document.querySelectorAll('.step').forEach((step, index) => {
            step.addEventListener('click', function() {
                // In a real application, this would navigate to the corresponding step
                document.querySelectorAll('.step').forEach(s => {
                    s.classList.remove('active', 'completed');
                });
                
                // Mark previous steps as completed and current as active
                for (let i = 0; i <= index; i++) {
                    if (i < index) {
                        document.querySelectorAll('.step')[i].classList.add('completed');
                    } else {
                        document.querySelectorAll('.step')[i].classList.add('active');
                    }
                }
            });
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
