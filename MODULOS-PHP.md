# Módulos PHP con Conexión a Base de Datos

## Módulos Convertidos

Este documento describe los 4 módulos que han sido convertidos de páginas HTML estáticas a PHP con conexión a base de datos.

### 1. Producción (produccion.php)

**Descripción:** Módulo principal de gestión de producción e inventario.

**Funcionalidades:**
- Visualización de lotes de producción desde la base de datos
- KPIs dinámicos con estadísticas reales
- Alertas de stock bajo
- Tabla de lotes activos con datos reales
- Navegación a Nuevo Lote

**Datos Mostrados:**
- Total de lotes producidos
- Valor total del inventario
- Lotes completados y en proceso
- Productos con stock bajo
- Lista completa de lotes de producción con:
  - Número de lote
  - Producto asociado
  - Fechas de producción y vencimiento
  - Cantidad producida
  - Estado del lote
  - Responsable

**Modelos Utilizados:**
- `Production` - Gestión de lotes
- `Product` - Datos de productos

---

### 2. Nuevo Lote (nuevo-lote.php)

**Descripción:** Formulario para registrar un nuevo lote de producción.

**Funcionalidades:**
- Formulario completo con validación
- Generación automática de número de lote
- Selección de productos desde la base de datos
- Registro de movimiento de inventario automático al crear lote
- Validación de campos requeridos
- Mensajes de error y éxito

**Campos del Formulario:**
- Producto* (selección desde DB)
- Cantidad producida* (kg)
- Fecha de producción*
- Fecha de vencimiento
- Estado del lote
- Observaciones

**Proceso:**
1. Usuario completa formulario
2. Sistema valida datos
3. Genera número único de lote
4. Crea registro en tabla `produccion`
5. Registra movimiento de inventario (entrada)
6. Actualiza stock del producto
7. Redirige a producción con mensaje de éxito

**Controlador:** `ProductionController::createBatch()`

---

### 3. Gestión de Inventario (inventario.php)

**Descripción:** Módulo de visualización y gestión de inventario.

**Funcionalidades:**
- KPIs de inventario en tiempo real
- Alertas de stock bajo
- Tabla de movimientos recientes
- Visualización de productos con stock crítico

**Datos Mostrados:**
- Valor total del inventario
- Cantidad de productos en stock
- Productos con stock bajo
- Movimientos recientes de inventario con:
  - Fecha y hora del movimiento
  - Producto afectado
  - Tipo de movimiento (entrada/salida/merma/ajuste)
  - Cantidad
  - Motivo
  - Usuario responsable

**Modelos Utilizados:**
- `Inventory` - Gestión de movimientos
- `Product` - Datos de productos

---

### 4. Nuevo Producto (nuevo-producto.php)

**Descripción:** Formulario para registrar un nuevo producto en el catálogo.

**Funcionalidades:**
- Formulario completo con validación
- Categorización de productos
- Configuración de stock mínimo
- Validación de campos requeridos
- Mensajes de error y éxito

**Campos del Formulario:**
- Nombre del producto*
- Descripción
- Categoría* (quesos, lácteos, cremas, otros)
- Unidad de medida* (kg, litros, unidad, paquete)
- Precio unitario* (S/)
- Stock inicial
- Stock mínimo*

**Proceso:**
1. Usuario completa formulario
2. Sistema valida datos
3. Crea registro en tabla `productos`
4. Redirige a inventario con mensaje de éxito

**Controlador:** `ProductController::createProduct()`

---

## Modelos Creados

### Production.php
```php
- getAll($filters)           // Obtener todos los lotes
- getById($id)               // Obtener lote por ID
- create($data)              // Crear nuevo lote
- update($id, $data)         // Actualizar lote
- getStats()                 // Obtener estadísticas
- getRecent($limit)          // Obtener lotes recientes
```

### Inventory.php
```php
- getAll($filters)           // Obtener todos los movimientos
- getById($id)               // Obtener movimiento por ID
- create($data)              // Registrar nuevo movimiento
- getStats()                 // Obtener estadísticas
- getRecent($limit)          // Obtener movimientos recientes
- getTotalValue()            // Obtener valor total de inventario
```

### Product.php (ya existente, sin modificaciones)
```php
- getAll($filters)           // Obtener todos los productos
- getById($id)               // Obtener producto por ID
- create($data)              // Crear nuevo producto
- update($id, $data)         // Actualizar producto
- delete($id)                // Eliminar producto
- updateStock($id, $qty)     // Actualizar stock
- getLowStock()              // Obtener productos con stock bajo
- getStats()                 // Obtener estadísticas
```

---

## Controladores Creados

### ProductionController.php
```php
- index()                    // Mostrar página de producción
- newBatch()                 // Mostrar formulario nuevo lote
- createBatch()              // Procesar creación de lote
```

### InventoryController.php
```php
- index()                    // Mostrar página de inventario
```

### ProductController.php
```php
- newProduct()               // Mostrar formulario nuevo producto
- createProduct()            // Procesar creación de producto
```

---

## Flujo de Datos

