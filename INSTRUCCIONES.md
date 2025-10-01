# Instrucciones Rápidas de Instalación

## 🚀 Inicio Rápido (3 pasos)

### 1. Preparar Base de Datos

Ejecutar en MySQL el archivo `database.sql`:

```bash
mysql -u root -p < database.sql
```

O desde phpMyAdmin: Importar el archivo `database.sql`

### 2. Configurar Credenciales

Editar `app/config/config.php` líneas 13-16:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'quesos_leslie_db');
define('DB_USER', 'root');          // Tu usuario MySQL
define('DB_PASS', '');              // Tu contraseña MySQL
```

### 3. Acceder al Sistema

Abrir en el navegador:
- Test de conexión: `http://localhost/prototipoleslie/test-connection.php`
- Login: `http://localhost/prototipoleslie/`

**Credenciales de prueba:**
- Email: `leslie@quesosleslie.com`
- Password: `admin123`

---

## 📋 Verificación de Instalación

### ✅ Checklist

- [ ] PHP 7.0+ instalado
- [ ] MySQL 5.7+ instalado y corriendo
- [ ] Apache con mod_rewrite habilitado
- [ ] Base de datos `quesos_leslie_db` creada
- [ ] 12 tablas creadas con datos de ejemplo
- [ ] Archivo `app/config/config.php` configurado
- [ ] Test de conexión exitoso

### 🧪 Test de Conexión

El archivo `test-connection.php` verifica:

1. ✅ Versión de PHP (>= 7.0)
2. ✅ Extensión PDO instalada
3. ✅ Extensión PDO_MySQL instalada
4. ✅ Conexión a base de datos
5. ✅ Tablas creadas (debe mostrar 12 tablas)
6. ✅ URL base configurada correctamente

---

## 🔑 Usuarios del Sistema

| Usuario | Email | Password | Rol |
|---------|-------|----------|-----|
| Leslie Lugo | leslie@quesosleslie.com | admin123 | admin |
| Juan Pérez | juan@quesosleslie.com | admin123 | operador |
| María García | maria@quesosleslie.com | admin123 | vendedor |
| Carlos Rodríguez | carlos@quesosleslie.com | admin123 | logistica |

---

## 📁 Estructura de Archivos Importantes

```
prototipoleslie/
├── database.sql              ← Script SQL (ejecutar primero)
├── test-connection.php       ← Test de conexión
├── index.php                 ← Login (punto de entrada)
├── dashboard.php             ← Dashboard principal
├── app/config/config.php     ← Configuración (editar credenciales aquí)
└── README.md                 ← Documentación completa
```

---

## 🐛 Solución de Problemas Comunes

### Error: "Access denied for user"
**Solución:** Verificar credenciales en `app/config/config.php`

### Error: "Unknown database"
**Solución:** Ejecutar el archivo `database.sql` para crear la base de datos

### Error: "Call to undefined function password_hash()"
**Solución:** Actualizar PHP a versión 7.0 o superior

### Página en blanco o errores 500
**Solución:** 
1. Verificar permisos de archivos
2. Revisar logs de PHP/Apache
3. Activar display_errors en desarrollo

### URL base incorrecta
**Solución:** El sistema detecta automáticamente la URL. Si hay problemas, editar la función `getBaseUrl()` en `app/config/config.php`

---

## 💡 Características Principales

✅ **Arquitectura MVC** - Código organizado y mantenible  
✅ **Autenticación Segura** - password_hash() con bcrypt  
✅ **Base de Datos** - 12 tablas con relaciones y datos de ejemplo  
✅ **URL Auto-configurable** - Funciona en cualquier directorio  
✅ **Bootstrap 5** - Diseño moderno y responsivo  
✅ **Validaciones** - Frontend y backend  
✅ **Sesiones Seguras** - Timeout configurable  

---

## 📚 Documentación Completa

Para instrucciones detalladas, consultar **README.md**

---

## 🆘 Soporte

En caso de problemas, verificar:
1. `test-connection.php` - Estado del sistema
2. Logs de Apache/PHP - Errores técnicos
3. Consola del navegador - Errores JavaScript
4. README.md - Documentación completa
