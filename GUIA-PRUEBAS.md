# Guía de Pruebas - Módulos PHP

Esta guía te ayudará a probar todos los módulos convertidos de HTML a PHP con conexión a base de datos.

## Pre-requisitos

Antes de empezar las pruebas, asegúrate de:

1. ✅ PHP 7.0+ instalado
2. ✅ MySQL 5.7+ instalado y ejecutándose
3. ✅ Apache con mod_rewrite habilitado
4. ✅ Base de datos `quesos_leslie_db` creada
5. ✅ Archivo `database.sql` importado
6. ✅ Credenciales configuradas en `app/config/config.php`

---

## Paso 1: Verificar Configuración

### 1.1 Test de Conexión
```
URL: http://localhost/prototipoleslie/test-connection.php
```

**Resultado Esperado:**
- ✅ Versión de PHP >= 7.0
- ✅ Extensión PDO instalada
- ✅ Extensión PDO_MySQL instalada
- ✅ Conexión a base de datos exitosa
- ✅ 12 tablas creadas
- ✅ URL base configurada correctamente

Si todo está en verde, continúa con las pruebas.

---

## Paso 2: Login y Autenticación

### 2.1 Acceder al Sistema
```
URL: http://localhost/prototipoleslie/
```

**Credenciales de Prueba:**
```
Email: leslie@quesosleslie.com
Password: admin123
```

**Resultado Esperado:**
- ✅ Formulario de login visible
- ✅ Login exitoso
- ✅ Redirección a dashboard.php
- ✅ Nombre del usuario visible en sidebar

---

## Paso 3: Probar Módulo de Producción

### 3.1 Ver Página de Producción
```
URL: http://localhost/prototipoleslie/produccion.php
```

**Verificar:**
- ✅ KPIs muestran números reales (no hardcoded)
- ✅ Tabla de lotes muestra datos de la base de datos
- ✅ Si hay lotes en DB, se ven en la tabla
- ✅ Si no hay lotes, tabla muestra "No hay lotes registrados"
- ✅ Si hay productos con stock bajo, se muestra alerta
- ✅ Botón "Nuevo Lote" funciona y navega a nuevo-lote.php

**Campos a Validar en KPIs:**
```php
- Producción Total: Suma de cantidad_producida de todos los lotes
- Inventario Total: Valor monetario del inventario (suma de stock * precio)
- Lotes Activos: COUNT de registros en tabla produccion
- Stock Bajo: COUNT de productos donde stock_actual <= stock_minimo
```

### 3.2 Crear Nuevo Lote
```
URL: http://localhost/prototipoleslie/nuevo-lote.php
```

**Pasos:**
1. Seleccionar un producto del dropdown
2. Ingresar cantidad (ej: 50)
3. Seleccionar fecha de producción (hoy por defecto)
4. Opcionalmente: Ingresar fecha de vencimiento
5. Seleccionar estado (por defecto: "En Proceso")
6. Opcionalmente: Agregar observaciones
7. Click en "Guardar Lote"

**Resultado Esperado:**
- ✅ Redirección a produccion.php
- ✅ Mensaje de éxito: "Lote creado exitosamente con número: LOTE-XXXX-XXXX"
- ✅ Nuevo lote visible en la tabla de producción
- ✅ Stock del producto incrementado automáticamente
- ✅ Movimiento de inventario registrado (tipo: entrada)

**Validaciones a Probar:**
```
- Dejar producto vacío → Error: "El producto es requerido"
- Cantidad 0 o negativa → Error: "La cantidad debe ser mayor a 0"
- Fecha vacía → Error: "La fecha de producción es requerida"
```

### 3.3 Verificar en Base de Datos
```sql
-- Ver nuevo lote creado
SELECT * FROM produccion ORDER BY id DESC LIMIT 1;

-- Ver movimiento de inventario generado
SELECT * FROM inventario_movimientos ORDER BY id DESC LIMIT 1;

-- Ver stock actualizado del producto
SELECT id, nombre, stock_actual FROM productos WHERE id = [ID_PRODUCTO];
```

---

## Paso 4: Probar Módulo de Inventario

### 4.1 Ver Página de Inventario
```
URL: http://localhost/prototipoleslie/inventario.php
```

**Verificar:**
- ✅ KPIs muestran números reales
- ✅ Valor Total Inventario calculado correctamente
- ✅ Productos en Stock cuenta registros activos
- ✅ Stock Bajo muestra productos donde stock_actual <= stock_minimo
- ✅ Tabla de movimientos muestra últimos 15 movimientos
- ✅ Si hay productos con stock bajo, se muestra alerta
- ✅ Botón "Añadir Producto" funciona

**Campos a Validar en Tabla de Movimientos:**
```
- Fecha: Formato dd/mm/YYYY HH:MM
- Producto: Nombre del producto desde la relación
- Tipo: "Entrada", "Salida", "Merma", "Ajuste"
- Cantidad: Con signo + o - según tipo
- Motivo: Descripción del movimiento
- Usuario: Nombre del usuario responsable
```

