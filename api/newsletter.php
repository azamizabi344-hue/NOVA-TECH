<?php
/**
 * NOVA TECH - Newsletter API Endpoint
 * 
 * Handles newsletter subscription via AJAX.
 *   POST /api/newsletter.php - Subscribe an email
 *   GET  /api/newsletter.php - Get subscriber count (admin)
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
        exit;
    }
    
    // Check if already subscribed
    $existing = fetch_one($pdo, 
        "SELECT id, is_active FROM newsletter_subscribers WHERE email = ?", 
        [$email]
    );
    
    if ($existing) {
        if ($existing['is_active']) {
            echo json_encode(['success' => false, 'message' => 'You are already subscribed!']);
        } else {
            // Re-activate subscription
            execute_query($pdo, 
                "UPDATE newsletter_subscribers SET is_active = 1 WHERE email = ?", 
                [$email]
            );
            echo json_encode(['success' => true, 'message' => 'Welcome back! Your subscription has been reactivated.']);
        }
    } else {
        // New subscriber
        $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
        $stmt->execute([$email]);
        echo json_encode(['success' => true, 'message' => 'Thank you for subscribing!']);
    }
    
} elseif ($method === 'GET') {
    // Only admins can view subscriber list
    $count = count_rows($pdo, "SELECT COUNT(*) as total FROM newsletter_subscribers WHERE is_active = 1");
    echo json_encode(['success' => true, 'count' => $count]);
    
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
}
