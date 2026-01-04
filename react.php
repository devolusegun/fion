<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$user_id = $_SESSION['user_id'];
$idea_id = (int)($_POST['idea_id'] ?? 0);
$type = $_POST['reaction'] ?? '';

$allowed = ['like','upvote','downvote'];
if ($idea_id <= 0 || !in_array($type, $allowed)) {
    http_response_code(400);
    exit;
}

/* Check ownership */
$chk = $conn->prepare("SELECT user_id FROM project_ideas WHERE id=?");
$chk->bind_param("i", $idea_id);
$chk->execute();
$owner = $chk->get_result()->fetch_assoc();

if (!$owner || $owner['user_id'] == $user_id) {
    http_response_code(403);
    exit;
}

/* Remove previous reaction */
$del = $conn->prepare("DELETE FROM idea_reactions WHERE idea_id=? AND user_id=?");
$del->bind_param("ii", $idea_id, $user_id);
$del->execute();

/* Insert new reaction */
$ins = $conn->prepare("
    INSERT INTO idea_reactions (idea_id, user_id, reaction)
    VALUES (?, ?, ?)
");
$ins->bind_param("iis", $idea_id, $user_id, $type);
$ins->execute();

/* Return updated counts */
$res = $conn->query("
    SELECT reaction, COUNT(*) c 
    FROM idea_reactions 
    WHERE idea_id = $idea_id 
    GROUP BY reaction
");

echo json_encode($res->fetch_all(MYSQLI_ASSOC));