### 4.2 Crear Nuevo Producto
```
URL: http://localhost/prototipoleslie/nuevo-producto.php
```

**Pasos:**
1. Ingresar nombre (ej: "Queso Parmesano")
2. Ingresar descripción (opcional)
3. Seleccionar categoría (ej: "Quesos")
4. Seleccionar unidad de medida (ej: "kg")
5. Ingresar precio unitario (ej: 45.50)
6. Ingresar stock inicial (ej: 0)
7. Ingresar stock mínimo (ej: 10)
8. Click en "Guardar Producto"

**Resultado Esperado:**
- ✅ Redirección a inventario.php
- ✅ Mensaje de éxito: "Producto creado exitosamente"
- ✅ Nuevo producto disponible en dropdown de nuevo-lote.php

**Validaciones a Probar:**
```
- Nombre vacío → Error: "El nombre del producto es requerido"
- Categoría vacía → Error: "La categoría es requerida"
- Precio 0 o negativo → Error: "El precio debe ser mayor a 0"
```

### 4.3 Verificar en Base de Datos
```sql
-- Ver nuevo producto creado
SELECT * FROM productos ORDER BY id DESC LIMIT 1;

-- Verificar que aparece en lista de productos
SELECT id, nombre, categoria, precio_unitario, stock_actual 
FROM productos WHERE activo = 1;
```

---

## Paso 5: Flujo Completo de Producción

### 5.1 Escenario: Crear Producto y Producir Lote

**Paso A: Crear Producto**
1. Ir a `nuevo-producto.php`
2. Crear "Queso Gouda Premium"
3. Precio: S/ 120.00
4. Stock inicial: 0
5. Stock mínimo: 15
6. Guardar

**Paso B: Verificar en Inventario**
1. Ir a `inventario.php`
2. Verificar que aparece alerta de stock bajo (0 < 15)
3. Verificar que KPI "Stock Bajo" incrementó en 1

**Paso C: Crear Lote de Producción**
1. Ir a `nuevo-lote.php`
2. Seleccionar "Queso Gouda Premium"
3. Cantidad: 50 kg
4. Fecha: Hoy
5. Estado: Completado
6. Observación: "Primer lote de prueba"
7. Guardar

**Paso D: Verificar Resultados**
1. Ir a `produccion.php`
2. ✅ Ver nuevo lote en la tabla
3. Ir a `inventario.php`
4. ✅ Stock del producto ahora es 50 kg
5. ✅ Alerta de stock bajo desaparece (50 > 15)
6. ✅ Ver movimiento de entrada en tabla
7. ✅ KPI "Stock Bajo" disminuye en 1

**Verificar en Base de Datos:**
```sql
-- Ver el producto con stock actualizado
SELECT id, nombre, stock_actual, stock_minimo 
FROM productos 
WHERE nombre LIKE '%Gouda Premium%';

-- Ver el lote creado
SELECT * FROM produccion 
WHERE producto_id = (
    SELECT id FROM productos 
    WHERE nombre LIKE '%Gouda Premium%'
);

-- Ver el movimiento de inventario
SELECT * FROM inventario_movimientos 
WHERE producto_id = (
    SELECT id FROM productos 
    WHERE nombre LIKE '%Gouda Premium%'
)
ORDER BY fecha_movimiento DESC;
```

---

## Paso 6: Pruebas de Navegación

### 6.1 Navegación desde Dashboard
```
URL: http://localhost/prototipoleslie/dashboard.php
```

**Verificar links:**
- ✅ Click en "Producción" → Va a produccion.php
- ✅ Click en "Inventario" → Va a inventario.php
- ✅ Click en "Ver Producción" (botón) → Va a produccion.php

### 6.2 Navegación entre Módulos
```
Flujo: Dashboard → Producción → Nuevo Lote → Producción
```

**Verificar:**
- ✅ Todos los enlaces funcionan
- ✅ Botones "Volver" funcionan correctamente
- ✅ Sidebar mantiene estado activo correcto
- ✅ Usuario siempre visible en sidebar

### 6.3 Cerrar Sesión
```
Click en "Cerrar Sesión" en cualquier página
```

**Resultado Esperado:**
- ✅ Confirmación: "¿Está seguro que desea cerrar sesión?"
- ✅ Si acepta → Redirección a index.php (login)
- ✅ Sesión destruida
- ✅ No puede acceder a páginas protegidas sin login

---

## Paso 7: Pruebas de Seguridad

### 7.1 Acceso sin Autenticación
```
1. Cerrar sesión
2. Intentar acceder directamente a:
   - http://localhost/prototipoleslie/produccion.php
   - http://localhost/prototipoleslie/nuevo-lote.php
   - http://localhost/prototipoleslie/inventario.php
   - http://localhost/prototipoleslie/nuevo-producto.php
```

**Resultado Esperado:**
- ✅ Redirección automática a index.php (login)
- ✅ No se puede acceder sin autenticación

### 7.2 Inyección SQL (Prevención)
```
Intentar en formularios:
- Nombre: ' OR '1'='1
- Observaciones: '; DROP TABLE productos; --
```

