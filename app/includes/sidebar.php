<?php
/**
 * Sidebar Navigation Component
 * Unified sidebar with search functionality for all pages
 */

// Determine active page
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Overlay para menú móvil -->
<div class="sidebar-overlay"></div>

<!-- Botón hamburguesa para móvil -->
<button class="hamburger-btn">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar Navigation -->
<div class="sidebar">
    <div class="brand-header">
        <div class="brand-title">QUESOS LESLIE</div>
        <div class="brand-subtitle">SISTEMA</div>
    </div>
    
    <!-- Buscador de Menú -->
    <div class="menu-search">
        <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" id="menuSearch" class="menu-search-input" placeholder="Buscar en el menú...">
        </div>
    </div>
    
    <!-- MÓDULOS DEL SISTEMA -->
    <div class="nav-section">
        <div class="nav-section-title">MÓDULOS</div>
        
        <a href="<?php echo BASE_URL; ?>/dashboard.php" class="nav-link <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>" data-search="dashboard principal inicio">
            <i class="fas fa-chart-pie"></i> Dashboard
        </a>
        
        <a href="<?php echo BASE_URL; ?>/produccion.php" class="nav-link <?php echo $currentPage === 'produccion.php' ? 'active' : ''; ?>" data-search="produccion lotes fabricacion">
            <i class="fas fa-industry"></i> Producción
        </a>
        <a href="<?php echo BASE_URL; ?>/nuevo-lote.php" class="nav-link submenu <?php echo $currentPage === 'nuevo-lote.php' ? 'active' : ''; ?>" data-search="nuevo lote produccion crear">
            <i class="fas fa-plus-circle"></i> Nuevo Lote
        </a>
        
        <a href="<?php echo BASE_URL; ?>/inventario.php" class="nav-link <?php echo $currentPage === 'inventario.php' ? 'active' : ''; ?>" data-search="inventario stock almacen productos">
            <i class="fas fa-boxes"></i> Gestión de Inventario
        </a>
        <a href="<?php echo BASE_URL; ?>/nuevo-producto.php" class="nav-link submenu <?php echo $currentPage === 'nuevo-producto.php' ? 'active' : ''; ?>" data-search="nuevo producto crear agregar">
            <i class="fas fa-plus-circle"></i> Nuevo Producto
        </a>
        
        <a href="<?php echo BASE_URL; ?>/registro-produccion.php" class="nav-link <?php echo $currentPage === 'registro-produccion.php' ? 'active' : ''; ?>" data-search="registro produccion historial">
            <i class="fas fa-clipboard-list"></i> Registro de Producción
        </a>
        
        <a href="<?php echo BASE_URL; ?>/pedidos.php" class="nav-link <?php echo $currentPage === 'pedidos.php' ? 'active' : ''; ?>" data-search="pedidos ordenes solicitudes">
            <i class="fas fa-shopping-cart"></i> Gestión de Pedidos
        </a>
        <a href="<?php echo BASE_URL; ?>/nuevo-pedido.php" class="nav-link submenu <?php echo $currentPage === 'nuevo-pedido.php' ? 'active' : ''; ?>" data-search="nuevo pedido crear orden">
            <i class="fas fa-plus-circle"></i> Nuevo Pedido
        </a>
        
        <a href="<?php echo BASE_URL; ?>/ventas-punto.php" class="nav-link <?php echo $currentPage === 'ventas-punto.php' ? 'active' : ''; ?>" data-search="ventas punto pos caja">
            <i class="fas fa-store"></i> Ventas en Punto
        </a>
        
        <a href="<?php echo BASE_URL; ?>/optimizacion-logistica.php" class="nav-link <?php echo $currentPage === 'optimizacion-logistica.php' ? 'active' : ''; ?>" data-search="logistica rutas transporte">
            <i class="fas fa-route"></i> Optimización Logística
        </a>
        <a href="<?php echo BASE_URL; ?>/nueva-ruta.php" class="nav-link submenu <?php echo $currentPage === 'nueva-ruta.php' ? 'active' : ''; ?>" data-search="nueva ruta crear">
            <i class="fas fa-plus-circle"></i> Nueva Ruta
        </a>
        
        <a href="<?php echo BASE_URL; ?>/control-retornos.php" class="nav-link <?php echo $currentPage === 'control-retornos.php' ? 'active' : ''; ?>" data-search="control retornos devoluciones">
            <i class="fas fa-undo-alt"></i> Control de Retornos
        </a>
        <a href="<?php echo BASE_URL; ?>/registrar-retorno.php" class="nav-link submenu <?php echo $currentPage === 'registrar-retorno.php' ? 'active' : ''; ?>" data-search="registrar retorno devolucion">
            <i class="fas fa-plus-circle"></i> Registrar Retorno
        </a>
        
        <a href="<?php echo BASE_URL; ?>/experiencia-cliente.php" class="nav-link <?php echo $currentPage === 'experiencia-cliente.php' ? 'active' : ''; ?>" data-search="experiencia cliente satisfaccion feedback">
            <i class="fas fa-smile"></i> Experiencia del Cliente
        </a>
        <a href="<?php echo BASE_URL; ?>/enviar-encuesta.php" class="nav-link submenu <?php echo $currentPage === 'enviar-encuesta.php' ? 'active' : ''; ?>" data-search="encuesta enviar cliente">
            <i class="fas fa-paper-plane"></i> Enviar Encuesta
        </a>
        
        <a href="<?php echo BASE_URL; ?>/analitica-reportes.php" class="nav-link <?php echo $currentPage === 'analitica-reportes.php' ? 'active' : ''; ?>" data-search="analitica reportes estadisticas graficos">
            <i class="fas fa-chart-bar"></i> Analítica y Reportes
        </a>
        <a href="<?php echo BASE_URL; ?>/nuevo-reporte.php" class="nav-link submenu <?php echo $currentPage === 'nuevo-reporte.php' ? 'active' : ''; ?>" data-search="nuevo reporte crear">
            <i class="fas fa-file-alt"></i> Nuevo Reporte
        </a>
        
        <a href="<?php echo BASE_URL; ?>/gestion-clientes.php" class="nav-link <?php echo $currentPage === 'gestion-clientes.php' ? 'active' : ''; ?>" data-search="gestion clientes contactos">
            <i class="fas fa-users"></i> Gestión de Clientes
        </a>
        <a href="<?php echo BASE_URL; ?>/nuevo-cliente.php" class="nav-link submenu <?php echo $currentPage === 'nuevo-cliente.php' ? 'active' : ''; ?>" data-search="nuevo cliente agregar">
            <i class="fas fa-user-plus"></i> Nuevo Cliente
        </a>
        
        <a href="<?php echo BASE_URL; ?>/administracion-financiera.php" class="nav-link <?php echo $currentPage === 'administracion-financiera.php' ? 'active' : ''; ?>" data-search="administracion financiera contabilidad dinero">
            <i class="fas fa-dollar-sign"></i> Administración Financiera
        </a>
        <a href="<?php echo BASE_URL; ?>/nueva-transaccion.php" class="nav-link submenu <?php echo $currentPage === 'nueva-transaccion.php' ? 'active' : ''; ?>" data-search="nueva transaccion pago cobro">
            <i class="fas fa-money-bill"></i> Nueva Transacción
        </a>
    </div>
    
    <!-- User Profile -->
    <div class="user-profile">
        <div class="user-info">
            <div class="user-name">
                <i class="fas fa-user-circle me-2"></i> 
                <?php echo htmlspecialchars($currentUser['nombre']); ?>
            </div>
            <div class="user-role"><?php echo htmlspecialchars($currentUser['rol']); ?></div>
        </div>
        <a href="<?php echo BASE_URL; ?>/mi-perfil.php" class="nav-link" data-search="mi perfil configuracion usuario">
            <i class="fas fa-user-cog"></i> Mi Perfil
        </a>
        <a href="<?php echo BASE_URL; ?>/index.php?action=logout" class="nav-link" onclick="return confirm('¿Está seguro que desea cerrar sesión?')">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </a>
    </div>
