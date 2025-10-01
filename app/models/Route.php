<?php
/**
 * Modelo Route
 * Gestiona las rutas de entrega del sistema
 */

class Route {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtiene todas las rutas
     */
    public function getAll($filters = []) {
        $sql = "SELECT r.*, u.nombre as conductor_nombre 
                FROM rutas r
                LEFT JOIN usuarios u ON r.conductor_id = u.id
                WHERE 1=1";
        $params = [];
        
        // Filtrar por estado
        if (isset($filters['estado']) && !empty($filters['estado'])) {
            $sql .= " AND r.estado = ?";
            $params[] = $filters['estado'];
        }
        
        // Filtrar por fecha
        if (isset($filters['fecha']) && !empty($filters['fecha'])) {
            $sql .= " AND r.fecha_ruta = ?";
            $params[] = $filters['fecha'];
        }
        
        $sql .= " ORDER BY r.fecha_ruta DESC, r.hora_inicio DESC";
        
        return $this->db->query($sql, $params);
    }
    
    /**
     * Obtiene una ruta por ID
     */
    public function getById($id) {
        $sql = "SELECT r.*, u.nombre as conductor_nombre 
                FROM rutas r
                LEFT JOIN usuarios u ON r.conductor_id = u.id
                WHERE r.id = ?";
        
        return $this->db->queryOne($sql, [$id]);
    }
    
    /**
     * Obtiene los pedidos asignados a una ruta
     */
    public function getPedidos($ruta_id) {
        $sql = "SELECT rp.*, p.numero_pedido, p.total, c.nombre as cliente_nombre, 
                       c.direccion, c.distrito
                FROM ruta_pedidos rp
                JOIN pedidos p ON rp.pedido_id = p.id
                JOIN clientes c ON p.cliente_id = c.id
                WHERE rp.ruta_id = ?
                ORDER BY rp.orden_entrega ASC";
        
        return $this->db->query($sql, [$ruta_id]);
    }
    
    /**
     * Crea una nueva ruta
     */
    public function create($data) {
        $sql = "INSERT INTO rutas (nombre_ruta, conductor_id, vehiculo, fecha_ruta, 
                hora_inicio, hora_fin, estado, distancia_total, tiempo_estimado, observaciones)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['nombre_ruta'],
            $data['conductor_id'] ?? null,
            $data['vehiculo'] ?? null,
            $data['fecha_ruta'],
            $data['hora_inicio'] ?? null,
            $data['hora_fin'] ?? null,
            $data['estado'] ?? 'planificada',
            $data['distancia_total'] ?? null,
            $data['tiempo_estimado'] ?? null,
            $data['observaciones'] ?? ''
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    /**
     * Asigna un pedido a una ruta
     */
    public function assignPedido($data) {
        $sql = "INSERT INTO ruta_pedidos (ruta_id, pedido_id, orden_entrega, 
                hora_estimada, estado_entrega, observaciones)
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['ruta_id'],
            $data['pedido_id'],
            $data['orden_entrega'] ?? 1,
            $data['hora_estimada'] ?? null,
            $data['estado_entrega'] ?? 'pendiente',
            $data['observaciones'] ?? ''
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    /**
     * Actualiza el estado de una ruta
     */
    public function updateStatus($id, $estado) {
        $sql = "UPDATE rutas SET estado = ? WHERE id = ?";
        return $this->db->execute($sql, [$estado, $id]);
    }
    
    /**
     * Obtiene estadísticas de rutas
     */
    public function getStats() {
        $sql = "SELECT 
                COUNT(*) as total_rutas,
                SUM(CASE WHEN estado = 'planificada' THEN 1 ELSE 0 END) as planificadas,
                SUM(CASE WHEN estado = 'en_curso' THEN 1 ELSE 0 END) as en_curso,
                SUM(CASE WHEN estado = 'completada' THEN 1 ELSE 0 END) as completadas,
                SUM(CASE WHEN DATE(fecha_ruta) = CURDATE() THEN 1 ELSE 0 END) as hoy,
                AVG(distancia_total) as distancia_promedio,
                AVG(tiempo_estimado) as tiempo_promedio
                FROM rutas";
        
        return $this->db->queryOne($sql);
    }
    
    /**
     * Obtiene rutas recientes
     */
    public function getRecent($limit = 5) {
        $sql = "SELECT r.*, u.nombre as conductor_nombre 
                FROM rutas r
                LEFT JOIN usuarios u ON r.conductor_id = u.id
                ORDER BY r.fecha_ruta DESC, r.hora_inicio DESC
                LIMIT ?";
        
        return $this->db->query($sql, [$limit]);
    }
    
    /**
     * Obtiene el último ID insertado
     */
    public function getLastInsertId() {
        return $this->db->getConnection()->lastInsertId();
    }
}
