<?php
require "admin_guard.php";
require "../db.php";

$id = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if (!$id || !in_array($action, ['approve','reject'])) {
    die("Invalid request");
}

$status = $action === 'approve' ? 'approved' : 'rejected';

$stmt = $conn->prepare("UPDATE project_ideas SET status=? WHERE id=?");
$stmt->bind_param("si", $status, $id);
$stmt->execute();

header("Location: ideas.php");
exit;
