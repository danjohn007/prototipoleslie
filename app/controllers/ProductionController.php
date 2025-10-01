<?php
/**
 * Controlador de Producción
 * Maneja las operaciones de producción y lotes
 */

class ProductionController {
    private $productionModel;
    private $productModel;
    
    public function __construct() {
        $this->productionModel = new Production();
        $this->productModel = new Product();
    }
    
    /**
     * Muestra la página principal de producción
     */
    public function index() {
        // Verificar autenticación
        $user = new User();
        if (!$user->isLoggedIn()) {
            redirect('/');
        }
        
        // Obtener datos
        $productions = $this->productionModel->getAll();
        $stats = $this->productionModel->getStats();
        $products = $this->productModel->getAll();
        $recentProductions = $this->productionModel->getRecent(5);
        
        // Incluir vista
        include BASE_PATH . '/app/views/production/index.php';
    }
    
    /**
     * Muestra el formulario de nuevo lote
     */
    public function newBatch() {
        // Verificar autenticación
        $user = new User();
        if (!$user->isLoggedIn()) {
            redirect('/');
        }
        
        // Obtener productos para el formulario
        $products = $this->productModel->getAll();
        
        // Incluir vista
        include BASE_PATH . '/app/views/production/new-batch.php';
    }
    
    /**
     * Procesa el formulario de nuevo lote
     */
    public function createBatch() {
        // Verificar autenticación
        $user = new User();
        if (!$user->isLoggedIn()) {
            redirect('/');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos
            $errors = [];
            
            if (empty($_POST['producto_id'])) {
                $errors[] = 'El producto es requerido';
            }
            if (empty($_POST['cantidad_producida']) || $_POST['cantidad_producida'] <= 0) {
                $errors[] = 'La cantidad debe ser mayor a 0';
            }
            if (empty($_POST['fecha_produccion'])) {
                $errors[] = 'La fecha de producción es requerida';
            }
            
            if (empty($errors)) {
                // Generar número de lote
                $numero_lote = generate_batch_number('LOTE');
                
                // Preparar datos
                $data = [
                    'numero_lote' => $numero_lote,
                    'producto_id' => clean_input($_POST['producto_id']),
                    'cantidad_producida' => clean_input($_POST['cantidad_producida']),
                    'fecha_produccion' => clean_input($_POST['fecha_produccion']),
                    'fecha_vencimiento' => !empty($_POST['fecha_vencimiento']) ? clean_input($_POST['fecha_vencimiento']) : null,
                    'estado' => clean_input($_POST['estado'] ?? 'en_proceso'),
                    'responsable_id' => $_SESSION['user_id'] ?? null,
                    'observaciones' => clean_input($_POST['observaciones'] ?? '')
                ];
                
                // Crear lote
                $id = $this->productionModel->create($data);
                
                if ($id) {
                    // Registrar movimiento de inventario
                    $inventoryModel = new Inventory();
                    $inventoryModel->create([
                        'producto_id' => $data['producto_id'],
                        'tipo_movimiento' => 'entrada',
                        'cantidad' => $data['cantidad_producida'],
                        'motivo' => 'Producción de lote ' . $numero_lote,
                        'referencia_id' => $id,
                        'usuario_id' => $_SESSION['user_id'] ?? null
                    ]);
                    
                    $_SESSION['success'] = 'Lote creado exitosamente con número: ' . $numero_lote;
                    redirect('/produccion.php');
                } else {
                    $_SESSION['error'] = 'Error al crear el lote';
                    redirect('/nuevo-lote.php');
                }
            } else {
                $_SESSION['errors'] = $errors;
                redirect('/nuevo-lote.php');
            }
        }
    }
}
