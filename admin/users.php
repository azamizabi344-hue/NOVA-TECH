<?php
/**
 * NOVA TECH - Admin Users
 * 
 * Manage registered users:
 * - View all users
 * - Add new user (with password hashing)
 * - Edit user (name, email, role, status)
 * - Reset password
 * - Delete user
 * - Search
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$current_section = 'users';
$admin_page_title = 'Users';

$message = '';

// ============================================================
// HANDLE ACTIONS
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = ['type' => 'error', 'text' => 'Invalid security token.'];
    } else {
        $action = $_POST['action'] ?? '';
        $id = (int)($_POST['id'] ?? 0);
        
        switch ($action) {
            case 'create':
                $full_name = trim($_POST['full_name'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';
                $role = $_POST['role'] ?? 'user';
                
                if (empty($full_name) || empty($email) || empty($password)) {
                    $message = ['type' => 'error', 'text' => 'All fields are required.'];
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $message = ['type' => 'error', 'text' => 'Invalid email.'];
                } elseif (strlen($password) < 6) {
                    $message = ['type' => 'error', 'text' => 'Password must be at least 6 characters.'];
                } else {
                    $existing = fetch_one($pdo, "SELECT id FROM users WHERE email = ?", [$email]);
                    if ($existing) {
                        $message = ['type' => 'error', 'text' => 'Email already registered.'];
                    } else {
                        $hashed = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?,?,?,?)");
                        $stmt->execute([$full_name, $email, $hashed, $role]);
                        $message = ['type' => 'success', 'text' => 'User created.'];
                    }
                }
                break;
            
            case 'update':
                $full_name = trim($_POST['full_name'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $role = $_POST['role'] ?? 'user';
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                $new_password = $_POST['new_password'] ?? '';
                
                if ($id <= 0 || empty($full_name) || empty($email)) {
                    $message = ['type' => 'error', 'text' => 'Invalid data.'];
                } else {
                    // Don't allow removing your own admin access
                    if ($id === (int)$_SESSION['user_id'] && $role !== 'admin') {
                        $message = ['type' => 'error', 'text' => 'You cannot demote your own account.'];
                        break;
                    }
                    $stmt = $pdo->prepare("UPDATE users SET full_name=?, email=?, role=?, is_active=? WHERE id=?");
                    $stmt->execute([$full_name, $email, $role, $is_active, $id]);
                    
                    if (!empty($new_password)) {
                        if (strlen($new_password) < 6) {
                            $message = ['type' => 'error', 'text' => 'New password is too short.'];
                            break;
                        }
                        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                        execute_query($pdo, "UPDATE users SET password=? WHERE id=?", [$hashed, $id]);
                    }
                    $message = ['type' => 'success', 'text' => 'User updated.'];
                }
                break;
            
            case 'delete':
                if ($id > 0 && $id !== (int)$_SESSION['user_id']) {
                    execute_query($pdo, "DELETE FROM users WHERE id=?", [$id]);
                    $message = ['type' => 'success', 'text' => 'User deleted.'];
                } else {
                    $message = ['type' => 'error', 'text' => 'You cannot delete your own account.'];
                }
                break;
        }
    }
}

// ============================================================
// LIST USERS
// ============================================================
$search = trim($_GET['search'] ?? '');
$where = '';
$params = [];
if ($search) { $where = "WHERE full_name LIKE ? OR email LIKE ?"; $l = '%'.$search.'%'; $params = [$l, $l]; }

$users = fetch_all($pdo, "SELECT * FROM users $where ORDER BY created_at DESC", $params);

$page_title = 'Users';
include __DIR__ . '/includes/layout_start.php';
?>

<?php if ($message): ?>
    <div class="alert alert--<?= $message['type'] ?>"><?= sanitize($message['text']) ?></div>
<?php endif; ?>

<div class="admin-panel">
    <div class="admin-panel__tools">
        <h2 class="admin-panel__title" style="margin:0;"><i class="fas fa-users"></i> All Users (<?= count($users) ?>)</h2>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <form method="GET" class="admin-search">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search users..." value="<?= sanitize($search) ?>">
            </form>
            <button class="btn btn--primary" onclick="openModal('userModal')"><i class="fas fa-plus"></i> Add User</button>
        </div>
    </div>
    
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr><th>User</th><th>Role</th><th>Status</th><th>Registered</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php if (empty($users)): ?>
                <tr><td colspan="5" style="text-align:center;padding:30px;color:var(--color-text-muted);">No users found.</td></tr>
            <?php else: ?>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td>
                        <span class="avatar-circle"><?= strtoupper(substr($u['full_name'], 0, 1)) ?></span>
                        <strong><?= sanitize($u['full_name']) ?></strong><br>
                        <small class="text-muted"><?= sanitize($u['email']) ?></small>
                    </td>
                    <td><?= $u['role'] === 'admin' ? '<span class="badge badge--done">Admin</span>' : '<span class="badge">User</span>' ?></td>
                    <td><?= $u['is_active'] ? '<span class="badge badge--done">Active</span>' : '<span class="badge badge--pending">Banned</span>' ?></td>
                    <td><?= format_date($u['created_at']) ?></td>
                    <td style="white-space:nowrap;">
                        <button class="btn btn--ghost btn--sm" onclick="editUser(<?= $u['id'] ?>)"><i class="fas fa-edit"></i> Edit</button>
                        <form method="POST" onsubmit="return confirm('Delete this user?')" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $u['id'] ?>">
                            <button class="btn btn--danger btn--sm" type="submit" <?= $u['id'] === (int)$_SESSION['user_id'] ? 'disabled title="You cannot delete your own account"' : '' ?>><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ADD / EDIT USER MODAL -->
<div class="admin-modal" id="userModal">
    <div class="admin-modal__content">
        <div class="admin-modal__header">
            <h3 id="userModalTitle">Add New User</h3>
            <button class="admin-modal__close" onclick="closeModal('userModal')">&times;</button>
        </div>
        <form method="POST" class="admin-form">
            <?= csrf_field() ?>
            <input type="hidden" name="action" id="u_action" value="create">
            <input type="hidden" name="id" id="u_id" value="">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="full_name" id="u_name" required>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" id="u_email" required>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role" id="u_role">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label id="u_password_label">Password *</label>
                <input type="password" name="password" id="u_password" minlength="6">
                <span class="help-text">Min 6 chars. Leave blank when editing to keep current.</span>
            </div>
            <div class="form-group--full" style="display:flex;gap:20px;">
                <label class="form-check"><input type="checkbox" name="is_active" id="u_active" value="1" checked> Active</label>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn--primary">Save User</button>
                <button type="button" class="btn btn--ghost" onclick="closeModal('userModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/layout_end.php'; ?>

<script>
function openModal(id) {
    document.getElementById(id).classList.add('active');
    document.getElementById('u_action').value = 'create';
    document.getElementById('u_id').value = '';
    document.getElementById('userModalTitle').textContent = 'Add New User';
    document.getElementById('u_name').value = '';
    document.getElementById('u_email').value = '';
    document.getElementById('u_role').value = 'user';
    document.getElementById('u_password').value = '';
    document.getElementById('u_password').required = true;
    document.getElementById('u_password_label').textContent = 'Password *';
    document.getElementById('u_active').checked = true;
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}

document.querySelectorAll('.admin-modal').forEach(m => {
    m.addEventListener('click', function (e) {
        if (e.target === this) this.classList.remove('active');
    });
});

function editUser(id) {
    <?php foreach ($users as $u): ?>
    if (id === <?= $u['id'] ?>) {
        document.getElementById('userModalTitle').textContent = 'Edit User';
        document.getElementById('u_action').value = 'update';
        document.getElementById('u_id').value = '<?= $u['id'] ?>';
        document.getElementById('u_name').value = '<?= addslashes($u['full_name']) ?>';
        document.getElementById('u_email').value = '<?= addslashes($u['email']) ?>';
        document.getElementById('u_role').value = '<?= $u['role'] ?>';
        document.getElementById('u_password').value = '';
        document.getElementById('u_password').required = false;
        document.getElementById('u_password_label').textContent = 'New Password';
        document.getElementById('u_active').checked = !!<?= (int)$u['is_active'] ?>;
        openModal('userModal');
    }
    <?php endforeach; ?>
}
</script>