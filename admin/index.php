<?php
/**
 * NOVA TECH - Admin Dashboard
 * 
 * The main admin overview page.
 * Shows statistics cards for all major data:
 * - Users
 * - Projects
 * - Messages
 * - Team members
 * - Blog posts
 * - Newsletter subscribers
 * 
 * Data is counted with SQL COUNT() queries.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// ANY admin page using layout_start protects itself:
$current_section = 'dashboard';
$admin_page_title = 'Dashboard';

// ============================================================
// STATISTICS - COUNT records from each table
// ============================================================
$stats = [];

// Total users (COUNT(*) counts all rows)
$stats['users'] = count_rows($pdo, "SELECT COUNT(*) as total FROM users");

// Total projects (only active ones shown to public, but admin sees all)
$stats['projects'] = count_rows($pdo, "SELECT COUNT(*) as total FROM projects");

// Total contact messages
$stats['messages'] = count_rows($pdo, "SELECT COUNT(*) as total FROM contact_messages");

// Unread messages (for badge display)
$stats['unread'] = count_rows($pdo, "SELECT COUNT(*) as total FROM contact_messages WHERE is_read = 0");

// Total team members
$stats['team'] = count_rows($pdo, "SELECT COUNT(*) as total FROM team_members");

// Total blog posts
$stats['blog'] = count_rows($pdo, "SELECT COUNT(*) as total FROM blog_posts");

// Total newsletter subscribers
$stats['newsletter'] = count_rows($pdo, "SELECT COUNT(*) as total FROM newsletter_subscribers WHERE is_active = 1");

// Total services
$stats['services'] = count_rows($pdo, "SELECT COUNT(*) as total FROM services");

// ============================================================
// RECENT DATA - latest 5 of each type
// ============================================================
$recent_projects = fetch_all($pdo, "
    SELECT * FROM projects 
    ORDER BY created_at DESC 
    LIMIT 5
");

$recent_messages = fetch_all($pdo, "
    SELECT * FROM contact_messages 
    ORDER BY created_at DESC 
    LIMIT 5
");

$recent_users = fetch_all($pdo, "
    SELECT * FROM users 
    ORDER BY created_at DESC 
    LIMIT 5
");

$recent_blog = fetch_all($pdo, "
    SELECT bp.*, c.name as category_name 
    FROM blog_posts bp 
    LEFT JOIN categories c ON bp.category_id = c.id 
    ORDER BY bp.created_at DESC 
    LIMIT 5
");

$page_title = 'Dashboard';
include __DIR__ . '/includes/layout_start.php';
?>

<!-- ============================================================
     STATISTICS CARDS
============================================================ -->
<div class="admin-cards">
    <div class="admin-card">
        <div class="admin-card__icon"><i class="fas fa-users"></i></div>
        <div class="admin-card__label">Total Users</div>
        <div class="admin-card__value"><?= (int)$stats['users'] ?></div>
        <div class="admin-card__trend"><i class="fas fa-arrow-up"></i> Registered accounts</div>
    </div>
    <div class="admin-card">
        <div class="admin-card__icon"><i class="fas fa-folder-open"></i></div>
        <div class="admin-card__label">Projects</div>
        <div class="admin-card__value"><?= (int)$stats['projects'] ?></div>
        <div class="admin-card__trend"><i class="fas fa-arrow-up"></i> Portfolio items</div>
    </div>
    <div class="admin-card">
        <div class="admin-card__icon"><i class="fas fa-envelope"></i></div>
        <div class="admin-card__label">Messages</div>
        <div class="admin-card__value"><?= (int)$stats['messages'] ?></div>
        <div class="admin-card__trend" style="color: <?= $stats['unread'] > 0 ? 'var(--color-warm)' : '#34d399' ?>;">
            <?= (int)$stats['unread'] ?> unread
        </div>
    </div>
    <div class="admin-card">
        <div class="admin-card__icon"><i class="fas fa-users"></i></div>
        <div class="admin-card__label">Team Members</div>
        <div class="admin-card__value"><?= (int)$stats['team'] ?></div>
        <div class="admin-card__trend"><i class="fas fa-arrow-up"></i> Staff</div>
    </div>
    <div class="admin-card">
        <div class="admin-card__icon"><i class="fas fa-newspaper"></i></div>
        <div class="admin-card__label">Blog Posts</div>
        <div class="admin-card__value"><?= (int)$stats['blog'] ?></div>
        <div class="admin-card__trend"><i class="fas fa-arrow-up"></i> Published articles</div>
    </div>
    <div class="admin-card">
        <div class="admin-card__icon"><i class="fas fa-paper-plane"></i></div>
        <div class="admin-card__label">Subscribers</div>
        <div class="admin-card__value"><?= (int)$stats['newsletter'] ?></div>
        <div class="admin-card__trend"><i class="fas fa-arrow-up"></i> Newsletter</div>
    </div>
</div>

<!-- ============================================================
     RECENT MESSAGES
============================================================ -->
<div class="admin-panel">
    <h2 class="admin-panel__title"><i class="fas fa-envelope-open-text"></i> Recent Messages</h2>
    <?php if (empty($recent_messages)): ?>
        <p class="text-muted">No messages yet.</p>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>Subject</th>
                        <th>Received</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($recent_messages as $msg): ?>
                    <tr>
                        <td>
                            <strong><?= sanitize($msg['name']) ?></strong><br>
                            <small class="text-muted"><?= sanitize($msg['email']) ?></small>
                        </td>
                        <td><?= sanitize(truncate($msg['subject'], 40)) ?></td>
                        <td><?= format_date($msg['created_at']) ?></td>
                        <td>
                            <?php if ($msg['is_read']): ?>
                                <span class="badge badge--done">Read</span>
                            <?php else: ?>
                                <span class="badge badge--pending">New</span>
                            <?php endif; ?>
                        </td>
                        <td><a href="<?= base_url('admin/messages.php') ?>" class="btn btn--ghost btn--sm">View</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="admin-panel">
    <h2 class="admin-panel__title"><i class="fas fa-folder-open"></i> Recent Projects</h2>
    <?php if (empty($recent_projects)): ?>
        <p class="text-muted">No projects yet.</p>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Category</th>
                        <th>Client</th>
                        <th>Created</th>
                        <th>Featured</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($recent_projects as $project): ?>
                    <tr>
                        <td><strong><?= sanitize($project['title']) ?></strong></td>
                        <td><?= sanitize($project['category']) ?></td>
                        <td><?= sanitize($project['client'] ?? '-') ?></td>
                        <td><?= format_date($project['created_at']) ?></td>
                        <td>
                            <?php if ($project['is_featured']): ?>
                                <span class="badge badge--active">Featured</span>
                            <?php else: ?>
                                <span class="badge">Standard</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="admin-panel">
    <h2 class="admin-panel__title"><i class="fas fa-newspaper"></i> Recent Blog Posts</h2>
    <?php if (empty($recent_blog)): ?>
        <p class="text-muted">No posts yet.</p>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Views</th>
                        <th>Published</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($recent_blog as $post): ?>
                    <tr>
                        <td><strong><?= sanitize(truncate($post['title'], 45)) ?></strong></td>
                        <td><?= sanitize($post['category_name'] ?? '-') ?></td>
                        <td><?= (int)$post['views'] ?></td>
                        <td><?= format_date($post['created_at']) ?></td>
                        <td>
                            <?php if ($post['is_published']): ?>
                                <span class="badge badge--done">Published</span>
                            <?php else: ?>
                                <span class="badge badge--pending">Draft</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="admin-panel">
    <h2 class="admin-panel__title"><i class="fas fa-users"></i> Recent Users</h2>
    <?php if (empty($recent_users)): ?>
        <p class="text-muted">No users yet.</p>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Registered</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($recent_users as $usr): ?>
                    <tr>
                        <td>
                            <span class="avatar-circle"><?= strtoupper(substr($usr['full_name'], 0, 1)) ?></span>
                            <strong><?= sanitize($usr['full_name']) ?></strong>
                        </td>
                        <td><?= $usr['role'] === 'admin' ? '<span class="badge badge--done">Admin</span>' : '<span class="badge">User</span>' ?></td>
                        <td><?= format_date($usr['created_at']) ?></td>
                        <td><?= $usr['is_active'] ? '<span class="badge badge--done">Active</span>' : '<span class="badge badge--pending">Banned</span>' ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/layout_end.php'; ?>