</div>

<script>
// Menu search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('menuSearch');
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            navLinks.forEach(function(link) {
                const searchData = link.getAttribute('data-search') || '';
                const linkText = link.textContent.toLowerCase();
                const searchableText = (searchData + ' ' + linkText).toLowerCase();
                
                if (searchableText.includes(searchTerm)) {
                    link.style.display = '';
                } else {
                    link.style.display = 'none';
                }
            });
            
            // Show/hide section titles based on visible links
            document.querySelectorAll('.nav-section').forEach(function(section) {
                const visibleLinks = section.querySelectorAll('.nav-link:not([style*="display: none"])');
                if (visibleLinks.length === 0) {
                    section.style.display = 'none';
                } else {
                    section.style.display = '';
                }
            });
        });
    }
    
    // Toggle sidebar on mobile
    const hamburgerBtn = document.querySelector('.hamburger-btn');
    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        });
    }
    
    // Close sidebar when clicking on overlay
    const overlay = document.querySelector('.sidebar-overlay');
    if (overlay) {
        overlay.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.remove('active');
            this.classList.remove('active');
        });
    }
    
    // Close sidebar when clicking on menu items on mobile
    navLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 991) {
                document.querySelector('.sidebar').classList.remove('active');
                document.querySelector('.sidebar-overlay').classList.remove('active');
            }
        });
    });
});
</script>
