<?php
/**
 * Modelo Ingredient
 * Gestiona los ingredientes del sistema
 */

class Ingredient {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtiene todos los ingredientes
     */
    public function getAll($filters = []) {
        $sql = "SELECT * FROM ingredientes WHERE 1=1";
        $params = [];
        
        // Filtrar por activos
        if (isset($filters['activo'])) {
            $sql .= " AND activo = ?";
            $params[] = $filters['activo'];
        }
        
        // Filtrar por stock bajo
        if (isset($filters['stock_bajo']) && $filters['stock_bajo']) {
            $sql .= " AND stock_actual <= stock_minimo";
        }
        
        $sql .= " ORDER BY nombre ASC";
        
        return $this->db->query($sql, $params);
    }
    
    /**
     * Obtiene un ingrediente por ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM ingredientes WHERE id = ?";
        return $this->db->queryOne($sql, [$id]);
    }
    
    /**
     * Crea un nuevo ingrediente
     */
    public function create($data) {
        $sql = "INSERT INTO ingredientes (nombre, descripcion, unidad_medida, costo_unitario, 
                stock_actual, stock_minimo, proveedor, activo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $result = $this->db->execute($sql, [
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['unidad_medida'] ?? 'kg',
            $data['costo_unitario'] ?? 0,
            $data['stock_actual'] ?? 0,
            $data['stock_minimo'] ?? 0,
            $data['proveedor'] ?? null,
            $data['activo'] ?? 1
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Actualiza un ingrediente
     */
    public function update($id, $data) {
        $sql = "UPDATE ingredientes 
                SET nombre = ?, descripcion = ?, unidad_medida = ?, costo_unitario = ?,
                    stock_actual = ?, stock_minimo = ?, proveedor = ?, activo = ?
                WHERE id = ?";
        
        return $this->db->execute($sql, [
            $data['nombre'],
            $data['descripcion'] ?? null,
            $data['unidad_medida'] ?? 'kg',
            $data['costo_unitario'] ?? 0,
            $data['stock_actual'] ?? 0,
            $data['stock_minimo'] ?? 0,
            $data['proveedor'] ?? null,
            $data['activo'] ?? 1,
            $id
        ]);
    }
    
    /**
     * Elimina un ingrediente
     */
    public function delete($id) {
        $sql = "DELETE FROM ingredientes WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    /**
     * Obtiene ingredientes con stock bajo
     */
    public function getLowStock() {
        $sql = "SELECT * FROM ingredientes 
                WHERE stock_actual <= stock_minimo AND activo = 1 
                ORDER BY (stock_actual/stock_minimo) ASC";
        return $this->db->query($sql);
    }
    
    /**
     * Actualiza el stock de un ingrediente
     */
    public function updateStock($id, $cantidad, $tipo = 'suma') {
        if ($tipo == 'suma') {
            $sql = "UPDATE ingredientes SET stock_actual = stock_actual + ? WHERE id = ?";
        } else {
            $sql = "UPDATE ingredientes SET stock_actual = stock_actual - ? WHERE id = ?";
        }
        
        return $this->db->execute($sql, [$cantidad, $id]);
    }
}