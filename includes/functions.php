<?php
/**
 * NOVA TECH - Helper Functions
 * 
 * Reusable utility functions used across the entire application.
 * Include this file on every page: require_once 'includes/functions.php';
 */

// ============================================================
// SECURITY FUNCTIONS
// ============================================================

/**
 * Sanitize user input - removes unwanted characters and HTML tags.
 * Use this on ALL user input before displaying or processing.
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate a CSRF token and store it in the session.
 * CSRF = Cross-Site Request Forgery. This prevents attackers
 * from submitting fake forms on behalf of your users.
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify that a submitted CSRF token matches the session token.
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Output a hidden input field with the CSRF token for forms.
 */
function csrf_field() {
    echo '<input type="hidden" name="csrf_token" value="' . generate_csrf_token() . '">';
}

/**
 * Check if the current user is logged in.
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if the current user is an admin.
 */
function is_admin() {
    return is_logged_in() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Require the user to be logged in. Redirect to login page if not.
 */
function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . SITE_URL . '/login.php');
        exit;
    }
}

/**
 * Require the user to be an admin. Redirect to home if not.
 */
function require_admin() {
    if (!is_admin()) {
        header('Location: ' . SITE_URL . '/login.php');
        exit;
    }
}

// ============================================================
// URL & REDIRECT FUNCTIONS
// ============================================================

/**
 * Redirect to a URL and stop script execution.
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Get the base URL of the site (handles subdirectories).
 */
function base_url($path = '') {
    $base = SITE_URL;
    if (!empty($path)) {
        $base .= '/' . ltrim($path, '/');
    }
    return $base;
}

/**
 * Get the current page filename (e.g., "index.php", "about.php").
 */
function current_page() {
    return basename($_SERVER['PHP_SELF']);
}

// ============================================================
// DATABASE HELPER FUNCTIONS
// ============================================================

/**
 * Fetch a single row from the database.
 * 
 * Usage:
 *   $user = fetch_one($pdo, "SELECT * FROM users WHERE id = ?", [$id]);
 */
function fetch_one($pdo, $sql, $params = []) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
}

/**
 * Fetch multiple rows from the database.
 * 
 * Usage:
 *   $projects = fetch_all($pdo, "SELECT * FROM projects WHERE is_active = 1");
 */
function fetch_all($pdo, $sql, $params = []) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Count rows matching a query.
 * 
 * Usage:
 *   $count = count_rows($pdo, "SELECT COUNT(*) as total FROM users");
 *   echo $count; // e.g., 15
 */
function count_rows($pdo, $sql, $params = []) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

/**
 * Execute an INSERT, UPDATE, or DELETE query.
 * Returns the number of affected rows.
 */
function execute_query($pdo, $sql, $params = []) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

// ============================================================
// FILE UPLOAD FUNCTIONS
// ============================================================

/**
 * Handle file upload and return the saved filename.
 * Validates file type and size for security.
 * 
 * Usage:
 *   $filename = upload_file($_FILES['image'], 'uploads/projects', ['jpg','png','webp']);
 */
function upload_file($file, $destination, $allowed_types = ['jpg', 'jpeg', 'png', 'webp'], $max_size = 5242880) {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'File upload failed. Error code: ' . $file['error']];
    }
    
    // Check file size (default 5MB)
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'File too large. Maximum size: ' . ($max_size / 1024 / 1024) . 'MB'];
    }
    
    // Check file extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_types)) {
        return ['success' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowed_types)];
    }
    
    // Generate a unique filename to prevent overwriting and path traversal attacks
    $filename = uniqid('file_', true) . '.' . $ext;
    $filepath = $destination . '/' . $filename;
    
    // Create destination directory if it doesn't exist
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    
    // Move the uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'error' => 'Failed to save uploaded file.'];
}

/**
 * Delete a file from the server.
 */
function delete_file($filepath) {
    if (!empty($filepath) && file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

// ============================================================
// OUTPUT FUNCTIONS
// ============================================================

/**
 * Set a flash message that disappears after one page load.
 * Used for success/error messages after form submissions.
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear the flash message.
 * Returns HTML for the flash message, or empty string if none.
 */
function get_flash() {
    if (isset($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type'];
        $message = $_SESSION['flash']['message'];
        unset($_SESSION['flash']);
        
        $class = ($type === 'success') ? 'alert--success' : 'alert--error';
        return '<div class="alert ' . $class . '">' . sanitize($message) . '</div>';
    }
    return '';
}

/**
 * Get a setting value from the database.
 */
function get_setting($pdo, $key, $default = '') {
    $result = fetch_one($pdo, "SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
    return $result ? $result['setting_value'] : $default;
}

/**
 * Truncate text to a specified length with "..." at the end.
 */
function truncate($text, $length = 150) {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

/**
 * Create a URL-friendly slug from a string.
 */
function create_slug($string) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    return $slug;
}

/**
 * Format a date string nicely.
 */
function format_date($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}
