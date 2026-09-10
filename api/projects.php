<?php
/**
 * NOVA TECH - Projects API Endpoint
 * 
 * REST-style API for projects.
 *   GET  /api/projects.php              - List all projects (with optional filters)
 *   GET  /api/projects.php?id=5         - Get single project by ID
 *   POST /api/projects.php              - Create project (admin only)
 *   PUT  /api/projects.php?id=5         - Update project (admin only)
 *   DELETE /api/projects.php?id=5       - Delete project (admin only)
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {

    // ============================================================
    // GET - Read projects
    // ============================================================
    case 'GET':
        if ($id) {
            // Get single project
            $project = fetch_one($pdo, "SELECT * FROM projects WHERE id = ?", [$id]);
            if ($project) {
                echo json_encode(['success' => true, 'data' => $project]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Project not found.']);
            }
        } else {
            // List projects with optional filters
            $where = "WHERE is_active = 1";
            $params = [];
            
            if (!empty($_GET['category'])) {
                $where .= " AND category = ?";
                $params[] = $_GET['category'];
            }
            if (!empty($_GET['featured'])) {
                $where .= " AND is_featured = 1";
            }
            if (!empty($_GET['search'])) {
                $where .= " AND (title LIKE ? OR description LIKE ? OR client LIKE ?)";
                $search = '%' . $_GET['search'] . '%';
                $params[] = $search;
                $params[] = $search;
                $params[] = $search;
            }
            
            $order = $_GET['sort'] === 'oldest' ? 'ASC' : 'DESC';
            
            $projects = fetch_all($pdo, 
                "SELECT * FROM projects $where ORDER BY created_at $order", 
                $params
            );
            
            // Decode JSON fields
            foreach ($projects as &$p) {
                $p['technologies'] = json_decode($p['technologies'] ?? '[]', true);
            }
            
            echo json_encode(['success' => true, 'data' => $projects, 'count' => count($projects)]);
        }
        break;

    // ============================================================
    // POST - Create project (admin only)
    // ============================================================
    case 'POST':
        require_admin();
        
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $full_description = trim($_POST['full_description'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $client = trim($_POST['client'] ?? '');
        $project_url = trim($_POST['project_url'] ?? '');
        $technologies = $_POST['technologies'] ?? [];
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        
        if (empty($title) || empty($description) || empty($category)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Title, description, and category are required.']);
            exit;
        }
        
        // Handle image upload
        $image = null;
        if (!empty($_FILES['image']['name'])) {
            $upload = upload_file($_FILES['image'], UPLOADS_PATH . '/projects');
            if ($upload['success']) {
                $image = $upload['filename'];
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $upload['error']]);
                exit;
            }
        }
        
        $stmt = $pdo->prepare(
            "INSERT INTO projects (title, description, full_description, image, category, technologies, client, project_url, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $title, $description, $full_description, $image,
            $category, json_encode($technologies), $client, $project_url, $is_featured
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Project created successfully.', 'id' => $pdo->lastInsertId()]);
        break;

    // ============================================================
    // PUT - Update project (admin only)
    // ============================================================
    case 'PUT':
        require_admin();
        
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Project ID is required.']);
            exit;
        }
        
        parse_str(file_get_contents('php://input'), $data);
        
        $fields = [];
        $params = [];
        
        foreach (['title', 'description', 'full_description', 'category', 'client', 'project_url'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        if (isset($data['technologies'])) {
            $fields[] = "technologies = ?";
            $params[] = is_array($data['technologies']) ? json_encode($data['technologies']) : $data['technologies'];
        }
        
        if (isset($data['is_featured'])) {
            $fields[] = "is_featured = ?";
            $params[] = $data['is_featured'] ? 1 : 0;
        }
        
        if (isset($data['is_active'])) {
            $fields[] = "is_active = ?";
            $params[] = $data['is_active'] ? 1 : 0;
        }
        
        if (empty($fields)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No fields to update.']);
            exit;
        }
        
        $params[] = $id;
        execute_query($pdo, "UPDATE projects SET " . implode(', ', $fields) . " WHERE id = ?", $params);
        
        echo json_encode(['success' => true, 'message' => 'Project updated successfully.']);
        break;

    // ============================================================
    // DELETE - Delete project (admin only)
    // ============================================================
    case 'DELETE':
        require_admin();
        
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Project ID is required.']);
            exit;
        }
        
        // Delete associated image file
        $project = fetch_one($pdo, "SELECT image FROM projects WHERE id = ?", [$id]);
        if ($project && $project['image']) {
            delete_file(UPLOADS_PATH . '/projects/' . $project['image']);
        }
        
        execute_query($pdo, "DELETE FROM projects WHERE id = ?", [$id]);
        echo json_encode(['success' => true, 'message' => 'Project deleted successfully.']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
}
