<?php
/**
 * Controlador de Productos
 * Maneja las operaciones CRUD de productos
 */

class ProductController {
    private $productModel;
    
    public function __construct() {
        $this->productModel = new Product();
    }
    
    /**
     * Muestra el formulario de nuevo producto
     */
    public function newProduct() {
        // Verificar autenticación
        $user = new User();
        if (!$user->isLoggedIn()) {
            redirect('/');
        }
        
        // Incluir vista
        include BASE_PATH . '/app/views/product/new-product.php';
    }
    
    /**
     * Procesa el formulario de nuevo producto
     */
    public function createProduct() {
        // Verificar autenticación
        $user = new User();
        if (!$user->isLoggedIn()) {
            redirect('/');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar datos
            $errors = [];
            
            if (empty($_POST['nombre'])) {
                $errors[] = 'El nombre del producto es requerido';
            }
            if (empty($_POST['categoria'])) {
                $errors[] = 'La categoría es requerida';
            }
            if (empty($_POST['precio_unitario']) || $_POST['precio_unitario'] <= 0) {
                $errors[] = 'El precio debe ser mayor a 0';
            }
            
            if (empty($errors)) {
                // Preparar datos
                $data = [
                    'nombre' => clean_input($_POST['nombre']),
                    'descripcion' => clean_input($_POST['descripcion'] ?? ''),
                    'categoria' => clean_input($_POST['categoria']),
                    'precio_unitario' => clean_input($_POST['precio_unitario']),
                    'stock_actual' => clean_input($_POST['stock_actual'] ?? 0),
                    'stock_minimo' => clean_input($_POST['stock_minimo'] ?? 10),
                    'unidad_medida' => clean_input($_POST['unidad_medida'] ?? 'unidad')
                ];
                
                // Crear producto
                $id = $this->productModel->create($data);
                
                if ($id) {
                    $_SESSION['success'] = 'Producto creado exitosamente';
                    redirect('/inventario.php');
                } else {
                    $_SESSION['error'] = 'Error al crear el producto';
                    redirect('/nuevo-producto.php');
                }
            } else {
                $_SESSION['errors'] = $errors;
                redirect('/nuevo-producto.php');
            }
        }
    }
}
