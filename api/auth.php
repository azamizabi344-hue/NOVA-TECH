<?php
/**
 * NOVA TECH - Auth API Endpoint
 * 
 * This handles AJAX login requests from the frontend.
 * It receives JSON data, processes it, and returns JSON responses.
 * 
 * REST-style endpoints:
 *   POST /api/auth.php?action=login    - Login a user
 *   POST /api/auth.php?action=logout   - Logout a user
 *   GET  /api/auth.php?action=check    - Check if user is logged in
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Get the action from the query string
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    
    // ============================================================
    // LOGIN
    // ============================================================
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
        
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
            exit;
        }
        
        // Find the user
        $user = fetch_one($pdo, 
            "SELECT * FROM users WHERE email = ? AND is_active = 1", 
            [$email]
        );
        
        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            session_regenerate_id(true);
            
            echo json_encode([
                'success' => true,
                'message' => 'Login successful!',
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['full_name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        }
        break;
    
    // ============================================================
    // REGISTER
    // ============================================================
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
        
        $full_name = trim($input['full_name'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        
        if (empty($full_name) || empty($email) || empty($password)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
            exit;
        }
        
        if (strlen($password) < 6) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
            exit;
        }
        
        // Check if email exists
        $existing = fetch_one($pdo, "SELECT id FROM users WHERE email = ?", [$email]);
        if ($existing) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'Email already registered.']);
            exit;
        }
        
        // Create user
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->execute([$full_name, $email, $hashed]);
        
        echo json_encode(['success' => true, 'message' => 'Account created successfully!']);
        break;
    
    // ============================================================
    // CHECK SESSION STATUS
    // ============================================================
    case 'check':
        if (is_logged_in()) {
            echo json_encode([
                'success' => true,
                'logged_in' => true,
                'user' => [
                    'id' => $_SESSION['user_id'],
                    'name' => $_SESSION['user_name'],
                    'email' => $_SESSION['user_email'],
                    'role' => $_SESSION['user_role']
                ]
            ]);
        } else {
            echo json_encode(['success' => true, 'logged_in' => false]);
        }
        break;
    
    // ============================================================
    // LOGOUT
    // ============================================================
    case 'logout':
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
        break;
    
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
