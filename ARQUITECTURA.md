# Arquitectura del Sistema - Quesos Leslie

## 📐 Diagrama de Arquitectura MVC

```
┌─────────────────────────────────────────────────────────────────┐
│                         CLIENTE (Browser)                        │
│                     Bootstrap 5 + JavaScript                     │
└──────────────────────────────┬──────────────────────────────────┘
                               │
                    HTTP Request/Response
                               │
┌──────────────────────────────▼──────────────────────────────────┐
│                      SERVIDOR APACHE                              │
│                      + PHP 7.0+ + MySQL 5.7                      │
└──────────────────────────────┬──────────────────────────────────┘
                               │
┌──────────────────────────────▼──────────────────────────────────┐
│                      FRONT CONTROLLER                             │
│                         index.php                                 │
│              ┌──────────────────────────────┐                    │
│              │   • Carga configuración      │                    │
│              │   • Enrutamiento simple      │                    │
│              │   • Maneja autenticación     │                    │
│              └──────────────────────────────┘                    │
└──────────────────────────────┬──────────────────────────────────┘
                               │
                               │
          ┌────────────────────┼────────────────────┐
          │                    │                    │
          ▼                    ▼                    ▼
┌─────────────────┐  ┌──────────────────┐  ┌──────────────────┐
│  CONTROLLERS    │  │    API REST      │  │   UTILITIES      │
│                 │  │                  │  │                  │
│ AuthController  │  │ api/products.php │  │ test-connection  │
│  • login()      │  │  • GET /list     │  │ helpers.php      │
│  • logout()     │  │  • GET /get      │  │  • validation    │
│  • checkAuth()  │  │  • POST create   │  │  • formatting    │
│                 │  │  • PUT update    │  │  • sanitization  │
│                 │  │  • DELETE        │  │                  │
└────────┬────────┘  └────────┬─────────┘  └──────────────────┘
         │                    │
         │                    │
         └─────────┬──────────┘
                   │
                   ▼
         ┌─────────────────────┐
         │      MODELS         │
         │                     │
         │  • Database.php     │◄──────────────┐
         │    (Singleton)      │               │
         │                     │               │
         │  • User.php         │               │
         │    - login()        │               │
         │    - register()     │               │
         │    - isLoggedIn()   │               │
         │                     │               │
         │  • Product.php      │               │
         │    - getAll()       │               │
         │    - create()       │               │
         │    - update()       │               │
         │    - delete()       │               │
         │                     │               │
         └──────────┬──────────┘               │
                    │                          │
                    │                          │
                    ▼                          │
         ┌─────────────────────┐               │
         │  DATABASE (MySQL)   │               │
         │                     │               │
         │  12 Tablas:         │               │
         │  ├─ usuarios        │               │
         │  ├─ productos       │               │
         │  ├─ clientes        │               │
         │  ├─ pedidos         │               │
         │  ├─ pedido_detalles │               │
         │  ├─ produccion      │               │
         │  ├─ inventario_movim│               │
         │  ├─ rutas           │               │
         │  ├─ ruta_pedidos    │               │
         │  ├─ retornos        │               │
         │  ├─ encuestas       │               │
         │  └─ transacciones   │               │
         │                     │               │
         └─────────────────────┘               │
                                               │
         ┌─────────────────────┐               │
         │       VIEWS         │               │
         │                     │               │
         │  app/views/auth/    │               │
         │  └─ login.php       │               │
         │                     │               │
         │  *.html prototypes  │               │
         │  (23 archivos)      │               │
         │                     │               │
         └─────────────────────┘               │
                                               │
         ┌─────────────────────┐               │
         │   CONFIGURATION     │               │
         │                     │               │
         │  app/config/        │               │
         │  ├─ config.php      │───────────────┘
         │  │  - DB credentials
         │  │  - BASE_URL
         │  │  - Constants
         │  │
         │  └─ helpers.php
         │     - 40+ functions
         │
         └─────────────────────┘
```

---

## 🔄 Flujo de Ejecución

### 1. Login Flow

