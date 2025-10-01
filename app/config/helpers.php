<?php
/**
 * Funciones Helper del Sistema
 * Funciones de utilidad general
 */

/**
 * Limpia y valida una entrada
 */
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Valida un email
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Genera un número de pedido único
 */
function generate_order_number($prefix = 'PED') {
    return $prefix . '-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

/**
 * Genera un número de lote único
 */
function generate_batch_number($prefix = 'LOTE') {
    return $prefix . '-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

/**
 * Formatea un número como moneda
 */
function format_currency($amount, $currency = 'S/') {
    return $currency . ' ' . number_format($amount, 2, '.', ',');
}

/**
 * Formatea una fecha
 */
function format_date($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * Formatea una fecha y hora
 */
function format_datetime($datetime, $format = 'd/m/Y H:i') {
    if (empty($datetime)) return '';
    return date($format, strtotime($datetime));
}

/**
 * Calcula la diferencia de días entre dos fechas
 */
function days_between($date1, $date2) {
    $datetime1 = new DateTime($date1);
    $datetime2 = new DateTime($date2);
    $interval = $datetime1->diff($datetime2);
    return $interval->days;
}

/**
 * Verifica si una fecha está vencida
 */
function is_expired($date) {
    return strtotime($date) < time();
}

/**
 * Genera un slug desde un texto
 */
function generate_slug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

/**
 * Redirecciona a una URL
 */
function redirect($url) {
    if (!headers_sent()) {
        header('Location: ' . BASE_URL . '/' . $url);
        exit;
    }
    echo '<script>window.location.href="' . BASE_URL . '/' . $url . '";</script>';
    exit;
}

/**
 * Muestra un mensaje de error y termina la ejecución
 */
function die_error($message) {
    http_response_code(500);
    die('Error: ' . htmlspecialchars($message));
}

/**
 * Verifica si un valor está en un rango
 */
function in_range($value, $min, $max) {
    return ($value >= $min && $value <= $max);
}

/**
 * Trunca un texto a una longitud específica
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Obtiene el rol en español
 */
function get_role_name($role) {
    $roles = [
        'admin' => 'Administrador',
        'operador' => 'Operador',
        'vendedor' => 'Vendedor',
        'logistica' => 'Logística'
    ];
    return $roles[$role] ?? $role;
}

/**
 * Obtiene la clase CSS para un estado
 */
function get_status_class($status) {
    $classes = [
        'activo' => 'success',
        'inactivo' => 'secondary',
        'pendiente' => 'warning',
        'completado' => 'success',
        'cancelado' => 'danger',
        'en_proceso' => 'info',
        'aprobado' => 'success',
        'rechazado' => 'danger'
    ];
    return $classes[$status] ?? 'secondary';
}

/**
 * Genera un token CSRF
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica un token CSRF
 */
function verify_csrf_token($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Registra una actividad en log
 */
function log_activity($message, $level = 'INFO') {
    $logFile = ROOT_PATH . '/logs/app.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $user = $_SESSION['user_email'] ?? 'guest';
    $logMessage = "[{$timestamp}] [{$level}] [{$user}] {$message}" . PHP_EOL;
    
    @file_put_contents($logFile, $logMessage, FILE_APPEND);
}

/**
 * Valida un RUC peruano (básico)
 */
function validate_ruc($ruc) {
    // Debe tener 11 dígitos
    if (!preg_match('/^\d{11}$/', $ruc)) {
        return false;
    }
    return true;
}

/**
 * Valida un número de teléfono peruano
 */
function validate_phone($phone) {
    // Acepta formatos: 999-888-777, 999888777, +51999888777
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return strlen($phone) >= 9 && strlen($phone) <= 11;
}

/**
 * Sanitiza un array de datos
 */
function sanitize_array($array) {
    $clean = [];
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $clean[$key] = sanitize_array($value);
        } else {
            $clean[$key] = clean_input($value);
        }
    }
    return $clean;
}

/**
 * Genera un número de retorno único
 */
function generate_return_number($prefix = 'RET') {
    return $prefix . '-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}
