# UI Features and Visual Description

This document describes the user interface changes and new features implemented.

## 1. Sidebar with Search Functionality

### Visual Description:
```
┌─────────────────────────────┐
│   QUESOS LESLIE             │
│   SISTEMA                   │
├─────────────────────────────┤
│ 🔍 [Search menu...]         │ ← NEW SEARCH BAR
├─────────────────────────────┤
│ MÓDULOS                     │
│ 📊 Dashboard               │
│ 🏭 Producción              │
│   ➕ Nuevo Lote            │
│ 📦 Gestión de Inventario   │
│   ➕ Nuevo Producto        │
│ 📋 Registro de Producción  │
│ 🛒 Gestión de Pedidos      │
│   ➕ Nuevo Pedido          │
│ 🏪 Ventas en Punto         │
│ 🚚 Optimización Logística  │
│   ➕ Nueva Ruta            │
│ ↩️  Control de Retornos     │
│   ➕ Registrar Retorno     │
│ 😊 Experiencia del Cliente │
│   ✉️  Enviar Encuesta      │
│ 📊 Analítica y Reportes    │
│   📄 Nuevo Reporte         │
│ 👥 Gestión de Clientes     │
│   ➕ Nuevo Cliente         │
│ 💰 Administración Financ.  │
│   💵 Nueva Transacción     │
├─────────────────────────────┤
│ 👤 [User Name]             │
│    [User Role]              │
│ ⚙️  Mi Perfil              │ ← NEW MENU ITEM
│ 🚪 Cerrar Sesión           │
└─────────────────────────────┘
```

### Key Features:
- **Search Bar**: Type to filter menu items in real-time
- **Hierarchical Menu**: Main items and sub-items (indented)
- **Active Highlighting**: Current page is highlighted
- **User Profile Section**: Shows user info at bottom
- **Mi Perfil Link**: New link to access profile page
- **Responsive**: Hamburger menu on mobile devices

### Search Functionality:
- Type "pedido" → Shows only "Gestión de Pedidos" and "Nuevo Pedido"
- Type "cliente" → Shows "Experiencia del Cliente", "Gestión de Clientes", "Nuevo Cliente"
- Type "dashboard" → Shows only "Dashboard"
- Case-insensitive search
- Searches both visible text and hidden keywords

---

## 2. Dashboard with Three Charts

### Layout:
```
┌────────────────────────────────────────────────────────────┐
│  Dashboard Principal                          📅 [Date]     │
├────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐  │
│  │ Total    │  │ En       │  │ Productos│  │ Stock    │  │
│  │ Pedidos  │  │ Producción│  │    150   │  │ Bajo     │  │
│  │   245    │  │    8     │  │          │  │    12    │  │
│  │ 🛒 Gestión│  │ 🏭 Lotes │  │ 📦 En   │  │ ⚠️ Requieren│
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘  │
│                                                             │
├────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌────────────────┐ ┌────────────────┐ ┌────────────────┐│
│  │ 📊 Pedidos por │ │ 📊 Producción  │ │ 📊 Estado del  ││
│  │    Estado      │ │    Reciente    │ │   Inventario   ││
│  │                │ │                │ │                ││
│  │   [Doughnut]   │ │   [Bar Chart]  │ │   [Pie Chart]  ││
│  │     Chart      │ │                │ │                ││
│  │                │ │                │ │                ││
│  │ 🟡 Pendiente   │ │ Gouda   ████   │ │ 🟢 Óptimo 65%  ││
│  │ 🔵 Confirmado  │ │ Manchego ███   │ │ 🟡 Bajo   25%  ││
│  │ 🟣 Preparación │ │ Yogurt  ████   │ │ 🔴 Sin    10%  ││
│  │ 🟠 En Ruta     │ │ Crema   ██     │ │      Stock     ││
│  │ 🟢 Entregado   │ │ Mante.  ███    │ │                ││
│  └────────────────┘ └────────────────┘ └────────────────┘│
│                                                             │
├────────────────────────────────────────────────────────────┤
│  ⚡ Accesos Rápidos                                        │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐ ┌──────┐│
│  │ ➕ Nuevo    │ │ 🏭 Nuevo    │ │ 📦 Nuevo    │ │ 📊   ││
│  │   Pedido    │ │   Lote      │ │  Producto   │ │ Ver  ││
│  │             │ │             │ │             │ │Report││
│  └─────────────┘ └─────────────┘ └─────────────┘ └──────┘│
└────────────────────────────────────────────────────────────┘
```

