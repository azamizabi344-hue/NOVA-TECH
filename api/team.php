<?php
/**
 * NOVA TECH - Team API Endpoint
 * 
 * REST-style API for team members.
 *   GET    /api/team.php        - List team members
 *   GET    /api/team.php?id=5   - Get single member
 *   POST   /api/team.php        - Create (admin)
 *   PUT    /api/team.php?id=5   - Update (admin)
 *   DELETE /api/team.php?id=5   - Delete (admin)
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {

    case 'GET':
        if ($id) {
            $member = fetch_one($pdo, "SELECT * FROM team_members WHERE id = ?", [$id]);
            if ($member) {
                $member['skills'] = json_decode($member['skills'] ?? '[]', true);
                $member['social_links'] = json_decode($member['social_links'] ?? '{}', true);
                echo json_encode(['success' => true, 'data' => $member]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Member not found.']);
            }
        } else {
            $where = "WHERE is_active = 1";
            $params = [];
            
            if (!empty($_GET['department'])) {
                $where .= " AND department = ?";
                $params[] = $_GET['department'];
            }
            if (!empty($_GET['search'])) {
                $where .= " AND (full_name LIKE ? OR role LIKE ? OR department LIKE ?)";
                $search = '%' . $_GET['search'] . '%';
                $params[] = $search; $params[] = $search; $params[] = $search;
            }
            
            $members = fetch_all($pdo, "SELECT * FROM team_members $where ORDER BY sort_order ASC", $params);
            foreach ($members as &$m) {
                $m['skills'] = json_decode($m['skills'] ?? '[]', true);
                $m['social_links'] = json_decode($m['social_links'] ?? '{}', true);
            }
            
            echo json_encode(['success' => true, 'data' => $members]);
        }
        break;

    case 'POST':
        require_admin();
        
        $full_name = trim($_POST['full_name'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $department = trim($_POST['department'] ?? 'General');
        $bio = trim($_POST['bio'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $skills = $_POST['skills'] ?? [];
        $social_links = $_POST['social_links'] ?? '{}';
        
        if (empty($full_name) || empty($role)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Name and role are required.']);
            exit;
        }
        
        $photo = null;
        if (!empty($_FILES['photo']['name'])) {
            $upload = upload_file($_FILES['photo'], UPLOADS_PATH . '/team');
            if ($upload['success']) {
                $photo = $upload['filename'];
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $upload['error']]);
                exit;
            }
        }
        
        $stmt = $pdo->prepare(
            "INSERT INTO team_members (full_name, role, department, bio, photo, email, skills, social_links) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$full_name, $role, $department, $bio, $photo, $email, json_encode($skills), is_string($social_links) ? $social_links : json_encode($social_links)]);
        
        echo json_encode(['success' => true, 'message' => 'Team member added successfully.', 'id' => $pdo->lastInsertId()]);
        break;

    case 'PUT':
        require_admin();
        if (!$id) { http_response_code(400); echo json_encode(['success' => false, 'message' => 'ID required.']); exit; }
        
        parse_str(file_get_contents('php://input'), $data);
        $fields = []; $params = [];
        
        foreach (['full_name', 'role', 'department', 'bio', 'email'] as $f) {
            if (isset($data[$f])) { $fields[] = "$f = ?"; $params[] = $data[$f]; }
        }
        if (isset($data['skills'])) {
            $fields[] = "skills = ?";
            $params[] = is_array($data['skills']) ? json_encode($data['skills']) : $data['skills'];
        }
        if (isset($data['social_links'])) {
            $fields[] = "social_links = ?";
            $params[] = is_string($data['social_links']) ? $data['social_links'] : json_encode($data['social_links']);
        }
        if (isset($data['is_active'])) { $fields[] = "is_active = ?"; $params[] = $data['is_active'] ? 1 : 0; }
        
        if (!empty($fields)) {
            $params[] = $id;
            execute_query($pdo, "UPDATE team_members SET " . implode(', ', $fields) . " WHERE id = ?", $params);
        }
        echo json_encode(['success' => true, 'message' => 'Member updated successfully.']);
        break;

    case 'DELETE':
        require_admin();
        if (!$id) { http_response_code(400); echo json_encode(['success' => false, 'message' => 'ID required.']); exit; }
        $m = fetch_one($pdo, "SELECT photo FROM team_members WHERE id = ?", [$id]);
        if ($m && $m['photo']) { delete_file(UPLOADS_PATH . '/team/' . $m['photo']); }
        execute_query($pdo, "DELETE FROM team_members WHERE id = ?", [$id]);
        echo json_encode(['success' => true, 'message' => 'Member deleted successfully.']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
}
