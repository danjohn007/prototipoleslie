<?php
/**
 * Modelo ReturnModel
 * Gestiona los retornos de productos del sistema
 */

class ReturnModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Obtiene todos los retornos
     */
    public function getAll($filters = []) {
        $sql = "SELECT r.*, c.nombre as cliente_nombre, pr.nombre as producto_nombre,
                       u.nombre as responsable_nombre, p.numero_pedido
                FROM retornos r
                LEFT JOIN clientes c ON r.cliente_id = c.id
                LEFT JOIN productos pr ON r.producto_id = pr.id
                LEFT JOIN usuarios u ON r.responsable_id = u.id
                LEFT JOIN pedidos p ON r.pedido_id = p.id
                WHERE 1=1";
        $params = [];
        
        // Filtrar por estado
        if (isset($filters['estado']) && !empty($filters['estado'])) {
            $sql .= " AND r.estado = ?";
            $params[] = $filters['estado'];
        }
        
        // Filtrar por motivo
        if (isset($filters['motivo']) && !empty($filters['motivo'])) {
            $sql .= " AND r.motivo = ?";
            $params[] = $filters['motivo'];
        }
        
        $sql .= " ORDER BY r.fecha_retorno DESC";
        
        return $this->db->query($sql, $params);
    }
    
    /**
     * Obtiene un retorno por ID
     */
    public function getById($id) {
        $sql = "SELECT r.*, c.nombre as cliente_nombre, c.telefono, c.direccion,
                       pr.nombre as producto_nombre, u.nombre as responsable_nombre,
                       p.numero_pedido
                FROM retornos r
                LEFT JOIN clientes c ON r.cliente_id = c.id
                LEFT JOIN productos pr ON r.producto_id = pr.id
                LEFT JOIN usuarios u ON r.responsable_id = u.id
                LEFT JOIN pedidos p ON r.pedido_id = p.id
                WHERE r.id = ?";
        
        return $this->db->queryOne($sql, [$id]);
    }
    
    /**
     * Crea un nuevo retorno
     */
    public function create($data) {
        $sql = "INSERT INTO retornos (numero_retorno, pedido_id, cliente_id, producto_id, 
                cantidad, motivo, descripcion, estado, responsable_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['numero_retorno'],
            $data['pedido_id'] ?? null,
            $data['cliente_id'],
            $data['producto_id'],
            $data['cantidad'],
            $data['motivo'],
            $data['descripcion'] ?? '',
            $data['estado'] ?? 'registrado',
            $data['responsable_id'] ?? null
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    /**
     * Actualiza el estado de un retorno
     */
    public function updateStatus($id, $estado, $responsable_id = null) {
        if ($estado === 'completado') {
            $sql = "UPDATE retornos 
                    SET estado = ?, fecha_resolucion = NOW(), responsable_id = ?
                    WHERE id = ?";
            return $this->db->execute($sql, [$estado, $responsable_id, $id]);
        } else {
            $sql = "UPDATE retornos SET estado = ?, responsable_id = ? WHERE id = ?";
            return $this->db->execute($sql, [$estado, $responsable_id, $id]);
        }
    }
    
    /**
     * Obtiene estadísticas de retornos
     */
    public function getStats() {
        $sql = "SELECT 
                COUNT(*) as total_retornos,
                SUM(CASE WHEN estado = 'registrado' THEN 1 ELSE 0 END) as registrados,
                SUM(CASE WHEN estado = 'en_revision' THEN 1 ELSE 0 END) as en_revision,
                SUM(CASE WHEN estado = 'aprobado' THEN 1 ELSE 0 END) as aprobados,
                SUM(CASE WHEN estado = 'completado' THEN 1 ELSE 0 END) as completados,
                SUM(CASE WHEN DATE(fecha_retorno) = CURDATE() THEN 1 ELSE 0 END) as hoy,
                SUM(CASE WHEN MONTH(fecha_retorno) = MONTH(CURDATE()) THEN 1 ELSE 0 END) as mes_actual
                FROM retornos";
        
        return $this->db->queryOne($sql);
    }
    
    /**
     * Obtiene retornos recientes
     */
    public function getRecent($limit = 10) {
        $sql = "SELECT r.*, c.nombre as cliente_nombre, pr.nombre as producto_nombre
                FROM retornos r
                LEFT JOIN clientes c ON r.cliente_id = c.id
                LEFT JOIN productos pr ON r.producto_id = pr.id
                ORDER BY r.fecha_retorno DESC
                LIMIT ?";
        
        return $this->db->query($sql, [$limit]);
    }
    
    /**
     * Obtiene retornos por motivo
     */
    public function getByMotivo() {
        $sql = "SELECT motivo, COUNT(*) as cantidad
                FROM retornos
                WHERE MONTH(fecha_retorno) = MONTH(CURDATE())
                GROUP BY motivo
                ORDER BY cantidad DESC";
        
        return $this->db->query($sql);
    }
    
    /**
     * Obtiene el último ID insertado
     */
    public function getLastInsertId() {
        return $this->db->getConnection()->lastInsertId();
    }
}