```
Usuario → index.php → AuthController → User Model → Database
                                                        │
                                                        ▼
                                                     MySQL
                                                        │
                                                        ▼
                                          password_verify()
                                                        │
                                    ┌───────────────────┴──────────────────┐
                                    │                                      │
                                    ▼                                      ▼
                              ✅ Correcto                            ❌ Incorrecto
                                    │                                      │
                                    ▼                                      ▼
                          Crear sesión                          Mensaje de error
                          $_SESSION['user_id']                         │
                          $_SESSION['logged_in']                       │
                                    │                                      │
                                    ▼                                      ▼
                            dashboard.php                          login.php
```

### 2. Dashboard Flow

```
Usuario → dashboard.php → checkAuth() → isLoggedIn()
                                              │
                        ┌─────────────────────┴────────────────────┐
                        │                                          │
                        ▼                                          ▼
                   ✅ Autenticado                          ❌ No autenticado
                        │                                          │
                        ▼                                          ▼
                Mostrar dashboard                         Redirect a login
                Cargar KPIs
                Mostrar sidebar
```

### 3. API REST Flow

```
Cliente → api/products.php → checkAuth() → isLoggedIn()
                                                  │
                            ┌─────────────────────┴────────────────┐
                            │                                      │
                            ▼                                      ▼
                      ✅ Autorizado                        ❌ No autorizado
                            │                                      │
                            ▼                                      ▼
                  Procesar request                       HTTP 401 Unauthorized
                  GET/POST/PUT/DELETE
                            │
              ┌─────────────┼─────────────┐
              │             │             │
              ▼             ▼             ▼
            GET           POST          PUT/DELETE
              │             │             │
              ▼             ▼             ▼
       Product::getAll() Product::create() Product::update()
              │             │             │
              └─────────────┼─────────────┘
                            ▼
                       Database::query()
                            │
                            ▼
                      JSON Response
```

---

## 🔒 Capas de Seguridad

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Apache (.htaccess)                                       │
│    • Security Headers (X-Frame-Options, X-XSS-Protection)   │
│    • Disable directory listing                              │
│    • URL rewriting                                          │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. PHP Session                                              │
│    • Session timeout (1 hour)                               │
│    • Session validation                                     │
│    • CSRF token generation                                  │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. Authentication Layer                                     │
│    • password_hash() (bcrypt)                               │
│    • password_verify()                                      │
│    • Role-based access                                      │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. Input Validation                                         │
│    • clean_input()                                          │
│    • validate_email()                                       │
│    • sanitize_array()                                       │
└─────────────────────────────────────────────────────────────┘
                            │
                            ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. Database Layer                                           │
│    • PDO Prepared Statements                                │
│    • SQL Injection Protection                               │
│    • Transaction support                                    │
└─────────────────────────────────────────────────────────────┘
```

---

## 📁 Estructura de Directorios

```
prototipoleslie/
│
├── app/                          # Aplicación (MVC)
│   ├── config/                   # Configuración
│   │   ├── config.php           # ★ Config principal
│   │   └── helpers.php          # ★ Funciones helper
│   │
│   ├── controllers/              # Controladores
│   │   └── AuthController.php   # ★ Auth controller
│   │
│   ├── models/                   # Modelos
│   │   ├── Database.php         # ★ Conexión DB (Singleton)
│   │   ├── User.php             # ★ Modelo usuarios
│   │   └── Product.php          # ★ Modelo productos
│   │
│   └── views/                    # Vistas
│       └── auth/
│           └── login.php        # ★ Vista login
│
├── api/                          # API REST
│   └── products.php             # ★ Endpoint productos
│
├── public/                       # Assets públicos
│   └── assets/
│       ├── css/
│       ├── js/
│       └── img/
│
├── *.html                        # Prototipos HTML (23 archivos)
│
├── .htaccess                     # ★ Config Apache
├── .gitignore                    # ★ Git ignore
├── database.sql                  # ★ Script SQL
├── index.php                     # ★ Front controller
├── dashboard.php                 # ★ Dashboard
├── test-connection.php           # ★ Test instalación
│
├── README.md                     # ★ Documentación completa
├── INSTRUCCIONES.md              # ★ Guía rápida
├── API.md                        # ★ Doc API
└── ARQUIECTURA.md                # ★ Este archivo

