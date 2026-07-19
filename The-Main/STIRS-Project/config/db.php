<?php
// This file already exists. You do NOT edit it.
// It defines getDB() which returns a mysqli connection.

define('DB_HOST', 'localhost:8080');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'stirs_db');

function getDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error)
        die(json_encode(['error' => 'DB failed']));
    $conn->set_charset('utf8mb4');
    return $conn;
}

?>