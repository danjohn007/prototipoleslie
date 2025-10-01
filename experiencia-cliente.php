<?php
/**
 * Experiencia del Cliente
 * Gestión de satisfacción y feedback de clientes
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

// Obtener datos de clientes y feedback
$clientModel = new Client();
$clients = $clientModel->getAll();

// Calcular estadísticas básicas (simuladas por ahora)
$stats = [
    'satisfaccion_general' => 4.7,
    'nps_score' => 72,
    'tiempo_respuesta' => 2.3,
    'tasa_resolucion' => 94
];

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
    <title>Experiencia del Cliente - Quesos Leslie</title>
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
        
        .feedback-item {
            padding: 15px;
            border-left: 3px solid var(--medium-gray);
            margin-bottom: 15px;
            background-color: var(--light-gray);
            border-radius: 4px;
        }
        
        .feedback-item.positive {
            border-left-color: var(--success);
        }
        
        .feedback-item.negative {
            border-left-color: var(--danger);
        }
        
        .feedback-item.neutral {
            border-left-color: var(--warning);
        }
        
        .sentiment-bar {
            height: 30px;
            background-color: var(--medium-gray);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 10px;
        }
        
        .sentiment-fill {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: 500;
        }
        
        .rating-stars {
            color: #ffc107;
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
            <div class="brand-subtitle">CLIENTES</div>
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
            <a href="<?php echo BASE_URL; ?>/experiencia-cliente.php" class="nav-link active">
                <i class="fas fa-users"></i> Clientes
            </a>
            <a href="<?php echo BASE_URL; ?>/analitica-reportes.php" class="nav-link">
                <i class="fas fa-chart-bar"></i> Analítica
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
            <a href="<?php echo BASE_URL; ?>/index.php?action=logout" class="nav-link" onclick="return confirm('¿Está seguro que desea cerrar sesión?')">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>
    </div>
    
    <!-- Main Content Area -->
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Experiencia del Cliente</h1>
            <div>
                <button class="btn btn-primary me-2">
                    <i class="fas fa-envelope"></i> Enviar Encuesta
                </button>
                <button class="btn btn-success">
                    <i class="fas fa-file-export"></i> Exportar Reporte
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
                    <div class="kpi-label">Satisfacción General</div>
                    <div class="kpi-value" style="color: var(--success);"><?php echo $stats['satisfaccion_general']; ?>/5</div>
                    <div class="kpi-trend up">
                        <i class="fas fa-arrow-up"></i> +0.3 pts este mes
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">NPS Score</div>
                    <div class="kpi-value" style="color: var(--success);"><?php echo $stats['nps_score']; ?></div>
                    <div class="kpi-trend up">
                        <i class="fas fa-arrow-up"></i> +5 pts
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Tiempo Respuesta (hrs)</div>
                    <div class="kpi-value" style="color: var(--info);"><?php echo $stats['tiempo_respuesta']; ?></div>
                    <div class="kpi-trend">
                        <i class="fas fa-minus"></i> Sin cambios
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card kpi-card">
                    <div class="kpi-label">Tasa Resolución</div>
                    <div class="kpi-value" style="color: var(--success);"><?php echo $stats['tasa_resolucion']; ?>%</div>
                    <div class="kpi-trend up">
                        <i class="fas fa-arrow-up"></i> +2%
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Clientes Activos -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Clientes Registrados</div>
                        <span class="badge bg-primary"><?php echo count($clients); ?> clientes activos</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Tipo</th>
                                        <th>Ciudad</th>
                                        <th>Contacto</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($clients)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                <i class="fas fa-info-circle me-2"></i>No hay clientes registrados
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach (array_slice($clients, 0, 10) as $client): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($client['nombre']); ?></strong>
                                                    <?php if (!empty($client['ruc'])): ?>
                                                        <br><small class="text-muted">RUC: <?php echo htmlspecialchars($client['ruc']); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo $client['tipo_cliente'] === 'oro' ? 'warning' : 
                                                            ($client['tipo_cliente'] === 'plata' ? 'secondary' : 'info'); 
                                                    ?>">
                                                        <?php echo strtoupper(htmlspecialchars($client['tipo_cliente'] ?? 'bronce')); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($client['ciudad'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <?php if (!empty($client['telefono'])): ?>
                                                        <i class="fas fa-phone text-success me-1"></i>
                                                        <?php echo htmlspecialchars($client['telefono']); ?>
                                                    <?php endif; ?>
                                                    <?php if (!empty($client['email'])): ?>
                                                        <br><i class="fas fa-envelope text-primary me-1"></i>
                                                        <small><?php echo htmlspecialchars($client['email']); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        <?php echo htmlspecialchars($client['estado']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Resumen de Satisfacción -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Distribución de Sentimientos</div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small>😊 Positivo</small>
                                <strong>75%</strong>
                            </div>
                            <div class="sentiment-bar">
                                <div class="sentiment-fill" style="width: 75%; background-color: var(--success);">75%</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small>😐 Neutral</small>
                                <strong>18%</strong>
                            </div>
                            <div class="sentiment-bar">
                                <div class="sentiment-fill" style="width: 18%; background-color: var(--warning);">18%</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small>😞 Negativo</small>
                                <strong>7%</strong>
                            </div>
                            <div class="sentiment-bar">
                                <div class="sentiment-fill" style="width: 7%; background-color: var(--danger);">7%</div>
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
                                <span>Calidad del Producto</span>
                                <span class="ms-auto rating-stars">★★★★★</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Frescura</span>
                                <span class="ms-auto rating-stars">★★★★★</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Sabor</span>
                                <span class="ms-auto rating-stars">★★★★☆</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Atención al Cliente</span>
                                <span class="ms-auto rating-stars">★★★★☆</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
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
                if (window.innerWidth < 992) {
                    document.querySelector('.sidebar').classList.remove('active');
                    document.querySelector('.sidebar-overlay').classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>
