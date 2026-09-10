<?php
/**
 * NOVA TECH - Logout
 * 
 * This script destroys the user's session and redirects to the home page.
 * It's important to properly destroy sessions on logout for security.
 */

require_once __DIR__ . '/config/config.php';

// Unset all session variables
$_SESSION = [];

// Destroy the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session on the server
session_destroy();

// Redirect to home page
header('Location: ' . SITE_URL . '/index.php');
exit;
