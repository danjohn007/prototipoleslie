<?php
/**
 * Archivo de prueba de conexión y configuración
 * Sistema de Logística Quesos Leslie
 */

// Cargar configuración
require_once __DIR__ . '/app/config/config.php';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Conexión - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 50px 0;
            font-family: 'Helvetica Neue', Arial, sans-serif;
        }
        
        .container {
            max-width: 900px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #2c3e50 0%, #e74c3c 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        
        .test-item {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .test-item:last-child {
            border-bottom: none;
        }
        
        .badge-success {
            background-color: #27ae60;
        }
        
        .badge-danger {
            background-color: #e74c3c;
        }
        
        .badge-warning {
            background-color: #f39c12;
        }
        
        .info-value {
            font-family: monospace;
            background: #f8f9fa;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center mb-4">
            <h1 class="text-white">
                <i class="fas fa-vial me-2"></i>
                Test de Conexión y Configuración
            </h1>
            <p class="text-white opacity-75">Sistema de Logística Quesos Leslie</p>
        </div>
        
        <!-- Test de PHP -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-code me-2"></i> Configuración de PHP</h5>
            </div>
            <div class="card-body">
                <div class="test-item">
                    <span><i class="fas fa-check-circle text-success me-2"></i> Versión de PHP</span>
                    <span class="badge badge-success"><?php echo phpversion(); ?></span>
                </div>
                <div class="test-item">
                    <span><i class="fas fa-<?php echo version_compare(phpversion(), '7.0', '>=') ? 'check-circle text-success' : 'times-circle text-danger'; ?> me-2"></i> PHP 7.0 o superior</span>
                    <span class="badge badge-<?php echo version_compare(phpversion(), '7.0', '>=') ? 'success' : 'danger'; ?>">
                        <?php echo version_compare(phpversion(), '7.0', '>=') ? 'OK' : 'Actualizar PHP'; ?>
                    </span>
                </div>
                <div class="test-item">
                    <span><i class="fas fa-<?php echo extension_loaded('pdo') ? 'check-circle text-success' : 'times-circle text-danger'; ?> me-2"></i> Extensión PDO</span>
                    <span class="badge badge-<?php echo extension_loaded('pdo') ? 'success' : 'danger'; ?>">
                        <?php echo extension_loaded('pdo') ? 'Instalada' : 'No Instalada'; ?>
                    </span>
                </div>
                <div class="test-item">
                    <span><i class="fas fa-<?php echo extension_loaded('pdo_mysql') ? 'check-circle text-success' : 'times-circle text-danger'; ?> me-2"></i> Extensión PDO MySQL</span>
                    <span class="badge badge-<?php echo extension_loaded('pdo_mysql') ? 'success' : 'danger'; ?>">
                        <?php echo extension_loaded('pdo_mysql') ? 'Instalada' : 'No Instalada'; ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Test de Base de Datos -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-database me-2"></i> Conexión a Base de Datos</h5>
            </div>
            <div class="card-body">
                <?php
                $dbConnected = false;
                $dbMessage = '';
                $tableCount = 0;
                
                try {
                    $db = Database::getInstance();
                    $connection = $db->getConnection();
                    
                    if ($connection) {
                        $dbConnected = true;
                        $dbMessage = 'Conexión exitosa';
                        
                        // Contar tablas
                        $result = $db->query("SHOW TABLES");
                        $tableCount = count($result);
                    }
                } catch (Exception $e) {
                    $dbMessage = 'Error: ' . $e->getMessage();
                }
                ?>
                
                <div class="test-item">
                    <span><i class="fas fa-<?php echo $dbConnected ? 'check-circle text-success' : 'times-circle text-danger'; ?> me-2"></i> Estado de Conexión</span>
                    <span class="badge badge-<?php echo $dbConnected ? 'success' : 'danger'; ?>">
                        <?php echo $dbMessage; ?>
                    </span>
                </div>
                
                <?php if ($dbConnected): ?>
                    <div class="test-item">
                        <span><i class="fas fa-server me-2"></i> Host</span>
                        <span class="info-value"><?php echo DB_HOST; ?></span>
                    </div>
                    <div class="test-item">
                        <span><i class="fas fa-database me-2"></i> Base de Datos</span>
                        <span class="info-value"><?php echo DB_NAME; ?></span>
                    </div>
                    <div class="test-item">
                        <span><i class="fas fa-table me-2"></i> Tablas Detectadas</span>
                        <span class="badge badge-<?php echo $tableCount > 0 ? 'success' : 'warning'; ?>">
                            <?php echo $tableCount; ?> tablas
                        </span>
                    </div>
                    
                    <?php if ($tableCount > 0): ?>
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Base de datos configurada correctamente.</strong> 
                            El sistema está listo para usarse.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Base de datos vacía.</strong> 
                            Por favor, importe el archivo <code>database.sql</code> para crear las tablas.
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-danger mt-3 mb-0">
                        <i class="fas fa-times-circle me-2"></i>
                        <strong>No se pudo conectar a la base de datos.</strong><br>
                        Verifique las credenciales en <code>app/config/config.php</code>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Test de Configuración -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-cog me-2"></i> Configuración del Sistema</h5>
            </div>
            <div class="card-body">
                <div class="test-item">
                    <span><i class="fas fa-link me-2"></i> URL Base</span>
                    <span class="info-value"><?php echo BASE_URL; ?></span>
                </div>
                <div class="test-item">
                    <span><i class="fas fa-folder me-2"></i> Directorio Raíz</span>
                    <span class="info-value"><?php echo ROOT_PATH; ?></span>
                </div>
                <div class="test-item">
                    <span><i class="fas fa-code-branch me-2"></i> Versión del Sistema</span>
                    <span class="info-value"><?php echo APP_VERSION; ?></span>
                </div>
                <div class="test-item">
                    <span><i class="fas fa-clock me-2"></i> Zona Horaria</span>
                    <span class="info-value"><?php echo date_default_timezone_get(); ?></span>
                </div>
                <div class="test-item">
                    <span><i class="fas fa-hourglass-half me-2"></i> Timeout de Sesión</span>
                    <span class="info-value"><?php echo SESSION_TIMEOUT / 60; ?> minutos</span>
                </div>
            </div>
        </div>
        
        <!-- Test de Permisos -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i> Permisos de Directorios</h5>
            </div>
            <div class="card-body">
                <?php
                $directories = [
                    'Raíz' => ROOT_PATH,
                    'App' => APP_PATH,
                    'Public' => PUBLIC_PATH,
                ];
                
                foreach ($directories as $name => $path):
                    $writable = is_writable($path);
                ?>
                    <div class="test-item">
                        <span><i class="fas fa-<?php echo $writable ? 'check-circle text-success' : 'exclamation-triangle text-warning'; ?> me-2"></i> <?php echo $name; ?></span>
                        <span class="badge badge-<?php echo $writable ? 'success' : 'warning'; ?>">
                            <?php echo $writable ? 'Escribible' : 'Solo Lectura'; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Acciones -->
        <div class="card">
            <div class="card-body text-center">
                <h5 class="mb-3">Acciones Disponibles</h5>
                <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-primary btn-lg me-2">
                    <i class="fas fa-sign-in-alt me-2"></i> Ir al Login
                </a>
                <a href="<?php echo BASE_URL; ?>/dashboard.php" class="btn btn-success btn-lg">
                    <i class="fas fa-chart-pie me-2"></i> Ir al Dashboard
                </a>
            </div>
        </div>
        
        <div class="text-center text-white mt-4">
            <small>
                <i class="fas fa-shield-alt me-1"></i>
                Sistema de Logística Quesos Leslie v<?php echo APP_VERSION; ?>
            </small>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
