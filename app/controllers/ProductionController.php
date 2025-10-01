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
                $cantidad = (float)clean_input($_POST['cantidad_producida']);
                $productModel = new Product();
                $product = $productModel->getById($_POST['producto_id']);
                
                // Calcular costos
                $precio_unitario = $product ? $product['precio_unitario'] : 0;
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
                
                if ($id) {
                    // Registrar movimiento de inventario si la clase existe
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
                            // Log del error pero no interrumpir el proceso
                            error_log('Error al registrar movimiento de inventario: ' . $e->getMessage());
                        }
                    }
                    
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
