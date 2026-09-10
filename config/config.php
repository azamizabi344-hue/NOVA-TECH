<?php
/**
 * NOVA TECH - Site Configuration
 * 
 * This file holds all site-wide settings:
 * - Database credentials
 * - Site URLs and paths
 * - Error reporting settings
 * 
 * IMPORTANT: Never commit this file to public repositories!
 * In production, use environment variables instead.
 */

// ============================================================
// Database Configuration
// ============================================================
define('DB_HOST', 'localhost');        // Database server (usually localhost)
define('DB_NAME', 'nova_tech');        // Database name we created in database.sql
define('DB_USER', 'root');             // MySQL username (default XAMPP is 'root')
define('DB_PASS', '');                 // MySQL password (default XAMPP is empty)
define('DB_CHARSET', 'utf8mb4');       // Character set for full Unicode support

// ============================================================
// Site Configuration
// ============================================================
define('SITE_NAME', 'NOVA TECH');
define('SITE_URL', 'http://localhost/NOVA-TECH');  // Change this to your URL
define('SITE_EMAIL', 'info@novatech.com');

// ============================================================
// Path Configuration  
// ============================================================
define('ROOT_PATH', dirname(__DIR__));           // One level up from config/
define('UPLOADS_PATH', ROOT_PATH . '/uploads');  // Where uploaded files go
define('INCLUDES_PATH', ROOT_PATH . '/includes'); // Reusable PHP components

// ============================================================
// Error Reporting
// ============================================================
// In development, show all errors. In production, hide them and log instead.
error_reporting(E_ALL);
ini_set('display_errors', 1);   // Set to 0 in production
ini_set('log_errors', 1);

// ============================================================
// Session Configuration (security best practices)
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    // These settings should be set BEFORE session_start()
    ini_set('session.cookie_httponly', 1);   // Prevent JavaScript access to session cookie
    ini_set('session.use_strict_mode', 1);   // Reject uninitialized session IDs
    ini_set('session.cookie_samesite', 'Lax'); // CSRF protection for cookies
    session_start();
}

// ============================================================
// Timezone
// ============================================================
date_default_timezone_set('America/New_York');
