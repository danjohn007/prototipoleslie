<?php
/**
 * Controlador de Retornos
 * Maneja las operaciones de retornos de productos
 */

class ReturnController {
    private $returnModel;
    private $orderModel;
    private $clientModel;
    private $productModel;
    
    public function __construct() {
        $this->returnModel = new ReturnModel();
        $this->orderModel = new Order();
        $this->clientModel = new Client();
        $this->productModel = new Product();
    }
    
    /**
     * Muestra la página principal de control de retornos
     */
    public function index() {
        // Verificar autenticación
        $user = new User();
        if (!$user->isLoggedIn()) {
            redirect('/');
        }
        
        // Obtener datos
        $returns = $this->returnModel->getAll();
        $stats = $this->returnModel->getStats();
        $recentReturns = $this->returnModel->getRecent(10);
        $byMotivo = $this->returnModel->getByMotivo();
        
        // Incluir vista
        require BASE_PATH . '/control-retornos.php';
    }
    
    /**
     * Muestra el formulario de registrar retorno
     */
    public function newReturn() {
        // Verificar autenticación
        $user = new User();
        if (!$user->isLoggedIn()) {
            redirect('/');
        }
        
        // Obtener datos para el formulario
        $clients = $this->clientModel->getAll();
        $products = $this->productModel->getAll();
        $orders = $this->orderModel->getAll();
        
        // Incluir vista
        require BASE_PATH . '/registrar-retorno.php';
    }
    
    /**
     * Procesa el formulario de nuevo retorno
     */
    public function createReturn() {
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
            if (empty($_POST['producto_id'])) {
                $errors[] = 'El producto es requerido';
            }
            if (empty($_POST['cantidad']) || $_POST['cantidad'] <= 0) {
                $errors[] = 'La cantidad debe ser mayor a 0';
            }
            if (empty($_POST['motivo'])) {
                $errors[] = 'El motivo del retorno es requerido';
            }
            
            if (empty($errors)) {
                // Generar número de retorno
                $numero_retorno = generate_return_number('RET');
                
                // Preparar datos del retorno
                $returnData = [
                    'numero_retorno' => $numero_retorno,
                    'pedido_id' => !empty($_POST['pedido_id']) ? clean_input($_POST['pedido_id']) : null,
                    'cliente_id' => clean_input($_POST['cliente_id']),
                    'producto_id' => clean_input($_POST['producto_id']),
                    'cantidad' => intval($_POST['cantidad']),
                    'motivo' => clean_input($_POST['motivo']),
                    'descripcion' => clean_input($_POST['descripcion'] ?? ''),
                    'estado' => 'registrado',
                    'responsable_id' => $_SESSION['user_id'] ?? null
                ];
                
                // Crear retorno
                $result = $this->returnModel->create($returnData);
                
                if ($result) {
                    $retorno_id = $this->returnModel->getLastInsertId();
                    
                    // Registrar movimiento de inventario (entrada por retorno)
                    $inventoryModel = new Inventory();
                    $inventoryModel->create([
                        'producto_id' => $returnData['producto_id'],
                        'tipo_movimiento' => 'entrada',
                        'cantidad' => $returnData['cantidad'],
                        'motivo' => 'Retorno ' . $numero_retorno . ' - ' . $returnData['motivo'],
                        'referencia_id' => $retorno_id,
                        'usuario_id' => $_SESSION['user_id'] ?? null
                    ]);
                    
                    // Actualizar stock del producto (incrementar)
                    $this->productModel->updateStock(
                        $returnData['producto_id'],
                        $returnData['cantidad']
                    );
                    
                    $_SESSION['success'] = 'Retorno registrado exitosamente con número: ' . $numero_retorno;
                    redirect('/control-retornos.php');
                } else {
                    $_SESSION['error'] = 'Error al registrar el retorno';
                    redirect('/registrar-retorno.php');
                }
            } else {
                $_SESSION['errors'] = $errors;
                redirect('/registrar-retorno.php');
            }
        }
    }
}
