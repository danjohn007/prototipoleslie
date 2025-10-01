<?php
/**
 * Modelo Sale
 * Gestiona las ventas en punto (POS - Point of Sale)
 */

class Sale {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtiene todas las ventas (usando tabla pedidos con tipo específico)
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
        
        // Filtrar por fecha
        if (isset($filters['fecha']) && !empty($filters['fecha'])) {
            $sql .= " AND DATE(p.fecha_pedido) = ?";
            $params[] = $filters['fecha'];
        }
        
        $sql .= " ORDER BY p.fecha_pedido DESC";
        
        return $this->db->query($sql, $params);
    }
    
    /**
     * Obtiene estadísticas de ventas
     */
    public function getStats() {
        $sql = "SELECT 
                COUNT(*) as total_ventas,
                SUM(CASE WHEN DATE(fecha_pedido) = CURDATE() THEN 1 ELSE 0 END) as ventas_hoy,
                SUM(CASE WHEN DATE(fecha_pedido) = CURDATE() THEN total ELSE 0 END) as ingresos_hoy,
                SUM(CASE WHEN WEEK(fecha_pedido) = WEEK(CURDATE()) THEN total ELSE 0 END) as ingresos_semana,
                SUM(CASE WHEN MONTH(fecha_pedido) = MONTH(CURDATE()) THEN total ELSE 0 END) as ingresos_mes,
                AVG(total) as ticket_promedio
                FROM pedidos
                WHERE estado != 'cancelado'";
        
        return $this->db->queryOne($sql);
    }
    
    /**
     * Obtiene las ventas recientes
     */
    public function getRecent($limit = 10) {
        $sql = "SELECT p.*, c.nombre as cliente_nombre 
                FROM pedidos p
                LEFT JOIN clientes c ON p.cliente_id = c.id
                WHERE DATE(p.fecha_pedido) = CURDATE()
                ORDER BY p.fecha_pedido DESC
                LIMIT ?";
        
        return $this->db->query($sql, [$limit]);
    }
    
    /**
     * Obtiene productos más vendidos
     */
    public function getTopProducts($limit = 5) {
        $sql = "SELECT pr.nombre, pr.categoria, 
                SUM(pd.cantidad) as total_vendido,
                SUM(pd.subtotal) as ingresos_total
                FROM pedido_detalles pd
                JOIN productos pr ON pd.producto_id = pr.id
                JOIN pedidos p ON pd.pedido_id = p.id
                WHERE p.estado != 'cancelado'
                AND MONTH(p.fecha_pedido) = MONTH(CURDATE())
                GROUP BY pr.id, pr.nombre, pr.categoria
                ORDER BY total_vendido DESC
                LIMIT ?";
        
        return $this->db->query($sql, [$limit]);
    }
    
    /**
     * Crea una venta rápida (pedido en punto de venta)
     */
    public function createQuickSale($data) {
        $sql = "INSERT INTO pedidos (numero_pedido, cliente_id, usuario_id, fecha_entrega, 
                estado, subtotal, descuento, total, observaciones)
                VALUES (?, ?, ?, CURDATE(), 'entregado', ?, ?, ?, ?)";
        
        $params = [
            $data['numero_pedido'],
            $data['cliente_id'],
            $data['usuario_id'],
            $data['subtotal'],
            $data['descuento'] ?? 0,
            $data['total'],
            $data['observaciones'] ?? 'Venta en punto'
        ];
        
        return $this->db->execute($sql, $params);
    }
}
