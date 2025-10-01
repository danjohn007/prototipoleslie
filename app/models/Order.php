<?php
/**
 * Modelo Order
 * Gestiona los pedidos del sistema
 */

class Order {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtiene todos los pedidos
     */
    public function getAll($filters = []) {
        $sql = "SELECT p.*, c.nombre as cliente_nombre, u.nombre as usuario_nombre 
                FROM pedidos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                LEFT JOIN usuarios u ON p.usuario_id = u.id
                WHERE 1=1";
        $params = [];
        
        // Filtrar por estado
        if (isset($filters['estado']) && !empty($filters['estado'])) {
            $sql .= " AND p.estado = ?";
            $params[] = $filters['estado'];
        }
        
        // Filtrar por cliente
        if (isset($filters['cliente_id']) && !empty($filters['cliente_id'])) {
            $sql .= " AND p.cliente_id = ?";
            $params[] = $filters['cliente_id'];
        }
        
        $sql .= " ORDER BY p.fecha_pedido DESC";
        
        return $this->db->query($sql, $params);
    }
    
    /**
     * Obtiene un pedido por ID
     */
    public function getById($id) {
        $sql = "SELECT p.*, c.nombre as cliente_nombre, c.direccion, c.telefono,
                       u.nombre as usuario_nombre 
                FROM pedidos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                LEFT JOIN usuarios u ON p.usuario_id = u.id
                WHERE p.id = ?";
        
        return $this->db->queryOne($sql, [$id]);
    }
    
    /**
     * Obtiene los detalles de un pedido
     */
    public function getDetails($pedido_id) {
        $sql = "SELECT pd.*, pr.nombre as producto_nombre, pr.unidad_medida
                FROM pedido_detalles pd
                LEFT JOIN productos pr ON pd.producto_id = pr.id
                WHERE pd.pedido_id = ?";
        
        return $this->db->query($sql, [$pedido_id]);
    }
    
    /**
     * Crea un nuevo pedido
     */
    public function create($data) {
        $sql = "INSERT INTO pedidos (numero_pedido, cliente_id, usuario_id, fecha_entrega, 
                estado, subtotal, descuento, total, observaciones)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['numero_pedido'],
            $data['cliente_id'],
            $data['usuario_id'],
            $data['fecha_entrega'],
            $data['estado'] ?? 'pendiente',
            $data['subtotal'],
            $data['descuento'] ?? 0,
            $data['total'],
            $data['observaciones'] ?? ''
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    /**
     * Agrega detalle a un pedido
     */
    public function addDetail($data) {
        $sql = "INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, 
                precio_unitario, subtotal)
                VALUES (?, ?, ?, ?, ?)";
        
        $params = [
            $data['pedido_id'],
            $data['producto_id'],
            $data['cantidad'],
            $data['precio_unitario'],
            $data['subtotal']
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    /**
     * Actualiza el estado de un pedido
     */
    public function updateStatus($id, $estado) {
        $sql = "UPDATE pedidos SET estado = ? WHERE id = ?";
        return $this->db->execute($sql, [$estado, $id]);
    }
    
    /**
     * Obtiene estadísticas de pedidos
     */
    public function getStats() {
        $sql = "SELECT 
                COUNT(*) as total_pedidos,
                SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado = 'confirmado' THEN 1 ELSE 0 END) as confirmados,
                SUM(CASE WHEN estado = 'en_preparacion' THEN 1 ELSE 0 END) as en_preparacion,
                SUM(CASE WHEN estado = 'en_ruta' THEN 1 ELSE 0 END) as en_ruta,
                SUM(CASE WHEN estado = 'entregado' THEN 1 ELSE 0 END) as entregados,
                SUM(CASE WHEN DATE(fecha_pedido) = CURDATE() THEN 1 ELSE 0 END) as hoy,
                SUM(total) as valor_total
                FROM pedidos";
        
        return $this->db->queryOne($sql);
    }
    
    /**
     * Obtiene pedidos recientes
     */
    public function getRecent($limit = 5) {
        $sql = "SELECT p.*, c.nombre as cliente_nombre 
                FROM pedidos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                ORDER BY p.fecha_pedido DESC
                LIMIT ?";
        
        return $this->db->query($sql, [$limit]);
    }
    
    /**
     * Obtiene pedidos por estado
     */
    public function getByStatus($estado) {
        return $this->getAll(['estado' => $estado]);
    }
    
    /**
     * Obtiene el último ID insertado
     */
    public function getLastInsertId() {
        return $this->db->getConnection()->lastInsertId();
    }
}
