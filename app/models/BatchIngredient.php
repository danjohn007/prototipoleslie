<?php
/**
 * Modelo BatchIngredient
 * Gestiona los ingredientes por lote de producción
 */

class BatchIngredient {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtiene ingredientes de un lote
     */
    public function getByBatch($lote_id) {
        $sql = "SELECT li.*, i.nombre, i.unidad_medida, i.costo_unitario
                FROM lote_ingredientes li
                JOIN ingredientes i ON li.ingrediente_id = i.id
                WHERE li.lote_id = ?
                ORDER BY i.nombre ASC";
        return $this->db->query($sql, [$lote_id]);
    }
    
    /**
     * Agrega un ingrediente a un lote
     */
    public function addToBatch($data) {
        $sql = "INSERT INTO lote_ingredientes (lote_id, ingrediente_id, cantidad_requerida, 
                cantidad_utilizada, costo_ingrediente, observaciones) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $result = $this->db->execute($sql, [
            $data['lote_id'],
            $data['ingrediente_id'],
            $data['cantidad_requerida'],
            $data['cantidad_utilizada'] ?? $data['cantidad_requerida'],
            $data['costo_ingrediente'] ?? 0,
            $data['observaciones'] ?? null
        ]);
        
        return $result ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Actualiza un ingrediente del lote
     */
    public function update($id, $data) {
        $sql = "UPDATE lote_ingredientes 
                SET cantidad_requerida = ?, cantidad_utilizada = ?, 
                    costo_ingrediente = ?, observaciones = ?
                WHERE id = ?";
        
        return $this->db->execute($sql, [
            $data['cantidad_requerida'],
            $data['cantidad_utilizada'],
            $data['costo_ingrediente'],
            $data['observaciones'] ?? null,
            $id
        ]);
    }
    
    /**
     * Elimina un ingrediente del lote
     */
    public function removeFromBatch($id) {
        $sql = "DELETE FROM lote_ingredientes WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    /**
     * Elimina todos los ingredientes de un lote
     */
    public function removeAllFromBatch($lote_id) {
        $sql = "DELETE FROM lote_ingredientes WHERE lote_id = ?";
        return $this->db->execute($sql, [$lote_id]);
    }
    
    /**
     * Calcula el costo total de ingredientes de un lote
     */
    public function getTotalCost($lote_id) {
        $sql = "SELECT SUM(costo_ingrediente) as total_costo
                FROM lote_ingredientes 
                WHERE lote_id = ?";
        $result = $this->db->queryOne($sql, [$lote_id]);
        return $result ? $result['total_costo'] : 0;
    }
    
    /**
     * Agrega múltiples ingredientes a un lote
     */
    public function addMultipleToBatch($lote_id, $ingredientes) {
        $this->db->beginTransaction();
        
        try {
            foreach ($ingredientes as $ingrediente) {
                $ingrediente['lote_id'] = $lote_id;
                $this->addToBatch($ingrediente);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log('Error adding ingredients to batch: ' . $e->getMessage());
            return false;
        }
    }
}