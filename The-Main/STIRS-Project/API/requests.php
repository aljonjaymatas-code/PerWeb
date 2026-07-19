<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

require_once __DIR__ . '/../config/db.php';

$action = $_GET['action'] ?? '';

switch ($action) {


    case 'list':
    $db = getDB();

    $status = $_GET['status'] ?? 'all';
    $q      = $_GET['q'] ?? '';

    $sql = "SELECT * FROM requests WHERE 1";

    if ($status !== 'all') {
        $status = $db->real_escape_string($status);
        $sql .= " AND status = '{$status}'";
    }

    if (!empty($q)) {
        $q = $db->real_escape_string($q);
        $sql .= " AND (
            tracking_number LIKE '%{$q}%'
            OR student_id LIKE '%{$q}%'
            OR full_name LIKE '%{$q}%'
        )";
    }

    $sql .= " ORDER BY submitted_at DESC";

    $res = $db->query($sql);

    $rows = [];
    while ($row = $res->fetch_assoc()) {
        $rows[] = $row;
    }

    echo json_encode($rows);

    $db->close();
    break;
    ///////////////////    

    case 'decide':
    $db = getDB();

    $data = json_decode(file_get_contents('php://input'), true);

    $id      = intval($data['id'] ?? 0);
    $status  = $db->real_escape_string($data['status'] ?? '');
    $comment = $db->real_escape_string($data['adminComment'] ?? '');

    if (!$id || !$status) {
        echo json_encode(['success' => false, 'error' => 'Invalid data']);
        exit;
    }

    $sql = "
        UPDATE requests
        SET status = '{$status}',
            admin_comment = '{$comment}',
            processed_at = NOW()
        WHERE id = {$id}
    ";

    if ($db->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $db->error]);
    }

    $db->close();
    break;
    // ===============================
    // SUBMIT REQUEST
    // ===============================
    case 'submit':
        $raw = json_decode(file_get_contents('php://input'), true);

        if (!$raw) {
            echo json_encode(['success' => false, 'error' => 'No data received']);
            exit;
        }

        $db = getDB();

        // Generate unique tracking number
        do {
            $year = date('Y');
            $rand = str_pad(mt_rand(10000, 99999), 5, '0', STR_PAD_LEFT);
            $tracking = "REQ-{$year}-{$rand}";
            $check = $db->query("SELECT id FROM requests WHERE tracking_number='$tracking' LIMIT 1");
        } while ($check && $check->num_rows > 0);

        $s = fn($v) => $db->real_escape_string(trim($v ?? ''));

        $sql = "INSERT INTO requests
        (tracking_number, request_type, student_status, student_id,
         full_name, birthdate, department, year_level,
         address, contact, emergency_name, emergency_address, emergency_contact,
         reason, photo, signature, status, submitted_at)
        VALUES (
            '{$tracking}',
            '{$s($raw['requestType'])}',
            '{$s($raw['studentStatus'])}',
            '{$s($raw['studentId'])}',
            '{$s($raw['fullName'])}',
            '{$s($raw['birthdate'])}',
            '{$s($raw['department'])}',
            " . intval($raw['yearLevel'] ?? 1) . ",
            '{$s($raw['address'])}',
            '{$s($raw['contact'])}',
            '{$s($raw['emergencyName'])}',
            '{$s($raw['emergencyAddress'])}',
            '{$s($raw['emergencyContact'])}',
            '{$s($raw['reason'])}',
            '{$s($raw['photo'])}',
            '{$s($raw['signature'])}',
            'Pending',
            NOW()
        )";

        if ($db->query($sql)) {
            echo json_encode([
                'success' => true,
                'trackingNumber' => $tracking
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => $db->error
            ]);
        }

        $db->close();
        break;


    // ===============================
    // TRACK REQUEST
    // ===============================
    case 'track':
        $db = getDB();

        $tn = trim($_GET['trackingNumber'] ?? '');
        $si = trim($_GET['studentId'] ?? '');

        // 🔥 robust matching (handles spacing issues just in case)
        $tn = $db->real_escape_string($tn);
        $si = $db->real_escape_string($si);

        $sql = "
            SELECT tracking_number, student_id, full_name, department, year_level,
                   request_type, reason, status, admin_comment,
                   DATE_FORMAT(submitted_at,'%M %d, %Y %h:%i %p') AS submitted_at,
                   DATE_FORMAT(processed_at,'%M %d, %Y %h:%i %p') AS processed_at
            FROM requests
            WHERE TRIM(tracking_number) = TRIM('{$tn}')
              AND TRIM(student_id) = TRIM('{$si}')
            LIMIT 1
        ";

        $res = $db->query($sql);

        if (!$res || $res->num_rows === 0) {
            echo json_encode([
                'found' => false
            ]);
        } else {
            $row = $res->fetch_assoc();

            echo json_encode([
                'found' => true,
                'tracking_number' => $row['tracking_number'],
                'student_id' => $row['student_id'],
                'full_name' => $row['full_name'],
                'department' => $row['department'],
                'year_level' => $row['year_level'],
                'request_type' => $row['request_type'],
                'reason' => $row['reason'],
                'status' => $row['status'],
                'admin_comment' => $row['admin_comment'],
                'submitted_at' => $row['submitted_at'],
                'processed_at' => $row['processed_at']
            ]);
        }

        $db->close();
        break;


    default:
        echo json_encode([
            'success' => false,
            'error' => 'Unknown action'
        ]);
}