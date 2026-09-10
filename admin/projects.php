<?php
/**
 * NOVA TECH - Admin Projects CRUD
 * 
 * This page manages projects (Create, Read, Update, Delete).
 * It combines all CRUD operations in one page:
 * - Lists all projects in a table
 * - Add new project via form (image upload supported)
 * - Edit project via modal form
 * - Delete project (with image cleanup)
 * - Search and filter by category
 * 
 * HOW IT WORKS:
 * The page handles three actions based on the hidden "action" field:
 *   action=create  -> INSERT a new project
 *   action=update  -> UPDATE an existing project
 *   action=delete  -> DELETE a project
 * All actions require the CSRF token and admin authentication.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$current_section = 'projects';
$admin_page_title = 'Projects';

// ============================================================
// HANDLE FORM ACTIONS (create / update / delete)
// ============================================================
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Always verify the CSRF token for important form actions
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = ['type' => 'error', 'text' => 'Invalid security token. Please try again.'];
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            
            // ============================================================
            // CREATE - Insert a new project
            // ============================================================
            case 'create':
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $full_description = trim($_POST['full_description'] ?? '');
                $category = trim($_POST['category'] ?? '');
                $client = trim($_POST['client'] ?? '');
                $project_url = trim($_POST['project_url'] ?? '');
                $technologies = $_POST['technologies'] ?? [];
                $is_featured = isset($_POST['is_featured']) ? 1 : 0;
                
                // Basic validation
                if (empty($title) || empty($description) || empty($category)) {
                    $message = ['type' => 'error', 'text' => 'Title, description, and category are required.'];
                } else {
                    // Handle image upload
                    $image = null;
                    if (!empty($_FILES['image']['name'])) {
                        $upload = upload_file($_FILES['image'], UPLOADS_PATH . '/projects');
                        if ($upload['success']) {
                            $image = $upload['filename'];
                        } else {
                            $message = ['type' => 'error', 'text' => $upload['error']];
                            break;
                        }
                    }
                    
                    // Convert technologies array to JSON for storage
                    $tech_array = array_filter(array_map('trim', explode(',', $technologies)));
                    $tech_json = json_encode(array_values($tech_array));
                    
                    try {
                        $stmt = $pdo->prepare(
                            "INSERT INTO projects (title, description, full_description, image, category, technologies, client, project_url, is_featured) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
                        );
                        $stmt->execute([
                            $title, $description, $full_description, $image,
                            $category, $tech_json, $client, $project_url, $is_featured
                        ]);
                        $message = ['type' => 'success', 'text' => 'Project "' . $title . '" created successfully.'];
                    } catch (PDOException $e) {
                        $message = ['type' => 'error', 'text' => 'Database error: ' . $e->getMessage()];
                    }
                }
                break;
            
            // ============================================================
            // UPDATE - Edit an existing project
            // ============================================================
            case 'update':
                $id = (int)($_POST['id'] ?? 0);
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $full_description = trim($_POST['full_description'] ?? '');
                $category = trim($_POST['category'] ?? '');
                $client = trim($_POST['client'] ?? '');
                $project_url = trim($_POST['project_url'] ?? '');
                $technologies = $_POST['technologies'] ?? '';
                $is_featured = isset($_POST['is_featured']) ? 1 : 0;
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                $keep_image = $_POST['keep_image'] ?? '';
                
                if ($id <= 0 || empty($title) || empty($description) || empty($category)) {
                    $message = ['type' => 'error', 'text' => 'Invalid data. All fields are required.'];
                } else {
                    // Check for new image upload
                    $image = $keep_image ?: null;
                    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $upload = upload_file($_FILES['image'], UPLOADS_PATH . '/projects');
                        if ($upload['success']) {
                            $image = $upload['filename'];
                            // Delete old image
                            $old = fetch_one($pdo, "SELECT image FROM projects WHERE id = ?", [$id]);
                            if ($old && $old['image']) {
                                delete_file(UPLOADS_PATH . '/projects/' . $old['image']);
                            }
                        } else {
                            $message = ['type' => 'error', 'text' => $upload['error']];
                            break;
                        }
                    }
                    
                    $tech_array = array_filter(array_map('trim', explode(',', $technologies)));
                    $tech_json = json_encode(array_values($tech_array));
                    
                    try {
                        $stmt = $pdo->prepare(
                            "UPDATE projects SET 
                                title = ?, description = ?, full_description = ?, image = ?,
                                category = ?, technologies = ?, client = ?, project_url = ?,
                                is_featured = ?, is_active = ?
                             WHERE id = ?"
                        );
                        $stmt->execute([
                            $title, $description, $full_description, $image,
                            $category, $tech_json, $client, $project_url,
                            $is_featured, $is_active, $id
                        ]);
                        $message = ['type' => 'success', 'text' => 'Project updated successfully.'];
                    } catch (PDOException $e) {
                        $message = ['type' => 'error', 'text' => 'Database error: ' . $e->getMessage()];
                    }
                }
                break;
            
            // ============================================================
            // DELETE - Remove a project and its image
            // ============================================================
            case 'delete':
                $id = (int)($_POST['id'] ?? 0);
                if ($id > 0) {
                    // Delete the image file first
                    $project = fetch_one($pdo, "SELECT image FROM projects WHERE id = ?", [$id]);
                    if ($project && $project['image']) {
                        delete_file(UPLOADS_PATH . '/projects/' . $project['image']);
                    }
                    
                    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
                    $stmt->execute([$id]);
                    $message = ['type' => 'success', 'text' => 'Project deleted successfully.'];
                }
                break;
        }
    }
}

// ============================================================
// SEARCH & FILTER
// ============================================================
$search = trim($_GET['search'] ?? '');
$category_filter = trim($_GET['category'] ?? '');

// Build the WHERE clause safely with parameters
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(title LIKE ? OR description LIKE ? OR client LIKE ? OR technologies LIKE ?)";
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if (!empty($category_filter)) {
    $where[] = "category = ?";
    $params[] = $category_filter;
}

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Fetch all matching projects
$projects = fetch_all($pdo, "
    SELECT * FROM projects $where_sql 
    ORDER BY created_at DESC
", $params);

// Fetch distinct categories for the filter dropdown
$categories = fetch_all($pdo, "
    SELECT DISTINCT category FROM projects ORDER BY category ASC
");

$page_title = 'Projects';
include __DIR__ . '/includes/layout_start.php';
?>

<!-- ============================================================
     FLASH / ACTION MESSAGE
============================================================ -->
<?php if ($message): ?>
    <div class="alert alert--<?= $message['type'] ?>"><?= sanitize($message['text']) ?></div>
<?php endif; ?>

<!-- ============================================================
     TOOLBAR: Add button + Search + Filter
============================================================ -->
<div class="admin-panel">
    <div class="admin-panel__tools">
        <h2 class="admin-panel__title" style="margin:0;"><i class="fas fa-folder-open"></i> All Projects (<?= count($projects) ?>)</h2>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <form method="GET" class="admin-search">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search projects..." value="<?= sanitize($search) ?>">
                <?php if (!empty($category_filter)): ?><input type="hidden" name="category" value="<?= sanitize($category_filter) ?>"><?php endif; ?>
            </form>
            <form method="GET">
                <select name="category" class="admin-filter" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= sanitize($cat['category']) ?>" <?= $category_filter === $cat['category'] ? 'selected' : '' ?>>
                            <?= sanitize($cat['category']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($search)): ?><input type="hidden" name="search" value="<?= sanitize($search) ?>"><?php endif; ?>
            </form>
            <button class="btn btn--primary" onclick="openModal('projectModal')">
                <i class="fas fa-plus"></i> Add Project
            </button>
        </div>
    </div>
    
    <!-- ============================================================
         PROJECTS TABLE
    ============================================================ -->
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Client</th>
                    <th>Featured</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($projects)): ?>
                <tr><td colspan="8" style="text-align:center;padding:30px;color:var(--color-text-muted);">No projects found.</td></tr>
            <?php else: ?>
                <?php foreach ($projects as $project): ?>
                <tr>
                    <td>
                        <?php if ($project['image']): ?>
                            <img src="<?= base_url('uploads/projects/' . $project['image']) ?>" alt="<?= sanitize($project['title']) ?>">
                        <?php else: ?>
                            <span class="avatar-circle" style="border-radius:8px;"><i class="fas fa-folder-open"></i></span>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= sanitize($project['title']) ?></strong></td>
                    <td><span class="badge badge--active"><?= sanitize($project['category']) ?></span></td>
                    <td><?= sanitize($project['client'] ?? '-') ?></td>
                    <td><?= $project['is_featured'] ? '<span class="badge badge--done">Yes</span>' : '<span class="badge">No</span>' ?></td>
                    <td><?= $project['is_active'] ? '<span class="badge badge--done">Active</span>' : '<span class="badge badge--pending">Hidden</span>' ?></td>
                    <td><?= format_date($project['created_at']) ?></td>
                    <td>
                        <button class="btn btn--ghost btn--sm" onclick="editProject(<?= $project['id'] ?>)">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <form method="POST" onsubmit="return confirm('Delete this project? This cannot be undone.')" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $project['id'] ?>">
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

<!-- ============================================================
     ADD / EDIT PROJECT MODAL
============================================================ -->
<div class="admin-modal" id="projectModal">
    <div class="admin-modal__content">
        <div class="admin-modal__header">
            <h3 id="projectModalTitle">Add New Project</h3>
            <button class="admin-modal__close" onclick="closeModal('projectModal')">&times;</button>
        </div>
        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <?= csrf_field() ?>
            <input type="hidden" name="action" id="projectAction" value="create">
            <input type="hidden" name="id" id="projectId" value="">
            <input type="hidden" name="keep_image" id="keepImage" value="">

            <div class="form-group">
                <label>Project Title *</label>
                <input type="text" name="title" id="f_title" required>
            </div>
            <div class="form-group">
                <label>Category *</label>
                <input type="text" name="category" id="f_category" required>
            </div>
            <div class="form-group">
                <label>Short Description *</label>
                <textarea name="description" id="f_description" required></textarea>
            </div>
            <div class="form-group">
                <label>Client</label>
                <input type="text" name="client" id="f_client">
            </div>
            <div class="form-group--full">
                <label>Full Description</label>
                <textarea name="full_description" id="f_full_description" style="min-height:140px;"></textarea>
            </div>
            <div class="form-group">
                <label>Project URL</label>
                <input type="url" name="project_url" id="f_project_url" placeholder="https://...">
            </div>
            <div class="form-group">
                <label>Technologies (comma separated)</label>
                <input type="text" name="technologies" id="f_technologies" placeholder="PHP, MySQL, JavaScript">
            </div>
            <div class="form-group">
                <label>Project Image</label>
                <input type="file" name="image" id="f_image" accept="image/png,image/jpeg,image/webp">
                <span class="help-text">Max 5MB. JPG, PNG, WebP.</span>
            </div>
            <div class="form-group--full" style="display:flex;gap:20px;">
                <label class="form-check"><input type="checkbox" name="is_featured" id="f_featured" value="1"> Featured</label>
                <label class="form-check"><input type="checkbox" name="is_active" id="f_active" value="1" checked> Active</label>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn--primary">Save Project</button>
                <button type="button" class="btn btn--ghost" onclick="closeModal('projectModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/layout_end.php'; ?>

<script>
// ============================================================
// MODAL OPEN / CLOSE
// ============================================================
function openModal(id) {
    document.getElementById(id).classList.add('active');
    document.getElementById('projectAction').value = 'create';
    document.getElementById('projectId').value = '';
    document.getElementById('projectModalTitle').textContent = 'Add New Project';
    document.getElementById('f_title').value = '';
    document.getElementById('f_category').value = '';
    document.getElementById('f_description').value = '';
    document.getElementById('f_full_description').value = '';
    document.getElementById('f_client').value = '';
    document.getElementById('f_project_url').value = '';
    document.getElementById('f_technologies').value = '';
    document.getElementById('f_image').value = '';
    document.getElementById('f_featured').checked = false;
    document.getElementById('f_active').checked = true;
    document.getElementById('keepImage').value = '';
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}

// Close modal on Escape key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.admin-modal.active').forEach(m => m.classList.remove('active'));
    }
});

// Close modal when clicking outside content
document.querySelectorAll('.admin-modal').forEach(m => {
    m.addEventListener('click', function (e) {
        if (e.target === this) this.classList.remove('active');
    });
});

// ============================================================
// EDIT - fetch project data via REST API and fill the form
// ============================================================
async function editProject(id) {
    try {
        const res = await fetch('../api/projects.php?id=' + id);
        const data = await res.json();

        if (data.success) {
            const p = data.data;
            document.getElementById('projectModalTitle').textContent = 'Edit Project';
            document.getElementById('projectAction').value = 'update';
            document.getElementById('projectId').value = p.id;
            document.getElementById('keepImage').value = p.image || '';

            document.getElementById('f_title').value = p.title;
            document.getElementById('f_category').value = p.category;
            document.getElementById('f_description').value = p.description;
            document.getElementById('f_full_description').value = p.full_description || '';
            document.getElementById('f_client').value = p.client || '';

            let techs = [];
            try { techs = JSON.parse(p.technologies || '[]'); } catch (e) { techs = []; }
            document.getElementById('f_technologies').value = techs.join(', ');

            document.getElementById('f_project_url').value = p.project_url || '';
            document.getElementById('f_featured').checked = !!p.is_featured;
            document.getElementById('f_active').checked = !!p.is_active;
            document.getElementById('f_image').value = '';

            openModal('projectModal');
        } else {
            alert('Could not load project: ' + (data.message || 'Unknown error'));
        }
    } catch (err) {
        alert('Network error loading project.');
    }
}
</script>