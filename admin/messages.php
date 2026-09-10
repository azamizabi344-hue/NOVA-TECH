<?php
/**
 * NOVA TECH - Admin Messages (Contact Inbox)
 * 
 * View contact messages submitted from the Contact page.
 * Features:
 * - List all messages with unread highlighting
 * - View a single message (marks it as read)
 * - Mark as read / unread
 * - Delete messages
 * - Search
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$current_section = 'messages';
$admin_page_title = 'Messages';

$message = '';

// ============================================================
// HANDLE ACTIONS
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = ['type' => 'error', 'text' => 'Invalid security token.'];
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'mark_read':
                execute_query($pdo, "UPDATE contact_messages SET is_read = 1 WHERE id = ?", [(int)$_POST['id']]);
                $message = ['type' => 'success', 'text' => 'Marked as read.'];
                break;
            case 'mark_unread':
                execute_query($pdo, "UPDATE contact_messages SET is_read = 0 WHERE id = ?", [(int)$_POST['id']]);
                $message = ['type' => 'success', 'text' => 'Marked as unread.'];
                break;
            case 'delete':
                execute_query($pdo, "DELETE FROM contact_messages WHERE id = ?", [(int)$_POST['id']]);
                $message = ['type' => 'success', 'text' => 'Message deleted.'];
                break;
        }
    }
}

// ============================================================
// LIST MESSAGES
// ============================================================
$search = trim($_GET['search'] ?? '');
$status_filter = trim($_GET['status'] ?? ''); // all | unread | read
$where = []; $params = [];
if ($search) { $where[] = "(name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)"; $l = '%'.$search.'%'; $params = array_merge($params, [$l,$l,$l,$l]); }
if ($status_filter === 'unread') $where[] = "is_read = 0";
if ($status_filter === 'read') $where[] = "is_read = 1";
$where_sql = $where ? 'WHERE '.implode(' AND ', $where) : '';

$messages = fetch_all($pdo, "SELECT * FROM contact_messages $where_sql ORDER BY is_read ASC, created_at DESC", $params);

$unread_count = count_rows($pdo, "SELECT COUNT(*) as total FROM contact_messages WHERE is_read = 0");

// If viewing a message detail
$viewing = null;
if (isset($_GET['view']) && (int)$_GET['view'] > 0) {
    $viewing = fetch_one($pdo, "SELECT * FROM contact_messages WHERE id = ?", [(int)$_GET['view']]);
    if ($viewing) {
        // Auto-mark as read when viewed
        execute_query($pdo, "UPDATE contact_messages SET is_read = 1 WHERE id = ?", [$viewing['id']]);
        $viewing['is_read'] = 1;
    }
}

$page_title = 'Messages';
include __DIR__ . '/includes/layout_start.php';
?>

<?php if ($message): ?>
    <div class="alert alert--<?= $message['type'] ?>"><?= sanitize($message['text']) ?></div>
<?php endif; ?>

<?php if ($viewing): ?>
    <!-- ============================================================
         MESSAGE DETAIL VIEW
    ============================================================ -->
    <div class="admin-panel">
        <h2 class="admin-panel__title"><i class="fas fa-envelope-open-text"></i> Message from <?= sanitize($viewing['name']) ?></h2>
        <div class="msg-view">
            <div class="msg-view__meta">
                <div class="msg-view__meta-item">
                    <label>From</label>
                    <span><?= sanitize($viewing['name']) ?></span>
                </div>
                <div class="msg-view__meta-item">
                    <label>Email</label>
                    <a href="mailto:<?= sanitize($viewing['email']) ?>"><?= sanitize($viewing['email']) ?></a>
                </div>
                <div class="msg-view__meta-item">
                    <label>Phone</label>
                    <span><?= sanitize($viewing['phone'] ?? '-') ?></span>
                </div>
                <div class="msg-view__meta-item">
                    <label>Subject</label>
                    <span><?= sanitize($viewing['subject']) ?></span>
                </div>
                <div class="msg-view__meta-item">
                    <label>Received</label>
                    <span><?= format_date($viewing['created_at'], 'M d, Y H:i') ?></span>
                </div>
            </div>
            <div class="msg-view__body"><?= sanitize($viewing['message']) ?></div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="<?= base_url('admin/messages.php') ?>" class="btn btn--ghost"><i class="fas fa-arrow-left"></i> Back to Inbox</a>
            <form method="POST" style="display:inline;">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="mark_unread">
                <input type="hidden" name="id" value="<?= $viewing['id'] ?>">
                <button class="btn btn--outline btn--sm" type="submit"><i class="fas fa-envelope"></i> Mark Unread</button>
            </form>
            <form method="POST" onsubmit="return confirm('Delete this message?')" style="display:inline;">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $viewing['id'] ?>">
                <button class="btn btn--danger btn--sm" type="submit"><i class="fas fa-trash"></i> Delete</button>
            </form>
        </div>
    </div>
<?php endif; ?>

<div class="admin-panel">
    <div class="admin-panel__tools">
        <h2 class="admin-panel__title" style="margin:0;">
            <i class="fas fa-envelope"></i> Inbox (<?= count($messages) ?>)
            <?php if ($unread_count > 0): ?>
                <span class="badge badge--pending" style="margin-left:8px;"><?= (int)$unread_count ?> unread</span>
            <?php endif; ?>
        </h2>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <form method="GET" class="admin-search">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search messages..." value="<?= sanitize($search) ?>">
            </form>
            <form method="GET">
                <select name="status" class="admin-filter" onchange="this.form.submit()">
                    <option value="">All Messages</option>
                    <option value="unread" <?= $status_filter === 'unread' ? 'selected' : '' ?>>Unread</option>
                    <option value="read" <?= $status_filter === 'read' ? 'selected' : '' ?>>Read</option>
                </select>
                <?php if ($search): ?><input type="hidden" name="search" value="<?= sanitize($search) ?>"><?php endif; ?>
            </form>
        </div>
    </div>
    
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr><th>From</th><th>Subject</th><th>Received</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php if (empty($messages)): ?>
                <tr><td colspan="5" style="text-align:center;padding:30px;color:var(--color-text-muted);">No messages found.</td></tr>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                <tr class="<?= $msg['is_read'] ? '' : 'unread-row' ?>">
                    <td>
                        <strong><?= sanitize($msg['name']) ?></strong><br>
                        <small class="text-muted"><?= sanitize($msg['email']) ?></small>
                    </td>
                    <td><?= sanitize(truncate($msg['subject'], 45)) ?></td>
                    <td><?= format_date($msg['created_at']) ?></td>
                    <td><?= $msg['is_read'] ? '<span class="badge badge--done">Read</span>' : '<span class="badge badge--pending">New</span>' ?></td>
                    <td style="white-space:nowrap;">
                        <a class="btn btn--ghost btn--sm" href="<?= base_url('admin/messages.php?view=' . $msg['id']) ?>"><i class="fas fa-eye"></i> View</a>
                        <form method="POST" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="<?= $msg['is_read'] ? 'mark_unread' : 'mark_read' ?>">
                            <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                            <button class="btn btn--outline btn--sm" type="submit">
                                <i class="fas fa-<?= $msg['is_read'] ? 'envelope' : 'envelope-open' ?>"></i>
                            </button>
                        </form>
                        <form method="POST" onsubmit="return confirm('Delete this message?')" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $msg['id'] ?>">
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

<?php include __DIR__ . '/includes/layout_end.php'; ?>