**Resultado Esperado:**
- ✅ Datos guardados como texto normal
- ✅ No se ejecutan comandos SQL
- ✅ Sistema no se rompe

### 7.3 XSS (Prevención)
```
Intentar en formularios:
- Nombre: <script>alert('XSS')</script>
- Observaciones: <img src=x onerror=alert('XSS')>
```

**Resultado Esperado:**
- ✅ HTML escapado al mostrarse
- ✅ No se ejecuta JavaScript
- ✅ Se muestra como texto plano

---

## Paso 8: Pruebas de Validación

### 8.1 Formulario Nuevo Lote
```
URL: http://localhost/prototipoleslie/nuevo-lote.php
```

**Casos de Prueba:**

| Campo | Valor | Resultado Esperado |
|-------|-------|-------------------|
| Producto | (vacío) | Error: "El producto es requerido" |
| Cantidad | 0 | Error: "La cantidad debe ser mayor a 0" |
| Cantidad | -5 | Error: "La cantidad debe ser mayor a 0" |
| Fecha Producción | (vacío) | Error: "La fecha de producción es requerida" |
| Todos válidos | Datos correctos | ✅ Lote creado exitosamente |

### 8.2 Formulario Nuevo Producto
```
URL: http://localhost/prototipoleslie/nuevo-producto.php
```

**Casos de Prueba:**

| Campo | Valor | Resultado Esperado |
|-------|-------|-------------------|
| Nombre | (vacío) | Error: "El nombre del producto es requerido" |
| Categoría | (vacío) | Error: "La categoría es requerida" |
| Precio | 0 | Error: "El precio debe ser mayor a 0" |
| Precio | -10 | Error: "El precio debe ser mayor a 0" |
| Todos válidos | Datos correctos | ✅ Producto creado exitosamente |

---

## Paso 9: Pruebas de Responsividad

### 9.1 Probar en Diferentes Resoluciones
```
- Desktop (1920x1080)
- Tablet (768x1024)
- Mobile (375x667)
```

**Verificar:**
- ✅ Sidebar se adapta correctamente
- ✅ Tablas son scrollables horizontalmente en mobile
- ✅ Formularios son usables en pantalla pequeña
- ✅ KPIs se reorganizan en columnas
- ✅ Botón hamburguesa aparece en mobile

### 9.2 Navegadores
```
- Chrome (últimas 2 versiones)
- Firefox (últimas 2 versiones)
- Safari (últimas 2 versiones)
- Edge (últimas 2 versiones)
```

**Verificar:**
- ✅ UI se ve correctamente
- ✅ Funcionalidad completa en todos
- ✅ No hay errores en consola

---

## Paso 10: Verificación Final

### 10.1 Checklist Completo

**Backend:**
- [ ] Todos los módulos requieren autenticación
- [ ] Formularios validan datos correctamente
- [ ] Datos se guardan en la base de datos
- [ ] Stock se actualiza automáticamente
- [ ] Movimientos de inventario se registran
- [ ] Mensajes de éxito/error funcionan
- [ ] Prepared statements usados en todas las consultas

**Frontend:**
- [ ] KPIs muestran datos reales
- [ ] Tablas muestran datos de la DB
- [ ] Alertas se muestran cuando corresponde
- [ ] Navegación funciona en todos los módulos
- [ ] Usuario autenticado visible en sidebar
- [ ] UI responsiva en mobile/tablet/desktop

**Seguridad:**
- [ ] No se puede acceder sin login
- [ ] SQL injection prevenida
- [ ] XSS prevenida
- [ ] Datos sensibles no expuestos
- [ ] Sesiones seguras

**Base de Datos:**
- [ ] Lotes se crean correctamente
- [ ] Productos se crean correctamente
- [ ] Movimientos de inventario se registran
- [ ] Stock se actualiza correctamente
- [ ] Relaciones entre tablas funcionan
- [ ] No hay errores SQL

---

## Problemas Comunes y Soluciones

### Problema: "Access denied for user"
**Solución:** Verificar credenciales en `app/config/config.php`

### Problema: Página en blanco
**Solución:** 
1. Revisar logs de PHP (`error_log`)
2. Activar `display_errors` en `php.ini`
3. Verificar permisos de archivos

### Problema: "Unknown database"
**Solución:** Ejecutar `database.sql` para crear la base de datos

### Problema: "Call to undefined function"
**Solución:** Verificar que todas las clases se cargan correctamente en `config.php`

### Problema: No aparecen datos
**Solución:**
1. Verificar que `database.sql` se importó completamente
2. Ejecutar las consultas SQL de verificación
3. Verificar que hay datos en las tablas

---

## Contacto y Soporte

Para problemas o consultas:
1. Revisar documentación en `MODULOS-PHP.md`
2. Revisar logs de errores
3. Verificar configuración en `app/config/config.php`
4. Ejecutar `test-connection.php`

---

## Conclusión

Si todas las pruebas pasan exitosamente, el sistema está listo para uso.

**Estado:** ✅ Todos los módulos funcionando correctamente
