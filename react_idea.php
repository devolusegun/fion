<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$user_id = $_SESSION['user_id'];

$idea_id  = (int)($_POST['idea_id'] ?? 0);
$reaction = $_POST['reaction'] ?? '';

$allowed = ['upvote', 'downvote', 'like'];
if (!$idea_id || !in_array($reaction, $allowed)) {
    http_response_code(400);
    exit;
}

/* check if already reacted */
$chk = $conn->prepare("
    SELECT id FROM idea_reactions 
    WHERE user_id=? AND idea_id=? AND reaction=?
");
$chk->bind_param("iis", $user_id, $idea_id, $reaction);
$chk->execute();
$chk->store_result();

if ($chk->num_rows > 0) {
    // already reacted → remove (toggle)
    $del = $conn->prepare("
        DELETE FROM idea_reactions 
        WHERE user_id=? AND idea_id=? AND reaction=?
    ");
    $del->bind_param("iis", $user_id, $idea_id, $reaction);
    $del->execute();
} else {
    // add reaction
    $ins = $conn->prepare("
        INSERT INTO idea_reactions (user_id, idea_id, reaction)
        VALUES (?, ?, ?)
    ");
    $ins->bind_param("iis", $user_id, $idea_id, $reaction);
    $ins->execute();
}

/* return updated counts */
$out = [];
foreach ($allowed as $r) {
    $q = $conn->prepare("
        SELECT COUNT(*) c 
        FROM idea_reactions 
        WHERE idea_id=? AND reaction=?
    ");
    $q->bind_param("is", $idea_id, $r);
    $q->execute();
    $out[$r] = $q->get_result()->fetch_assoc()['c'];
}

header('Content-Type: application/json');
echo json_encode($out);
