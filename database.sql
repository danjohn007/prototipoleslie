-- ============================================
-- Sistema de Logística Quesos Leslie
-- Base de datos MySQL 5.7
-- ============================================

-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS quesos_leslie_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quesos_leslie_db;

-- ============================================
-- Tabla de Usuarios
-- ============================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'operador', 'vendedor', 'logistica') DEFAULT 'operador',
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_sesion TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_rol (rol)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla de Productos
-- ============================================
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    categoria ENUM('quesos', 'lacteos', 'cremas', 'otros') DEFAULT 'quesos',
    precio_unitario DECIMAL(10, 2) NOT NULL,
    stock_actual INT DEFAULT 0,
    stock_minimo INT DEFAULT 10,
    unidad_medida VARCHAR(20) DEFAULT 'unidad',
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_categoria (categoria),
    INDEX idx_stock (stock_actual)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla de Clientes
-- ============================================
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    ruc VARCHAR(20),
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    distrito VARCHAR(100),
    ciudad VARCHAR(100),
    tipo_cliente ENUM('oro', 'plata', 'bronce') DEFAULT 'bronce',
    limite_credito DECIMAL(10, 2) DEFAULT 0,
    estado ENUM('activo', 'inactivo', 'suspendido') DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_compra TIMESTAMP NULL,
    INDEX idx_nombre (nombre),
    INDEX idx_tipo (tipo_cliente),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla de Pedidos
-- ============================================
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_pedido VARCHAR(50) UNIQUE NOT NULL,
    cliente_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_entrega DATE,
    estado ENUM('pendiente', 'confirmado', 'en_preparacion', 'en_ruta', 'entregado', 'cancelado') DEFAULT 'pendiente',
    subtotal DECIMAL(10, 2) DEFAULT 0,
    descuento DECIMAL(10, 2) DEFAULT 0,
    total DECIMAL(10, 2) NOT NULL,
    observaciones TEXT,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    INDEX idx_numero_pedido (numero_pedido),
    INDEX idx_estado (estado),
    INDEX idx_fecha_entrega (fecha_entrega)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla de Detalle de Pedidos
