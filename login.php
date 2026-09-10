<?php
/**
 * NOVA TECH - Login Page
 * 
 * This page handles user login.
 * - Displays a login form
 * - Processes form submissions via POST
 * - Verifies password with password_verify()
 * - Sets session variables on success
 * - Supports "remember me" functionality
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect(base_url(is_admin() ? 'admin/index.php' : 'index.php'));
}

$error = '';

// ============================================================
// PROCESS LOGIN FORM SUBMISSION
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validate input
        if (empty($email) || empty($password)) {
            $error = 'Please fill in all fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            // Look up the user in the database
            // We use a prepared statement with ? placeholder to prevent SQL injection
            $user = fetch_one($pdo, 
                "SELECT * FROM users WHERE email = ? AND is_active = 1", 
                [$email]
            );
            
            // password_verify() checks the submitted password against the stored bcrypt hash
            if ($user && password_verify($password, $user['password'])) {
                // Login successful! Set session variables
                // These session variables are available on every page until the user logs out
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                // Generate new session ID to prevent session fixation attacks
                session_regenerate_id(true);
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    redirect(base_url('admin/index.php'));
                } else {
                    redirect(base_url('index.php'));
                }
            } else {
                // Wrong credentials - use a generic message for security
                // (don't reveal whether the email exists or not)
                $error = 'Invalid email or password.';
            }
        }
    }
}

$page_title = 'Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NOVA TECH</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/animations.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/responsive.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="auth-page">

<!-- ============================================================
     LOGIN FORM
============================================================ -->
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-card__header">
            <a href="<?= base_url('index.php') ?>" class="auth-card__logo">
                <i class="fas fa-rocket"></i>
                <span>NOVA TECH</span>
            </a>
            <h1>Welcome Back</h1>
            <p>Sign in to your account to continue</p>
        </div>
        
        <!-- Error message display -->
        <?php if ($error): ?>
            <div class="alert alert--error"><?= sanitize($error) ?></div>
        <?php endif; ?>
        
        <!-- Flash messages (e.g., after registration) -->
        <?= get_flash() ?>
        
        <form class="auth-form" method="POST" action="<?= base_url('login.php') ?>" novalidate>
            <?= csrf_field() ?>
            
            <div class="auth-form__group">
                <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" 
                       value="<?= sanitize($_POST['email'] ?? '') ?>" required autofocus>
            </div>
            
            <div class="auth-form__group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <div class="auth-form__password-wrapper">
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <button type="button" class="auth-form__toggle-password" aria-label="Toggle password visibility">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="btn btn--primary btn--full">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>
        
        <div class="auth-card__footer">
            <p>Don't have an account? <a href="<?= base_url('register.php') ?>">Create Account</a></p>
        </div>
        
        <div class="auth-card__demo">
            <p><strong>Demo Credentials:</strong></p>
            <p>Admin: admin@novatech.com / password</p>
            <p>User: user@novatech.com / password</p>
        </div>
    </div>
</div>

<script>
// Password visibility toggle
document.querySelector('.auth-form__toggle-password')?.addEventListener('click', function() {
    const input = document.getElementById('password');
    const icon = this.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
});
</script>

</body>
</html>
