<?php
/**
 * NOVA TECH - Admin Header Component
 * 
 * This header appears at the top of every admin page.
 * It shows the page title and a mobile toggle for the sidebar.
 * 
 * Variables expected:
 *   $admin_page_title - string - Title shown in the header
 */

if (!isset($admin_page_title)) {
    $admin_page_title = 'Dashboard';
}
?>
<header class="admin__header">
    <button class="admin__menu-toggle" id="adminMenuToggle" aria-label="Toggle sidebar">
        <i class="fas fa-bars"></i>
    </button>
    <h1 class="admin__page-title"><?= sanitize($admin_page_title) ?></h1>
    <div class="admin__header-actions">
        <span class="admin__user-info">
            <i class="fas fa-user-circle"></i>
            <?= sanitize($_SESSION['user_name'] ?? 'Admin') ?>
        </span>
    </div>
</header>
