<?php
/**
 * NOVA TECH - Admin Sidebar Component
 * 
 * This sidebar is included on every admin page.
 * It provides navigation links to all admin sections.
 * 
 * Variables expected:
 *   $current_section - string - Which admin page is active (e.g., 'projects')
 */

// Default to empty if not set
if (!isset($current_section)) {
    $current_section = '';
}
?>
<aside class="admin__sidebar" id="adminSidebar">
    <div class="admin__sidebar-header">
        <a href="<?= base_url('admin/index.php') ?>" class="admin__logo">
            <i class="fas fa-rocket"></i>
            <span>NOVA TECH</span>
        </a>
        <span class="admin__badge">Admin</span>
    </div>
    
    <nav class="admin__nav">
        <ul class="admin__nav-list">
            <li>
                <a href="<?= base_url('admin/index.php') ?>" class="<?= $current_section === 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/projects.php') ?>" class="<?= $current_section === 'projects' ? 'active' : '' ?>">
                    <i class="fas fa-folder-open"></i>
                    <span>Projects</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/services.php') ?>" class="<?= $current_section === 'services' ? 'active' : '' ?>">
                    <i class="fas fa-cogs"></i>
                    <span>Services</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/team.php') ?>" class="<?= $current_section === 'team' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i>
                    <span>Team Members</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/blog.php') ?>" class="<?= $current_section === 'blog' ? 'active' : '' ?>">
                    <i class="fas fa-newspaper"></i>
                    <span>Blog Posts</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/messages.php') ?>" class="<?= $current_section === 'messages' ? 'active' : '' ?>">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/newsletter.php') ?>" class="<?= $current_section === 'newsletter' ? 'active' : '' ?>">
                    <i class="fas fa-paper-plane"></i>
                    <span>Newsletter</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url('admin/users.php') ?>" class="<?= $current_section === 'users' ? 'active' : '' ?>">
                    <i class="fas fa-user-shield"></i>
                    <span>Users</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="admin__sidebar-footer">
        <a href="<?= base_url('index.php') ?>" class="admin__sidebar-link">
            <i class="fas fa-globe"></i>
            <span>View Website</span>
        </a>
        <a href="<?= base_url('logout.php') ?>" class="admin__sidebar-link admin__sidebar-link--danger">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>