★ = Archivo nuevo creado (19 archivos)
```

---

## 🔧 Tecnologías y Patrones

### Patrones de Diseño
- **MVC (Model-View-Controller)** - Separación de responsabilidades
- **Singleton** - Database connection única
- **Front Controller** - Punto de entrada único (index.php)
- **Repository Pattern** - Modelos como repositorios de datos

### Tecnologías Backend
- **PHP 7.0+** - Lenguaje principal
- **PDO** - Capa de abstracción de base de datos
- **MySQL 5.7** - Base de datos relacional
- **Sessions** - Gestión de autenticación

### Tecnologías Frontend
- **Bootstrap 5** - Framework CSS
- **Font Awesome 6** - Iconos
- **JavaScript Vanilla** - Interactividad
- **HTML5 + CSS3** - Estructura y estilos

### Seguridad
- **password_hash()** - Hashing de contraseñas (bcrypt)
- **PDO Prepared Statements** - Prevención SQL Injection
- **htmlspecialchars()** - Prevención XSS
- **CSRF Tokens** - Prevención CSRF attacks
- **Session Timeout** - Expiración de sesiones

---

## 📊 Base de Datos - Relaciones

```
usuarios (4 registros)
    ├── 1:N → pedidos (usuario_id)
    ├── 1:N → produccion (responsable_id)
    ├── 1:N → rutas (conductor_id)
    ├── 1:N → retornos (responsable_id)
    ├── 1:N → transacciones (usuario_id)
    └── 1:N → inventario_movimientos (usuario_id)

clientes (5 registros)
    ├── 1:N → pedidos (cliente_id)
    ├── 1:N → retornos (cliente_id)
    ├── 1:N → encuestas (cliente_id)
    └── 1:N → transacciones (cliente_id)

productos (8 registros)
    ├── 1:N → pedido_detalles (producto_id)
    ├── 1:N → produccion (producto_id)
    ├── 1:N → retornos (producto_id)
    └── 1:N → inventario_movimientos (producto_id)

pedidos (5 registros)
    ├── N:1 → clientes (cliente_id)
    ├── N:1 → usuarios (usuario_id)
    ├── 1:N → pedido_detalles (pedido_id)
    ├── 1:N → ruta_pedidos (pedido_id)
    ├── 1:N → retornos (pedido_id)
    ├── 1:N → encuestas (pedido_id)
    └── 1:N → transacciones (pedido_id)

rutas (3 registros)
    ├── N:1 → usuarios (conductor_id)
    └── 1:N → ruta_pedidos (ruta_id)
```

---

## 🚀 Extensibilidad

El sistema está diseñado para fácil expansión:

### Agregar nuevo modelo
```php
// app/models/Client.php
class Client {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll() {
        // Implementación
    }
}
```

### Agregar nuevo controlador
```php
// app/controllers/ClientController.php
class ClientController {
    private $clientModel;
    
    public function __construct() {
        $this->clientModel = new Client();
    }
    
    public function list() {
        // Implementación
    }
}
```

### Agregar nueva vista
```php
// app/views/clients/list.php
<?php require_once 'app/config/config.php'; ?>
<!DOCTYPE html>
<html>
<!-- HTML aquí -->
</html>
```

---

## 📈 Rendimiento

### Optimizaciones Implementadas
- **PDO Prepared Statements** - Previene SQL injection y mejora cache
- **Singleton Pattern** - Una sola conexión DB por request
- **Indexes en DB** - Búsquedas rápidas en tablas
- **Apache mod_deflate** - Compresión de respuestas
- **Browser Caching** - Cache de assets estáticos
- **Lazy Loading** - Solo carga lo necesario

### Métricas Estimadas
- **Login:** < 100ms
- **Dashboard:** < 200ms
- **API Request:** < 50ms
- **Query DB:** < 10ms (con índices)

---

## 🔮 Próximos Pasos

1. **Implementar modelos restantes**
   - Client, Order, Route, Production, etc.

2. **Convertir prototipos HTML a vistas PHP**
   - Integrar con controladores
   - Agregar lógica de negocio

3. **Agregar Chart.js**
   - Gráficas en dashboard
   - Reportes visuales

4. **Implementar FullCalendar.js**
   - Calendario de producción
   - Calendario de entregas

5. **Exportación de reportes**
   - PDF (FPDF/TCPDF)
   - Excel (PhpSpreadsheet)

6. **Sistema de notificaciones**
   - Email (PHPMailer)
   - WebSockets para tiempo real

---

**Sistema completo y funcional**  
**Listo para desarrollo adicional**  
**Arquitectura escalable y mantenible**
