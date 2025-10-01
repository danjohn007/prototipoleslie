# API REST - Sistema Quesos Leslie

Documentación de endpoints API para integraciones.

## Autenticación

Todos los endpoints requieren autenticación mediante sesión activa.

**Headers requeridos:**
```
Cookie: PHPSESSID=<session_id>
Content-Type: application/json
```

## Endpoints Disponibles

### Productos

#### Listar Productos
```http
GET /api/products.php?action=list
```

**Parámetros opcionales:**
- `categoria` - Filtrar por categoría (quesos, lacteos, cremas, otros)
- `stock_bajo` - Mostrar solo productos con stock bajo (true/false)

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nombre": "Queso Fresco",
      "descripcion": "Queso fresco artesanal de 500g",
      "categoria": "quesos",
      "precio_unitario": "15.50",
      "stock_actual": 120,
      "stock_minimo": 20,
      "unidad_medida": "unidad",
      "activo": 1
    }
  ],
  "count": 1
}
```

#### Obtener Producto
```http
GET /api/products.php?action=get&id=1
```

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "nombre": "Queso Fresco",
    "descripcion": "Queso fresco artesanal de 500g",
    "categoria": "quesos",
    "precio_unitario": "15.50",
    "stock_actual": 120,
    "stock_minimo": 20
  }
}
```

#### Estadísticas de Productos
```http
GET /api/products.php?action=stats
```

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "data": {
    "total_productos": 8,
    "productos_bajo_stock": 2,
    "valor_inventario": "15350.00"
  }
}
```

#### Productos con Stock Bajo
```http
GET /api/products.php?action=low-stock
```

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 6,
      "nombre": "Queso Parmesano",
      "stock_actual": 10,
      "stock_minimo": 10
    }
  ],
  "count": 1
}
```

#### Crear Producto
```http
POST /api/products.php
```

**Body (JSON):**
```json
{
  "nombre": "Nuevo Producto",
  "descripcion": "Descripción del producto",
  "categoria": "quesos",
  "precio_unitario": 25.00,
  "stock_actual": 50,
  "stock_minimo": 15,
  "unidad_medida": "unidad"
}
```

**Respuesta exitosa (201):**
```json
{
  "success": true,
  "message": "Producto creado exitosamente",
  "id": 9
}
```

#### Actualizar Producto
```http
PUT /api/products.php
```

**Body (JSON):**
```json
{
  "id": 9,
  "nombre": "Producto Actualizado",
  "descripcion": "Nueva descripción",
  "categoria": "lacteos",
  "precio_unitario": 30.00,
  "stock_actual": 60,
  "stock_minimo": 20,
  "unidad_medida": "kilogramo"
}
```

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "message": "Producto actualizado exitosamente"
}
```

#### Eliminar Producto
```http
DELETE /api/products.php?id=9
```

**Respuesta exitosa (200):**
```json
{
  "success": true,
  "message": "Producto eliminado exitosamente"
}
```

## Códigos de Estado HTTP

- `200 OK` - Solicitud exitosa
- `201 Created` - Recurso creado exitosamente
- `400 Bad Request` - Datos inválidos
- `401 Unauthorized` - No autenticado
- `404 Not Found` - Recurso no encontrado
- `405 Method Not Allowed` - Método HTTP no permitido
- `500 Internal Server Error` - Error del servidor

## Manejo de Errores

**Respuesta de error:**
```json
{
  "error": "Mensaje de error descriptivo"
}
```

## Ejemplo de Uso con JavaScript

### Fetch API

```javascript
// Listar productos
fetch('/api/products.php?action=list')
  .then(response => response.json())
  .then(data => {
    console.log(data.data);
  })
  .catch(error => console.error('Error:', error));

// Crear producto
fetch('/api/products.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    nombre: 'Nuevo Producto',
    categoria: 'quesos',
    precio_unitario: 25.00,
    stock_actual: 50
  })
})
  .then(response => response.json())
  .then(data => {
    console.log('Producto creado:', data.id);
  });
```

### jQuery Ajax

```javascript
// Listar productos
$.ajax({
  url: '/api/products.php',
  data: { action: 'list' },
  success: function(response) {
    console.log(response.data);
  }
});

// Crear producto
$.ajax({
  url: '/api/products.php',
  method: 'POST',
  contentType: 'application/json',
  data: JSON.stringify({
    nombre: 'Nuevo Producto',
    categoria: 'quesos',
    precio_unitario: 25.00
  }),
  success: function(response) {
    console.log('Producto creado:', response.id);
  }
});
```

## Notas

- Todas las respuestas están en formato JSON
- Los timestamps se devuelven en formato ISO 8601
- Los precios están en formato decimal (2 decimales)
- La autenticación se maneja mediante sesiones PHP
- Se recomienda usar HTTPS en producción

## Próximos Endpoints (En Desarrollo)

- `/api/clients.php` - Gestión de clientes
- `/api/orders.php` - Gestión de pedidos
- `/api/routes.php` - Gestión de rutas
- `/api/production.php` - Gestión de producción
- `/api/inventory.php` - Movimientos de inventario
- `/api/reports.php` - Generación de reportes
