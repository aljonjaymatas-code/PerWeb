<?php
/**
 * STIRS – One-time database setup
 * Run once: http://localhost/stirs/api/setup.php
 */

$conn = new mysqli('localhost:8080', 'root', '', '');
if ($conn->connect_error) die('Cannot connect to MySQL: ' . $conn->connect_error);

// Create database
$conn->query("CREATE DATABASE IF NOT EXISTS `stirs_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db('stirs_db');

// Requests table
$conn->query("
CREATE TABLE IF NOT EXISTS `requests` (
    `id`               INT AUTO_INCREMENT PRIMARY KEY,
    `tracking_number`  VARCHAR(30)   NOT NULL UNIQUE,
    `request_type`     VARCHAR(30)   NOT NULL,
    `student_status`   VARCHAR(20)   NOT NULL,
    `student_id`       VARCHAR(20)   NOT NULL,
    `full_name`        VARCHAR(120)  NOT NULL,
    `birthdate`        DATE          NOT NULL,
    `department`       VARCHAR(100)  NOT NULL,
    `year_level`       TINYINT       NOT NULL,
    `address`          VARCHAR(255)  NOT NULL,
    `contact`          VARCHAR(15)   NOT NULL,
    `emergency_name`   VARCHAR(120)  NOT NULL,
    `emergency_address` VARCHAR(255) NOT NULL,
    `emergency_contact` VARCHAR(15)  NOT NULL,
    `reason`           TEXT,
    `photo`            LONGTEXT,
    `signature`        LONGTEXT,
    `status`           ENUM('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
    `admin_comment`    TEXT,
    `submitted_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `processed_at`     DATETIME,
    INDEX (`student_id`),
    INDEX (`status`),
    INDEX (`submitted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

// Admins table
$conn->query("
CREATE TABLE IF NOT EXISTS `admins` (
    `id`           INT AUTO_INCREMENT PRIMARY KEY,
    `username`     VARCHAR(60)  NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name`    VARCHAR(120) NOT NULL,
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");

// Seed default admin: admin / admin123
$hash = password_hash('admin123', PASSWORD_DEFAULT);
$conn->query("
    INSERT IGNORE INTO `admins` (username, password_hash, full_name)
    VALUES ('admin', '$hash', 'Admin Doms')
");

$conn->close();
echo '<h2 style=\"font-family:monospace;color:#2E7D32;\">✅ STIRS database set up successfully!</h2>';
echo '<p>Default login → username: <b>admin</b> | password: <b>admin123</b></p>';
echo '<p><a href=\"../admin/adminLogin.php\">Go to Admin Login</a></p>';
