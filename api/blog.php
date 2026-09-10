<?php
/**
 * NOVA TECH - Blog API Endpoint
 * 
 * REST-style API for blog posts.
 *   GET    /api/blog.php              - List posts
 *   GET    /api/blog.php?id=5         - Get single post
 *   GET    /api/blog.php?slug=xxx     - Get by slug
 *   POST   /api/blog.php              - Create (admin)
 *   PUT    /api/blog.php?id=5         - Update (admin)
 *   DELETE /api/blog.php?id=5         - Delete (admin)
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$slug = $_GET['slug'] ?? null;

switch ($method) {

    case 'GET':
        if ($id || $slug) {
            // Get single post
            if ($slug) {
                $post = fetch_one($pdo, "SELECT * FROM blog_posts WHERE slug = ?", [$slug]);
            } else {
                $post = fetch_one($pdo, "SELECT * FROM blog_posts WHERE id = ?", [$id]);
            }
            if ($post) {
                // Increment view count
                execute_query($pdo, "UPDATE blog_posts SET views = views + 1 WHERE id = ?", [$post['id']]);
                $post['views']++;
                
                // Get category name
                if ($post['category_id']) {
                    $cat = fetch_one($pdo, "SELECT name, slug FROM categories WHERE id = ?", [$post['category_id']]);
                    $post['category'] = $cat;
                }
                // Get author name
                if ($post['author_id']) {
                    $author = fetch_one($pdo, "SELECT full_name FROM users WHERE id = ?", [$post['author_id']]);
                    $post['author'] = $author['full_name'] ?? 'Unknown';
                }
                echo json_encode(['success' => true, 'data' => $post]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Post not found.']);
            }
        } else {
            // List posts with filters
            $where = "WHERE bp.is_published = 1";
            $params = [];
            
            if (!empty($_GET['category_id'])) {
                $where .= " AND bp.category_id = ?";
                $params[] = (int)$_GET['category_id'];
            }
            if (!empty($_GET['category'])) {
                $where .= " AND c.slug = ?";
                $params[] = $_GET['category'];
            }
            if (!empty($_GET['search'])) {
                $where .= " AND (bp.title LIKE ? OR bp.excerpt LIKE ? OR bp.content LIKE ?)";
                $search = '%' . $_GET['search'] . '%';
                $params[] = $search; $params[] = $search; $params[] = $search;
            }
            
            // Pagination
            $page = max(1, (int)($_GET['page'] ?? 1));
            $per_page = min(50, max(1, (int)($_GET['per_page'] ?? 10)));
            $offset = ($page - 1) * $per_page;
            
            // Get total count
            $count_sql = "SELECT COUNT(*) as total FROM blog_posts bp LEFT JOIN categories c ON bp.category_id = c.id $where";
            $total = count_rows($pdo, $count_sql, $params);
            $total_pages = ceil($total / $per_page);
            
            // Get posts
            $sql = "SELECT bp.*, c.name as category_name, c.slug as category_slug 
                    FROM blog_posts bp 
                    LEFT JOIN categories c ON bp.category_id = c.id 
                    $where 
                    ORDER BY bp.created_at DESC 
                    LIMIT $per_page OFFSET $offset";
            $posts = fetch_all($pdo, $sql, $params);
            
            echo json_encode([
                'success' => true, 
                'data' => $posts, 
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $total_pages,
                    'total_posts' => $total,
                    'per_page' => $per_page
                ]
            ]);
        }
        break;

    case 'POST':
        require_admin();
        
        $title = trim($_POST['title'] ?? '');
        $slug_input = trim($_POST['slug'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = $_POST['content'] ?? '';
        $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        $is_published = isset($_POST['is_published']) ? 1 : 0;
        
        if (empty($title) || empty($content)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Title and content are required.']);
            exit;
        }
        
        // Auto-generate slug if not provided
        if (empty($slug_input)) {
            $slug_input = create_slug($title);
        }
        
        // Handle image upload
        $image = null;
        if (!empty($_FILES['image']['name'])) {
            $upload = upload_file($_FILES['image'], UPLOADS_PATH . '/blog');
            if ($upload['success']) { $image = $upload['filename']; }
        }
        
        $stmt = $pdo->prepare(
            "INSERT INTO blog_posts (title, slug, excerpt, content, image, category_id, author_id, is_published) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$title, $slug_input, $excerpt, $content, $image, $category_id, $_SESSION['user_id'] ?? null, $is_published]);
        
        echo json_encode(['success' => true, 'message' => 'Blog post created successfully.', 'id' => $pdo->lastInsertId()]);
        break;

    case 'PUT':
        require_admin();
        if (!$id) { http_response_code(400); echo json_encode(['success' => false, 'message' => 'ID required.']); exit; }
        
        parse_str(file_get_contents('php://input'), $data);
        $fields = []; $params = [];
        
        foreach (['title', 'slug', 'excerpt', 'content'] as $f) {
            if (isset($data[$f])) { $fields[] = "$f = ?"; $params[] = $data[$f]; }
        }
        if (isset($data['category_id'])) { $fields[] = "category_id = ?"; $params[] = (int)$data['category_id']; }
        if (isset($data['is_published'])) { $fields[] = "is_published = ?"; $params[] = $data['is_published'] ? 1 : 0; }
        
        if (!empty($fields)) {
            $params[] = $id;
            execute_query($pdo, "UPDATE blog_posts SET " . implode(', ', $fields) . " WHERE id = ?", $params);
        }
        echo json_encode(['success' => true, 'message' => 'Post updated successfully.']);
        break;

    case 'DELETE':
        require_admin();
        if (!$id) { http_response_code(400); echo json_encode(['success' => false, 'message' => 'ID required.']); exit; }
        $p = fetch_one($pdo, "SELECT image FROM blog_posts WHERE id = ?", [$id]);
        if ($p && $p['image']) { delete_file(UPLOADS_PATH . '/blog/' . $p['image']); }
        execute_query($pdo, "DELETE FROM blog_posts WHERE id = ?", [$id]);
        echo json_encode(['success' => true, 'message' => 'Post deleted successfully.']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
}