### Chart Details:

**Chart 1: Pedidos por Estado (Doughnut)**
- Shows order distribution across all statuses
- Interactive tooltips on hover
- Color-coded segments
- Real data from database

**Chart 2: Producción Reciente (Bar)**
- Last 5 production batches
- Product names on X-axis
- Quantity produced on Y-axis
- Green bars for completed production

**Chart 3: Estado del Inventario (Pie)**
- Three segments: Óptimo, Bajo Stock, Sin Stock
- Color indicators: Green (good), Yellow (warning), Red (critical)
- Percentage distribution

### KPI Cards:
- Show real-time statistics from database
- Each card has an icon and label
- Clickable to navigate to respective modules

---

## 3. Mi Perfil (My Profile) Page

### Layout:
```
┌────────────────────────────────────────────────────────────┐
│  Mi Perfil                                                  │
├────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌──────────────┐  ┌─────────────────────────────────────┐│
│  │              │  │ 📝 Información del Perfil            ││
│  │      👤      │  │                                      ││
│  │              │  │ Nombre Completo:                     ││
│  │  [Avatar]    │  │ [___________________________]        ││
│  │              │  │                                      ││
│  │  John Doe    │  │ Email:                               ││
│  │  Administrador│  │ [___________________________]        ││
│  │ john@email   │  │                                      ││
│  │              │  │ Rol: Administrador (solo lectura)   ││
│  │ Miembro desde│  │                                      ││
│  │  15/01/2024  │  │            [💾 Guardar Cambios]     ││
│  │              │  │                                      ││
│  └──────────────┘  └─────────────────────────────────────┘│
│                                                             │
│                    ┌─────────────────────────────────────┐│
│                    │ 🔑 Cambiar Contraseña               ││
│                    │                                      ││
│                    │ Contraseña Actual:                   ││
│                    │ [___________________________]        ││
│                    │                                      ││
│                    │ Nueva Contraseña:                    ││
│                    │ [___________________________]        ││
│                    │ Mínimo 6 caracteres                  ││
│                    │                                      ││
│                    │ Confirmar Nueva Contraseña:          ││
│                    │ [___________________________]        ││
│                    │                                      ││
│                    │            [🔒 Cambiar Contraseña]  ││
│                    └─────────────────────────────────────┘│
└────────────────────────────────────────────────────────────┘
```

### Features:
1. **Profile Avatar**: Large icon-based avatar at top
2. **User Info Display**: Name, role, email, join date
3. **Edit Profile Form**: 
   - Update name and email
   - Role is read-only
   - Validation on save
4. **Change Password Form**:
   - Requires current password
   - New password with confirmation
   - Minimum length validation
   - Passwords must match

### Validation Messages:
- Success: Green banner "Perfil actualizado correctamente"
- Error: Red banner with specific error message
- Field validation in real-time

---

## 4. Nuevo Pedido (New Order) Form

### Layout:
```
┌────────────────────────────────────────────────────────────┐
│  Nuevo Pedido                   [← Volver a Pedidos]       │
├────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌───────────────────────────────┐  ┌──────────────────┐  │
│  │ ℹ️  Información del Pedido    │  │ 🧮 Resumen del  │  │
│  │                                │  │    Pedido        │  │
│  │ Número de Pedido:              │  │                  │  │
│  │ PED-20240115-001 (auto)        │  │ Subtotal:        │  │
│  │                                │  │       $0.00      │  │
│  │ Fecha de Entrega:              │  │                  │  │
│  │ [📅 DD/MM/YYYY]               │  │ Descuento: $ [0] │  │
│  │                                │  │ ──────────────── │  │
│  │ Cliente:                       │  │ Total:           │  │
│  │ [Select cliente... ▼]          │  │    $0.00         │  │
│  │ ¿No está? Crear nuevo cliente  │  │                  │  │
│  │                                │  │ [💾 Guardar]    │  │
│  │ Observaciones:                 │  │ [❌ Cancelar]   │  │
│  │ [_________________________]    │  │                  │  │
│  │ [_________________________]    │  │ ℹ️  Info:        │  │
│  │ [_________________________]    │  │ • Auto número   │  │
│  └───────────────────────────────┘  │ • Fecha futura  │  │
│                                      │ • Cliente req.  │  │
│  ┌───────────────────────────────┐  │ • Min 1 producto│  │
│  │ 📦 Productos del Pedido        │  └──────────────────┘  │
│  │                                │                         │
│  │ ┌──────────────────────────┐  │                         │
│  │ │ Producto: [Select... ▼]  │  │                         │
│  │ │          (precio, stock) │  │                         │
│  │ │ Cantidad: [1]            │  │                         │
│  │ │ Subtotal: [$0.00]  [🗑️] │  │                         │
│  │ └──────────────────────────┘  │                         │
│  │                                │                         │
│  │ [+ Agregar Producto]           │                         │
│  └───────────────────────────────┘                         │
└────────────────────────────────────────────────────────────┘
```

