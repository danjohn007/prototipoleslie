<?php
/**
 * Controlador de Producción
 * Maneja las operaciones de producción y lotes
 */

class ProductionController {
    private $productionModel;
    private $productModel;
    private $ingredientModel;
    private $batchIngredientModel;
    
    public function __construct() {
        $this->productionModel = new Production();
        $this->productModel = new Product();
        $this->ingredientModel = new Ingredient();
        $this->batchIngredientModel = new BatchIngredient();
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
        
        try {
            // Obtener datos
            $productions = $this->productionModel->getAll();
            $stats = $this->productionModel->getStats();
            $products = $this->productModel->getAll();
            $recentProductions = $this->productionModel->getRecent(5);
            
            // Incluir vista
            include ROOT_PATH . '/app/views/production/index.php';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al cargar los datos de producción: ' . $e->getMessage();
            redirect('/dashboard.php');
        }
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
        
        try {
            // Obtener productos para el formulario
            $products = $this->productModel->getAll();
            // Obtener ingredientes activos
            $ingredients = $this->ingredientModel->getAll(['activo' => 1]);
            
            // Incluir vista
            include ROOT_PATH . '/app/views/production/new-batch.php';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al cargar el formulario: ' . $e->getMessage();
            redirect('/produccion.php');
        }
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
            try {
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
                
                if (!empty($errors)) {
                    $_SESSION['errors'] = $errors;
                    redirect('/nuevo-lote.php');
                    return;
                }
                
                // Iniciar transacción
                $this->productionModel->beginTransaction();
                
                // Generar número de lote único
                do {
                    $numero_lote = generate_batch_number('LOTE');
                    $existingBatch = $this->productionModel->getByNumber($numero_lote);
                } while ($existingBatch);
                
                // Preparar datos del lote
                $cantidad = (float)clean_input($_POST['cantidad_producida']);
                $product = $this->productModel->getById($_POST['producto_id']);
                
                if (!$product) {
                    throw new Exception('Producto no encontrado');
                }
                
                // Calcular costos
                $precio_unitario = $product['precio_unitario'];
                $costo_materias_primas = $cantidad * $precio_unitario * 0.6;
                $costo_mano_obra = $cantidad * 8;
                $otros_costos = $cantidad * 2;
                $costo_total = $costo_materias_primas + $costo_mano_obra + $otros_costos;
                
                $data = [
                    'numero_lote' => $numero_lote,
                    'producto_id' => clean_input($_POST['producto_id']),
                    'cantidad_producida' => $cantidad,
                    'tipo_produccion' => clean_input($_POST['tipo_produccion'] ?? 'granel'),
                    'fecha_produccion' => clean_input($_POST['fecha_produccion']),
                    'fecha_vencimiento' => !empty($_POST['fecha_vencimiento']) ? clean_input($_POST['fecha_vencimiento']) : null,
                    'estado' => clean_input($_POST['estado'] ?? 'en_proceso'),
                    'costo_materias_primas' => round($costo_materias_primas, 2),
                    'costo_mano_obra' => round($costo_mano_obra, 2),
                    'otros_costos' => round($otros_costos, 2),
                    'costo_total' => round($costo_total, 2),
                    'responsable_id' => $_SESSION['user_id'] ?? null,
                    'observaciones' => clean_input($_POST['observaciones'] ?? '')
                ];
                
                // Crear lote
                $id = $this->productionModel->create($data);
                
                if (!$id) {
                    throw new Exception('Error al crear el lote en la base de datos');
                }
                
                // Procesar ingredientes si están presentes
                if (!empty($_POST['ingredientes']) && is_array($_POST['ingredientes'])) {
                    $ingredientes = [];
                    foreach ($_POST['ingredientes'] as $ingrediente_data) {
                        if (!empty($ingrediente_data['ingrediente_id']) && !empty($ingrediente_data['cantidad'])) {
                            $ingrediente = $this->ingredientModel->getById($ingrediente_data['ingrediente_id']);
                            if ($ingrediente) {
                                $costo_ingrediente = $ingrediente['costo_unitario'] * $ingrediente_data['cantidad'];
                                $ingredientes[] = [
                                    'ingrediente_id' => $ingrediente_data['ingrediente_id'],
                                    'cantidad_requerida' => $ingrediente_data['cantidad'],
                                    'cantidad_utilizada' => $ingrediente_data['cantidad'],
                                    'costo_ingrediente' => $costo_ingrediente,
                                    'observaciones' => $ingrediente_data['observaciones'] ?? null
                                ];
                            }
                        }
                    }
                    
                    if (!empty($ingredientes)) {
                        $this->batchIngredientModel->addMultipleToBatch($id, $ingredientes);
                    }
                }
                
                // Registrar movimiento de inventario
                if (class_exists('Inventory')) {
                    try {
                        $inventoryModel = new Inventory();
                        $inventoryModel->create([
                            'producto_id' => $data['producto_id'],
                            'tipo_movimiento' => 'entrada',
                            'cantidad' => $data['cantidad_producida'],
                            'motivo' => 'Producción de lote ' . $numero_lote,
                            'referencia_id' => $id,
                            'usuario_id' => $_SESSION['user_id'] ?? null
                        ]);
                    } catch (Exception $e) {
                        error_log('Error al registrar movimiento de inventario: ' . $e->getMessage());
                    }
                }
                
                // Confirmar transacción
                $this->productionModel->commit();
                
                $_SESSION['success'] = 'Lote creado exitosamente con número: ' . $numero_lote;
                redirect('/produccion.php');
                
            } catch (Exception $e) {
                // Revertir transacción en caso de error
                if ($this->productionModel->inTransaction()) {
                    $this->productionModel->rollback();
                }
                
                error_log('Error creating batch: ' . $e->getMessage());
                $_SESSION['error'] = 'Error al crear el lote: ' . $e->getMessage();
                redirect('/nuevo-lote.php');
            }
        }
    }
}
