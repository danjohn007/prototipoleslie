-- ============================================
-- Script de actualización para la tabla de producción
-- Ejecutar en phpMyAdmin o tu cliente MySQL
-- ============================================

-- Agregar nuevas columnas a la tabla produccion
ALTER TABLE produccion 
ADD COLUMN tipo_produccion ENUM('granel', 'pieza', 'paquete') DEFAULT 'granel' AFTER cantidad_producida,
ADD COLUMN costo_materias_primas DECIMAL(10, 2) DEFAULT 0 AFTER estado,
ADD COLUMN costo_mano_obra DECIMAL(10, 2) DEFAULT 0 AFTER costo_materias_primas,
ADD COLUMN otros_costos DECIMAL(10, 2) DEFAULT 0 AFTER costo_mano_obra,
ADD COLUMN costo_total DECIMAL(10, 2) DEFAULT 0 AFTER otros_costos,
ADD COLUMN turno ENUM('matutino', 'vespertino', 'nocturno') DEFAULT 'matutino' AFTER responsable_id,
ADD COLUMN fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER observaciones,
ADD COLUMN fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER fecha_creacion;

-- Modificar la columna cantidad_producida para permitir decimales
ALTER TABLE produccion 
MODIFY COLUMN cantidad_producida DECIMAL(10, 2) NOT NULL;

-- Agregar el estado 'rechazado' a la enumeración
ALTER TABLE produccion 
MODIFY COLUMN estado ENUM('en_proceso', 'completado', 'inspeccion', 'aprobado', 'rechazado') DEFAULT 'en_proceso';

-- Agregar índice para el producto
ALTER TABLE produccion 
ADD INDEX idx_producto (producto_id);

-- Actualizar datos existentes con valores calculados (opcional)
UPDATE produccion p 
JOIN productos pr ON p.producto_id = pr.id 
SET 
    p.costo_materias_primas = p.cantidad_producida * pr.precio_unitario * 0.6,
    p.costo_mano_obra = p.cantidad_producida * 8,
    p.otros_costos = p.cantidad_producida * 2,
    p.costo_total = (p.cantidad_producida * pr.precio_unitario * 0.6) + (p.cantidad_producida * 8) + (p.cantidad_producida * 2)
WHERE p.costo_total = 0;

-- Verificar los cambios
DESCRIBE produccion;

-- Mostrar algunos registros actualizados
SELECT numero_lote, cantidad_producida, tipo_produccion, costo_total, turno 
FROM produccion 
LIMIT 5;