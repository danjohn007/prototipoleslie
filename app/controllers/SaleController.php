<?php
/**
 * Controlador de Ventas en Punto
 * Maneja las operaciones de punto de venta (POS)
 */

class SaleController {
    private $saleModel;
    private $orderModel;
    private $clientModel;
    private $productModel;
    
    public function __construct() {
        $this->saleModel = new Sale();
        $this->orderModel = new Order();
        $this->clientModel = new Client();
        $this->productModel = new Product();
    }
    
    /**
     * Muestra la página principal de ventas en punto
     */
    public function index() {
        // Verificar autenticación
        $user = new User();
        if (!$user->isLoggedIn()) {
            redirect('/');
        }
        
        // Obtener datos
        $sales = $this->saleModel->getRecent(20);
        $stats = $this->saleModel->getStats();
        $topProducts = $this->saleModel->getTopProducts(5);
        $products = $this->productModel->getAll();
        $clients = $this->clientModel->getAll();
        
        // Incluir vista
        require BASE_PATH . '/ventas-punto.php';
    }
    
    /**
     * Procesa una venta rápida
     */
    public function processQuickSale() {
        // Verificar autenticación
        $user = new User();
        if (!$user->isLoggedIn()) {
            redirect('/');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos
            $errors = [];
            
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
                
                // Generar número de venta
                $numero_venta = generate_order_number('VTA');
                
                // Preparar datos de la venta
                $saleData = [
                    'numero_pedido' => $numero_venta,
                    'cliente_id' => !empty($_POST['cliente_id']) ? clean_input($_POST['cliente_id']) : 1, // Cliente por defecto
                    'usuario_id' => $_SESSION['user_id'] ?? null,
                    'subtotal' => $subtotal,
                    'descuento' => $descuento,
                    'total' => $total,
                    'observaciones' => clean_input($_POST['observaciones'] ?? 'Venta en punto')
                ];
                
                // Crear venta
                $result = $this->saleModel->createQuickSale($saleData);
                
                if ($result) {
                    $venta_id = $this->orderModel->getLastInsertId();
                    
                    // Agregar detalles de la venta
                    foreach ($productos as $prod) {
                        if (!empty($prod['producto_id']) && !empty($prod['cantidad'])) {
                            $producto = $this->productModel->getById($prod['producto_id']);
                            if ($producto) {
                                $detailData = [
                                    'pedido_id' => $venta_id,
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
                                    'motivo' => 'Venta en punto ' . $numero_venta,
                                    'referencia_id' => $venta_id,
                                    'usuario_id' => $_SESSION['user_id'] ?? null
                                ]);
                            }
                        }
                    }
                    
                    $_SESSION['success'] = 'Venta procesada exitosamente. Número: ' . $numero_venta;
                    redirect('/ventas-punto.php');
                } else {
                    $_SESSION['error'] = 'Error al procesar la venta';
                    redirect('/ventas-punto.php');
                }
            } else {
                $_SESSION['errors'] = $errors;
                redirect('/ventas-punto.php');
            }
        }
    }
}
