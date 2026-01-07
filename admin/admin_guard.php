<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit;
}

require_once __DIR__ . "/../db.php";

$stmt = $conn->prepare("SELECT role FROM users WHERE id=? LIMIT 1");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || $user['role'] !== 'admin') {
    http_response_code(403);
    die("Access denied.");
}
