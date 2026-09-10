<?php
/**
 * NOVA TECH - Register Page
 * 
 * This page handles new user registration.
 * - Displays a registration form
 * - Validates input (name, email, password)
 * - Hashes the password with password_hash()
 * - Inserts the new user into the database
 * - Redirects to login page on success
 * 
 * PASSWORD HASHING EXPLAINED:
 * password_hash() uses the bcrypt algorithm to convert a plain
 * text password into a one-way hash. This means:
 * - You can NEVER reverse it to get the original password
 * - You can VERIFY a password using password_verify()
 * - Even if the database is stolen, passwords remain protected
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect(base_url('index.php'));
}

$error = '';
$success = '';

// ============================================================
// PROCESS REGISTRATION FORM SUBMISSION
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // --- VALIDATION ---
        // We validate every field to ensure data quality and security
        
        if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
            $error = 'Please fill in all fields.';
        } elseif (strlen($full_name) < 2) {
            $error = 'Name must be at least 2 characters.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } else {
            // Check if email already exists in the database
            $existing = fetch_one($pdo, 
                "SELECT id FROM users WHERE email = ?", 
                [$email]
            );
            
            if ($existing) {
                $error = 'An account with this email already exists.';
            } else {
                // --- CREATE THE USER ---
                // password_hash() converts the plain text password into a secure hash
                // PASSWORD_DEFAULT uses bcrypt, which is the recommended algorithm
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare(
                    "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, 'user')"
                );
                $stmt->execute([$full_name, $email, $hashed_password]);
                
                // Set flash message and redirect to login
                set_flash('success', 'Account created successfully! Please sign in.');
                redirect(base_url('login.php'));
            }
        }
    }
}

$page_title = 'Register';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - NOVA TECH</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/animations.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/responsive.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="auth-page">

<!-- ============================================================
     REGISTRATION FORM
============================================================ -->
<div class="auth-container">
    <div class="auth-card">
        <div class="auth-card__header">
            <a href="<?= base_url('index.php') ?>" class="auth-card__logo">
                <i class="fas fa-rocket"></i>
                <span>NOVA TECH</span>
            </a>
            <h1>Create Account</h1>
            <p>Join NOVA TECH and get started today</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert--error"><?= sanitize($error) ?></div>
        <?php endif; ?>
        
        <form class="auth-form" method="POST" action="<?= base_url('register.php') ?>" novalidate>
            <?= csrf_field() ?>
            
            <div class="auth-form__group">
                <label for="full_name"><i class="fas fa-user"></i> Full Name</label>
                <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" 
                       value="<?= sanitize($_POST['full_name'] ?? '') ?>" required autofocus>
            </div>
            
            <div class="auth-form__group">
                <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" 
                       value="<?= sanitize($_POST['email'] ?? '') ?>" required>
            </div>
            
            <div class="auth-form__group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <div class="auth-form__password-wrapper">
                    <input type="password" id="password" name="password" placeholder="At least 6 characters" required>
                    <button type="button" class="auth-form__toggle-password" aria-label="Toggle password visibility">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="auth-form__group">
                <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat your password" required>
            </div>
            
            <button type="submit" class="btn btn--primary btn--full">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>
        
        <div class="auth-card__footer">
            <p>Already have an account? <a href="<?= base_url('login.php') ?>">Sign In</a></p>
        </div>
    </div>
</div>

<script>
// Password visibility toggle
document.querySelectorAll('.auth-form__toggle-password').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const input = this.previousElementSibling;
        const icon = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
});

// Real-time password match indicator
document.getElementById('confirm_password')?.addEventListener('input', function() {
    const password = document.getElementById('password').value;
    if (this.value && this.value !== password) {
        this.style.borderColor = '#ff6b6b';
    } else if (this.value && this.value === password) {
        this.style.borderColor = '#00d2ff';
    } else {
        this.style.borderColor = '';
    }
});
</script>

</body>
</html>
