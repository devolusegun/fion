<?php
require "db.php";
session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["user_id"])) {
    http_response_code(403);
    echo json_encode(['success'=>false,'errors'=>['Unauthorized']]);
    exit;
}

$user_id = (int)$_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(['success'=>false,'errors'=>['Invalid request method']]);
    exit;
}

function clean($v) {
    return htmlspecialchars(trim($v), ENT_QUOTES, "UTF-8");
}

$errors = [];

// required inputs
$category = isset($_POST['category']) ? clean($_POST['category']) : '';
$title = isset($_POST['title']) ? clean($_POST['title']) : '';
$summary = isset($_POST['summary']) ? clean($_POST['summary']) : '';
$justification = isset($_POST['justification']) ? clean($_POST['justification']) : '';
$budget = isset($_POST['estimated_budget']) ? clean($_POST['estimated_budget']) : '';
$location = isset($_POST['location']) ? clean($_POST['location']) : '';

if (!$category) $errors[] = 'Category is required.';
if (!$title) $errors[] = 'Title is required.';
if (!$summary) $errors[] = 'Summary is required.';
if (!$justification) $errors[] = 'Justification is required.';

// files
$allowed = ['pdf','doc','docx'];
$maxBytes = 10 * 1024 * 1024; // 10MB
$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$fileKeys = ['qualification','proposal'];
$paths = [];

foreach ($fileKeys as $k) {
    if (!isset($_FILES[$k]) || !is_uploaded_file($_FILES[$k]['tmp_name'])) {
        $errors[] = ucfirst($k)." file is required.";
        continue;
    }
    if ($_FILES[$k]['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Error uploading {$k} file.";
        continue;
    }
    $ext = strtolower(pathinfo($_FILES[$k]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        $errors[] = ucfirst($k)." must be PDF or Word document.";
        continue;
    }
    if ($_FILES[$k]['size'] > $maxBytes) {
        $errors[] = ucfirst($k)." exceeds 10MB limit.";
        continue;
    }
    // safe filename
    $safe = uniqid($k.'_') . '.' . $ext;
    $dest = $uploadDir . '/' . $safe;
    if (!move_uploaded_file($_FILES[$k]['tmp_name'], $dest)) {
        $errors[] = "Failed to save {$k} file.";
        continue;
    }
    // store relative path for DB
    $paths[$k] = 'uploads/' . $safe;
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success'=>false,'errors'=>$errors]);
    exit;
}

// insert into DB (using MySQLi $conn as in prior files)
$stmt = $conn->prepare("
    INSERT INTO project_ideas 
    (user_id, category, title, summary, justification, estimated_budget, location, qualification_doc, proposal_doc) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success'=>false,'errors'=>['Database error (prepare).']]);
    exit;
}

$stmt->bind_param(
    "issssssss",
    $user_id,
    $category,
    $title,
    $summary,
    $justification,
    $budget,
    $location,
    $paths['qualification'],
    $paths['proposal']
);

$ok = $stmt->execute();
if (!$ok) {
    http_response_code(500);
    echo json_encode(['success'=>false,'errors'=>['Database insert failed.']]);
    exit;
}

// create notification row
$notif = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
$msg = "New project idea submitted in category: $category";
if ($notif) {
    $notif->bind_param("is", $user_id, $msg);
    $notif->execute();
}

// success
echo json_encode([
    'success'=>true,
    'message'=>'Your idea was submitted successfully. We will review it shortly.'
]);
exit;