### Creación de Lote de Producción
```
Usuario → Formulario nuevo-lote.php
         ↓
ProductionController::createBatch()
         ↓
Production::create() → Insertar en tabla `produccion`
         ↓
Inventory::create() → Insertar en tabla `inventario_movimientos`
         ↓
Product::updateStock() → Actualizar stock en tabla `productos`
         ↓
Redirigir a produccion.php con mensaje de éxito
```

### Visualización de Producción
```
Usuario → Accede a produccion.php
         ↓
Production::getAll() → Consulta tabla `produccion` con joins
         ↓
Product::getStats() → Consulta estadísticas de productos
         ↓
Product::getLowStock() → Consulta productos con stock bajo
         ↓
Renderizar vista con datos dinámicos
```

---

## Seguridad Implementada

1. **Autenticación:** Todos los módulos requieren autenticación
2. **Validación de entrada:** Sanitización con `clean_input()`
3. **Prepared statements:** Todas las consultas SQL usan PDO prepared statements
4. **Protección XSS:** `htmlspecialchars()` en todas las salidas
5. **Validación de formularios:** Validación del lado del servidor
6. **Manejo de errores:** Mensajes de error informativos sin exponer datos sensibles

---

## Base de Datos

### Tablas Utilizadas

**produccion:**
- id (PK)
- numero_lote (UNIQUE)
- producto_id (FK → productos)
- cantidad_producida
- fecha_produccion
- fecha_vencimiento
- estado (enum: en_proceso, completado, inspeccion, aprobado)
- responsable_id (FK → usuarios)
- observaciones

**inventario_movimientos:**
- id (PK)
- producto_id (FK → productos)
- tipo_movimiento (enum: entrada, salida, ajuste, merma)
- cantidad
- motivo
- referencia_id (ID de pedido o producción relacionado)
- usuario_id (FK → usuarios)
- fecha_movimiento

**productos:**
- id (PK)
- nombre
- descripcion
- categoria (enum: quesos, lacteos, cremas, otros)
- precio_unitario
- stock_actual
- stock_minimo
- unidad_medida
- activo
- fecha_creacion
- fecha_actualizacion

---

## Uso

### Requisitos
- PHP 7.0+
- MySQL 5.7+
- Apache con mod_rewrite
- Extensiones PHP: PDO, PDO_MySQL

### Configuración
1. Importar `database.sql` en MySQL
2. Configurar credenciales en `app/config/config.php`
3. Asegurar que Apache tenga permisos de lectura

### Acceso
1. Login: `http://localhost/prototipoleslie/`
2. Dashboard: `http://localhost/prototipoleslie/dashboard.php`
3. Producción: `http://localhost/prototipoleslie/produccion.php`
4. Nuevo Lote: `http://localhost/prototipoleslie/nuevo-lote.php`
5. Inventario: `http://localhost/prototipoleslie/inventario.php`
6. Nuevo Producto: `http://localhost/prototipoleslie/nuevo-producto.php`

---

## Cambios Realizados

### Archivos Modificados
- `dashboard.php` - Actualizado enlaces .html → .php

### Archivos Creados
- `app/models/Production.php`
- `app/models/Inventory.php`
- `app/controllers/ProductionController.php`
- `app/controllers/InventoryController.php`
- `app/controllers/ProductController.php`
- `produccion.php`
- `nuevo-lote.php`
- `inventario.php`
- `nuevo-producto.php`

### Archivos Originales (No Modificados)
- `produccion.html` - Página HTML original (mantener como referencia)
- `nuevo-lote.html` - Página HTML original (mantener como referencia)
- `inventario.html` - Página HTML original (mantener como referencia)
- `nuevo-producto.html` - Página HTML original (mantener como referencia)

---

## Próximos Pasos

Para expandir la funcionalidad:

1. **Edición de Lotes:** Implementar edición de lotes existentes
2. **Eliminación:** Implementar soft delete de lotes
3. **Búsqueda y Filtros:** Añadir filtros avanzados en listados
4. **Exportación:** Implementar exportación de reportes a PDF/Excel
5. **Gráficos:** Añadir visualizaciones de datos con Chart.js
6. **Validación Cliente:** Añadir validación JavaScript en formularios
7. **AJAX:** Implementar carga dinámica de datos sin recargar página
8. **API REST:** Usar la API REST existente en `api/products.php`

---

## Notas Técnicas

- **Compatibilidad:** Código compatible con PHP 7.0+
- **Base de Datos:** Usa PDO para compatibilidad con diferentes DBMS
- **Arquitectura:** Patrón MVC implementado
- **Sesiones:** Timeout de 1 hora configurable
- **URL Base:** Auto-detección configurada en `config.php`
- **Helpers:** Funciones de utilidad en `app/config/helpers.php`

---

## Soporte

Para problemas o consultas:
1. Revisar logs de PHP y Apache
2. Verificar configuración en `app/config/config.php`
3. Ejecutar `test-connection.php` para verificar conexión DB
4. Revisar documentación en `ARCHIVOS-CREADOS.md` y `RESUMEN-FINAL.md`
