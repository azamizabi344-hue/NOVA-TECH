<?php
/**
 * NOVA TECH - Admin Team CRUD
 * 
 * Manages team members (Create, Read, Update, Delete).
 * Each member has: full_name, role, department, bio, photo, email,
 * skills (JSON), social_links (JSON), is_active.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$current_section = 'team';
$admin_page_title = 'Team Members';

$message = '';

// ============================================================
// HANDLE FORM ACTIONS
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = ['type' => 'error', 'text' => 'Invalid security token.'];
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'create':
                $full_name = trim($_POST['full_name'] ?? '');
                $role = trim($_POST['role'] ?? '');
                $department = trim($_POST['department'] ?? 'General');
                $bio = trim($_POST['bio'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $skills = list_to_json($_POST['skills'] ?? '');
                
                if (empty($full_name) || empty($role)) {
                    $message = ['type' => 'error', 'text' => 'Name and role are required.'];
                } else {
                    // photo upload
                    $photo = null;
                    if (!empty($_FILES['photo']['name'])) {
                        $upload = upload_file($_FILES['photo'], UPLOADS_PATH . '/team', ['jpg','jpeg','png','webp']);
                        if ($upload['success']) $photo = $upload['filename'];
                        else { $message = ['type' => 'error', 'text' => $upload['error']]; break; }
                    }
                    try {
                        $stmt = $pdo->prepare(
                            "INSERT INTO team_members (full_name, role, department, bio, photo, email, skills) VALUES (?,?,?,?,?,?,?)"
                        );
                        $stmt->execute([$full_name, $role, $department, $bio, $photo, $email, $skills]);
                        $message = ['type' => 'success', 'text' => 'Team member "' . $full_name . '" added.'];
                    } catch (PDOException $e) {
                        $message = ['type' => 'error', 'text' => 'Database error: ' . $e->getMessage()];
                    }
                }
                break;
            
            case 'update':
                $id = (int)($_POST['id'] ?? 0);
                $full_name = trim($_POST['full_name'] ?? '');
                $role = trim($_POST['role'] ?? '');
                $department = trim($_POST['department'] ?? 'General');
                $bio = trim($_POST['bio'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                $keep_photo = $_POST['keep_photo'] ?? '';
                $skills = list_to_json($_POST['skills'] ?? '');
                
                if ($id <= 0 || empty($full_name) || empty($role)) {
                    $message = ['type' => 'error', 'text' => 'Invalid data.'];
                } else {
                    $photo = $keep_photo ?: null;
                    if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                        $upload = upload_file($_FILES['photo'], UPLOADS_PATH . '/team', ['jpg','jpeg','png','webp']);
                        if ($upload['success']) {
                            $photo = $upload['filename'];
                            $old = fetch_one($pdo, "SELECT photo FROM team_members WHERE id = ?", [$id]);
                            if ($old && $old['photo']) delete_file(UPLOADS_PATH . '/team/' . $old['photo']);
                        } else { $message = ['type' => 'error', 'text' => $upload['error']]; break; }
                    }
                    try {
                        $stmt = $pdo->prepare(
                            "UPDATE team_members SET full_name=?, role=?, department=?, bio=?, photo=?, email=?, skills=?, is_active=? WHERE id=?"
                        );
                        $stmt->execute([$full_name, $role, $department, $bio, $photo, $email, $skills, $is_active, $id]);
                        $message = ['type' => 'success', 'text' => 'Team member updated.'];
                    } catch (PDOException $e) {
                        $message = ['type' => 'error', 'text' => 'Database error: ' . $e->getMessage()];
                    }
                }
                break;
            
            case 'delete':
                $id = (int)($_POST['id'] ?? 0);
                if ($id > 0) {
                    $member = fetch_one($pdo, "SELECT photo FROM team_members WHERE id = ?", [$id]);
                    if ($member && $member['photo']) delete_file(UPLOADS_PATH . '/team/' . $member['photo']);
                    execute_query($pdo, "DELETE FROM team_members WHERE id = ?", [$id]);
                    $message = ['type' => 'success', 'text' => 'Team member deleted.'];
                }
                break;
        }
    }
}

function list_to_json($value) {
    $trimmed = trim($value);
    if ($trimmed === '') return '[]';
    json_decode($trimmed);
    if (json_last_error() === JSON_ERROR_NONE) return $trimmed;
    $items = preg_split('/[\r\n,]+/', $trimmed);
    return json_encode(array_values(array_filter(array_map('trim', $items))));
}

// ============================================================
// LIST TEAM MEMBERS
// ============================================================
$search = trim($_GET['search'] ?? '');
$department_filter = trim($_GET['department'] ?? '');
$where = []; $params = [];
if ($search) { $where[] = "(full_name LIKE ? OR role LIKE ? OR department LIKE ?)"; $l = '%'.$search.'%'; $params = array_merge($params, [$l,$l,$l]); }
if ($department_filter) { $where[] = "department = ?"; $params[] = $department_filter; }
$where_sql = $where ? 'WHERE '.implode(' AND ', $where) : '';

$members = fetch_all($pdo, "SELECT * FROM team_members $where_sql ORDER BY sort_order ASC, id ASC", $params);
$departments = fetch_all($pdo, "SELECT DISTINCT department FROM team_members ORDER BY department ASC");

$page_title = 'Team';
include __DIR__ . '/includes/layout_start.php';
?>

<?php if ($message): ?>
    <div class="alert alert--<?= $message['type'] ?>"><?= sanitize($message['text']) ?></div>
<?php endif; ?>

<div class="admin-panel">
    <div class="admin-panel__tools">
        <h2 class="admin-panel__title" style="margin:0;"><i class="fas fa-users"></i> Team Members (<?= count($members) ?>)</h2>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <form method="GET" class="admin-search">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search team..." value="<?= sanitize($search) ?>">
                <?php if ($department_filter): ?><input type="hidden" name="department" value="<?= sanitize($department_filter) ?>"><?php endif; ?>
            </form>
            <form method="GET">
                <select name="department" class="admin-filter" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= sanitize($d['department']) ?>" <?= $department_filter === $d['department'] ? 'selected' : '' ?>><?= sanitize($d['department']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($search): ?><input type="hidden" name="search" value="<?= sanitize($search) ?>"><?php endif; ?>
            </form>
            <button class="btn btn--primary" onclick="openModal('teamModal')"><i class="fas fa-plus"></i> Add Member</button>
        </div>
    </div>
    
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr><th>Photo</th><th>Name</th><th>Role</th><th>Department</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php if (empty($members)): ?>
                <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--color-text-muted);">No team members found.</td></tr>
            <?php else: ?>
                <?php foreach ($members as $m): ?>
                <tr>
                    <td>
                        <?php if ($m['photo']): ?>
                            <img src="<?= base_url('uploads/team/' . $m['photo']) ?>" alt="<?= sanitize($m['full_name']) ?>">
                        <?php else: ?>
                            <span class="avatar-circle"><?= strtoupper(substr($m['full_name'], 0, 1)) ?></span>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= sanitize($m['full_name']) ?></strong></td>
                    <td><?= sanitize($m['role']) ?></td>
                    <td><span class="badge badge--active"><?= sanitize($m['department']) ?></span></td>
                    <td><?= $m['is_active'] ? '<span class="badge badge--done">Active</span>' : '<span class="badge badge--pending">Hidden</span>' ?></td>
                    <td>
                        <button class="btn btn--ghost btn--sm" onclick="editMember(<?= $m['id'] ?>)"><i class="fas fa-edit"></i> Edit</button>
                        <form method="POST" onsubmit="return confirm('Delete this member?')" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $m['id'] ?>">
                            <button type="submit" class="btn btn--danger btn--sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ADD / EDIT TEAM MODAL -->
<div class="admin-modal" id="teamModal">
    <div class="admin-modal__content">
        <div class="admin-modal__header">
            <h3 id="teamModalTitle">Add New Member</h3>
            <button class="admin-modal__close" onclick="closeModal('teamModal')">&times;</button>
        </div>
        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <?= csrf_field() ?>
            <input type="hidden" name="action" id="t_action" value="create">
            <input type="hidden" name="id" id="t_id" value="">
            <input type="hidden" name="keep_photo" id="t_keep_photo" value="">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="full_name" id="t_name" required>
            </div>
            <div class="form-group">
                <label>Role *</label>
                <input type="text" name="role" id="t_role" placeholder="Senior Developer" required>
            </div>
            <div class="form-group">
                <label>Department</label>
                <input type="text" name="department" id="t_department" placeholder="Engineering">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="t_email">
            </div>
            <div class="form-group--full">
                <label>Bio</label>
                <textarea name="bio" id="t_bio"></textarea>
            </div>
            <div class="form-group">
                <label>Skills (comma or newline separated)</label>
                <textarea name="skills" id="t_skills" placeholder="PHP, React, MySQL"></textarea>
            </div>
            <div class="form-group">
                <label>Photo</label>
                <input type="file" name="photo" id="t_photo" accept="image/png,image/jpeg,image/webp">
                <span class="help-text">Max 5MB. JPG, PNG, WebP.</span>
            </div>
            <div class="form-group--full" style="display:flex;gap:20px;">
                <label class="form-check"><input type="checkbox" name="is_active" id="t_active" value="1" checked> Active</label>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn--primary">Save Member</button>
                <button type="button" class="btn btn--ghost" onclick="closeModal('teamModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/layout_end.php'; ?>

<script>
function openModal(id) {
    document.getElementById(id).classList.add('active');
    document.getElementById('t_action').value = 'create';
    document.getElementById('t_id').value = '';
    document.getElementById('teamModalTitle').textContent = 'Add New Member';
    document.getElementById('t_name').value = '';
    document.getElementById('t_role').value = '';
    document.getElementById('t_department').value = '';
    document.getElementById('t_email').value = '';
    document.getElementById('t_bio').value = '';
    document.getElementById('t_skills').value = '';
    document.getElementById('t_photo').value = '';
    document.getElementById('t_keep_photo').value = '';
    document.getElementById('t_active').checked = true;
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}

document.querySelectorAll('.admin-modal').forEach(m => {
    m.addEventListener('click', function (e) {
        if (e.target === this) this.classList.remove('active');
    });
});

async function editMember(id) {
    try {
        const res = await fetch('../api/team.php?id=' + id);
        const data = await res.json();
        if (data.success) {
            const m = data.data;
            document.getElementById('teamModalTitle').textContent = 'Edit Member';
            document.getElementById('t_action').value = 'update';
            document.getElementById('t_id').value = m.id;
            document.getElementById('t_keep_photo').value = m.photo || '';
            document.getElementById('t_name').value = m.full_name;
            document.getElementById('t_role').value = m.role;
            document.getElementById('t_department').value = m.department;
            document.getElementById('t_email').value = m.email || '';
            document.getElementById('t_bio').value = m.bio || '';
            document.getElementById('t_skills').value = (m.skills || []).join(', ');
            document.getElementById('t_photo').value = '';
            document.getElementById('t_active').checked = !!m.is_active;
            openModal('teamModal');
        } else {
            alert('Could not load member: ' + (data.message || 'Unknown error'));
        }
    } catch (err) {
        alert('Network error loading member.');
    }
}
</script>