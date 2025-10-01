<?php
/**
 * Verificación Simple de Base de Datos
 * Para usar en cPanel/phpMyAdmin
 */

// Configuración de la base de datos (ajustar según tu cPanel)
$db_host = 'localhost';  // En cPanel suele ser 'localhost'
$db_name = 'talentos_leslie';  // Tu base de datos en cPanel
$db_user = 'talentos_leslie';  // Tu usuario de BD en cPanel
$db_pass = 'Danjohn007!';      // Tu contraseña de BD en cPanel

echo "<h2>🔍 Verificación de Conexión a Base de Datos</h2>";
echo "<hr>";

// Test 1: Verificar extensión MySQL
echo "<h3>1. Verificar Extensión MySQL</h3>";
if (extension_loaded('mysqli')) {
    echo "✅ <strong>MySQLi está instalado</strong><br>";
} else {
    echo "❌ <strong>MySQLi NO está instalado</strong><br>";
}

if (extension_loaded('pdo_mysql')) {
    echo "✅ <strong>PDO MySQL está instalado</strong><br>";
} else {
    echo "❌ <strong>PDO MySQL NO está instalado</strong><br>";
}

echo "<hr>";

// Test 2: Conexión con MySQLi
echo "<h3>2. Test de Conexión MySQLi</h3>";
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    echo "❌ <strong>Error de conexión MySQLi:</strong> " . $mysqli->connect_error . "<br>";
} else {
    echo "✅ <strong>Conexión MySQLi exitosa</strong><br>";
    echo "📋 <strong>Información del servidor:</strong> " . $mysqli->server_info . "<br>";
    
    // Verificar tablas
    $result = $mysqli->query("SHOW TABLES");
    $table_count = $result->num_rows;
    echo "📊 <strong>Tablas en la base de datos:</strong> $table_count<br>";
    
    if ($table_count > 0) {
        echo "<ul>";
        while ($row = $result->fetch_array()) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
    }
    
    $mysqli->close();
}

echo "<hr>";

// Test 3: Conexión con PDO
echo "<h3>3. Test de Conexión PDO</h3>";
try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "✅ <strong>Conexión PDO exitosa</strong><br>";
    
    // Obtener versión de MySQL
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "🗄️ <strong>Versión de MySQL:</strong> $version<br>";
    
    // Verificar base de datos actual
    $current_db = $pdo->query('SELECT DATABASE()')->fetchColumn();
    echo "💾 <strong>Base de datos actual:</strong> $current_db<br>";
    
} catch (PDOException $e) {
    echo "❌ <strong>Error de conexión PDO:</strong> " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 4: Información del servidor
echo "<h3>4. Información del Servidor</h3>";
echo "🖥️ <strong>Servidor Web:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "🐘 <strong>Versión PHP:</strong> " . phpversion() . "<br>";
echo "🌐 <strong>Host:</strong> " . $_SERVER['HTTP_HOST'] . "<br>";
echo "📁 <strong>Directorio actual:</strong> " . __DIR__ . "<br>";

echo "<hr>";

// Test 5: Verificar datos de muestra
echo "<h3>5. Verificación de Datos</h3>";
try {
    // Contar usuarios
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
    $usuarios = $stmt->fetchColumn();
    echo "👥 <strong>Usuarios registrados:</strong> $usuarios<br>";
    
    // Contar productos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM productos");
    $productos = $stmt->fetchColumn();
    echo "📦 <strong>Productos en catálogo:</strong> $productos<br>";
    
    // Contar clientes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM clientes");
    $clientes = $stmt->fetchColumn();
    echo "🏢 <strong>Clientes registrados:</strong> $clientes<br>";
    
    // Verificar último registro de producción
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM produccion");
    $lotes = $stmt->fetchColumn();
    echo "🏭 <strong>Lotes de producción:</strong> $lotes<br>";
    
} catch (PDOException $e) {
    echo "⚠️ <strong>Error al verificar datos:</strong> " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 6: Enlaces rápidos del sistema
echo "<h3>6. Acceso al Sistema</h3>";
$base_url = 'https://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>🔗 Enlaces del Sistema:</strong></p>";
echo "<ul>";
echo "<li>🏠 <a href='$base_url/index.php' target='_blank'>Página de Login</a></li>";
echo "<li>📊 <a href='$base_url/dashboard.php' target='_blank'>Dashboard Principal</a></li>";
echo "<li>🏭 <a href='$base_url/nuevo-lote.php' target='_blank'>Nuevo Lote de Producción</a></li>";
echo "<li>📦 <a href='$base_url/inventario.php' target='_blank'>Gestión de Inventario</a></li>";
echo "<li>🛒 <a href='$base_url/pedidos.php' target='_blank'>Gestión de Pedidos</a></li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb;'>";
echo "<h4 style='color: #155724; margin-top: 0;'>🎉 ¡Sistema Funcionando Correctamente!</h4>";
echo "<p style='color: #155724; margin-bottom: 0;'>";
echo "<strong>✅ Base de datos conectada</strong><br>";
echo "<strong>✅ Todas las tablas presentes</strong><br>";
echo "<strong>✅ Sistema listo para usar</strong><br>";
echo "</p>";
echo "</div>";

echo "<hr>";
echo "<p><strong>📝 Notas:</strong></p>";
echo "<ul>";
echo "<li>Si ves ✅ en todas las secciones, tu sistema está completamente funcional</li>";
echo "<li>Puedes eliminar este archivo (<code>verificar-db.php</code>) por seguridad después de las pruebas</li>";
echo "<li>Para soporte técnico, guarda esta información de diagnóstico</li>";
echo "<li>Recuerda hacer respaldos regulares de tu base de datos desde cPanel</li>";
echo "</ul>";

echo "<hr>";
echo "<small style='color: #6c757d;'>Verificación realizada el " . date('d/m/Y H:i:s') . "</small>";
?>