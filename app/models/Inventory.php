<?php
/**
 * Modelo Inventory
 * Gestiona los movimientos de inventario del sistema
 */

class Inventory {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtiene todos los movimientos de inventario
     */
    public function getAll($filters = []) {
        $sql = "SELECT i.*, p.nombre as producto_nombre, u.nombre as usuario_nombre 
                FROM inventario_movimientos i
                LEFT JOIN productos p ON i.producto_id = p.id
                LEFT JOIN usuarios u ON i.usuario_id = u.id
                WHERE 1=1";
        $params = [];
        
        // Filtrar por tipo
        if (isset($filters['tipo_movimiento']) && !empty($filters['tipo_movimiento'])) {
            $sql .= " AND i.tipo_movimiento = ?";
            $params[] = $filters['tipo_movimiento'];
        }
        
        // Filtrar por producto
        if (isset($filters['producto_id']) && !empty($filters['producto_id'])) {
            $sql .= " AND i.producto_id = ?";
            $params[] = $filters['producto_id'];
        }
        
        $sql .= " ORDER BY i.fecha_movimiento DESC";
        
        return $this->db->query($sql, $params);
    }
    
    /**
     * Obtiene un movimiento por ID
     */
    public function getById($id) {
        $sql = "SELECT i.*, p.nombre as producto_nombre, u.nombre as usuario_nombre 
                FROM inventario_movimientos i
                LEFT JOIN productos p ON i.producto_id = p.id
                LEFT JOIN usuarios u ON i.usuario_id = u.id
                WHERE i.id = ?";
        return $this->db->queryOne($sql, [$id]);
    }
    
    /**
     * Registra un movimiento de inventario
     */
    public function create($data) {
        $sql = "INSERT INTO inventario_movimientos (producto_id, tipo_movimiento, 
                cantidad, motivo, referencia_id, usuario_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $result = $this->db->execute($sql, [
            $data['producto_id'],
            $data['tipo_movimiento'],
            $data['cantidad'],
            $data['motivo'] ?? null,
            $data['referencia_id'] ?? null,
            $data['usuario_id'] ?? null
        ]);
        
        // Actualizar stock del producto
        if ($result && isset($data['producto_id']) && isset($data['cantidad'])) {
            $productModel = new Product();
            if ($data['tipo_movimiento'] === 'entrada') {
                $productModel->updateStock($data['producto_id'], $data['cantidad'], 'add');
            } elseif ($data['tipo_movimiento'] === 'salida' || $data['tipo_movimiento'] === 'merma') {
                $productModel->updateStock($data['producto_id'], $data['cantidad'], 'subtract');
            }
        }
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Obtiene estadísticas de inventario
     */
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total_movimientos,
                    COUNT(CASE WHEN tipo_movimiento = 'entrada' THEN 1 END) as total_entradas,
                    COUNT(CASE WHEN tipo_movimiento = 'salida' THEN 1 END) as total_salidas,
                    COUNT(CASE WHEN tipo_movimiento = 'merma' THEN 1 END) as total_mermas
                FROM inventario_movimientos";
        
        return $this->db->queryOne($sql);
    }
    
    /**
     * Obtiene movimientos recientes
     */
    public function getRecent($limit = 10) {
        $sql = "SELECT i.*, p.nombre as producto_nombre, u.nombre as usuario_nombre 
                FROM inventario_movimientos i
                LEFT JOIN productos p ON i.producto_id = p.id
                LEFT JOIN usuarios u ON i.usuario_id = u.id
                ORDER BY i.fecha_movimiento DESC
                LIMIT ?";
        return $this->db->query($sql, [$limit]);
    }
    
    /**
     * Obtiene el valor total del inventario
     */
    public function getTotalValue() {
        $sql = "SELECT SUM(stock_actual * precio_unitario) as valor_total
                FROM productos 
                WHERE activo = 1";
        
        $result = $this->db->queryOne($sql);
        return $result['valor_total'] ?? 0;
    }
}
