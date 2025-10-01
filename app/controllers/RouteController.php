<?php
/**
 * Controlador de Rutas
 * Maneja las operaciones de optimización logística y rutas de entrega
 */

class RouteController {
    private $routeModel;
    private $orderModel;
    private $userModel;
    
    public function __construct() {
        $this->routeModel = new Route();
        $this->orderModel = new Order();
        $this->userModel = new User();
    }
    
    /**
     * Muestra la página principal de optimización logística
     */
    public function index() {
        // Verificar autenticación
        $user = new User();
        if (!$user->isLoggedIn()) {
            redirect('/');
        }
        
        // Obtener datos
        $routes = $this->routeModel->getAll();
        $stats = $this->routeModel->getStats();
        $recentRoutes = $this->routeModel->getRecent(10);
        
        // Incluir vista
        require BASE_PATH . '/optimizacion-logistica.php';
    }
    
    /**
     * Muestra el formulario de nueva ruta
     */
    public function newRoute() {
        // Verificar autenticación
        $user = new User();
        if (!$user->isLoggedIn()) {
            redirect('/');
        }
        
        // Obtener conductores (usuarios con rol logistica)
        $drivers = $this->userModel->getByRole('logistica');
        
        // Obtener pedidos pendientes de asignación a ruta
        $pendingOrders = $this->orderModel->getByStatus('confirmado');
        
        // Incluir vista
        require BASE_PATH . '/nueva-ruta.php';
    }
    
    /**
     * Procesa el formulario de nueva ruta
     */
    public function createRoute() {
        // Verificar autenticación
        $user = new User();
        if (!$user->isLoggedIn()) {
            redirect('/');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos
            $errors = [];
            
            if (empty($_POST['nombre_ruta'])) {
                $errors[] = 'El nombre de la ruta es requerido';
            }
            if (empty($_POST['fecha_ruta'])) {
                $errors[] = 'La fecha de la ruta es requerida';
            }
            if (empty($_POST['pedidos']) || !is_array($_POST['pedidos'])) {
                $errors[] = 'Debe asignar al menos un pedido a la ruta';
            }
            
            if (empty($errors)) {
                // Preparar datos de la ruta
                $routeData = [
                    'nombre_ruta' => clean_input($_POST['nombre_ruta']),
                    'conductor_id' => !empty($_POST['conductor_id']) ? clean_input($_POST['conductor_id']) : null,
                    'vehiculo' => clean_input($_POST['vehiculo'] ?? ''),
                    'fecha_ruta' => clean_input($_POST['fecha_ruta']),
                    'hora_inicio' => clean_input($_POST['hora_inicio'] ?? '08:00'),
                    'hora_fin' => clean_input($_POST['hora_fin'] ?? null),
                    'estado' => 'planificada',
                    'distancia_total' => !empty($_POST['distancia_total']) ? floatval($_POST['distancia_total']) : null,
                    'tiempo_estimado' => !empty($_POST['tiempo_estimado']) ? intval($_POST['tiempo_estimado']) : null,
                    'observaciones' => clean_input($_POST['observaciones'] ?? '')
                ];
                
                // Crear ruta
                $result = $this->routeModel->create($routeData);
                
                if ($result) {
                    $ruta_id = $this->routeModel->getLastInsertId();
                    
                    // Asignar pedidos a la ruta
                    $pedidos = $_POST['pedidos'];
                    $orden = 1;
                    foreach ($pedidos as $pedido_id) {
                        if (!empty($pedido_id)) {
                            $this->routeModel->assignPedido([
                                'ruta_id' => $ruta_id,
                                'pedido_id' => $pedido_id,
                                'orden_entrega' => $orden++,
                                'estado_entrega' => 'pendiente'
                            ]);
                            
                            // Actualizar estado del pedido a "en_ruta"
                            $this->orderModel->updateStatus($pedido_id, 'en_ruta');
                        }
                    }
                    
                    $_SESSION['success'] = 'Ruta creada exitosamente: ' . $routeData['nombre_ruta'];
                    redirect('/optimizacion-logistica.php');
                } else {
                    $_SESSION['error'] = 'Error al crear la ruta';
                    redirect('/nueva-ruta.php');
                }
            } else {
                $_SESSION['errors'] = $errors;
                redirect('/nueva-ruta.php');
            }
        }
    }
}
