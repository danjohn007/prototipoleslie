<?php
/**
 * Modelo Client
 * Gestiona los clientes del sistema
 */

class Client {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtiene todos los clientes activos
     */
    public function getAll($filters = []) {
        $sql = "SELECT * FROM clientes WHERE 1=1";
        $params = [];
        
        // Filtrar por estado
        if (isset($filters['estado']) && !empty($filters['estado'])) {
            $sql .= " AND estado = ?";
            $params[] = $filters['estado'];
        } else {
            $sql .= " AND estado = 'activo'";
        }
        
        // Filtrar por tipo
        if (isset($filters['tipo_cliente']) && !empty($filters['tipo_cliente'])) {
            $sql .= " AND tipo_cliente = ?";
            $params[] = $filters['tipo_cliente'];
        }
        
        $sql .= " ORDER BY nombre ASC";
        
        return $this->db->query($sql, $params);
    }
    
    /**
     * Obtiene un cliente por ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM clientes WHERE id = ?";
        return $this->db->queryOne($sql, [$id]);
    }
    
    /**
     * Crea un nuevo cliente
     */
    public function create($data) {
        $sql = "INSERT INTO clientes (nombre, ruc, telefono, email, direccion, distrito, 
                ciudad, tipo_cliente, limite_credito, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['nombre'],
            $data['ruc'] ?? null,
            $data['telefono'] ?? null,
            $data['email'] ?? null,
            $data['direccion'] ?? null,
            $data['distrito'] ?? null,
            $data['ciudad'] ?? null,
            $data['tipo_cliente'] ?? 'bronce',
            $data['limite_credito'] ?? 0,
            $data['estado'] ?? 'activo'
        ];
        
        return $this->db->execute($sql, $params);
    }
}
