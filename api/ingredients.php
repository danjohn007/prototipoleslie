<?php
/**
 * API REST para Ingredientes
 * Endpoint para gestión de ingredientes
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
$ingredientModel = new Ingredient();

try {
    switch ($method) {
        case 'GET':
            if ($action === 'list') {
                // Listar ingredientes
                $filters = [];
                if (isset($_GET['activo'])) {
                    $filters['activo'] = $_GET['activo'];
                }
                if (isset($_GET['stock_bajo'])) {
                    $filters['stock_bajo'] = true;
                }
                
                $ingredients = $ingredientModel->getAll($filters);
                echo json_encode([
                    'success' => true,
                    'data' => $ingredients,
                    'count' => count($ingredients)
                ]);
                
            } elseif ($action === 'get' && isset($_GET['id'])) {
                // Obtener un ingrediente
                $ingredient = $ingredientModel->getById($_GET['id']);
                if ($ingredient) {
                    echo json_encode([
                        'success' => true,
                        'data' => $ingredient
                    ]);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Ingrediente no encontrado']);
                }
                
            } elseif ($action === 'low-stock') {
                // Ingredientes con stock bajo
                $ingredients = $ingredientModel->getLowStock();
                echo json_encode([
                    'success' => true,
                    'data' => $ingredients,
                    'count' => count($ingredients)
                ]);
            }
            break;
            
        case 'POST':
            // Crear ingrediente
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['nombre'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos inválidos']);
                break;
            }
            
            $id = $ingredientModel->create($data);
            if ($id) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Ingrediente creado exitosamente',
                    'id' => $id
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al crear ingrediente']);
            }
            break;
            
        case 'PUT':
            // Actualizar ingrediente
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID requerido']);
                break;
            }
            
            $result = $ingredientModel->update($data['id'], $data);
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Ingrediente actualizado exitosamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al actualizar ingrediente']);
            }
            break;
            
        case 'DELETE':
            // Eliminar ingrediente
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID requerido']);
                break;
            }
            
            $result = $ingredientModel->delete($_GET['id']);
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Ingrediente eliminado exitosamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al eliminar ingrediente']);
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