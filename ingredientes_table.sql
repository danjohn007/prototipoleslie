-- ============================================
-- Script para crear tabla de ingredientes
-- Ejecutar en phpMyAdmin o tu cliente MySQL
-- ============================================

-- Crear tabla de ingredientes
CREATE TABLE IF NOT EXISTS ingredientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    unidad_medida VARCHAR(20) DEFAULT 'kg',
    costo_unitario DECIMAL(10, 2) NOT NULL DEFAULT 0,
    stock_actual DECIMAL(10, 2) DEFAULT 0,
    stock_minimo DECIMAL(10, 2) DEFAULT 0,
    proveedor VARCHAR(100),
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla de ingredientes por lote de producción
CREATE TABLE IF NOT EXISTS lote_ingredientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lote_id INT NOT NULL,
    ingrediente_id INT NOT NULL,
    cantidad_requerida DECIMAL(10, 2) NOT NULL,
    cantidad_utilizada DECIMAL(10, 2) DEFAULT 0,
    costo_ingrediente DECIMAL(10, 2) DEFAULT 0,
    observaciones TEXT,
    FOREIGN KEY (lote_id) REFERENCES produccion(id) ON DELETE CASCADE,
    FOREIGN KEY (ingrediente_id) REFERENCES ingredientes(id) ON DELETE RESTRICT,
    INDEX idx_lote (lote_id),
    INDEX idx_ingrediente (ingrediente_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar ingredientes de ejemplo
INSERT INTO ingredientes (nombre, descripcion, unidad_medida, costo_unitario, stock_actual, stock_minimo, proveedor) VALUES
('Leche Fresca', 'Leche fresca de vaca para producción de quesos', 'litros', 3.50, 500.00, 100.00, 'Granja San José'),
('Cuajo Líquido', 'Cuajo líquido para coagulación de la leche', 'ml', 0.15, 2000.00, 500.00, 'Productos Lácteos SA'),
('Sal Fina', 'Sal fina para salado de quesos', 'kg', 2.50, 50.00, 10.00, 'Sal Marina Perú'),
('Cultivos Lácticos', 'Cultivos lácticos para fermentación', 'sobres', 8.00, 100.00, 20.00, 'Biotech Dairy'),
('Cloruro de Calcio', 'Cloruro de calcio para mejorar coagulación', 'ml', 0.08, 1000.00, 200.00, 'Química Industrial'),
('Conservante Natural', 'Conservante natural para productos lácteos', 'ml', 0.25, 500.00, 100.00, 'Aditivos Naturales'),
('Envases Plásticos', 'Envases plásticos para empaque', 'unidades', 0.50, 1000.00, 200.00, 'Envases Perú'),
('Etiquetas', 'Etiquetas adhesivas para productos', 'unidades', 0.05, 2000.00, 500.00, 'Gráfica Lima');

-- Verificar las tablas creadas
DESCRIBE ingredientes;
DESCRIBE lote_ingredientes;

-- Mostrar los ingredientes insertados
SELECT * FROM ingredientes;