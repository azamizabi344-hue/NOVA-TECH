<?php
/**
 * NOVA TECH - Admin Services CRUD
 * 
 * Manages company services (Create, Read, Update, Delete).
 * Services have: title, short_description, full_description, icon,
 * category, price_from, features (JSON), is_active.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$current_section = 'services';
$admin_page_title = 'Services';

$message = '';

// ============================================================
// HANDLE FORM ACTIONS
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = ['type' => 'error', 'text' => 'Invalid security token. Please try again.'];
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'create':
                $title = trim($_POST['title'] ?? '');
                $short_description = trim($_POST['short_description'] ?? '');
                $full_description = trim($_POST['full_description'] ?? '');
                $icon = trim($_POST['icon'] ?? 'fas fa-cog');
                $category = trim($_POST['category'] ?? 'General');
                $price_from = floatval($_POST['price_from'] ?? 0);
                $features = maybe_json($_POST['features'] ?? '');
                
                if (empty($title) || empty($short_description)) {
                    $message = ['type' => 'error', 'text' => 'Title and description are required.'];
                } else {
                    try {
                        $stmt = $pdo->prepare(
                            "INSERT INTO services (title, short_description, full_description, icon, category, price_from, features) 
                             VALUES (?, ?, ?, ?, ?, ?, ?)"
                        );
                        $stmt->execute([$title, $short_description, $full_description, $icon, $category, $price_from, $features]);
                        $message = ['type' => 'success', 'text' => 'Service "' . $title . '" created successfully.'];
                    } catch (PDOException $e) {
                        $message = ['type' => 'error', 'text' => 'Database error: ' . $e->getMessage()];
                    }
                }
                break;
            
            case 'update':
                $id = (int)($_POST['id'] ?? 0);
                $title = trim($_POST['title'] ?? '');
                $short_description = trim($_POST['short_description'] ?? '');
                $full_description = trim($_POST['full_description'] ?? '');
                $icon = trim($_POST['icon'] ?? 'fas fa-cog');
                $category = trim($_POST['category'] ?? 'General');
                $price_from = floatval($_POST['price_from'] ?? 0);
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                $features = maybe_json($_POST['features'] ?? '');
                
                if ($id <= 0 || empty($title) || empty($short_description)) {
                    $message = ['type' => 'error', 'text' => 'Invalid data.'];
                } else {
                    try {
                        $stmt = $pdo->prepare(
                            "UPDATE services SET title=?, short_description=?, full_description=?, icon=?, 
                             category=?, price_from=?, features=?, is_active=? WHERE id=?"
                        );
                        $stmt->execute([$title, $short_description, $full_description, $icon, $category, $price_from, $features, $is_active, $id]);
                        $message = ['type' => 'success', 'text' => 'Service updated successfully.'];
                    } catch (PDOException $e) {
                        $message = ['type' => 'error', 'text' => 'Database error: ' . $e->getMessage()];
                    }
                }
                break;
            
            case 'delete':
                $id = (int)($_POST['id'] ?? 0);
                if ($id > 0) {
                    $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
                    $stmt->execute([$id]);
                    $message = ['type' => 'success', 'text' => 'Service deleted successfully.'];
                }
                break;
        }
    }
}

// helper to convert comma/newline separated text into JSON, or keep JSON as-is
function maybe_json($value) {
    $trimmed = trim($value);
    if ($trimmed === '') return '[]';
    // If it's already valid JSON, store it directly
    json_decode($trimmed);
    if (json_last_error() === JSON_ERROR_NONE) return $trimmed;
    // Otherwise treat as newline/comma-separated list
    $items = preg_split('/[\r\n,]+/', $trimmed);
    $items = array_values(array_filter(array_map('trim', $items)));
    return json_encode($items);
}

// ============================================================
// LIST SERVICES
// ============================================================
$search = trim($_GET['search'] ?? '');
$where = '';
$params = [];
if (!empty($search)) {
    $where = "WHERE title LIKE ? OR short_description LIKE ? OR category LIKE ?";
    $like = '%' . $search . '%';
    $params = [$like, $like, $like];
}

$services = fetch_all($pdo, "SELECT * FROM services $where ORDER BY sort_order ASC, id ASC", $params);

$page_title = 'Services';
include __DIR__ . '/includes/layout_start.php';
?>

<?php if ($message): ?>
    <div class="alert alert--<?= $message['type'] ?>"><?= sanitize($message['text']) ?></div>
<?php endif; ?>

<div class="admin-panel">
    <div class="admin-panel__tools">
        <h2 class="admin-panel__title" style="margin:0;"><i class="fas fa-cogs"></i> All Services (<?= count($services) ?>)</h2>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <form method="GET" class="admin-search">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search services..." value="<?= sanitize($search) ?>">
            </form>
            <button class="btn btn--primary" onclick="openModal('serviceModal')">
                <i class="fas fa-plus"></i> Add Service
            </button>
        </div>
    </div>
    
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Icon</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Price From</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($services)): ?>
                <tr><td colspan="6" style="text-align:center;padding:30px;color:var(--color-text-muted);">No services found.</td></tr>
            <?php else: ?>
                <?php foreach ($services as $service): ?>
                <tr>
                    <td><span class="avatar-circle" style="border-radius:8px;"><i class="<?= sanitize($service['icon']) ?>"></i></span></td>
                    <td><strong><?= sanitize($service['title']) ?></strong><br><small class="text-muted"><?= sanitize(truncate($service['short_description'], 60)) ?></small></td>
                    <td><span class="badge badge--active"><?= sanitize($service['category']) ?></span></td>
                    <td>$<?= number_format($service['price_from'], 2) ?></td>
                    <td><?= $service['is_active'] ? '<span class="badge badge--done">Active</span>' : '<span class="badge badge--pending">Hidden</span>' ?></td>
                    <td>
                        <button class="btn btn--ghost btn--sm" onclick="editService(<?= $service['id'] ?>)"><i class="fas fa-edit"></i> Edit</button>
                        <form method="POST" onsubmit="return confirm('Delete this service?')" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $service['id'] ?>">
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

<!-- ADD / EDIT SERVICE MODAL -->
<div class="admin-modal" id="serviceModal">
    <div class="admin-modal__content">
        <div class="admin-modal__header">
            <h3 id="serviceModalTitle">Add New Service</h3>
            <button class="admin-modal__close" onclick="closeModal('serviceModal')">&times;</button>
        </div>
        <form method="POST" class="admin-form">
            <?= csrf_field() ?>
            <input type="hidden" name="action" id="s_action" value="create">
            <input type="hidden" name="id" id="s_id" value="">
            <div class="form-group">
                <label>Title *</label>
                <input type="text" name="title" id="s_title" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" id="s_category" placeholder="Development">
            </div>
            <div class="form-group">
                <label>Short Description *</label>
                <textarea name="short_description" id="s_short_desc" required></textarea>
            </div>
            <div class="form-group">
                <label>Icon (FontAwesome class)</label>
                <input type="text" name="icon" id="s_icon" placeholder="fas fa-code">
            </div>
            <div class="form-group--full">
                <label>Full Description</label>
                <textarea name="full_description" id="s_full_desc" style="min-height:140px;"></textarea>
            </div>
            <div class="form-group">
                <label>Price From ($)</label>
                <input type="number" name="price_from" id="s_price" step="0.01" min="0">
            </div>
            <div class="form-group">
                <label>Features (one per line)</label>
                <textarea name="features" id="s_features" placeholder="Responsive Design&#10;SEO Optimization"></textarea>
            </div>
            <div class="form-group--full" style="display:flex;gap:20px;">
                <label class="form-check"><input type="checkbox" name="is_active" id="s_active" value="1" checked> Active</label>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn--primary">Save Service</button>
                <button type="button" class="btn btn--ghost" onclick="closeModal('serviceModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/layout_end.php'; ?>

<script>
function openModal(id) {
    document.getElementById(id).classList.add('active');
    document.getElementById('s_action').value = 'create';
    document.getElementById('s_id').value = '';
    document.getElementById('serviceModalTitle').textContent = 'Add New Service';
    document.getElementById('s_title').value = '';
    document.getElementById('s_category').value = '';
    document.getElementById('s_short_desc').value = '';
    document.getElementById('s_full_desc').value = '';
    document.getElementById('s_icon').value = 'fas fa-cog';
    document.getElementById('s_price').value = '';
    document.getElementById('s_features').value = '';
    document.getElementById('s_active').checked = true;
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}

document.querySelectorAll('.admin-modal').forEach(m => {
    m.addEventListener('click', function (e) {
        if (e.target === this) this.classList.remove('active');
    });
});

async function editService(id) {
    try {
        const res = await fetch('../api/services.php?id=' + id);
        const data = await res.json();
        if (data.success) {
            const s = data.data;
            document.getElementById('serviceModalTitle').textContent = 'Edit Service';
            document.getElementById('s_action').value = 'update';
            document.getElementById('s_id').value = s.id;
            document.getElementById('s_title').value = s.title;
            document.getElementById('s_category').value = s.category;
            document.getElementById('s_short_desc').value = s.short_description;
            document.getElementById('s_full_desc').value = s.full_description || '';
            document.getElementById('s_icon').value = s.icon;
            document.getElementById('s_price').value = s.price_from;
            document.getElementById('s_features').value = (s.features || []).join('\n');
            document.getElementById('s_active').checked = !!s.is_active;
            openModal('serviceModal');
        } else {
            alert('Could not load service: ' + (data.message || 'Unknown error'));
        }
    } catch (err) {
        alert('Network error loading service.');
    }
}
</script>