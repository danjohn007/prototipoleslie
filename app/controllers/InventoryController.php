<?php
/**
 * Controlador de Inventario
 * Maneja las operaciones de inventario
 */

class InventoryController {
    private $inventoryModel;
    private $productModel;
    
    public function __construct() {
        $this->inventoryModel = new Inventory();
        $this->productModel = new Product();
    }
    
    /**
     * Muestra la página principal de inventario
     */
    public function index() {
        // Verificar autenticación
        $user = new User();
        if (!$user->isLoggedIn()) {
            redirect('/');
        }
        
        // Obtener datos
        $movements = $this->inventoryModel->getRecent(15);
        $products = $this->productModel->getAll();
        $lowStock = $this->productModel->getLowStock();
        $stats = $this->productModel->getStats();
        $totalValue = $this->inventoryModel->getTotalValue();
        
        // Incluir vista
        include BASE_PATH . '/app/views/inventory/index.php';
    }
}
