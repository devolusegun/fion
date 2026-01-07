<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../db.php';

/* Require logged-in user */
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login.php");
        exit;
    }
}

/* Require specific role */
function requireRole($role = 'user') {
    requireLogin();

    global $conn;
    $stmt = $conn->prepare("SELECT role FROM users WHERE id=? LIMIT 1");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user || $user['role'] !== $role) {
        http_response_code(403);
        die("Access denied.");
    }
}

/* Require email verification */
function requireVerifiedEmail() {
    requireLogin();

    global $conn;
    $stmt = $conn->prepare("SELECT email_verified FROM users WHERE id=?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();

    if ((int)$u['email_verified'] !== 1) {
        header("Location: /verify_notice.php");
        exit;
    }
}

/* Require profile completion */
function requireProfileCompletion($min = 75) {
    requireLogin();

    global $conn;
    $stmt = $conn->prepare("
        SELECT phone, address, dob, gender
        FROM registered
        WHERE id=?
    ");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();

    $fields = ['phone','address','dob','gender'];
    $filled = 0;
    foreach ($fields as $f) {
        if (!empty($p[$f])) $filled++;
    }

    $percent = ($filled / count($fields)) * 100;

    if ($percent < $min) {
        header("Location: /profile.php?incomplete=1");
        exit;
    }
}
