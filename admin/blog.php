<?php
/**
 * NOVA TECH - Admin Blog CRUD
 * 
 * Manages blog posts (Create, Read, Update, Delete).
 * Also manages categories in a simple sidebar.
 * Posts have: title, slug, excerpt, content, image, category_id, is_published.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$current_section = 'blog';
$admin_page_title = 'Blog Posts';

$message = '';

// ============================================================
// HANDLE FORM ACTIONS (post + category)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $message = ['type' => 'error', 'text' => 'Invalid security token.'];
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            // ---- POST CREATE ----
            case 'create':
                $title = trim($_POST['title'] ?? '');
                $slug = trim($_POST['slug'] ?? '');
                $excerpt = trim($_POST['excerpt'] ?? '');
                $content = $_POST['content'] ?? '';
                $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
                $is_published = isset($_POST['is_published']) ? 1 : 0;
                
                if (empty($title) || empty($content)) {
                    $message = ['type' => 'error', 'text' => 'Title and content are required.'];
                } else {
                    if (empty($slug)) $slug = create_slug($title);
                    // Ensure unique slug
                    $existing = fetch_one($pdo, "SELECT id FROM blog_posts WHERE slug = ?", [$slug]);
                    if ($existing) $slug .= '-' . uniqid();
                    
                    $image = null;
                    if (!empty($_FILES['image']['name'])) {
                        $upload = upload_file($_FILES['image'], UPLOADS_PATH . '/blog');
                        if ($upload['success']) $image = $upload['filename'];
                        else { $message = ['type' => 'error', 'text' => $upload['error']]; break; }
                    }
                    try {
                        $stmt = $pdo->prepare(
                            "INSERT INTO blog_posts (title, slug, excerpt, content, image, category_id, author_id, is_published) VALUES (?,?,?,?,?,?,?,?)"
                        );
                        $stmt->execute([$title, $slug, $excerpt, $content, $image, $category_id, $_SESSION['user_id'], $is_published]);
                        $message = ['type' => 'success', 'text' => 'Post "' . $title . '" created.'];
                    } catch (PDOException $e) {
                        $message = ['type' => 'error', 'text' => 'Database error: ' . $e->getMessage()];
                    }
                }
                break;
            
            // ---- POST UPDATE ----
            case 'update':
                $id = (int)($_POST['id'] ?? 0);
                $title = trim($_POST['title'] ?? '');
                $slug = trim($_POST['slug'] ?? '');
                $excerpt = trim($_POST['excerpt'] ?? '');
                $content = $_POST['content'] ?? '';
                $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
                $is_published = isset($_POST['is_published']) ? 1 : 0;
                $keep_image = $_POST['keep_image'] ?? '';
                
                if ($id <= 0 || empty($title) || empty($content)) {
                    $message = ['type' => 'error', 'text' => 'Invalid data.'];
                } else {
                    if (empty($slug)) $slug = create_slug($title);
                    // unique slug excluding self
                    $existing = fetch_one($pdo, "SELECT id FROM blog_posts WHERE slug = ? AND id != ?", [$slug, $id]);
                    if ($existing) $slug .= '-' . $id;
                    
                    $image = $keep_image ?: null;
                    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $upload = upload_file($_FILES['image'], UPLOADS_PATH . '/blog');
                        if ($upload['success']) {
                            $image = $upload['filename'];
                            $old = fetch_one($pdo, "SELECT image FROM blog_posts WHERE id = ?", [$id]);
                            if ($old && $old['image']) delete_file(UPLOADS_PATH . '/blog/' . $old['image']);
                        } else { $message = ['type' => 'error', 'text' => $upload['error']]; break; }
                    }
                    try {
                        $stmt = $pdo->prepare(
                            "UPDATE blog_posts SET title=?, slug=?, excerpt=?, content=?, image=?, category_id=?, is_published=? WHERE id=?"
                        );
                        $stmt->execute([$title, $slug, $excerpt, $content, $image, $category_id, $is_published, $id]);
                        $message = ['type' => 'success', 'text' => 'Post updated.'];
                    } catch (PDOException $e) {
                        $message = ['type' => 'error', 'text' => 'Database error: ' . $e->getMessage()];
                    }
                }
                break;
            
            // ---- POST DELETE ----
            case 'delete':
                $id = (int)($_POST['id'] ?? 0);
                if ($id > 0) {
                    $post = fetch_one($pdo, "SELECT image FROM blog_posts WHERE id = ?", [$id]);
                    if ($post && $post['image']) delete_file(UPLOADS_PATH . '/blog/' . $post['image']);
                    execute_query($pdo, "DELETE FROM blog_posts WHERE id = ?", [$id]);
                    $message = ['type' => 'success', 'text' => 'Post deleted.'];
                }
                break;
            
            // ---- CATEGORY ADD ----
            case 'category_add':
                $cat_name = trim($_POST['cat_name'] ?? '');
                if (empty($cat_name)) {
                    $message = ['type' => 'error', 'text' => 'Category name is required.'];
                } else {
                    $cat_slug = create_slug($cat_name);
                    try {
                        $stmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
                        $stmt->execute([$cat_name, $cat_slug]);
                        $message = ['type' => 'success', 'text' => 'Category "' . $cat_name . '" added.'];
                    } catch (PDOException $e) {
                        $message = ['type' => 'error', 'text' => 'Category may already exist.'];
                    }
                }
                break;
            
            // ---- CATEGORY DELETE ----
            case 'category_delete':
                $id = (int)($_POST['cat_id'] ?? 0);
                if ($id > 0) {
                    execute_query($pdo, "DELETE FROM categories WHERE id = ?", [$id]);
                    $message = ['type' => 'success', 'text' => 'Category deleted.'];
                }
                break;
        }
    }
}

// ============================================================
// LIST POSTS
// ============================================================
$search = trim($_GET['search'] ?? '');
$cat_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$where = []; $params = [];
if ($search) { $where[] = "(title LIKE ? OR excerpt LIKE ? OR content LIKE ?)"; $l = '%'.$search.'%'; $params = array_merge($params, [$l,$l,$l]); }
if ($cat_filter) { $where[] = "category_id = ?"; $params[] = $cat_filter; }
$where_sql = $where ? 'WHERE '.implode(' AND ', $where) : '';

$posts = fetch_all($pdo, "
    SELECT bp.*, c.name as category_name 
    FROM blog_posts bp 
    LEFT JOIN categories c ON bp.category_id = c.id 
    $where_sql 
    ORDER BY bp.created_at DESC
", $params);

$categories = fetch_all($pdo, "SELECT * FROM categories ORDER BY name ASC");

// count per category
foreach ($categories as &$cat) {
    $cat['count'] = count_rows($pdo, "SELECT COUNT(*) as total FROM blog_posts WHERE category_id = ?", [$cat['id']]);
}
unset($cat);

$page_title = 'Blog';
include __DIR__ . '/includes/layout_start.php';
?>

<!-- Two column layout: posts (main) + categories (side) -->
<div style="display:grid;grid-template-columns:1fr 280px;gap:24px;align-items:start;">
    <div>
        <div class="admin-panel">
            <div class="admin-panel__tools">
                <h2 class="admin-panel__title" style="margin:0;"><i class="fas fa-newspaper"></i> Posts (<?= count($posts) ?>)</h2>
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <form method="GET" class="admin-search">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Search posts..." value="<?= sanitize($search) ?>">
                        <?php if ($cat_filter): ?><input type="hidden" name="category" value="<?= (int)$cat_filter ?>"><?php endif; ?>
                    </form>
                    <button class="btn btn--primary" onclick="openModal('postModal')"><i class="fas fa-plus"></i> Add Post</button>
                </div>
            </div>
            
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr><th>Image</th><th>Title</th><th>Category</th><th>Views</th><th>Status</th><th>Created</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                    <?php if (empty($posts)): ?>
                        <tr><td colspan="7" style="text-align:center;padding:30px;color:var(--color-text-muted);">No posts found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                        <tr>
                            <td>
                                <?php if ($post['image']): ?>
                                    <img src="<?= base_url('uploads/blog/' . $post['image']) ?>" alt="">
                                <?php else: ?>
                                    <span class="avatar-circle" style="border-radius:8px;"><i class="fas fa-file-alt"></i></span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= sanitize(truncate($post['title'], 45)) ?></strong></td>
                            <td><?= $post['category_name'] ? '<span class="badge badge--active">' . sanitize($post['category_name']) . '</span>' : '<span class="badge">Uncategorized</span>' ?></td>
                            <td><?= (int)$post['views'] ?></td>
                            <td><?= $post['is_published'] ? '<span class="badge badge--done">Published</span>' : '<span class="badge badge--pending">Draft</span>' ?></td>
                            <td><?= format_date($post['created_at']) ?></td>
                            <td>
                                <button class="btn btn--ghost btn--sm" onclick="editPost(<?= $post['id'] ?>)"><i class="fas fa-edit"></i> Edit</button>
                                <form method="POST" onsubmit="return confirm('Delete this post?')" style="display:inline;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $post['id'] ?>">
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
    </div>
    
    <!-- ============================================================
         CATEGORIES SIDEBAR
    ============================================================ -->
    <div>
        <div class="admin-panel">
            <h2 class="admin-panel__title"><i class="fas fa-tags"></i> Categories</h2>
            <form method="POST" style="display:flex;gap:8px;margin-bottom:16px;">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="category_add">
                <input type="text" name="cat_name" placeholder="New category..." style="flex:1;background:var(--color-bg);border:1px solid var(--color-border);border-radius:var(--radius-md);padding:9px 12px;color:var(--color-text);">
                <button class="btn btn--primary btn--sm" type="submit"><i class="fas fa-plus"></i></button>
            </form>
            <ul style="list-style:none;display:grid;gap:8px;">
                <?php foreach ($categories as $cat): ?>
                <li style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:var(--color-bg);border:1px solid var(--color-border);border-radius:var(--radius-md);font-size:var(--size-sm);">
                    <span><?= sanitize($cat['name']) ?> (<?= (int)$cat['count'] ?>)</span>
                    <form method="POST" onsubmit="return confirm('Delete this category?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="category_delete">
                        <input type="hidden" name="cat_id" value="<?= $cat['id'] ?>">
                        <button type="submit" style="background:none;border:none;color:var(--color-warm);cursor:pointer;"><i class="fas fa-trash"></i></button>
                    </form>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<!-- ADD / EDIT POST MODAL -->
<div class="admin-modal" id="postModal">
    <div class="admin-modal__content" style="max-width:820px;">
        <div class="admin-modal__header">
            <h3 id="postModalTitle">Add New Post</h3>
            <button class="admin-modal__close" onclick="closeModal('postModal')">&times;</button>
        </div>
        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <?= csrf_field() ?>
            <input type="hidden" name="action" id="p_action" value="create">
            <input type="hidden" name="id" id="p_id" value="">
            <input type="hidden" name="keep_image" id="p_keep_image" value="">
            <div class="form-group">
                <label>Title *</label>
                <input type="text" name="title" id="p_title" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" id="p_category">
                    <option value="">Uncategorized</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= sanitize($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Slug (optional)</label>
                <input type="text" name="slug" id="p_slug" placeholder="auto-generated">
            </div>
            <div class="form-group">
                <label>Post Image</label>
                <input type="file" name="image" id="p_image" accept="image/png,image/jpeg,image/webp">
                <span class="help-text">Max 5MB. JPG, PNG, WebP.</span>
            </div>
            <div class="form-group--full">
                <label>Excerpt (short summary)</label>
                <textarea name="excerpt" id="p_excerpt"></textarea>
            </div>
            <div class="form-group--full">
                <label>Content *</label>
                <textarea name="content" id="p_content" style="min-height:200px;" required></textarea>
                <span class="help-text">Basic HTML allowed: &lt;h2&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt;.</span>
            </div>
            <div class="form-group--full" style="display:flex;gap:20px;">
                <label class="form-check"><input type="checkbox" name="is_published" id="p_published" value="1" checked> Published</label>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn--primary">Save Post</button>
                <button type="button" class="btn btn--ghost" onclick="closeModal('postModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/layout_end.php'; ?>

<script>
function openModal(id) {
    document.getElementById(id).classList.add('active');
    document.getElementById('p_action').value = 'create';
    document.getElementById('p_id').value = '';
    document.getElementById('postModalTitle').textContent = 'Add New Post';
    ['p_title','p_slug','p_excerpt','p_content'].forEach(i => document.getElementById(i).value = '');
    document.getElementById('p_category').value = '';
    document.getElementById('p_image').value = '';
    document.getElementById('p_keep_image').value = '';
    document.getElementById('p_published').checked = true;
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}

document.querySelectorAll('.admin-modal').forEach(m => {
    m.addEventListener('click', function (e) {
        if (e.target === this) this.classList.remove('active');
    });
});

async function editPost(id) {
    try {
        const res = await fetch('../api/blog.php?id=' + id);
        const data = await res.json();
        if (data.success) {
            const p = data.data;
            document.getElementById('postModalTitle').textContent = 'Edit Post';
            document.getElementById('p_action').value = 'update';
            document.getElementById('p_id').value = p.id;
            document.getElementById('p_keep_image').value = p.image || '';
            document.getElementById('p_title').value = p.title;
            document.getElementById('p_slug').value = p.slug;
            document.getElementById('p_excerpt').value = p.excerpt || '';
            document.getElementById('p_content').value = p.content;
            document.getElementById('p_category').value = p.category_id || '';
            document.getElementById('p_image').value = '';
            document.getElementById('p_published').checked = !!p.is_published;
            openModal('postModal');
        } else {
            alert('Could not load post: ' + (data.message || 'Unknown error'));
        }
    } catch (err) {
        alert('Network error loading post.');
    }
}
</script>