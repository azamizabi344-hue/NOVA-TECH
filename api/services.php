<?php
/**
 * NOVA TECH - Services API Endpoint
 * 
 * REST-style API for services.
 *   GET    /api/services.php        - List services
 *   GET    /api/services.php?id=5   - Get single service
 *   POST   /api/services.php        - Create (admin)
 *   PUT    /api/services.php?id=5   - Update (admin)
 *   DELETE /api/services.php?id=5   - Delete (admin)
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {

    case 'GET':
        if ($id) {
            $service = fetch_one($pdo, "SELECT * FROM services WHERE id = ?", [$id]);
            if ($service) {
                $service['features'] = json_decode($service['features'] ?? '[]', true);
                echo json_encode(['success' => true, 'data' => $service]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Service not found.']);
            }
        } else {
            $where = "WHERE is_active = 1";
            $params = [];
            
            if (!empty($_GET['category'])) {
                $where .= " AND category = ?";
                $params[] = $_GET['category'];
            }
            if (!empty($_GET['search'])) {
                $where .= " AND (title LIKE ? OR short_description LIKE ?)";
                $search = '%' . $_GET['search'] . '%';
                $params[] = $search;
                $params[] = $search;
            }
            
            $services = fetch_all($pdo, "SELECT * FROM services $where ORDER BY sort_order ASC", $params);
            foreach ($services as &$s) {
                $s['features'] = json_decode($s['features'] ?? '[]', true);
            }
            
            echo json_encode(['success' => true, 'data' => $services]);
        }
        break;

    case 'POST':
        require_admin();
        
        $title = trim($_POST['title'] ?? '');
        $short_description = trim($_POST['short_description'] ?? '');
        $full_description = trim($_POST['full_description'] ?? '');
        $icon = trim($_POST['icon'] ?? 'fas fa-cog');
        $category = trim($_POST['category'] ?? 'General');
        $price_from = floatval($_POST['price_from'] ?? 0);
        $features = $_POST['features'] ?? [];
        
        if (empty($title) || empty($short_description)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Title and description are required.']);
            exit;
        }
        
        $stmt = $pdo->prepare(
            "INSERT INTO services (title, short_description, full_description, icon, category, price_from, features) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$title, $short_description, $full_description, $icon, $category, $price_from, json_encode($features)]);
        
        echo json_encode(['success' => true, 'message' => 'Service created successfully.', 'id' => $pdo->lastInsertId()]);
        break;

    case 'PUT':
        require_admin();
        if (!$id) { http_response_code(400); echo json_encode(['success' => false, 'message' => 'ID required.']); exit; }
        
        parse_str(file_get_contents('php://input'), $data);
        $fields = []; $params = [];
        
        foreach (['title', 'short_description', 'full_description', 'icon', 'category'] as $f) {
            if (isset($data[$f])) { $fields[] = "$f = ?"; $params[] = $data[$f]; }
        }
        if (isset($data['price_from'])) { $fields[] = "price_from = ?"; $params[] = floatval($data['price_from']); }
        if (isset($data['features'])) {
            $fields[] = "features = ?";
            $params[] = is_array($data['features']) ? json_encode($data['features']) : $data['features'];
        }
        if (isset($data['is_active'])) { $fields[] = "is_active = ?"; $params[] = $data['is_active'] ? 1 : 0; }
        
        if (!empty($fields)) {
            $params[] = $id;
            execute_query($pdo, "UPDATE services SET " . implode(', ', $fields) . " WHERE id = ?", $params);
        }
        echo json_encode(['success' => true, 'message' => 'Service updated successfully.']);
        break;

    case 'DELETE':
        require_admin();
        if (!$id) { http_response_code(400); echo json_encode(['success' => false, 'message' => 'ID required.']); exit; }
        execute_query($pdo, "DELETE FROM services WHERE id = ?", [$id]);
        echo json_encode(['success' => true, 'message' => 'Service deleted successfully.']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
}
