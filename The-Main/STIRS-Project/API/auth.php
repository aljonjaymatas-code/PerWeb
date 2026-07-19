<?php
/**
 * STIRS – Auth API
 * POST /api/auth.php?action=login
 * POST /api/auth.php?action=logout
 * GET  /api/auth.php?action=check
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

session_start();
require_once __DIR__ . '/../config/db.php';
// require_once __DIR__ . '/db.php';

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'login':
        $raw  = json_decode(file_get_contents('php://input'), true);
        $user = trim($raw['username'] ?? '');
        $pass = trim($raw['password'] ?? '');

        if (!$user || !$pass) {
            echo json_encode(['success' => false, 'message' => 'Username and password are required.']);
            exit;
        }

        $db  = getDB();
        $u   = $db->real_escape_string($user);
        $res = $db->query("SELECT * FROM admins WHERE username='{$u}' LIMIT 1");

        if ($res->num_rows === 0 || !password_verify($pass, $res->fetch_assoc()['password_hash'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
        } else {
            $res->data_seek(0);
            $admin = $res->fetch_assoc();
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            echo json_encode(['success' => true, 'name' => $admin['full_name']]);
        }
        $db->close();
        break;

    case 'logout':
        session_destroy();
        echo json_encode(['success' => true]);
        break;

    case 'check':
        echo json_encode([
            'loggedIn' => isset($_SESSION['admin_id']),
            'name'     => $_SESSION['admin_name'] ?? null,
        ]);
        break;

    default:
        echo json_encode(['error' => 'Unknown action']);
}
