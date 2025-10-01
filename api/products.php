<?php
/**
 * API REST para Productos
 * Ejemplo de endpoint JSON
 */

header('Content-Type: application/json');

// Cargar configuración
require_once __DIR__ . '/../app/config/config.php';

// Verificar autenticación
$authController = new AuthController();
$userModel = new User();

if (!$userModel->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Obtener método y acción
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

// Crear instancia del modelo
$productModel = new Product();

try {
    switch ($method) {
        case 'GET':
            if ($action === 'list') {
                // Listar productos
                $filters = [];
                if (isset($_GET['categoria'])) {
                    $filters['categoria'] = $_GET['categoria'];
                }
                if (isset($_GET['stock_bajo'])) {
                    $filters['stock_bajo'] = true;
                }
                
                $products = $productModel->getAll($filters);
                echo json_encode([
                    'success' => true,
                    'data' => $products,
                    'count' => count($products)
                ]);
                
            } elseif ($action === 'get' && isset($_GET['id'])) {
                // Obtener un producto
                $product = $productModel->getById($_GET['id']);
                if ($product) {
                    echo json_encode([
                        'success' => true,
                        'data' => $product
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Producto no encontrado']);
                }
                
            } elseif ($action === 'stats') {
                // Obtener estadísticas
                $stats = $productModel->getStats();
                echo json_encode([
                    'success' => true,
                    'data' => $stats
                ]);
                
            } elseif ($action === 'low-stock') {
                // Productos con stock bajo
                $products = $productModel->getLowStock();
                echo json_encode([
                    'success' => true,
                    'data' => $products,
                    'count' => count($products)
                ]);
            }
            break;
            
        case 'POST':
            // Crear producto
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['nombre'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos inválidos']);
                break;
            }
            
            $id = $productModel->create($data);
            if ($id) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Producto creado exitosamente',
                    'id' => $id
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al crear producto']);
            }
            break;
            
        case 'PUT':
            // Actualizar producto
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID requerido']);
                break;
            }
            
            $result = $productModel->update($data['id'], $data);
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Producto actualizado exitosamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al actualizar producto']);
            }
            break;
            
        case 'DELETE':
            // Eliminar producto
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID requerido']);
                break;
            }
            
            $result = $productModel->delete($_GET['id']);
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Producto eliminado exitosamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al eliminar producto']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
