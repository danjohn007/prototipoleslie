<?php
/**
 * Modelo Product
 * Gestiona los productos del sistema
 */

class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtiene todos los productos
     */
    public function getAll($filters = []) {
        $sql = "SELECT * FROM productos WHERE activo = 1";
        $params = [];
        
        // Filtrar por categoría si existe
        if (isset($filters['categoria']) && !empty($filters['categoria'])) {
            $sql .= " AND categoria = ?";
            $params[] = $filters['categoria'];
        }
        
        // Filtrar por stock bajo si existe
        if (isset($filters['stock_bajo']) && $filters['stock_bajo']) {
            $sql .= " AND stock_actual <= stock_minimo";
        }
        
        $sql .= " ORDER BY nombre ASC";
        
        return $this->db->query($sql, $params);
    }
    
    /**
     * Obtiene un producto por ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM productos WHERE id = ? AND activo = 1";
        return $this->db->queryOne($sql, [$id]);
    }
    
    /**
     * Crea un nuevo producto
     */
    public function create($data) {
        $sql = "INSERT INTO productos (nombre, descripcion, categoria, precio_unitario, 
                stock_actual, stock_minimo, unidad_medida) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $result = $this->db->execute($sql, [
            $data['nombre'],
            $data['descripcion'],
            $data['categoria'],
            $data['precio_unitario'],
            $data['stock_actual'] ?? 0,
            $data['stock_minimo'] ?? 10,
            $data['unidad_medida'] ?? 'unidad'
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Actualiza un producto
     */
    public function update($id, $data) {
        $sql = "UPDATE productos 
                SET nombre = ?, descripcion = ?, categoria = ?, 
                    precio_unitario = ?, stock_actual = ?, stock_minimo = ?, 
                    unidad_medida = ?
                WHERE id = ?";
        
        return $this->db->execute($sql, [
            $data['nombre'],
            $data['descripcion'],
            $data['categoria'],
            $data['precio_unitario'],
            $data['stock_actual'],
            $data['stock_minimo'],
            $data['unidad_medida'],
            $id
        ]);
    }
    
    /**
     * Elimina (desactiva) un producto
     */
    public function delete($id) {
        $sql = "UPDATE productos SET activo = 0 WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    /**
     * Actualiza el stock de un producto
     */
    public function updateStock($id, $quantity, $operation = 'add') {
        if ($operation === 'add') {
            $sql = "UPDATE productos SET stock_actual = stock_actual + ? WHERE id = ?";
        } else {
            $sql = "UPDATE productos SET stock_actual = stock_actual - ? WHERE id = ?";
        }
        
        return $this->db->execute($sql, [$quantity, $id]);
    }
    
    /**
     * Obtiene productos con stock bajo
     */
    public function getLowStock() {
        $sql = "SELECT * FROM productos 
                WHERE stock_actual <= stock_minimo AND activo = 1
                ORDER BY (stock_minimo - stock_actual) DESC";
        return $this->db->query($sql);
    }
    
    /**
     * Obtiene estadísticas de productos
     */
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total_productos,
                    COUNT(CASE WHEN stock_actual <= stock_minimo THEN 1 END) as productos_bajo_stock,
                    SUM(stock_actual * precio_unitario) as valor_inventario
                FROM productos 
                WHERE activo = 1";
        
        return $this->db->queryOne($sql);
    }
}
