<?php
/**
 * Modelo Production
 * Gestiona los lotes de producción del sistema
 */

class Production {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtiene todos los lotes de producción
     */
    public function getAll($filters = []) {
        $sql = "SELECT p.*, pr.nombre as producto_nombre, u.nombre as responsable_nombre 
                FROM produccion p
                LEFT JOIN productos pr ON p.producto_id = pr.id
                LEFT JOIN usuarios u ON p.responsable_id = u.id
                WHERE 1=1";
        $params = [];
        
        // Filtrar por estado
        if (isset($filters['estado']) && !empty($filters['estado'])) {
            $sql .= " AND p.estado = ?";
            $params[] = $filters['estado'];
        }
        
        // Filtrar por producto
        if (isset($filters['producto_id']) && !empty($filters['producto_id'])) {
            $sql .= " AND p.producto_id = ?";
            $params[] = $filters['producto_id'];
        }
        
        $sql .= " ORDER BY p.fecha_produccion DESC";
        
        return $this->db->query($sql, $params);
    }
    
    /**
     * Obtiene un lote por ID
     */
    public function getById($id) {
        $sql = "SELECT p.*, pr.nombre as producto_nombre, u.nombre as responsable_nombre 
                FROM produccion p
                LEFT JOIN productos pr ON p.producto_id = pr.id
                LEFT JOIN usuarios u ON p.responsable_id = u.id
                WHERE p.id = ?";
        return $this->db->queryOne($sql, [$id]);
    }
    
    /**
     * Crea un nuevo lote de producción
     */
    public function create($data) {
        $sql = "INSERT INTO produccion (numero_lote, producto_id, cantidad_producida, 
                fecha_produccion, fecha_vencimiento, estado, responsable_id, observaciones) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $result = $this->db->execute($sql, [
            $data['numero_lote'],
            $data['producto_id'],
            $data['cantidad_producida'],
            $data['fecha_produccion'],
            $data['fecha_vencimiento'] ?? null,
            $data['estado'] ?? 'en_proceso',
            $data['responsable_id'] ?? null,
            $data['observaciones'] ?? null
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Actualiza un lote de producción
     */
    public function update($id, $data) {
        $sql = "UPDATE produccion 
                SET producto_id = ?, cantidad_producida = ?, fecha_produccion = ?, 
                    fecha_vencimiento = ?, estado = ?, responsable_id = ?, observaciones = ?
                WHERE id = ?";
        
        return $this->db->execute($sql, [
            $data['producto_id'],
            $data['cantidad_producida'],
            $data['fecha_produccion'],
            $data['fecha_vencimiento'] ?? null,
            $data['estado'],
            $data['responsable_id'] ?? null,
            $data['observaciones'] ?? null,
            $id
        ]);
    }
    
    /**
     * Obtiene estadísticas de producción
     */
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total_lotes,
                    COUNT(CASE WHEN estado = 'completado' THEN 1 END) as lotes_completados,
                    COUNT(CASE WHEN estado = 'en_proceso' THEN 1 END) as lotes_en_proceso,
                    SUM(cantidad_producida) as total_produccion
                FROM produccion";
        
        return $this->db->queryOne($sql);
    }
    
    /**
     * Obtiene producción reciente
     */
    public function getRecent($limit = 5) {
        $sql = "SELECT p.*, pr.nombre as producto_nombre 
                FROM produccion p
                LEFT JOIN productos pr ON p.producto_id = pr.id
                ORDER BY p.fecha_produccion DESC
                LIMIT ?";
        return $this->db->query($sql, [$limit]);
    }
}
