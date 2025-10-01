<?php
/**
 * Registrar Retorno
 * Formulario para registrar un retorno de producto
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

// Procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $returnController = new ReturnController();
    $returnController->createReturn();
    exit;
}

// Obtener datos para el formulario
$clientModel = new Client();
$clients = $clientModel->getAll();

$productModel = new Product();
$products = $productModel->getAll();

$orderModel = new Order();
$orders = $orderModel->getAll();

// Generar número de retorno
$numero_retorno = generate_return_number('RET');

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
    <title>Registrar Retorno - <?php echo APP_NAME; ?></title>
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
        
        .return-card {
            padding: 15px;
            border-left: 4px solid var(--warning);
            background-color: rgba(255, 193, 7, 0.05);
            margin-bottom: 15px;
        }
        
        .return-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .return-status.pendiente {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning);
        }
        
        .return-status.procesando {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--human-rights);
        }
        
        .return-status.completado {
            background-color: rgba(39, 174, 96, 0.1);
            color: var(--success);
        }
        
        .return-status.rechazado {
            background-color: rgba(220, 53, 69, 0.1);
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
            letter-spacing: 0.5px;
        }
        
        .user-table td {
            padding: 12px 10px;
            border-bottom: 1px solid var(--medium-gray);
        }
        
        .user-table tr:hover {
            background-color: var(--light-gray);
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
            <div class="brand-subtitle">RETORNOS</div>
        </div>
        
                <!-- MÓDULOS DEL SISTEMA -->
        <div class="nav-section">
            <div class="nav-section-title">MÓDULOS</div>
            <a href="dashboard.html" class="nav-link">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
            <a href="produccion.html" class="nav-link">
                <i class="fas fa-industry"></i> Producción
                <span class="nav-badge">15</span>
            </a>
            <a href="nuevo-lote.html" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Lote
            </a>
            <a href="inventario.html" class="nav-link">
                <i class="fas fa-boxes"></i> Gestión de Inventario
                <span class="nav-badge">8</span>
            </a>
            <a href="nuevo-producto.html" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Producto
            </a>
            <a href="registro-produccion.html" class="nav-link">
                <i class="fas fa-clipboard-list"></i> Registro de Producción
                <span class="nav-badge">3</span>
            </a>
            <a href="pedidos.html" class="nav-link">
                <i class="fas fa-shopping-cart"></i> Gestión de Pedidos
                <span class="nav-badge">47</span>
            </a>
            <a href="nuevo-pedido.html" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nuevo Pedido
            </a>
            <a href="ventas-punto.html" class="nav-link">
                <i class="fas fa-store"></i> Ventas en Punto
                <span class="nav-badge">12</span>
            </a>
            <a href="optimizacion-logistica.html" class="nav-link">
                <i class="fas fa-route"></i> Optimización Logística
                <span class="nav-badge">5</span>
            </a>
            <a href="nueva-ruta.html" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Nueva Ruta
            </a>
            <a href="control-retornos.html" class="nav-link">
                <i class="fas fa-undo-alt"></i> Control de Retornos
                <span class="nav-badge">7</span>
            </a>
            <a href="registrar-retorno.html" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-plus-circle"></i> Registrar Retorno
            </a>
            <a href="experiencia-cliente.html" class="nav-link">
                <i class="fas fa-smile"></i> Experiencia del Cliente
            </a>
            <a href="enviar-encuesta.html" class="nav-link" style="padding-left: 40px;">
                <i class="fas fa-envelope"></i> Enviar Encuesta
            </a>
            <a href="analitica-reportes.html" class="nav-link">
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
            <h1 class="page-title">Registrar Retorno</h1>
            <div>
                <button class="btn btn-primary me-2">
                    <i class="fas fa-save"></i> Guardar Retorno
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
                    <div class="kpi-label">Retornos Pendientes</div>
                    <div class="kpi-value" style="color: var(--warning);">7</div>
                    <div class="kpi-trend down">
                        <i class="fas fa-arrow-down"></i> 3 menos vs ayer
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Retornos Este Mes</div>
                    <div class="kpi-value" style="color: var(--secondary);">23</div>
                    <div class="kpi-trend up">
                        <i class="fas fa-arrow-up"></i> 5 más vs mes pasado
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Tiempo Promedio Resolución</div>
                    <div class="kpi-value" style="color: var(--human-rights);">2.5 días</div>
                    <div class="kpi-trend down">
                        <i class="fas fa-arrow-down"></i> 0.5 días menos
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Satisfacción Post-Retorno</div>
                    <div class="kpi-value" style="color: var(--success);">4.6/5</div>
                    <div class="kpi-trend up">
                        <i class="fas fa-arrow-up"></i> 0.3 puntos más
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Retornos Recientes -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Retornos Recientes</div>
                        <div>
                            <select class="form-select form-select-sm" style="width: 150px;">
                                <option>Todos</option>
                                <option>Pendientes</option>
                                <option>Procesando</option>
                                <option>Completados</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="return-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">Retorno #R-2024-045</h6>
                                    <small class="text-muted">Cliente: Supermercado La Esperanza</small>
                                </div>
                                <span class="return-status pendiente">Pendiente</span>
                            </div>
                            <p class="mb-2 small">Motivo: Producto próximo a caducar (3 días)</p>
                            <div class="row mt-2">
                                <div class="col-4">
                                    <small class="text-muted">Producto</small>
                                    <div class="fw-bold">Queso Fresco 500g</div>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">Cantidad</small>
                                    <div class="fw-bold">12 unidades</div>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">Fecha Solicitud</small>
                                    <div class="fw-bold">15/01/2024</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="return-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">Retorno #R-2024-044</h6>
                                    <small class="text-muted">Cliente: Tienda Don Pepe</small>
                                </div>
                                <span class="return-status procesando">Procesando</span>
                            </div>
                            <p class="mb-2 small">Motivo: Cambio por producto dañado en transporte</p>
                            <div class="row mt-2">
                                <div class="col-4">
                                    <small class="text-muted">Producto</small>
                                    <div class="fw-bold">Yogurt Natural 1L</div>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">Cantidad</small>
                                    <div class="fw-bold">5 unidades</div>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">Fecha Solicitud</small>
                                    <div class="fw-bold">14/01/2024</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="return-card" style="border-left-color: var(--success); background-color: rgba(39, 174, 96, 0.05);">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">Retorno #R-2024-043</h6>
                                    <small class="text-muted">Cliente: Minimarket El Trébol</small>
                                </div>
                                <span class="return-status completado">Completado</span>
                            </div>
                            <p class="mb-2 small">Motivo: Error en cantidad pedida</p>
                            <div class="row mt-2">
                                <div class="col-4">
                                    <small class="text-muted">Producto</small>
                                    <div class="fw-bold">Crema de Leche</div>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">Cantidad</small>
                                    <div class="fw-bold">8 unidades</div>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">Fecha Resolución</small>
                                    <div class="fw-bold">13/01/2024</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Análisis de Motivos</div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small>Próximo a Caducar</small>
                                <small class="fw-bold">45%</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: 45%"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small>Producto Dañado</small>
                                <small class="fw-bold">30%</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-danger" style="width: 30%"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small>Error en Pedido</small>
                                <small class="fw-bold">15%</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info" style="width: 15%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <small>Otros Motivos</small>
                                <small class="fw-bold">10%</small>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-secondary" style="width: 10%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Acciones Rápidas</div>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-check-circle me-2"></i>Aprobar Retornos Pendientes
                        </button>
                        <button class="btn btn-outline-warning w-100 mb-2">
                            <i class="fas fa-truck me-2"></i>Programar Recolección
                        </button>
                        <button class="btn btn-outline-success w-100">
                            <i class="fas fa-file-alt me-2"></i>Generar Informe Mensual
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabla de Histórico -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Histórico de Retornos</div>
                        <input type="text" class="form-control form-control-sm" placeholder="Buscar..." style="width: 250px;">
                    </div>
                    <div class="card-body">
                        <table class="user-table">
                            <thead>
                                <tr>
                                    <th>ID Retorno</th>
                                    <th>Cliente</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Motivo</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>R-2024-043</strong></td>
                                    <td>Minimarket El Trébol</td>
                                    <td>Crema de Leche</td>
                                    <td>8 unidades</td>
                                    <td>Error en pedido</td>
                                    <td><span class="return-status completado">Completado</span></td>
                                    <td>13/01/2024</td>
                                </tr>
                                <tr>
                                    <td><strong>R-2024-042</strong></td>
                                    <td>Bodega María</td>
                                    <td>Queso Fresco 500g</td>
                                    <td>15 unidades</td>
                                    <td>Próximo a caducar</td>
                                    <td><span class="return-status completado">Completado</span></td>
                                    <td>12/01/2024</td>
                                </tr>
                                <tr>
                                    <td><strong>R-2024-041</strong></td>
                                    <td>Supermercado Norte</td>
                                    <td>Yogurt Natural 1L</td>
                                    <td>20 unidades</td>
                                    <td>Producto dañado</td>
                                    <td><span class="return-status completado">Completado</span></td>
                                    <td>11/01/2024</td>
                                </tr>
                                <tr>
                                    <td><strong>R-2024-040</strong></td>
                                    <td>Tienda La Esquina</td>
                                    <td>Mantequilla 250g</td>
                                    <td>6 unidades</td>
                                    <td>Error en pedido</td>
                                    <td><span class="return-status completado">Completado</span></td>
                                    <td>10/01/2024</td>
                                </tr>
                            </tbody>
                        </table>
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
