<?php
/**
 * NOVA TECH - Admin Layout Opening
 * 
 * Use this at the top of every admin page.
 * It requires admin authentication and outputs the HTML head.
 * 
 * Variables expected:
 *   $page_title       - string - Admin page title
 *   $current_section  - string - Which sidebar item is active
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Protect the entire admin section - only admins can access these pages
require_admin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= sanitize($page_title ?? 'Dashboard') ?> - NOVA TECH Admin</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/animations.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/responsive.css') ?>">
    <link rel="stylesheet" href="<?= base_url('admin/css/admin.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="admin-body">

<div class="admin-layout">
<?php include __DIR__ . '/sidebar.php'; ?>

<main class="admin__main">
    <?php include __DIR__ . '/header.php'; ?>
    
    <div class="admin__content">
    <?php if (isset($_SESSION['flash'])) echo get_flash(); ?>