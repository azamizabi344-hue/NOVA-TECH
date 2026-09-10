<?php
/**
 * NOVA TECH - Admin Newsletter
 * 
 * View newsletter subscribers, activate/deactivate, delete, and export.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$current_section = 'newsletter';
$admin_page_title = 'Newsletter';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = ['type' => 'error', 'text' => 'Invalid security token.'];
    } else {
        $action = $_POST['action'] ?? '';
        $id = (int)($_POST['id'] ?? 0);
        if ($action === 'delete' && $id > 0) {
            execute_query($pdo, "DELETE FROM newsletter_subscribers WHERE id = ?", [$id]);
            $message = ['type' => 'success', 'text' => 'Subscriber removed.'];
        } elseif ($action === 'toggle' && $id > 0) {
            $sub = fetch_one($pdo, "SELECT is_active FROM newsletter_subscribers WHERE id = ?", [$id]);
            if ($sub) {
                execute_query($pdo, "UPDATE newsletter_subscribers SET is_active = ? WHERE id = ?", [$sub['is_active'] ? 0 : 1, $id]);
                $message = ['type' => 'success', 'text' => 'Subscriber status updated.'];
            }
        }
    }
}

// ============================================================
// LIST SUBSCRIBERS
// ============================================================
$search = trim($_GET['search'] ?? '');
$where = '';
$params = [];
if ($search) { $where = "WHERE email LIKE ?"; $params[] = '%'.$search.'%'; }

$subscribers = fetch_all($pdo, "SELECT * FROM newsletter_subscribers $where ORDER BY created_at DESC", $params);
$total_count = count_rows($pdo, "SELECT COUNT(*) as total FROM newsletter_subscribers");
$active_count = count_rows($pdo, "SELECT COUNT(*) as total FROM newsletter_subscribers WHERE is_active = 1");

// ============================================================
// CSV EXPORT (must run before any HTML output)
// ============================================================
if (isset($_GET['export']) && $_GET['export'] == 1) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="newsletter_subscribers_' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Email', 'Active', 'Subscribed At']);
    foreach ($subscribers as $sub) {
        fputcsv($output, [$sub['id'], $sub['email'], $sub['is_active'] ? 'Yes' : 'No', $sub['created_at']]);
    }
    fclose($output);
    exit;
}

$page_title = 'Newsletter';
include __DIR__ . '/includes/layout_start.php';
?>

<?php if ($message): ?>
    <div class="alert alert--<?= $message['type'] ?>"><?= sanitize($message['text']) ?></div>
<?php endif; ?>

<!-- Stats -->
<div class="admin-cards" style="grid-template-columns:repeat(3,1fr);">
    <div class="admin-card">
        <div class="admin-card__icon"><i class="fas fa-paper-plane"></i></div>
        <div class="admin-card__label">Total Subscribers</div>
        <div class="admin-card__value"><?= (int)$total_count ?></div>
    </div>
    <div class="admin-card">
        <div class="admin-card__icon"><i class="fas fa-user-check"></i></div>
        <div class="admin-card__label">Active</div>
        <div class="admin-card__value"><?= (int)$active_count ?></div>
    </div>
    <div class="admin-card">
        <div class="admin-card__icon"><i class="fas fa-user-slash"></i></div>
        <div class="admin-card__label">Inactive</div>
        <div class="admin-card__value"><?= (int)($total_count - $active_count) ?></div>
    </div>
</div>

<div class="admin-panel">
    <div class="admin-panel__tools">
        <h2 class="admin-panel__title" style="margin:0;"><i class="fas fa-list"></i> Subscribers (<?= count($subscribers) ?>)</h2>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <form method="GET" class="admin-search">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search email..." value="<?= sanitize($search) ?>">
            </form>
            <a href="?export=1<?= $search ? '&amp;search=' . urlencode($search) : '' ?>" class="btn btn--outline btn--sm"><i class="fas fa-download"></i> Export CSV</a>
        </div>
    </div>
    
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr><th>#</th><th>Email</th><th>Status</th><th>Subscribed</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php if (empty($subscribers)): ?>
                <tr><td colspan="5" style="text-align:center;padding:30px;color:var(--color-text-muted);">No subscribers found.</td></tr>
            <?php else: ?>
                <?php foreach ($subscribers as $i => $sub): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><strong><?= sanitize($sub['email']) ?></strong></td>
                    <td><?= $sub['is_active'] ? '<span class="badge badge--done">Active</span>' : '<span class="badge badge--pending">Inactive</span>' ?></td>
                    <td><?= format_date($sub['created_at']) ?></td>
                    <td style="white-space:nowrap;">
                        <form method="POST" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="id" value="<?= $sub['id'] ?>">
                            <button class="btn btn--outline btn--sm" type="submit">
                                <i class="fas fa-<?= $sub['is_active'] ? 'ban' : 'check' ?>"></i>
                            </button>
                        </form>
                        <form method="POST" onsubmit="return confirm('Remove this subscriber?')" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $sub['id'] ?>">
                            <button class="btn btn--danger btn--sm" type="submit"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include __DIR__ . '/includes/layout_end.php';
?>