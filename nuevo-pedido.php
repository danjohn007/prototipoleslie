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
                <a href="pedidos.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a Pedidos
                </a>
            </div>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <ul class="mb-0">
                <?php foreach ($errors as $err): ?>
                    <li><?php echo htmlspecialchars($err); ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <!-- Order Form -->
        <form method="POST" action="" id="orderForm">
        <div class="row">
            <!-- Order Information Card -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-info-circle me-2"></i> Información del Pedido
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="numero_pedido" class="form-label">Número de Pedido</label>
                                <input type="text" class="form-control" id="numero_pedido" name="numero_pedido" 
                                       value="<?php echo htmlspecialchars($numero_pedido); ?>" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fecha_entrega" class="form-label">Fecha de Entrega</label>
                                <input type="date" class="form-control" id="fecha_entrega" name="fecha_entrega" 
                                       min="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="cliente_id" class="form-label">Cliente</label>
                            <select class="form-select" id="cliente_id" name="cliente_id" required>
                                <option value="">Seleccionar cliente...</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?php echo $client['id']; ?>">
                                        <?php echo htmlspecialchars($client['nombre']); ?>
                                        <?php if ($client['tipo_cliente']): ?>
                                            (<?php echo ucfirst($client['tipo_cliente']); ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">
                                <a href="nuevo-cliente.php" target="_blank">¿No está el cliente? Crear nuevo cliente</a>
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3" 
                                      placeholder="Instrucciones especiales, notas adicionales..."></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Products Section -->
                <div class="card mt-3">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-box me-2"></i> Productos del Pedido
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="productosContainer">
                            <div class="producto-row mb-3 border p-3 rounded">
                                <div class="row align-items-end">
                                    <div class="col-md-5">
                                        <label class="form-label">Producto</label>
                                        <select class="form-select producto-select" name="productos[0][producto_id]" required>
                                            <option value="">Seleccionar producto...</option>
                                            <?php foreach ($products as $product): ?>
                                                <option value="<?php echo $product['id']; ?>" 
                                                        data-precio="<?php echo $product['precio_unitario']; ?>">
                                                    <?php echo htmlspecialchars($product['nombre']); ?> 
                                                    - $<?php echo number_format($product['precio_unitario'], 2); ?>
                                                    (Stock: <?php echo $product['stock_actual']; ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Cantidad</label>
                                        <input type="number" class="form-control producto-cantidad" 
                                               name="productos[0][cantidad]" min="1" value="1" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Subtotal</label>
                                        <input type="text" class="form-control producto-subtotal" readonly value="$0.00">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm remove-producto-btn" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success" id="addProductoBtn">
                            <i class="fas fa-plus"></i> Agregar Producto
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <i class="fas fa-calculator me-2"></i> Resumen del Pedido
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong id="subtotalDisplay">$0.00</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Descuento:</span>
                            <div class="input-group input-group-sm" style="width: 120px;">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="descuento" name="descuento" 
                                       value="0" min="0" step="0.01">
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong id="totalDisplay" style="font-size: 24px; color: var(--primary);">$0.00</strong>
                        </div>
                        
                        <input type="hidden" name="subtotal" id="subtotalInput">
                        <input type="hidden" name="total" id="totalInput">
                        
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-2"></i> Guardar Pedido
                        </button>
                        <a href="pedidos.php" class="btn btn-secondary w-100">
                            <i class="fas fa-times me-2"></i> Cancelar
                        </a>
                    </div>
                </div>
                
                <!-- Info Card -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h6><i class="fas fa-info-circle me-2 text-info"></i> Información</h6>
                        <ul class="small mb-0">
                            <li>El número de pedido se genera automáticamente</li>
                            <li>La fecha de entrega debe ser futura</li>
                            <li>Todos los campos marcados son obligatorios</li>
                            <li>El descuento es opcional</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let productoCounter = 1;
        
        // Calculate totals
        function calculateTotals() {
            let subtotal = 0;
            document.querySelectorAll('.producto-row').forEach(function(row) {
                const selectElement = row.querySelector('.producto-select');
                const cantidadElement = row.querySelector('.producto-cantidad');
                const subtotalElement = row.querySelector('.producto-subtotal');
                
                if (selectElement && cantidadElement) {
                    const selectedOption = selectElement.options[selectElement.selectedIndex];
                    const precio = parseFloat(selectedOption.getAttribute('data-precio')) || 0;
                    const cantidad = parseInt(cantidadElement.value) || 0;
                    const productoSubtotal = precio * cantidad;
                    
                    subtotalElement.value = '$' + productoSubtotal.toFixed(2);
                    subtotal += productoSubtotal;
                }
            });
            
            const descuento = parseFloat(document.getElementById('descuento').value) || 0;
            const total = subtotal - descuento;
            
            document.getElementById('subtotalDisplay').textContent = '$' + subtotal.toFixed(2);
            document.getElementById('totalDisplay').textContent = '$' + total.toFixed(2);
            document.getElementById('subtotalInput').value = subtotal.toFixed(2);
            document.getElementById('totalInput').value = total.toFixed(2);
        }
        
        // Add product row
        document.getElementById('addProductoBtn').addEventListener('click', function() {
            const container = document.getElementById('productosContainer');
            const newRow = document.querySelector('.producto-row').cloneNode(true);
            
            // Update names and clear values
            newRow.querySelectorAll('select, input').forEach(function(element) {
                if (element.name) {
                    element.name = element.name.replace(/\[\d+\]/, '[' + productoCounter + ']');
                }
                if (element.classList.contains('producto-select')) {
                    element.selectedIndex = 0;
                } else if (element.classList.contains('producto-cantidad')) {
                    element.value = 1;
                } else if (element.classList.contains('producto-subtotal')) {
                    element.value = '$0.00';
                }
            });
            
            // Enable remove button
            newRow.querySelector('.remove-producto-btn').disabled = false;
            
            container.appendChild(newRow);
            productoCounter++;
            
            // Add event listeners to new row
            addProductoEventListeners(newRow);
            updateRemoveButtons();
        });
        
        // Remove product row
        function removeProductoRow(button) {
            const row = button.closest('.producto-row');
            row.remove();
            calculateTotals();
            updateRemoveButtons();
        }
        
        // Update remove buttons state
        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.producto-row');
            rows.forEach(function(row, index) {
                const removeBtn = row.querySelector('.remove-producto-btn');
                removeBtn.disabled = rows.length === 1;
            });
        }
        
        // Add event listeners to product row
        function addProductoEventListeners(row) {
            row.querySelector('.producto-select').addEventListener('change', calculateTotals);
            row.querySelector('.producto-cantidad').addEventListener('input', calculateTotals);
            row.querySelector('.remove-producto-btn').addEventListener('click', function() {
                removeProductoRow(this);
            });
        }
        
        // Initialize event listeners
        document.querySelectorAll('.producto-row').forEach(addProductoEventListeners);
        document.getElementById('descuento').addEventListener('input', calculateTotals);
        
        // Form validation
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            const productos = document.querySelectorAll('.producto-select');
            let hasProducts = false;
            
            productos.forEach(function(select) {
                if (select.value) {
                    hasProducts = true;
                }
            });
            
            if (!hasProducts) {
                e.preventDefault();
                alert('Debe agregar al menos un producto al pedido');
                return false;
            }
            
            const total = parseFloat(document.getElementById('totalInput').value);
            if (total < 0) {
                e.preventDefault();
                alert('El total del pedido no puede ser negativo');
                return false;
            }
        });
        
        // Hamburger menu for mobile
        const hamburgerBtn = document.querySelector('.hamburger-btn');
        if (hamburgerBtn) {
            hamburgerBtn.addEventListener('click', function() {
                document.querySelector('.sidebar').classList.toggle('active');
                document.querySelector('.sidebar-overlay').classList.toggle('active');
            });
        }
        
        const overlay = document.querySelector('.sidebar-overlay');
        if (overlay) {
            overlay.addEventListener('click', function() {
                document.querySelector('.sidebar').classList.remove('active');
                this.classList.remove('active');
            });
        }
        
        document.querySelectorAll('.sidebar .nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 991) {
                    document.querySelector('.sidebar').classList.remove('active');
                    document.querySelector('.sidebar-overlay').classList.remove('active');
                }
            });
        });
        
        // Logout button
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                if (!confirm('¿Está seguro que desea cerrar sesión?')) {
                    e.preventDefault();
                }
            });
        }
    </script>
</body>
</html>
