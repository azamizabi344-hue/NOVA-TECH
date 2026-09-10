<?php
/**
 * NOVA TECH - Common Header Component
 * 
 * This file is included at the top of every public PHP page.
 * It outputs the HTML <head>, opening <body>, and the navigation bar.
 * The class names match css/style.css so the existing design is preserved.
 * 
 * Variables expected:
 *   $page_title - string - Page title (appended with site name)
 *   $pdo        - PDO object - Database connection
 */

// Default values if not provided
if (!isset($page_title)) $page_title = 'NOVA TECH';
if (!isset($page_description)) {
    $page_description = 'NOVA TECH - Leading software development company specializing in web development, mobile apps, AI solutions, and cloud services.';
}

// Load site settings from database
$site_name = get_setting($pdo, 'site_name', 'NOVA TECH');

// Active page detection for navbar highlighting
$current = basename($_SERVER['PHP_SELF']);
function nav_active($page, $current) {
    return $page === $current ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= sanitize($page_description) ?>">
    <meta name="author" content="<?= sanitize($site_name) ?>">
    <title><?= sanitize($page_title) ?> | <?= sanitize($site_name) ?></title>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/animations.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/responsive.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<!-- ============================================================
     NAVBAR / HEADER
============================================================ -->
<header class="navbar" id="navbar">
    <div class="container navbar__inner">
        <!-- Logo -->
        <a href="<?= base_url('index.php') ?>" class="navbar__logo" aria-label="NOVA TECH home">
            <span class="navbar__logo-icon" aria-hidden="true">N</span>
            <span class="navbar__logo-text">NOVA<span>TECH</span></span>
        </a>

        <!-- Desktop navigation links -->
        <nav class="navbar__menu" id="navbar-menu" aria-label="Main navigation">
            <ul class="navbar__list">
                <li><a href="<?= base_url('index.php') ?>" class="navbar__link <?= nav_active('index.php', $current) ?>">Home</a></li>
                <li><a href="<?= base_url('about.php') ?>" class="navbar__link <?= nav_active('about.php', $current) ?>">About</a></li>
                <li><a href="<?= base_url('services.php') ?>" class="navbar__link <?= nav_active('services.php', $current) ?>">Services</a></li>
                <li><a href="<?= base_url('projects.php') ?>" class="navbar__link <?= nav_active('projects.php', $current) ?>">Projects</a></li>
                <li><a href="<?= base_url('team.php') ?>" class="navbar__link <?= nav_active('team.php', $current) ?>">Team</a></li>
                <li><a href="<?= base_url('blog.php') ?>" class="navbar__link <?= nav_active('blog.php', $current) ?>">Blog</a></li>
                <li><a href="<?= base_url('contact.php') ?>" class="navbar__link <?= nav_active('contact.php', $current) ?>">Contact</a></li>
            </ul>
        </nav>

        <!-- Right side actions -->
        <div class="navbar__actions">
            <?php if (is_logged_in()): ?>
                <?php if (is_admin()): ?>
                    <a href="<?= base_url('admin/index.php') ?>" class="btn btn--primary btn--sm">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                <?php endif; ?>
                <a href="<?= base_url('logout.php') ?>" class="btn btn--ghost btn--sm">Logout</a>
            <?php else: ?>
                <a href="<?= base_url('login.php') ?>" class="btn btn--ghost btn--sm">Login</a>
            <?php endif; ?>
            <button class="navbar__toggle" id="navbar-toggle" aria-label="Toggle navigation menu" aria-expanded="false">
                <span class="navbar__toggle-bar"></span>
                <span class="navbar__toggle-bar"></span>
                <span class="navbar__toggle-bar"></span>
            </button>
        </div>
    </div>
</header>