-- ============================================
CREATE TABLE IF NOT EXISTS pedido_detalles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT,
    INDEX idx_pedido (pedido_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla de Producción (Lotes)
-- ============================================
CREATE TABLE IF NOT EXISTS produccion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_lote VARCHAR(50) UNIQUE NOT NULL,
    producto_id INT NOT NULL,
    cantidad_producida DECIMAL(10, 2) NOT NULL,
    tipo_produccion ENUM('granel', 'pieza', 'paquete') DEFAULT 'granel',
    fecha_produccion DATE NOT NULL,
    fecha_vencimiento DATE,
    estado ENUM('en_proceso', 'completado', 'inspeccion', 'aprobado', 'rechazado') DEFAULT 'en_proceso',
    costo_materias_primas DECIMAL(10, 2) DEFAULT 0,
    costo_mano_obra DECIMAL(10, 2) DEFAULT 0,
    otros_costos DECIMAL(10, 2) DEFAULT 0,
    costo_total DECIMAL(10, 2) DEFAULT 0,
    responsable_id INT,
    turno ENUM('matutino', 'vespertino', 'nocturno') DEFAULT 'matutino',
    observaciones TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT,
    FOREIGN KEY (responsable_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_numero_lote (numero_lote),
    INDEX idx_estado (estado),
    INDEX idx_fecha_produccion (fecha_produccion),
    INDEX idx_producto (producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla de Inventario (Movimientos)
-- ============================================
CREATE TABLE IF NOT EXISTS inventario_movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida', 'ajuste', 'merma') NOT NULL,
    cantidad INT NOT NULL,
    motivo VARCHAR(200),
    referencia_id INT NULL COMMENT 'ID de pedido o producción relacionado',
    usuario_id INT,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_producto (producto_id),
    INDEX idx_tipo (tipo_movimiento),
    INDEX idx_fecha (fecha_movimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla de Rutas de Entrega
-- ============================================
CREATE TABLE IF NOT EXISTS rutas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_ruta VARCHAR(100) NOT NULL,
    conductor_id INT,
    vehiculo VARCHAR(50),
    fecha_ruta DATE NOT NULL,
    hora_inicio TIME,
    hora_fin TIME,
    estado ENUM('planificada', 'en_curso', 'completada', 'cancelada') DEFAULT 'planificada',
    distancia_total DECIMAL(10, 2),
    tiempo_estimado INT COMMENT 'En minutos',
    observaciones TEXT,
    FOREIGN KEY (conductor_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_fecha (fecha_ruta),
    INDEX idx_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla de Pedidos asignados a Rutas
-- ============================================
CREATE TABLE IF NOT EXISTS ruta_pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ruta_id INT NOT NULL,
    pedido_id INT NOT NULL,
    orden_entrega INT DEFAULT 1,
    hora_estimada TIME,
    hora_real_entrega TIME,
    estado_entrega ENUM('pendiente', 'entregado', 'no_entregado') DEFAULT 'pendiente',
    observaciones TEXT,
    FOREIGN KEY (ruta_id) REFERENCES rutas(id) ON DELETE CASCADE,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    INDEX idx_ruta (ruta_id),
    INDEX idx_pedido (pedido_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla de Retornos
-- ============================================
CREATE TABLE IF NOT EXISTS retornos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_retorno VARCHAR(50) UNIQUE NOT NULL,
    pedido_id INT,
    cliente_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    motivo ENUM('producto_dañado', 'error_pedido', 'caducidad', 'cliente_insatisfecho', 'otro') NOT NULL,
    descripcion TEXT,
    estado ENUM('registrado', 'en_revision', 'aprobado', 'rechazado', 'completado') DEFAULT 'registrado',
    fecha_retorno TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_resolucion TIMESTAMP NULL,
    responsable_id INT,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE SET NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT,
    FOREIGN KEY (responsable_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_numero_retorno (numero_retorno),
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha_retorno)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla de Encuestas de Satisfacción
-- ============================================
CREATE TABLE IF NOT EXISTS encuestas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    cliente_id INT NOT NULL,
    calificacion_general INT CHECK (calificacion_general BETWEEN 1 AND 5),
    calificacion_producto INT CHECK (calificacion_producto BETWEEN 1 AND 5),
    calificacion_entrega INT CHECK (calificacion_entrega BETWEEN 1 AND 5),
    calificacion_atencion INT CHECK (calificacion_atencion BETWEEN 1 AND 5),
    comentarios TEXT,
    fecha_encuesta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE SET NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    INDEX idx_fecha (fecha_encuesta),
    INDEX idx_calificacion (calificacion_general)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabla de Transacciones Financieras
-- ============================================
CREATE TABLE IF NOT EXISTS transacciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('ingreso', 'egreso') NOT NULL,
    categoria VARCHAR(100),
    monto DECIMAL(10, 2) NOT NULL,
    descripcion TEXT,
    pedido_id INT NULL,
    cliente_id INT NULL,
    metodo_pago ENUM('efectivo', 'transferencia', 'tarjeta', 'credito') DEFAULT 'efectivo',
    fecha_transaccion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE SET NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_tipo (tipo),
    INDEX idx_fecha (fecha_transaccion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Insertar Datos de Ejemplo
-- ============================================

-- Usuarios de ejemplo (password: admin123)
INSERT INTO usuarios (nombre, email, password, rol) VALUES
('Leslie Lugo', 'leslie@quesosleslie.com', '$2y$10$8aWoSlCeVfRVlP/L3maWKOQSrvBq1TfjJ59rRJPimyAss92eb2Pne', 'admin'),
('Juan Pérez', 'juan@quesosleslie.com', '$2y$10$8aWoSlCeVfRVlP/L3maWKOQSrvBq1TfjJ59rRJPimyAss92eb2Pne', 'operador'),
('María García', 'maria@quesosleslie.com', '$2y$10$8aWoSlCeVfRVlP/L3maWKOQSrvBq1TfjJ59rRJPimyAss92eb2Pne', 'vendedor'),
('Carlos Rodríguez', 'carlos@quesosleslie.com', '$2y$10$8aWoSlCeVfRVlP/L3maWKOQSrvBq1TfjJ59rRJPimyAss92eb2Pne', 'logistica');

-- Productos de ejemplo
INSERT INTO productos (nombre, descripcion, categoria, precio_unitario, stock_actual, stock_minimo) VALUES
('Queso Fresco', 'Queso fresco artesanal de 500g', 'quesos', 15.50, 120, 20),
('Queso Maduro', 'Queso maduro de 1kg', 'quesos', 35.00, 80, 15),
('Crema de Leche', 'Crema de leche natural 250ml', 'cremas', 8.50, 200, 30),
('Yogurt Natural', 'Yogurt natural 1L', 'lacteos', 12.00, 150, 25),
('Mantequilla', 'Mantequilla artesanal 250g', 'lacteos', 10.00, 100, 20),
('Queso Parmesano', 'Queso parmesano rallado 200g', 'quesos', 18.00, 60, 10),
('Leche Entera', 'Leche entera pasteurizada 1L', 'lacteos', 5.50, 300, 50),
('Queso Mozzarella', 'Queso mozzarella 500g', 'quesos', 22.00, 90, 15);

-- Clientes de ejemplo
INSERT INTO clientes (nombre, ruc, telefono, email, direccion, distrito, ciudad, tipo_cliente, limite_credito) VALUES
('Bodega La Esperanza', '20123456789', '999-888-777', 'contacto@laesperanza.com', 'Av. Principal 123', 'San Isidro', 'Lima', 'oro', 15000.00),
('Tienda Don Pepe', '20987654321', '987-654-321', 'donpepe@email.com', 'Jr. Los Pinos 456', 'Miraflores', 'Lima', 'plata', 8000.00),
('Minimarket El Trébol', '20456789123', '965-432-198', 'eltrebol@gmail.com', 'Calle Las Flores 789', 'San Borja', 'Lima', 'bronce', 3000.00),
('Supermercado Central', '20789123456', '955-123-456', 'central@super.com', 'Av. Arequipa 321', 'Lince', 'Lima', 'oro', 20000.00),
('Bodega María', '20321654987', '944-567-890', 'bodegamaria@email.com', 'Jr. San Martín 654', 'Surco', 'Lima', 'bronce', 2500.00);

-- Pedidos de ejemplo
INSERT INTO pedidos (numero_pedido, cliente_id, usuario_id, fecha_entrega, estado, total) VALUES
('PED-2024-001', 1, 3, '2024-01-16', 'en_ruta', 450.50),
('PED-2024-002', 2, 3, '2024-01-16', 'confirmado', 320.00),
('PED-2024-003', 3, 3, '2024-01-17', 'pendiente', 180.50),
('PED-2024-004', 4, 3, '2024-01-17', 'en_preparacion', 850.00),
('PED-2024-005', 5, 3, '2024-01-18', 'pendiente', 125.00);

-- Detalle de pedidos
INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(1, 1, 10, 15.50, 155.00),
(1, 3, 20, 8.50, 170.00),
(1, 5, 15, 10.00, 150.00),
(2, 2, 5, 35.00, 175.00),
(2, 4, 10, 12.00, 120.00),
(3, 1, 8, 15.50, 124.00),
(3, 7, 12, 5.50, 66.00),
(4, 2, 15, 35.00, 525.00),
(4, 6, 20, 18.00, 360.00),
(5, 3, 10, 8.50, 85.00),
(5, 5, 5, 10.00, 50.00);

-- Producción (lotes) de ejemplo
INSERT INTO produccion (numero_lote, producto_id, cantidad_producida, fecha_produccion, fecha_vencimiento, estado, responsable_id) VALUES
('LOTE-2024-001', 1, 50, '2024-01-10', '2024-02-10', 'aprobado', 2),
('LOTE-2024-002', 2, 30, '2024-01-11', '2024-04-11', 'aprobado', 2),
('LOTE-2024-003', 3, 100, '2024-01-12', '2024-02-12', 'completado', 2),
('LOTE-2024-004', 4, 80, '2024-01-13', '2024-01-28', 'en_proceso', 2);

-- Rutas de entrega
INSERT INTO rutas (nombre_ruta, conductor_id, vehiculo, fecha_ruta, hora_inicio, estado, distancia_total, tiempo_estimado) VALUES
('Ruta Norte - Lima', 4, 'VAN-001', '2024-01-16', '08:00:00', 'en_curso', 45.5, 180),
('Ruta Sur - Lima', 4, 'VAN-002', '2024-01-16', '09:00:00', 'planificada', 38.2, 150),
('Ruta Centro - Lima', 4, 'VAN-001', '2024-01-17', '08:00:00', 'planificada', 25.8, 120);

-- Asignar pedidos a rutas
INSERT INTO ruta_pedidos (ruta_id, pedido_id, orden_entrega, hora_estimada, estado_entrega) VALUES
(1, 1, 1, '09:30:00', 'pendiente'),
(1, 2, 2, '10:45:00', 'pendiente'),
(2, 4, 1, '10:00:00', 'pendiente'),
(3, 3, 1, '09:00:00', 'pendiente'),
(3, 5, 2, '10:15:00', 'pendiente');

-- Retornos de ejemplo
INSERT INTO retornos (numero_retorno, pedido_id, cliente_id, producto_id, cantidad, motivo, descripcion, estado) VALUES
('R-2024-001', 1, 1, 3, 2, 'producto_dañado', 'Envases rotos durante transporte', 'completado'),
('R-2024-002', NULL, 2, 1, 5, 'error_pedido', 'Cliente recibió producto equivocado', 'en_revision');

-- Encuestas de satisfacción
INSERT INTO encuestas (pedido_id, cliente_id, calificacion_general, calificacion_producto, calificacion_entrega, calificacion_atencion, comentarios) VALUES
(1, 1, 5, 5, 4, 5, 'Excelente servicio, productos frescos y de calidad'),
(2, 2, 4, 4, 4, 5, 'Muy buen servicio, entrega puntual');

-- Transacciones financieras
INSERT INTO transacciones (tipo, categoria, monto, descripcion, pedido_id, cliente_id, metodo_pago, usuario_id) VALUES
('ingreso', 'Venta', 450.50, 'Pago pedido PED-2024-001', 1, 1, 'transferencia', 3),
('ingreso', 'Venta', 320.00, 'Pago pedido PED-2024-002', 2, 2, 'efectivo', 3),
('egreso', 'Compra insumos', 500.00, 'Compra de leche para producción', NULL, NULL, 'transferencia', 2),
('egreso', 'Combustible', 80.00, 'Combustible para vehículos de reparto', NULL, NULL, 'efectivo', 4);

-- Movimientos de inventario
INSERT INTO inventario_movimientos (producto_id, tipo_movimiento, cantidad, motivo, usuario_id) VALUES
(1, 'entrada', 50, 'Producción lote LOTE-2024-001', 2),
(2, 'entrada', 30, 'Producción lote LOTE-2024-002', 2),
(3, 'entrada', 100, 'Producción lote LOTE-2024-003', 2),
(1, 'salida', 10, 'Pedido PED-2024-001', 3),
(3, 'salida', 20, 'Pedido PED-2024-001', 3),
(2, 'salida', 5, 'Pedido PED-2024-002', 3);

-- ============================================
-- FIN DEL SCRIPT
-- ============================================
