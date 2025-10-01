<?php
/**
 * Controlador de Pedidos
 * Maneja las operaciones de pedidos
 */

class OrderController {
    private $orderModel;
    private $clientModel;
    private $productModel;
    
    public function __construct() {
        $this->orderModel = new Order();
        $this->clientModel = new Client();
        $this->productModel = new Product();
    }
    
    /**
     * Muestra la página principal de pedidos
     */
    public function index() {
        // Verificar autenticación
        $user = new User();
        if (!$user->isLoggedIn()) {
            redirect('/');
        }
        
        // Obtener datos
        $orders = $this->orderModel->getAll();
        $stats = $this->orderModel->getStats();
        $recentOrders = $this->orderModel->getRecent(5);
        
        // Incluir vista
        require BASE_PATH . '/pedidos.php';
    }
    
    /**
     * Muestra el formulario de nuevo pedido
     */
    public function newOrder() {
        // Verificar autenticación
        $user = new User();
        if (!$user->isLoggedIn()) {
            redirect('/');
        }
        
        // Obtener clientes y productos para el formulario
        $clients = $this->clientModel->getAll();
        $products = $this->productModel->getAll();
        
        // Incluir vista
        require BASE_PATH . '/nuevo-pedido.php';
    }
    
    /**
     * Procesa el formulario de nuevo pedido
     */
    public function createOrder() {
        // Verificar autenticación
        $user = new User();
        if (!$user->isLoggedIn()) {
            redirect('/');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos
            $errors = [];
            
            if (empty($_POST['cliente_id'])) {
                $errors[] = 'El cliente es requerido';
            }
            if (empty($_POST['fecha_entrega'])) {
                $errors[] = 'La fecha de entrega es requerida';
            }
            if (empty($_POST['productos']) || !is_array($_POST['productos'])) {
                $errors[] = 'Debe agregar al menos un producto';
            }
            
            if (empty($errors)) {
                // Calcular totales
                $subtotal = 0;
                $productos = $_POST['productos'];
                
                foreach ($productos as $prod) {
                    if (!empty($prod['producto_id']) && !empty($prod['cantidad'])) {
                        $producto = $this->productModel->getById($prod['producto_id']);
                        if ($producto) {
                            $subtotal += $producto['precio_unitario'] * $prod['cantidad'];
                        }
                    }
                }
                
                $descuento = !empty($_POST['descuento']) ? floatval($_POST['descuento']) : 0;
                $total = $subtotal - $descuento;
                
                // Generar número de pedido
                $numero_pedido = generate_order_number('PED');
                
                // Preparar datos del pedido
                $orderData = [
                    'numero_pedido' => $numero_pedido,
                    'cliente_id' => clean_input($_POST['cliente_id']),
                    'usuario_id' => $_SESSION['user_id'] ?? null,
                    'fecha_entrega' => clean_input($_POST['fecha_entrega']),
                    'estado' => 'pendiente',
                    'subtotal' => $subtotal,
                    'descuento' => $descuento,
                    'total' => $total,
                    'observaciones' => clean_input($_POST['observaciones'] ?? '')
                ];
                
                // Crear pedido
                $result = $this->orderModel->create($orderData);
                
                if ($result) {
                    $pedido_id = $this->orderModel->getLastInsertId();
                    
                    // Agregar detalles del pedido
                    foreach ($productos as $prod) {
                        if (!empty($prod['producto_id']) && !empty($prod['cantidad'])) {
                            $producto = $this->productModel->getById($prod['producto_id']);
                            if ($producto) {
                                $detailData = [
                                    'pedido_id' => $pedido_id,
                                    'producto_id' => $prod['producto_id'],
                                    'cantidad' => $prod['cantidad'],
                                    'precio_unitario' => $producto['precio_unitario'],
                                    'subtotal' => $producto['precio_unitario'] * $prod['cantidad']
                                ];
                                $this->orderModel->addDetail($detailData);
                                
                                // Actualizar stock (reducir)
                                $this->productModel->updateStock(
                                    $prod['producto_id'], 
                                    -$prod['cantidad']
                                );
                                
                                // Registrar movimiento de inventario
                                $inventoryModel = new Inventory();
                                $inventoryModel->create([
                                    'producto_id' => $prod['producto_id'],
                                    'tipo_movimiento' => 'salida',
                                    'cantidad' => $prod['cantidad'],
                                    'motivo' => 'Pedido ' . $numero_pedido,
                                    'referencia_id' => $pedido_id,
                                    'usuario_id' => $_SESSION['user_id'] ?? null
                                ]);
                            }
                        }
                    }
                    
                    $_SESSION['success'] = 'Pedido creado exitosamente con número: ' . $numero_pedido;
                    redirect('/pedidos.php');
                } else {
                    $_SESSION['error'] = 'Error al crear el pedido';
                    redirect('/nuevo-pedido.php');
                }
            } else {
                $_SESSION['errors'] = $errors;
                redirect('/nuevo-pedido.php');
            }
        }
    }
}
