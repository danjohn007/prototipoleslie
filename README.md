# Sistema de Logística Quesos Leslie

Sistema integral de gestión logística desarrollado con PHP puro, MySQL y Bootstrap 5.

## 📋 Descripción

Sistema completo de gestión logística para Quesos Leslie que incluye módulos de:
- 🏭 Producción e Inventario
- 📦 Gestión de Pedidos
- 🚚 Optimización Logística
- 👥 Experiencia del Cliente
- 📊 Analítica y Reportes
- 💰 Administración Financiera

## 🛠️ Tecnologías Utilizadas

- **Backend:** PHP 7.0+ (sin framework)
- **Base de datos:** MySQL 5.7
- **Frontend:** HTML5, CSS3, JavaScript
- **Framework CSS:** Bootstrap 5
- **Iconos:** Font Awesome 6
- **Gráficas:** Chart.js
- **Arquitectura:** MVC (Model-View-Controller)
- **Autenticación:** Sessions + password_hash()
- **Servidor:** Apache con mod_rewrite

## 📦 Requisitos del Sistema

- PHP 7.0 o superior
- MySQL 5.7 o superior
- Apache 2.4+ con mod_rewrite habilitado
- Extensiones PHP requeridas:
  - PDO
  - PDO_MySQL
  - mbstring
  - session

## 🚀 Instalación

### 1. Clonar o descargar el repositorio

```bash
git clone https://github.com/danjohn007/prototipoleslie.git
cd prototipoleslie
```

### 2. Configurar el servidor Apache

Copiar el proyecto a la carpeta de Apache:

```bash
# En Windows (XAMPP)
cp -r prototipoleslie C:/xampp/htdocs/

# En Linux/Mac (LAMP/MAMP)
sudo cp -r prototipoleslie /var/www/html/
```

### 3. Crear la base de datos

Ejecutar el archivo `database.sql` en MySQL:

**Opción A: Desde phpMyAdmin**
1. Abrir phpMyAdmin (http://localhost/phpmyadmin)
2. Ir a la pestaña "SQL"
3. Copiar todo el contenido de `database.sql`
4. Pegar y ejecutar

**Opción B: Desde línea de comandos**
```bash
mysql -u root -p < database.sql
```

El script creará:
- Base de datos: `quesos_leslie_db`
- 12 tablas con estructura completa
- Datos de ejemplo (usuarios, productos, clientes, pedidos, etc.)

### 4. Configurar credenciales de base de datos

Editar el archivo `app/config/config.php` y ajustar las credenciales:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'quesos_leslie_db');
define('DB_USER', 'root');        // Cambiar según tu configuración
define('DB_PASS', '');            // Cambiar según tu configuración
```

### 5. Configurar permisos (Linux/Mac)

```bash
sudo chmod -R 755 /var/www/html/prototipoleslie
sudo chown -R www-data:www-data /var/www/html/prototipoleslie
```

### 6. Verificar instalación

Acceder al test de conexión:
```
http://localhost/prototipoleslie/test-connection.php
```

Este archivo verificará:
- ✅ Versión de PHP
- ✅ Extensiones requeridas
- ✅ Conexión a base de datos
- ✅ Tablas creadas
- ✅ URL base configurada

## 🔐 Acceso al Sistema

### Usuarios de Prueba

Una vez instalado, acceder al sistema en:
```
http://localhost/prototipoleslie/
```

**Credenciales de acceso:**

| Email | Password | Rol |
|-------|----------|-----|
| leslie@quesosleslie.com | admin123 | admin |
| juan@quesosleslie.com | admin123 | operador |
| maria@quesosleslie.com | admin123 | vendedor |
| carlos@quesosleslie.com | admin123 | logistica |

## 📁 Estructura del Proyecto

```
prototipoleslie/
├── app/
│   ├── config/
│   │   └── config.php              # Configuración general y DB
│   ├── controllers/
│   │   └── AuthController.php      # Controlador de autenticación
│   ├── models/
│   │   ├── Database.php            # Conexión a BD (PDO)
│   │   └── User.php                # Modelo de usuarios
│   └── views/
│       └── auth/
│           └── login.php           # Vista de login
├── public/
│   └── assets/                     # Recursos estáticos (CSS, JS, imágenes)
├── .htaccess                       # Configuración Apache
├── database.sql                    # Script SQL con estructura y datos
├── index.php                       # Front controller (Login)
├── dashboard.php                   # Dashboard principal (requiere auth)
├── test-connection.php             # Test de conexión y configuración
├── README.md                       # Este archivo
└── *.html                          # Vistas de módulos del sistema
```

## 🔧 Configuración Avanzada

### URL Base Auto-configurable

El sistema detecta automáticamente la URL base, funciona en:
- `http://localhost/prototipoleslie/`
- `http://localhost/` (si está en raíz)
- `http://midominio.com/`
- Cualquier subdirectorio de Apache

### URLs Amigables

El archivo `.htaccess` está configurado para:
- Redirección de URLs
- Seguridad HTTP headers
- Compresión de archivos
- Caché de assets estáticos

### Seguridad

- Contraseñas hasheadas con `password_hash()` (bcrypt)
- Sesiones con timeout configurable (1 hora por defecto)
- Protección contra SQL Injection (PDO prepared statements)
- Headers de seguridad (X-Frame-Options, X-XSS-Protection)
- Validación de entradas

## 📊 Base de Datos

### Tablas principales

1. **usuarios** - Gestión de usuarios del sistema
2. **productos** - Catálogo de productos
3. **clientes** - Registro de clientes
4. **pedidos** - Pedidos realizados
5. **pedido_detalles** - Detalle de cada pedido
6. **produccion** - Lotes de producción
7. **inventario_movimientos** - Movimientos de stock
8. **rutas** - Rutas de entrega
9. **ruta_pedidos** - Asignación pedidos a rutas
10. **retornos** - Gestión de devoluciones
11. **encuestas** - Satisfacción del cliente
12. **transacciones** - Movimientos financieros

## 🎨 Características del Sistema

### Autenticación y Sesiones
- Login con email y contraseña
- Sesiones seguras con timeout
- Diferentes roles de usuario
- Logout seguro

### Diseño Responsivo
- Bootstrap 5
- Compatible con móviles, tablets y desktop
- Sidebar colapsable en móviles
- Interfaz moderna y elegante

### Módulos Implementados
- Dashboard con KPIs principales
- Prototipos HTML de todos los módulos
- Estructura MVC preparada para expansión

## 🧪 Testing

### Test de Conexión

Acceder a `test-connection.php` para verificar:
- Versión de PHP y extensiones
- Conexión a base de datos
- Tablas creadas correctamente
- Configuración del sistema
- Permisos de directorios

## 🚀 Próximos Pasos (Desarrollo Futuro)

- [ ] Implementar CRUD completo para todos los módulos
- [ ] Integrar Chart.js para gráficas
- [ ] Implementar FullCalendar.js para calendario
- [ ] API REST para integración con apps móviles
- [ ] Exportación de reportes (PDF, Excel)
- [ ] Notificaciones en tiempo real
- [ ] Sistema de permisos granular

## 🤝 Contribución

Las contribuciones son bienvenidas. Por favor:
1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## 📝 Licencia

Este proyecto es de código abierto y está disponible bajo la licencia MIT.

## 👤 Autor

**Leslie Lugo - Quesos Leslie**

## 📞 Soporte

Para soporte y preguntas, por favor abrir un issue en el repositorio de GitHub.

---

**Versión:** 1.0.0  
**Última actualización:** Enero 2024 