### Key Features:

**Order Information**:
- Auto-generated order number (read-only)
- Date picker for delivery date (future dates only)
- Client dropdown populated from database
- Link to create new client if needed
- Optional observations/notes field

**Product Management**:
- Start with one product row
- Each row has:
  - Product dropdown (shows price and stock)
  - Quantity input
  - Auto-calculated subtotal
  - Remove button (disabled for first row)
- "Agregar Producto" button adds new rows
- Remove buttons appear when 2+ rows exist

**Live Calculations**:
- Subtotal updates when product or quantity changes
- Discount is optional (defaults to 0)
- Total = Subtotal - Discount
- All calculations happen in real-time

**Order Summary Card**:
- Always visible on right side
- Shows current subtotal
- Input for discount amount
- Large total display
- Action buttons (Save/Cancel)
- Helpful info tips

### Form Validation:
- Client selection required
- Delivery date required (must be future)
- At least one product with quantity > 0
- Total cannot be negative
- Stock availability shown in dropdown
- Validation on submit with helpful error messages

### User Experience:
- Responsive design works on mobile
- Clear visual feedback
- Real-time calculations
- No page reloads needed for adding products
- Smooth add/remove animations

---

## Mobile Responsive Features

All pages include:
- **Hamburger Menu**: Three-line icon appears on screens < 992px
- **Slide-out Sidebar**: Sidebar slides from left when hamburger clicked
- **Overlay**: Dark overlay appears behind sidebar
- **Touch Friendly**: Large tap targets, easy scrolling
- **Adaptive Layout**: Forms stack vertically on mobile

```
Mobile View:
┌──────────────┐
│ ☰  Page Title│
├──────────────┤
│   Content    │
│   Stacked    │
│  Vertically  │
│              │
└──────────────┘

When menu opened:
┌────┬─────────┐
│[X] │  Page   │
│    ├─────────┤
│Nav │ Content │
│Menu│ Dimmed  │
│... │         │
└────┴─────────┘
```

---

## Color Scheme and Design

**Primary Colors**:
- Primary: #2c3e50 (Dark blue-gray)
- Success: #27ae60 (Green)
- Warning: #ffc107 (Yellow)
- Danger: #dc3545 (Red)
- Info: #3498db (Blue)

**Typography**:
- Font: Helvetica Neue, Arial, sans-serif
- Weights: 300 (light), 400 (regular), 500 (medium), 600 (semibold)

**Design Principles**:
- Clean, modern interface
- Consistent spacing and alignment
- Clear visual hierarchy
- Intuitive icons from Font Awesome
- Professional color palette
- Smooth transitions and animations

---

## Summary of Changes

### New Files Created:
1. `app/includes/sidebar.php` - Unified sidebar with search
2. `app/includes/sidebar-styles.php` - Menu search styles
3. `mi-perfil.php` - Complete profile page
4. `IMPLEMENTATION-SUMMARY.md` - Technical documentation

### Files Modified:
1. `dashboard.php` - Added 3 charts + unified sidebar
2. `pedidos.php` - Added unified sidebar
3. `nuevo-pedido.php` - Functional order form
4. `app/models/User.php` - Profile and password methods

### Features Added:
- ✅ Real-time menu search
- ✅ Unified sidebar component
- ✅ 3 interactive charts with real data
- ✅ Complete profile management
- ✅ Functional order creation form
- ✅ Database integration throughout
- ✅ Mobile-responsive design
- ✅ Form validation
- ✅ Live calculations
- ✅ Security features (password verification, CSRF protection)
